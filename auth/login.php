<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTCI E-Library Portal - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../src/css/global.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../src/img/ptci/logo.png" alt="PTCI Logo" class="logo">
                PALAWAN TECHNOLOGICAL COLLEGE, Inc.
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="row main-container">
            <div class="col-lg-6 left-section">
                <p class="welcome-text">Welcome to <span>Palawan Technological College, Inc.</span></p>
                <h1>E-LIBRARY<br>PORTAL</h1>
                <p>Login to access the library's online resources and services anytime, anywhere.</p>
                <ul>
                    <li>Search library's collection.</li>
                    <li>File reservation requests.</li>
                    <li>Online book borrowing, renewal, and many more.</li>
                </ul>
                <button class="btn btn-contact">Contact Us</button>
            </div>
            <div class="col-lg-6 right-section">
                <div class="login-container">
                    <div class="login-header">
                        <h3>Welcome Back</h3>
                        <p>Please login to your account</p>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Enter your username">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password">
                    </div>

                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="button" class="btn btn-primary btn-login w-100">Login</button>

                    <div class="signup-link">
                        Don't have an account? <a href="../public/signup.php">Sign up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>