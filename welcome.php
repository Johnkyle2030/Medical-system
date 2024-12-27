<?php
session_start();

// Database connection with error handling
$con = mysqli_connect("localhost", "root", "", "medical");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}


if (!isset($_SESSION['id'])) {
    // If not logged in, redirect to login page
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['id']; 
$query = "SELECT * FROM patients WHERE id = '$patient_id'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);


$page = $_GET['page'] ?? 'home';
$allowed_pages = ['home', 'diagnosis', 'results'];
$page = in_array($page, $allowed_pages) ? $page : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Medical Diagnosis System</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="welcome.php">Medical Diagnosis System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav me-auto">
                    <a class="nav-link" href="welcome.php">Home</a>
                    <a class="nav-link" href="welcome.php?page=diagnosis">New Diagnosis</a>
                </div>
                <div class="navbar-nav">
                    <span class="navbar-text me-3">
                        Welcome, <?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?>
                    </span>
                    <a href="logout.php" class="nav-link text-warning">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php
        // Display any session messages
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        
        // Include the appropriate page view
        $file_path = "views/{$page}.php";
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            include 'views/home.php';
        }
        ?>
    </div>

    <footer class="container mt-5 mb-3">
        <hr>
        <p class="text-center text-muted">
            © 2024 Medical Diagnosis System - Kenya. This system is for informational purposes only and 
            should not replace professional medical advice.
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>