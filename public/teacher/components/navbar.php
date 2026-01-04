<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user_type = $_SESSION['user_type'];
$username = $_SESSION['username'];
?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../src/img/ptci/logo.png" alt="PTCI Logo" class="logo" style="height: 40px;">
            PTCI E-Library
        </a>
        <div class="d-flex align-items-center">
            <span class="navbar-text me-3 text-white">Welcome, <?php echo htmlspecialchars($username); ?> (<?php echo ucfirst($user_type); ?>)</span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-modern">Logout</a>
        </div>
    </div>
</nav>
