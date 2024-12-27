<?php
if (!isset($_SESSION['diagnosis_results']) || !isset($_SESSION['patient_info'])) {
    header("Location: welcome.php?page=diagnosis");
    exit();
}

$results = $_SESSION['diagnosis_results'];
$patient = $_SESSION['patient_info'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnosis Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .confidence-meter {
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        .confidence-bar {
            height: 100%;
            transition: width 0.5s ease-in-out;
        }
        .medication-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 10px;
        }
        .prescription-required {
            border-left-color: #ffc107;
        }
        .severity-high {
            border-left-color: #dc3545;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .card {
                border: 1px solid #dee2e6 !important;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white py-3">
            <h2 class="mb-0">Diagnostic Results</h2>
        </div>
        <div class="card-body">
            <!-- Patient Information -->
            <div class="mb-4">
                <h3 class="border-bottom pb-2">Patient Information</h3>
                <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <?php echo isset($_SESSION['fname']) ? htmlspecialchars($_SESSION['fname']) : 'Not provided'; ?></p>
                    <p><strong>Age:</strong> <?php echo isset($_SESSION['age']) ? htmlspecialchars($_SESSION['age']) : 'Not provided'; ?></p>
                    <p><strong>Gender:</strong> <?php echo isset($_SESSION['gender']) ? htmlspecialchars(ucfirst($_SESSION['gender'])) : 'Not provided'; ?></p>
                </div>
                    <div class="col-md-6">
                        <p><strong>Reported Symptoms:</strong> <?php echo htmlspecialchars($patient['symptoms']['description']); ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($patient['symptoms']['duration']))); ?></p>
                        <p><strong>Severity:</strong> <?php echo htmlspecialchars(ucfirst($patient['symptoms']['severity'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Diagnostic Alert -->
            <div class="alert alert-warning mb-4">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Important Notice</h4>
                <p class="mb-0">This is an automated preliminary assessment based on the symptoms you provided. The results 
                   should not be considered as a final diagnosis. Please consult with a healthcare professional for proper 
                   medical evaluation.</p>
            </div>

            <!-- Potential Conditions -->
            <div class="mb-4">
                <h3 class="border-bottom pb-2">Potential Conditions</h3>
                <?php if (empty($results)): ?>
                <div class="alert alert-info">
                    <h5 class="alert-heading">No Matches Found</h5>
                    <p class="mb-0">No specific conditions matched your symptoms. Please consult a healthcare provider for proper evaluation.</p>
                </div>
                <?php else: ?>
                    <?php foreach ($results as $index => $result): ?>
                    <div class="card mb-4 <?php echo $index === 0 ? 'border-primary' : ''; ?>">
                        <div class="card-header <?php echo $index === 0 ? 'bg-primary text-white' : 'bg-light'; ?>">
                            <h4 class="mb-0">
                                <?php echo htmlspecialchars($result['disease_name']); ?>
                                <span class="float-end badge <?php 
                                    echo $result['match_percentage'] >= 70 ? 'bg-danger' : 
                                         ($result['match_percentage'] >= 50 ? 'bg-warning' : 'bg-info'); 
                                ?>">
                                    <?php echo htmlspecialchars($result['match_percentage']); ?>% Match
                                </span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Disease Information -->
                                    <div class="mb-3">
                                        <h5>Description</h5>
                                        <p><?php echo htmlspecialchars($result['description']); ?></p>
                                        <h5>Common Symptoms</h5>
                                        <p><?php echo htmlspecialchars($result['symptom_list']); ?></p>
                                        
                                        <!-- Severity Level -->
                                        <div class="alert alert-<?php 
                                            echo $result['severity_level'] == 'high' ? 'danger' : 
                                                 ($result['severity_level'] == 'moderate' ? 'warning' : 'info'); 
                                        ?> mb-3">
                                            <strong>Severity Level:</strong> 
                                            <?php echo ucfirst(htmlspecialchars($result['severity_level'])); ?>
                                        </div>
                                    </div>

                                    <!-- Medication Recommendations -->
                                    <?php if (!empty($result['recommended_medications']) && 
                                            $result['severity_level'] != 'high' && 
                                            $result['severity_level'] != 'critical'): ?>
                                    <div class="mt-4">
                                        <h5>Recommended Medications</h5>
                                        <div class="alert alert-info">
                                            <small><i class="fas fa-info-circle"></i> Note: These are general recommendations. 
                                            Please consult a healthcare provider before taking any medication.</small>
                                        </div>
                                        <div class="row">
                                            <?php foreach ($result['recommended_medications'] as $med): ?>
                                            <div class="col-12">
                                                <div class="card medication-card <?php echo $med['requires_prescription'] == 1 ? 'prescription-required' : ''; ?> mb-2">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            <?php echo htmlspecialchars($med['name']); ?>
                                                            <?php if ($med['requires_prescription'] == 1): ?>
                                                                <span class="badge bg-warning float-end">Prescription Required</span>
                                                            <?php endif; ?>
                                                        </h6>
                                                        <p class="card-text">
                                                            <small class="text-muted">
                                                                <?php echo htmlspecialchars($med['instructions']); ?>
                                                            </small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Hospital Recommendations -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">Recommended Hospital<?php echo count($result['recommended_hospitals']) > 1 ? 's' : ''; ?></h5>
                                        </div>
                                        <div class="card-body">
                                            <?php foreach ($result['recommended_hospitals'] as $index => $hospital): ?>
                                            <div class="<?php echo $index > 0 ? 'mt-3 pt-3 border-top' : ''; ?>">
                                                <h6 class="card-title"><?php echo htmlspecialchars($hospital['name']); ?></h6>
                                                <p class="card-text">
                                                    <strong>Level:</strong> <?php echo htmlspecialchars($hospital['level']); ?><br>
                                                    <strong>Location:</strong> <?php echo htmlspecialchars($hospital['location']); ?><br>
                                                    <strong>Contact:</strong> <?php echo htmlspecialchars($hospital['contact']); ?><br>
                                                    <?php if (!empty($hospital['specializations'])): ?>
                                                    <strong>Specializations:</strong> <?php echo htmlspecialchars($hospital['specializations']); ?>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Emergency Information -->
            <?php if (isset($patient['symptoms']['severity']) && 
                      in_array($patient['symptoms']['severity'], ['severe', 'critical'])): ?>
            <div class="alert alert-danger">
                <h4 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Emergency Notice!</h4>
                <p>Based on the severity of your symptoms, we recommend immediate medical attention.</p>
                <hr>
                <p class="mb-0">
                    <strong>Emergency Contacts:</strong><br>
                    Emergency Services: 999<br>
                    Ambulance: 1199
                </p>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="mt-4 no-print">
                <div class="row">
                    <div class="col-auto">
                        <a href="welcome.php?page=diagnosis" class="btn btn-primary">
                            <i class="fas fa-stethoscope"></i> Start New Diagnosis
                        </a>
                    </div>
                    <div class="col-auto">
                        <button onclick="window.print()" class="btn btn-secondary">
                            <i class="fas fa-print"></i> Print Results
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="welcome.php" class="btn btn-outline-primary">
                            <i class="fas fa-home"></i> Return Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer with Disclaimer -->
<footer class="container mb-4">
    <div class="alert alert-secondary">
        <small class="text-muted">
            Disclaimer: This system is designed to provide preliminary guidance only and should not replace professional 
            medical advice. Always consult with qualified healthcare professionals for proper diagnosis and treatment.
        </small>
    </div>
</footer>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate confidence bars on load
    const confidenceBars = document.querySelectorAll('.confidence-bar');
    confidenceBars.forEach(bar => {
        const width = bar.getAttribute('data-width');
        setTimeout(() => {
            bar.style.width = width + '%';
        }, 100);
    });

    // Initialize any tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php
// Clear session data after displaying results
unset($_SESSION['diagnosis_results']);
unset($_SESSION['patient_info']);
?>

</body>
</html>