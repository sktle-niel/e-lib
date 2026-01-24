<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTCI E-Library Portal - Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../src/css/global.css" rel="stylesheet">
</head>
<body>
    <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
        <div id="error-message" style="position: fixed; top: 20px; right: 20px; padding: 15px 20px; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border-radius: 5px; opacity: 0; transition: opacity 1s; font-size: 16px; z-index: 1000;">Invalid username or password.</div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var msg = document.getElementById("error-message");
                msg.style.opacity = "1";
                setTimeout(function() {
                    msg.style.opacity = "0";
                    setTimeout(function() {
                        msg.style.display = "none";
                    }, 1000);
                }, 3000);
            });
        </script>
    <?php endif; ?>
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
                <p>Reset your password to regain access to the library's online resources and services.</p>
                <ul>
                    <li>Search library's collection.</li>
                    <li>File reservation requests.</li>
                    <li>Online book borrowing, renewal, and many more.</li>
                </ul>
            </div>
            <div class="col-lg-6 right-section">
                <div class="login-container">
                    <div class="login-header">
                        <h3>Forgot Password</h3>
                        <p>Enter your email to reset your password</p>
                    </div>

                    <form action="../../back-end/update/forgotPassword.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                   placeholder="Enter your username" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-login w-100">Send Reset Link</button>
                    </form>

                    <div class="signup-link">
                        <a href="login.php">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>