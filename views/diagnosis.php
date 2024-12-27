<div class="card">
    <div class="card-header">
        <h2>Patient Information and Symptoms</h2>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h5>Important:</h5>
            <p>Please provide accurate information to help us better assess your condition.</p>
        </div>
        
        <form action="diagnosis2.php" method="POST" class="needs-validation" novalidate>
           
            
            <!-- Symptoms Information -->
            <div class="mb-4">
    <h4 class="mb-3">Symptoms Information</h4>
    <div class="mb-4">
    
        <label for="symptoms" class="form-label">What symptoms are you experiencing?</label>
        <select class="form-control" id="symptoms" name="symptoms">
            <option value="fever">Fever</option>
            <option value="cough">Cough</option>
            <option value="runny nose">Runny Nose</option>
            <option value="high fever">High Fever</option>
            <option value="body aches">Body Aches</option>
            <option value="fatigue">Fatigue</option>
            <option value="severe cough">Severe Cough</option>
            <option value="chest pain">Chest Pain</option>
            <option value="difficulty breathing">Difficulty Breathing</option>
            <option value="persistent cough">Persistent Cough</option>
            <option value="mucus">Mucus</option>
            <option value="wheezing">Wheezing</option>
        </select>
        <small class="form-text text-muted">You can select multiple symptoms by holding down the Ctrl (Windows) or Command (Mac) key while selecting.</small>
        <div class="invalid-feedback">Please select at least one symptom.</div>
    </div>
</div>

</div>
                <div class="mb-3">
                    <label for="duration" class="form-label">How long have you had these symptoms?</label>
                    <select class="form-control" id="duration" name="duration" required>
                        <option value="">Select duration</option>
                        <option value="today">Just today</option>
                        <option value="few_days">Few days</option>
                        <option value="week">About a week</option>
                        <option value="more">More than a week</option>
                    </select>
                    <div class="invalid-feedback">Please select the duration of your symptoms.</div>
                </div>
                <div class="mb-3">
                    <label for="severity" class="form-label">How severe are your symptoms?</label>
                    <select class="form-control" id="severity" name="severity" required>
                        <option value="">Select severity</option>
                        <option value="mild">Mild - Noticeable but not interfering with daily activities</option>
                        <option value="moderate">Moderate - Somewhat interfering with daily activities</option>
                        <option value="critical">Critical - Unable to perform daily activities</option>
                    </select>
                    <div class="invalid-feedback">Please select the severity of your symptoms.</div>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        I understand that this is an automated system and the results should not replace 
                        professional medical advice.
                    </label>
                    <div class="invalid-feedback">You must agree before submitting.</div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Get Diagnosis</button>
        </form>
    </div>
</div>

<script>
// Form validation script
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>