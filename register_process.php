<?php
session_start();
require_once 'db_connection.php';

// Disable error output before redirect
error_reporting(0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $birth_place = mysqli_real_escape_string($conn, $_POST['birth_place']);
    $currentcity = mysqli_real_escape_string($conn, $_POST['currentcity']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $religion = mysqli_real_escape_string($conn, $_POST['religion']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    
    // Calculate Age
    $age = date_diff(date_create($date_of_birth), date_create('today'))->y;

    // Password Hashing
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
   
    // Validate Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Check if email already exists
    $email_check = "SELECT * FROM patients WHERE email = '$email'";
    $email_result = $conn->query($email_check);
    if ($email_result->num_rows > 0) {
        die("Email already registered");
    }

    // Prepare SQL to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO patients 
        (fname, lname, gender, email, contact, password, date_of_birth, birth_place, currentcity, age, religion) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Bind parameters
    $stmt->bind_param("sssssssssss", 
        $fname, $lname, $gender, $email, $contact, 
        $password, $date_of_birth, $birth_place, 
        $currentcity, $age, $religion);

    // Execute query
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;

    
        $_SESSION['id'] = $user_id;  
        $_SESSION['fname'] = $fname;
        $_SESSION['email'] = $email;
        
        // Ensure no output before redirect
        ob_clean();
        
        // Redirect to welcome page
        header("Location: welcome.php");
        exit();
    } else {
        // Log the error or handle it appropriately
        error_log("Registration Error: " . $stmt->error);
        die("Registration failed. Please try again.");
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>