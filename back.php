<?php

class Database {
    private $host = "localhost";
    private $db_name = "medical";
    private $username = "root";  
    private $password = "";      
    private $conn;
    
    public function getConnection() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            return $this->conn;
        } catch(Exception $e) {
            echo "Connection error: " . $e->getMessage();
            return null;
        }
    }
}

// classes/PatientSystem.php
class PatientSystem {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Register new patient
    public function registerPatient($data) {
        $query = "INSERT INTO patients (first_name, last_name, date_of_birth, gender, phone_number, 
                                      email, birth_place, current_address, city, country) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssssssss", 
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'],
                $data['gender'],
                $data['phone_number'],
                $data['email'],
                $data['birth_place'],
                $data['current_address'],
                $data['city'],
                $data['country']
            );
            
            if($stmt->execute()) {
                return $this->conn->insert_id;
            }
            return false;
        } catch(Exception $e) {
            error_log("Error registering patient: " . $e->getMessage());
            return false;
        }
    }
    
    // Record health issues
    public function recordHealthIssue($patientId, $data) {
        $query = "INSERT INTO health_issues (patient_id, description, onset_date, severity) 
                  VALUES (?, ?, ?, ?)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("isss", 
                $patientId,
                $data['description'],
                $data['onset_date'],
                $data['severity']
            );
            
            if($stmt->execute()) {
                return $this->conn->insert_id;
            }
            return false;
        } catch(Exception $e) {
            error_log("Error recording health issue: " . $e->getMessage());
            return false;
        }
    }
    
    // Perform diagnosis
    public function performDiagnosis($healthIssueId) {
        try {
            // Get health issue details
            $query = "SELECT hi.*, p.city 
                     FROM health_issues hi 
                     JOIN patients p ON hi.patient_id = p.patient_id 
                     WHERE hi.issue_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $healthIssueId);
            $stmt->execute();
            $issue = $stmt->get_result()->fetch_assoc();
            
            // Match symptoms with diseases
            $query = "SELECT * FROM diseases WHERE common_symptoms LIKE ?";
            $stmt = $this->conn->prepare($query);
            $searchTerm = '%' . $issue['description'] . '%';
            $stmt->bind_param("s", $searchTerm);
            $stmt->execute();
            $diseases = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            // Get recommended hospital for severe cases
            $hospital = null;
            if ($issue['severity'] == 'severe') {
                $query = "SELECT * FROM kenya_specialist_hospitals 
                         WHERE level = 'Level 6' 
                         ORDER BY RAND() LIMIT 1";
                $result = $this->conn->query($query);
                $hospital = $result->fetch_assoc();
            }
            
            return [
                'diseases' => $diseases,
                'hospital' => $hospital
            ];
        } catch(Exception $e) {
            error_log("Error performing diagnosis: " . $e->getMessage());
            return false;
        }
    }
    
    // Book appointment
    public function bookAppointment($patientId, $hospitalId, $appointmentDate, $reason) {
        $query = "INSERT INTO appointments (patient_id, hospital_id, appointment_date, status, reason) 
                  VALUES (?, ?, ?, 'pending', ?)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iiss", 
                $patientId,
                $hospitalId,
                $appointmentDate,
                $reason
            );
            
            if($stmt->execute()) {
                return $this->conn->insert_id;
            }
            return false;
        } catch(Exception $e) {
            error_log("Error booking appointment: " . $e->getMessage());
            return false;
        }
    }
}
?>