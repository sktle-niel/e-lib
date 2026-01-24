<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/create/createAdmin.php';

$currentPage = 'Admin';

$message = '';
$messageType = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    // Create admin account directly (no pending approval needed)
    $result = createAdmin($username, $password, $firstname, $lastname);

    if ($result['success']) {
        $successMessage = 'Admin account created successfully!';
    } else {
        $message = $result['message'];
        $messageType = 'danger';
    }
}
?>

<link rel="stylesheet" href="../../src/css/phoneMediaQuery.css">

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
    display: none;
}

#next-programs {
    position: absolute;
    right: -60px !important;
    top: 50%;
    transform: translateY(-50%) !important;
    cursor: pointer;
}
</style>

<!-- Success Message -->
<div id="success-message" class="success-message">Admin account created successfully!</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $currentPage; ?> Account Creation</h1>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Success message function
    function showSuccessMessage() {
        const msg = document.getElementById('success-message');
        msg.style.display = 'block';
        msg.style.opacity = '1';
        setTimeout(function() {
            msg.style.opacity = '0';
            setTimeout(function() {
                msg.style.display = 'none';
                location.reload();
            }, 1000);
        }, 3000);
    }

    // Show success message if account was created
    <?php if (!empty($successMessage)): ?>
        showSuccessMessage();
    <?php endif; ?>
});
</script>

    <!-- Message Display -->
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Create Admin Form -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Create New Admin Account</h5>
                    </div>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" required>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-person-plus me-2"></i>Create Admin Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">Admin Account Information</h6>
                    <div class="text-muted small">
                        <p><i class="bi bi-info-circle me-2"></i>Admin accounts have full access to the system including:</p>
                        <ul class="mb-0">
                            <li>Approving/rejecting teacher and librarian accounts</li>
                            <li>Viewing all approved accounts</li>
                            <li>Creating additional admin accounts</li>
                            <li>Full system management</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
