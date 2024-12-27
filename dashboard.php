<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "medical");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function to get disease distribution
function getDiseaseDistribution($conn) {
    $sql = "SELECT d.disease_name, COUNT(diag.diagnosis_id) as count 
            FROM diseases d 
            LEFT JOIN diagnosis diag ON d.disease_id = diag.suspected_disease_id 
            GROUP BY d.disease_id 
            ORDER BY count DESC";
    return $conn->query($sql);
}

// Helper function to get hospital referral distribution
function getHospitalReferrals($conn) {
    $sql = "SELECT h.name, COUNT(d.diagnosis_id) as referral_count 
            FROM kenya_specialist_hospitals h 
            LEFT JOIN diagnosis d ON h.hospital_id = d.hospital_referral_id 
            GROUP BY h.hospital_id 
            ORDER BY referral_count DESC";
    return $conn->query($sql);
}

// Helper function to get medication usage
function getMedicationUsage($conn) {
    $sql = "SELECT m.name, COUNT(dm.disease_id) as usage_count 
            FROM medications m 
            LEFT JOIN disease_medications dm ON m.medication_id = dm.medication_id 
            GROUP BY m.medication_id 
            ORDER BY usage_count DESC";
    return $conn->query($sql);
}

// Helper function to get severity distribution
function getSeverityDistribution($conn) {
    $sql = "SELECT severity_level, COUNT(*) as count 
            FROM diseases 
            GROUP BY severity_level";
    return $conn->query($sql);
}

// Get the data
$diseases = getDiseaseDistribution($conn);
$hospitals = getHospitalReferrals($conn);
$medications = getMedicationUsage($conn);
$severities = getSeverityDistribution($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical System Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Medical System Analytics Dashboard</h1>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <?php
            // Total Patients
            $patientCount = $conn->query("SELECT COUNT(*) as count FROM patient")->fetch_assoc()['count'];
            // Total Diagnoses
            $diagnosisCount = $conn->query("SELECT COUNT(*) as count FROM diagnosis")->fetch_assoc()['count'];
            // Average Confidence
            $avgConfidence = $conn->query("SELECT AVG(match_confidence) as avg FROM diagnosis")->fetch_assoc()['avg'];
            ?>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Total Patients</h3>
                <p class="text-3xl font-bold"><?php echo $patientCount; ?></p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Total Diagnoses</h3>
                <p class="text-3xl font-bold"><?php echo $diagnosisCount; ?></p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Average Diagnosis Confidence</h3>
                <p class="text-3xl font-bold"><?php echo number_format($avgConfidence, 1); ?>%</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Disease Distribution Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Disease Distribution</h2>
                <canvas id="diseaseChart"></canvas>
            </div>

            <!-- Hospital Referrals Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Hospital Referrals</h2>
                <canvas id="hospitalChart"></canvas>
            </div>

            <!-- Medication Usage Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Medication Usage</h2>
                <canvas id="medicationChart"></canvas>
            </div>

            <!-- Severity Distribution Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Disease Severity Distribution</h2>
                <canvas id="severityChart"></canvas>
            </div>
        </div>

        <script>
            // Disease Distribution Chart
            new Chart(document.getElementById('diseaseChart'), {
                type: 'bar',
                data: {
                    labels: [<?php 
                        $labels = [];
                        $data = [];
                        while($row = $diseases->fetch_assoc()) {
                            $labels[] = "'" . $row['disease_name'] . "'";
                            $data[] = $row['count'];
                        }
                        echo implode(',', $labels);
                    ?>],
                    datasets: [{
                        label: 'Number of Cases',
                        data: [<?php echo implode(',', $data); ?>],
                        backgroundColor: 'rgba(54, 162, 235, 0.5)'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Hospital Referrals Chart
            new Chart(document.getElementById('hospitalChart'), {
                type: 'pie',
                data: {
                    labels: [<?php 
                        $labels = [];
                        $data = [];
                        while($row = $hospitals->fetch_assoc()) {
                            $labels[] = "'" . $row['name'] . "'";
                            $data[] = $row['referral_count'];
                        }
                        echo implode(',', $labels);
                    ?>],
                    datasets: [{
                        data: [<?php echo implode(',', $data); ?>],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(153, 102, 255, 0.5)'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Medication Usage Chart
            new Chart(document.getElementById('medicationChart'), {
                type: 'doughnut',
                data: {
                    labels: [<?php 
                        $labels = [];
                        $data = [];
                        while($row = $medications->fetch_assoc()) {
                            $labels[] = "'" . $row['name'] . "'";
                            $data[] = $row['usage_count'];
                        }
                        echo implode(',', $labels);
                    ?>],
                    datasets: [{
                        data: [<?php echo implode(',', $data); ?>],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(153, 102, 255, 0.5)'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Severity Distribution Chart
            new Chart(document.getElementById('severityChart'), {
                type: 'polarArea',
                data: {
                    labels: [<?php 
                        $labels = [];
                        $data = [];
                        while($row = $severities->fetch_assoc()) {
                            $labels[] = "'" . $row['severity_level'] . "'";
                            $data[] = $row['count'];
                        }
                        echo implode(',', $labels);
                    ?>],
                    datasets: [{
                        data: [<?php echo implode(',', $data); ?>],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        </script>
    </div>
</body>
</html>

<?php
$conn->close();
?>