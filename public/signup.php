<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTCI E-Library Portal - Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../src/css/global.css" rel="stylesheet">
    <style>
        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 5px;
            opacity: 0;
            transition: opacity 1s;
            font-size: 16px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
        <?php include 'student/status/successMessage.php'; ?>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <?php include 'student/status/errorMessage.php'; ?>
    <?php endif; ?>
    <nav class="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../../src/img/ptci/logo.png" alt="PTCI Logo" class="logo">
                PALAWAN TECHNOLOGICAL COLLEGE, Inc.
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="row main-container">
            <div class="col-lg-6 left-section">
                <p class="welcome-text">Welcome to <span>Palawan Technological College, Inc.</span></p>
                <h1>E-LIBRARY<br>PORTAL</h1>
                <p>Join our library community and access online resources anytime, anywhere.</p>
                <ul>
                    <li>Search library's collection.</li>
                    <li>File reservation requests.</li>
                    <li>Online book borrowing, renewal, and many more.</li>
                </ul>
                <button class="btn btn-contact">Contact Us</button>
            </div>
            <div class="col-lg-6 right-section">
                <div class="login-container" style="max-width: 500px;">
                    <div class="login-header">
                        <h3>Create Account</h3>
                        <p>Please create your account</p>
                    </div>

                    <form action="../../back-end/create/createUser.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname"
                                       placeholder="Enter your first name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname"
                                       placeholder="Enter your last name" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                   placeholder="Enter your username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Enter your password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                   placeholder="Confirm your password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">User Type</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="user_type" id="student" value="student" required>
                                <label class="form-check-label" for="student">
                                    Student
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="user_type" id="teacher" value="teacher" required>
                                <label class="form-check-label" for="teacher">
                                    Teacher
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-login w-100">Sign Up</button>
                    </form>
                    
                    <div class="signup-link">
                        Already have an account? <a href="../../auth/login.php">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
