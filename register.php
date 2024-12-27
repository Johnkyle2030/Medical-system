<!DOCTYPE html>
<html>
<head>
    <title>HMS</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link rel="stylesheet" type="text/css" href="style1.css">
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">

    <style>
        .form-control {
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;  /* Added spacing between form fields */
            padding: 0.8rem;        /* Increased internal padding */
            font-size: 1rem;
        }

        /* Improved label styling */
        .form-group label {
            margin-bottom: 0.5rem;
            color: #495057;
            font-weight: 500;
        }

        /* Enhanced radio button styling */
        .maxl {
            margin: 1.5rem 0;
        }

        .radio {
            margin-right: 1.5rem;
        }

        .radio input[type="radio"] {
            margin-right: 0.5rem;
        }

        /* Keep all original styles */
        .register {
            background: -webkit-linear-gradient(left, #3931af, #00c6ff);
            margin-top: 3%;
            padding: 3%;
        }

        .register-left {
            text-align: center;
            color: #fff;
            margin-top: 4%;
        }

        .register-left img {
            margin-top: 15%;
            margin-bottom: 5%;
            width: 25%;
            -webkit-animation: mover 2s infinite alternate;
            animation: mover 1s infinite alternate;
        }

        .register-right {
            background: #f8f9fa;
            border-top-left-radius: 10% 50%;
            border-bottom-left-radius: 10% 50%;
            padding: 3rem;  /* Added padding to the form container */
        }

        .btnRegister {
            float: right;
            margin-top: 2rem;  /* Adjusted button spacing */
            border: none;
            border-radius: 1.5rem;
            padding: 0.8rem 2rem;  /* Improved button padding */
            background: #0062cc;
            color: #fff;
            font-weight: 600;
            width: auto;          /* Changed to auto width */
            min-width: 150px;     /* Added minimum width */
            cursor: pointer;
            transition: all 0.3s ease;  /* Added smooth hover effect */
        }

        .btnRegister:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .register-heading {
            text-align: center;
            margin: 2rem 0 3rem 0;  /* Improved heading spacing */
            color: #495057;
            font-weight: bold;
        }

        /* Added field group spacing */
        .field-group {
            margin-bottom: 2rem;
            padding: 1rem;
            background: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Added required field indicator */
        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        /* Added responsive padding */
        @media (max-width: 768px) {
            .register-right {
                padding: 1.5rem;
            }
            .form-control {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Keep the original navbar -->
    [Previous navbar code remains unchanged]

    <div class="container register">
        <div class="row">
            <div class="col-md-3 register-left">
                <img src="images/welcome.webp" alt=""/>
                <h3>Welcome</h3>
            </div>
            <div class="col-md-9 register-right">
                <!-- Keep original tabs -->
                [Previous tabs code remains unchanged]

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                        <h3 class="register-heading">Register as Patient</h3>
                        <form method="post" action="func2.php">
                            <div class="row register-form">
                                <div class="col-md-6">
                                    <div class="field-group">
                                        <label class="required-field">Personal Information</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="First Name" name="fname" onkeydown="return alphaOnly(event);" required/>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Second Name" name="sname" onkeydown="return alphaOnly(event);" required/>
                                        </div>
                                        <div class="form-group">
                                            <input type="date" class="form-control" placeholder="Date of Birth" name="dob" required/>
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <label class="required-field">Location Details</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Birth Place" name="birthplace" required/>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Current City/Town" name="currentcity" required/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="field-group">
                                        <label class="required-field">Additional Information</label>
                                        <div class="form-group">
                                            <input type="number" class="form-control" placeholder="Age" name="age" min="0" max="100" required/>
                                        </div>
                                        <div class="form-group">
                                            <div class="maxl">
                                                <label class="required-field">Gender</label><br/>
                                                <label class="radio inline"> 
                                                    <input type="radio" name="gender" value="Male" checked>
                                                    <span>Male</span> 
                                                </label>
                                                <label class="radio inline"> 
                                                    <input type="radio" name="gender" value="Female">
                                                    <span>Female</span> 
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <label class="required-field">Contact Information</label>
                                        <div class="form-group">
                                            <input type="tel" class="form-control" minlength="10" maxlength="10" placeholder="Phone Number" name="contact" required/>
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" placeholder="Email" name="email" required/>
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <label class="required-field">Account Details</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Username" name="username" required/>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" placeholder="Password" id="password" name="password" onkeyup='check();' required/>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" placeholder="Confirm Password" id="cpassword" name="cpassword" onkeyup='check();' required/>
                                            <span id='message'></span>
                                        </div>
                                    </div>

                                    <input type="submit" class="btnRegister" name="patsub1" onclick="return checklen();" value="Register"/>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Keep other tabs unchanged -->
                    [Previous doctor, receptionist, and admin tabs remain unchanged]
                </div>
            </div>
        </div>
    </div>

    <!-- Keep original scripts -->
    [Previous scripts remain unchanged]
</body>
</html>