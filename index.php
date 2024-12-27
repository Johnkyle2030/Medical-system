<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration - Diagnosis System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f6f8f9 0%, #e5ebee 100%);
            font-family: 'Inter', sans-serif;
        }
        .registration-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 50px;
        }
        .registration-header {
            background: linear-gradient(to right, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .registration-form {
            padding: 30px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border-color: #e1e5eb;
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .btn-primary {
            background: linear-gradient(to right, #4e73df 0%, #224abe 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1);
        }
        .side-image {
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%234e73df" fill-opacity="1" d="M0,160L48,176C96,192,192,224,288,229.3C384,235,480,213,576,197.3C672,181,768,171,864,181.3C960,192,1056,224,1152,229.3C1248,235,1344,213,1392,202.7L1440,192L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0L192,0L96,0L0,0Z"></path></svg>') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .side-image img {
            max-width: 300px;
            opacity: 0.9;
        }
        #password-requirements {
            font-size: 0.8rem;
            margin-top: 5px;
        }
        .requirement {
            color: red;
        }
        .requirement.valid {
            color: green;
        }
        #message {
            font-size: 0.8rem;
            margin-top: 5px;
        }
        .login-link {
            color: #4e73df;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .login-link:hover {
            color: #224abe;
            text-decoration: underline;
        }
        .error-message {
            color: #f55252;
            font-size: 0.8rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row registration-container">
            <div class="col-md-5 d-none d-md-flex side-image">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGRhdGEtbmFtZT0iTGF5ZXIgMSIgdmlld0JveD0iMCAwIDUxMiA1MTIiPjxwYXRoIGZpbGw9IiM0ZTczZGYiIGQ9Ik00MTQuNiAxNjguM2ExMC42IDEwLjYgMCAwIDAtMTAuNiAxMC42djExNy41aC0xMTcuNWExMC42IDEwLjYgMCAwIDAtMTAuNiAxMC42djMxLjhhMTAuNiAxMC42IDAgMCAwIDEwLjYgMTAuNmgxMTcuNXYxMTcuNWExMC42IDEwLjYgMCAwIDAgMTAuNiAxMC42aDMxLjhhMTAuNiAxMC42IDAgMCAwIDEwLjYtMTAuNnYtMTE3LjVoMTE3LjVhMTAuNiAxMC42IDAgMCAwIDEwLjYtMTAuNnYtMzEuOGExMC42IDEwLjYgMCAwIDAtMTAuNi0xMC42aC0xMTcuNXYtMTE3LjVhMTAuNiAxMC42IDAgMCAwLTEwLjYtMTAuNmgtMzEuOHoiLz48Y2lyY2xlIGN4PSIyNTYiIGN5PSIyNTYiIHI9IjIwNS40IiBmaWxsPSJub25lIiBzdHJva2U9IiM0ZTczZGYiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgc3Ryb2tlLXdpZHRoPSIzMCIvPjwvc3ZnPg==" alt="Registration Illustration">
            </div>
            <div class="col-md-7 registration-form">
                <div class="registration-header">
                    <h2>Patient Registration</h2>
                    <p class="text-white-50">Create your patient account</p>
                </div>
                <form method="post" action="register_process.php" class="p-4" id="registrationForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="fname" name="fname" 
                                   onkeydown="return alphaOnly(event);" required placeholder="Enter first name"/>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lname" name="lname" 
                                   onkeydown="return alphaOnly(event);" required placeholder="Enter last name"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" 
                                   name="date_of_birth" onchange="calculateAge()" required>
                        </div>
                        <div class="col-md-6 mb-3">
    <label for="birth_place" class="form-label">Birth Place</label>
    <select class="form-control" id="birth_place" name="birth_place" required>
        <option value="" disabled selected>Select your county</option>
        <option value="Nairobi">Nairobi</option>
        <option value="Mombasa">Mombasa</option>
        <option value="Kisumu">Kisumu</option>
        <option value="Nakuru">Nakuru</option>
        <option value="Kiambu">Kiambu</option>
        <option value="Machakos">Machakos</option>
        <option value="Uasin Gishu">Uasin Gishu</option>
        <option value="Kericho">Kericho</option>
        <option value="Nyeri">Nyeri</option>
        <option value="Bungoma">Bungoma</option>
        <option value="Kakamega">Kakamega</option>
        <option value="Meru">Meru</option>
        <option value="Kitui">Kitui</option>
        <option value="Homabay">Homabay</option>
        <option value="Kisii">Kisii</option>
    </select>
    <div id="birthPlaceError" class="error-message"></div>
</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" 
                                   name="email" required placeholder="Enter email address">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="contact" 
                                   name="contact" minlength="10" maxlength="10" required 
                                   placeholder="Enter phone number" 
                                   onkeypress="return validatePhoneNumber(event)">
                            <div id="contactError" class="error-message"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" 
                                   name="password" onkeyup='checkPasswordStrength(); check();' required placeholder="Create password">
                            <div id="password-requirements">
                                <div id="length" class="requirement">At least 8 characters long</div>
                                <div id="capital" class="requirement">At least one capital letter</div>
                                <div id="small" class="requirement">At least one small letter</div>
                                <div id="number" class="requirement">At least one number</div>
                                <div id="symbol" class="requirement">At least one symbol (!@#$%^&*)</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cpassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="cpassword" 
                                   name="cpassword" onkeyup='check();' required placeholder="Confirm password">
                            <span id='message'></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="gender" id="male" value="Male" checked>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="female" value="Female">
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="currentcity" class="form-label">Current City/Town</label>
                            <select class="form-control" id="birth_place" name="birth_place" required>
                              <option value="" disabled selected>Select your county</option>
                             <option value="Nairobi">Nairobi</option>
                            <option value="Mombasa">Mombasa</option>
                             <option value="Kisumu">Kisumu</option>
                             <option value="Nakuru">Nakuru</option>
                             <option value="Kiambu">Kiambu</option>
                             <option value="Machakos">Machakos</option>
                             <option value="Uasin Gishu">Uasin Gishu</option>
                             <option value="Kericho">Kericho</option>
                             <option value="Nyeri">Nyeri</option>
                             <option value="Bungoma">Bungoma</option>
                             <option value="Kakamega">Kakamega</option>
                            <option value="Meru">Meru</option>
                            <option value="Kitui">Kitui</option>
                            <option value="Homabay">Homabay</option>
                            <option value="Kisii">Kisii</option>
                           </select>
                                   
                                 <div id="currentCityError" class="error-message"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="patsub1" class="btn btn-primary w-100">Register</button>
                        <p class="mt-3">
                            Already have an account? 
                            <a href="index1.php" class="login-link">Login here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function validatePhoneNumber(event) {
            var charCode = event.which ? event.which : event.keyCode;
            var contactError = document.getElementById('contactError');
           
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                contactError.textContent = 'Please enter numbers only';
                return false;
            }
         
            contactError.textContent = '';
            return true;
        }

        function validateLettersOnly(event, errorElementId) {
            var charCode = event.which ? event.which : event.keyCode;
            var errorElement = document.getElementById(errorElementId);
            
           
            if (!(
                (charCode >= 65 && charCode <= 90) || 
                (charCode >= 97 && charCode <= 122) || 
                charCode === 8 || 
                charCode === 32
            )) {
                errorElement.textContent = 'Please enter letters only';
                return false;
            }
            
            errorElement.textContent = '';
            return true;
        }

        function alphaOnly(event) {
            var key = event.keyCode;
            return ((key >= 65 && key <= 90) || key == 8 || key == 32);
        }

        function checkPasswordStrength() {
    var password = document.getElementById('password').value;
    
    // Length check
    var lengthRequirement = document.getElementById('length');
    if (password.length >= 8) {
        lengthRequirement.classList.remove('requirement');
        lengthRequirement.classList.add('valid');
    } else {
        lengthRequirement.classList.remove('valid');
        lengthRequirement.classList.add('requirement');
    }
    
    // Capital letter check
    var capitalRequirement = document.getElementById('capital');
    if (/[A-Z]/.test(password)) {
        capitalRequirement.classList.remove('requirement');
        capitalRequirement.classList.add('valid');
    } else {
        capitalRequirement.classList.remove('valid');
        capitalRequirement.classList.add('requirement');
    }
    
    // Small letter check
    var smallRequirement = document.getElementById('small');
    if (/[a-z]/.test(password)) {
        smallRequirement.classList.remove('requirement');
        smallRequirement.classList.add('valid');
    } else {
        smallRequirement.classList.remove('valid');
        smallRequirement.classList.add('requirement');
    }
    
    // Number check
    var numberRequirement = document.getElementById('number');
    if (/[0-9]/.test(password)) {
        numberRequirement.classList.remove('requirement');
        numberRequirement.classList.add('valid');
    } else {
        numberRequirement.classList.remove('valid');
        numberRequirement.classList.add('requirement');
    }
    
    // Symbol check
    var symbolRequirement = document.getElementById('symbol');
    if (/[!@#$%^&*]/.test(password)) {
        symbolRequirement.classList.remove('requirement');
        symbolRequirement.classList.add('valid');
    } else {
        symbolRequirement.classList.remove('valid');
        symbolRequirement.classList.add('requirement');
    }
}

var check = function() {
    if (document.getElementById('password').value ==
        document.getElementById('cpassword').value) {
        document.getElementById('message').style.color = '#5dd05d';
        document.getElementById('message').innerHTML = 'Passwords Matched';
    } else {
        document.getElementById('message').style.color = '#f55252';
        document.getElementById('message').innerHTML = 'Passwords Not Matching';
    }
}

function calculateAge() {
    var dob = new Date(document.getElementById('date_of_birth').value);
    var today = new Date();
    var age = today.getFullYear() - dob.getFullYear();
    
    var m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
    }

    

    if (dob > today) {
        alert('Future dates are not allowed');
        document.getElementById('date_of_birth').value = '';
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>