<?php
class Database {
    private $host = "localhost";
    private $db_name = "medical";
    private $username = "root";
    private $password = "";
    private $conn;
    
    public function connect() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8mb4");
            return $this->conn;
        } catch(Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            return null;
        }
    }

    public function getError() {
        return $this->conn ? $this->conn->error : "No database connection";
    }
}