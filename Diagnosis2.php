<?php
session_start();

// Database connection with error handling
$con = mysqli_connect("localhost", "root", "", "medical");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

class DiagnosisProcessor {
    private $conn;
    private $diseasesData;
    private $hospitalData;

    public function __construct($dbConnection) {
        if ($dbConnection === null) {
            throw new Exception("Database connection cannot be null");
        }
        $this->conn = $dbConnection;
        $this->loadDiseaseData();
        $this->loadHospitalData();
    }

    private function loadDiseaseData() {
        // Load diseases and symptoms from CSV
        $this->diseasesData = [];
        $csvPath = __DIR__ . "/diseases_symptoms.csv";
        if (file_exists($csvPath) && ($handle = fopen($csvPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $this->diseasesData[] = $data;
            }
            fclose($handle);
        }
    }

    private function loadHospitalData() {
        // Load hospital data from CSV
        $this->hospitalData = [];
        $csvPath = __DIR__ . "/hospitals.csv";
        if (file_exists($csvPath) && ($handle = fopen($csvPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $this->hospitalData[] = $data;
            }
            fclose($handle);
        }
    }

    public function processDiagnosis($symptoms, $duration, $severity) {
        $matchedDiseases = $this->findMatchingDiseases($symptoms);
        $recommendedHospital = $this->getRecommendedHospital($severity);
        $recommendedMedication = $this->getRecommendedMedication($matchedDiseases);

        // Save patient and diagnosis details
        $this->saveDiagnosisDetails($matchedDiseases, $recommendedHospital, $recommendedMedication, $severity, $duration, $symptoms);

        return [
            'diseases' => $matchedDiseases,
            'hospital' => $recommendedHospital,
            'medication' => $recommendedMedication
        ];
    }

    private function findMatchingDiseases($userSymptoms) {
        $matchedDiseases = [];
        $userSymptomsList = explode(',', strtolower($userSymptoms));

        foreach ($this->diseasesData as $diseaseEntry) {
            $diseaseSymptoms = explode(',', strtolower($diseaseEntry[1]));
            $matchCount = count(array_intersect($userSymptomsList, $diseaseSymptoms));
            
            if ($matchCount > 0) {
                $matchedDiseases[] = [
                    'name' => $diseaseEntry[0],
                    'symptoms' => $diseaseEntry[1],
                    'match_percentage' => ($matchCount / count($diseaseSymptoms)) * 100
                ];
            }
        }

        // Sort by match percentage in descending order
        usort($matchedDiseases, function($a, $b) {
            return $b['match_percentage'] <=> $a['match_percentage'];
        });

        return $matchedDiseases;
    }

    private function getRecommendedHospital($severity) {
        $levelMap = [
            'mild' => 4,
            'moderate' => 5,
            'critical' => 6
        ];

        $recommendedHospitals = array_filter($this->hospitalData, function($hospital) use ($levelMap, $severity) {
            return $hospital[1] == $levelMap[$severity];
        });

        return $recommendedHospitals ? $recommendedHospitals[array_rand($recommendedHospitals)] : null;
    }

    private function getRecommendedMedication($diseases) {
        $recommendations = [];
        foreach ($diseases as $disease) {
            // This would typically come from your CSV or database
            $recommendations[] = $this->findMedicationForDisease($disease['name']);
        }
        return $recommendations;
    }

    private function findMedicationForDisease($diseaseName) {
        // Placeholder - in a real system, this would query a medication CSV or database
        $medicationMap = [
            'Common Cold' => 'Paracetamol, Rest, Hydration',
            'Flu' => 'Oseltamivir, Ibuprofen, Rest',
            // Add more mappings
        ];

        return $medicationMap[$diseaseName] ?? 'Generic Symptom Relief Medication';
    }

    private function saveDiagnosisDetails($diseases, $hospital, $medication, $severity, $duration, $symptoms) {
        // Debugging: Log the details
        error_log("Saving Diagnosis Details");
        error_log("Severity: $severity");
        error_log("Duration: $duration");
        error_log("Symptoms: $symptoms");

        // Ensure user_id is set
        if (!isset($_SESSION['user_id'])) {
            // If not set, try to fetch the most recent patient ID
            $userId = $this->getLatestPatientId();
            
            if ($userId === null) {
                error_log("No valid user ID found");
                return;
            }
        } else {
            $userId = $_SESSION['user_id'];
        }

        // Prepare disease name and hospital name
        $diseaseName = $diseases ? $diseases[0]['name'] : 'Undiagnosed';
        $hospitalName = $hospital ? $hospital[0] : 'No Hospital Recommended';

        // Prepare medication string
        $medicationString = is_array($medication) ? implode(', ', $medication) : $medication;

        // Add error checking for database preparation
        try {
            // Updated SQL to match the diseases table schema
            $stmt = $this->conn->prepare("INSERT INTO diseases (pid, disease_name, hospital_recommended, severity_level, medication) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            // Bind parameters with correct types
            $stmt->bind_param("issss", $userId, $diseaseName, $hospitalName, $severity, $medicationString);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute statement: " . $stmt->error);
            }

            error_log("Diagnosis details saved successfully");
            $stmt->close();
        } catch (Exception $e) {
            // Log the error
            error_log("Database Error: " . $e->getMessage());
        }
    }

    private function getLatestPatientId() {
        // Fetch the most recently inserted patient ID
        $query = "SELECT id FROM patients ORDER BY id DESC LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result && $row = $result->fetch_assoc()) {
            return $row['id'];
        }
        
        return null;
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $processor = new DiagnosisProcessor($con);
        $diagnosisResult = $processor->processDiagnosis(
            $_POST['symptoms'], 
            $_POST['duration'], 
            $_POST['severity']
        );

        // Store results in session for result page
        $_SESSION['diagnosis_result'] = $diagnosisResult;

        // Redirect to results page
        header("Location: diagnosis_result.php");
        exit();
    } catch (Exception $e) {
        // Handle any exceptions that might occur
        error_log("Processing Error: " . $e->getMessage());
        die("An error occurred: " . $e->getMessage());
    }
}
?>