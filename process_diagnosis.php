<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->connect();
    
    if (!$conn) {
        $_SESSION['error'] = "System error. Please try again later.";
        header("Location: welcome.php?page=diagnosis");
        exit();
    }

    try {
        $conn->begin_transaction();

        // Validate patient ID from login session
        if (!isset($_SESSION['patient_id'])) {
            throw new Exception("Patient not logged in.");
        }
        $patient_id = $_SESSION['patient_id'];

        // Sanitize and validate inputs
        $symptoms = filter_var(trim($_POST['symptoms']), FILTER_SANITIZE_STRING);
        $duration = filter_var(trim($_POST['duration']), FILTER_SANITIZE_STRING);
        $severity = filter_var(trim($_POST['severity']), FILTER_SANITIZE_STRING);

        if (!$symptoms || !$duration || !$severity) {
            throw new Exception("All fields are required.");
        }

        // Process symptoms
        $symptoms_array = explode(',', strtolower($symptoms));
        $symptoms_array = array_map('trim', $symptoms_array);
        $symptoms_string = implode(' ', $symptoms_array);

        // Modified disease matching query with severity filtering
        $sql = "SELECT d.*, 
                GROUP_CONCAT(DISTINCT ds.symptom) as symptom_list,
                COUNT(DISTINCT ds.symptom) as matching_symptoms,
                GROUP_CONCAT(DISTINCT 
                    CONCAT(m.name, ':|:', m.usage_instructions, ':|:', m.requires_prescription)
                    SEPARATOR '||'
                ) as medications
                FROM diseases d
                JOIN disease_symptoms ds ON d.disease_id = ds.disease_id
                LEFT JOIN disease_medications dm ON d.disease_id = dm.disease_id
                LEFT JOIN medications m ON dm.medication_id = m.medication_id
                WHERE (MATCH(ds.symptom) AGAINST (? IN NATURAL LANGUAGE MODE)
                OR ds.symptom REGEXP ?)
                AND d.severity_level = ?
                GROUP BY d.disease_id
                HAVING matching_symptoms >= 1
                ORDER BY matching_symptoms DESC";
        
        $symptom_pattern = implode('|', $symptoms_array);
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $symptoms_string, $symptom_pattern, $severity);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $matches = [];
        while ($row = $result->fetch_assoc()) {
            // Calculate match confidence
            $total_disease_symptoms = count(explode(',', $row['symptom_list']));
            $matching_symptom_count = $row['matching_symptoms'];
            
            $symptom_match_score = ($matching_symptom_count / $total_disease_symptoms) * 70;
            similar_text(strtolower($symptoms_string), strtolower($row['symptom_list']), $text_similarity);
            $text_similarity_score = $text_similarity * 0.3;
            
            $match_confidence = min(100, $symptom_match_score + $text_similarity_score);
            $match_percentage = round($match_confidence);

            if ($match_percentage > 30) {
                // Process medications
                $medications = [];
                if ($row['medications']) {
                    foreach (explode('||', $row['medications']) as $med) {
                        list($name, $instructions, $requires_prescription) = explode(':|:', $med);
                        $medications[] = [
                            'name' => $name,
                            'instructions' => $instructions,
                            'requires_prescription' => $requires_prescription
                        ];
                    }
                }

                // Hospital recommendation based on severity
                $hospital_query = "SELECT hospital_id, hospital_name, level FROM kenya_specialist_hospitals WHERE 1=1 ";
                
                switch($severity) {
                    case 'mild':
                        $hospital_query .= "AND level IN ('Level 4', 'Level 5') ";
                        $hospital_limit = 1;
                        break;
                    case 'moderate':
                        $hospital_query .= "AND level IN ('Level 5', 'Level 6') ";
                        $hospital_limit = 1;
                        break;
                    case 'critical':
                        $hospital_query .= "AND level = 'Level 6' ";
                        $hospital_limit = 1;
                        break;
                    default:
                        $hospital_limit = 1;
                }

                $hospital_query .= "ORDER BY RAND() LIMIT " . $hospital_limit;
                
                $hospital_stmt = $conn->prepare($hospital_query);
                $hospital_stmt->execute();
                $hospital_result = $hospital_stmt->get_result();
                $recommended_hospital = $hospital_result->fetch_assoc();

                // Prepare disease insertion
                $insert_disease_sql = "INSERT INTO diseases (
                    disease_name, 
                    description, 
                    severity_level, 
                    patient_id, 
                    recommended_hospital_id, 
                    match_confidence, 
                    symptoms, 
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

                $insert_stmt = $conn->prepare($insert_disease_sql);
                $insert_stmt->bind_param(
                    "sssiisd", 
                    $row['disease_name'], 
                    $row['description'], 
                    $severity, 
                    $patient_id, 
                    $recommended_hospital['hospital_id'], 
                    $match_percentage,
                    $symptoms_string
                );
                $insert_stmt->execute();
                $new_disease_id = $insert_stmt->insert_id;

                // Store medications
                if (!empty($medications)) {
                    $med_insert_sql = "INSERT INTO disease_medications (disease_id, medication_id) VALUES (?, ?)";
                    $med_stmt = $conn->prepare($med_insert_sql);
                    
                    foreach ($medications as $med) {
                        $medication_lookup_sql = "SELECT medication_id FROM medications WHERE name = ?";
                        $lookup_stmt = $conn->prepare($medication_lookup_sql);
                        $lookup_stmt->bind_param("s", $med['name']);
                        $lookup_stmt->execute();
                        $medication_result = $lookup_stmt->get_result();
                        
                        if ($medication_result->num_rows > 0) {
                            $medication = $medication_result->fetch_assoc();
                            $med_stmt->bind_param("ii", $new_disease_id, $medication['medication_id']);
                            $med_stmt->execute();
                        }
                    }
                }

                $matches[] = [
                    'disease_name' => $row['disease_name'],
                    'match_percentage' => $match_percentage,
                    'recommended_hospital' => $recommended_hospital['hospital_name'],
                    'hospital_level' => $recommended_hospital['level']
                ];
            }
        }

        $conn->commit();

        $_SESSION['diagnosis_results'] = $matches;
        $_SESSION['patient_info'] = [
            'symptoms' => [
                'description' => $symptoms,
                'duration' => $duration,
                'severity' => $severity
            ]
        ];
        
        header("Location: welcome.php?page=results");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Diagnosis Error: " . $e->getMessage());
        $_SESSION['error'] = "Error processing diagnosis. Please try again or contact support if the problem persists.";
        header("Location: welcome.php?page=diagnosis");
        exit();
    }
} else {
    header("Location: welcome.php");
    exit();
}

