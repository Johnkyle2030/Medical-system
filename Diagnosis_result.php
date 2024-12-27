<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "medical");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if diagnosis results exist in session
if (!isset($_SESSION['diagnosis_result'])) {
    header("Location: index.php");
    exit();
}

$diagnosisResult = $_SESSION['diagnosis_result'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Diagnosis Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2>Diagnosis Results</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Potential Diseases</h4>
                        <ul class="list-group">
                            <?php foreach ($diagnosisResult['diseases'] as $disease): ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($disease['name']); ?> 
                                    (Match: <?php printf("%.2f%%", $disease['match_percentage']); ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Recommended Hospital</h4>
                        <p class="alert alert-info">
                            <?php echo htmlspecialchars($diagnosisResult['hospital'][0] ?? 'No hospital recommended'); ?>
                        </p>

                        <h4>Recommended Medication</h4>
                        <ul class="list-group">
                            <?php foreach ($diagnosisResult['medication'] as $medication): ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($medication); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>