<?php
session_start();
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $lrn = trim($_POST['lrn']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($lrn) || empty($new_password) || empty($confirm_password)) {
        header("Location: ../../auth/forgotPassword.php?error=1");
        exit;
    }

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        header("Location: ../../auth/forgotPassword.php?error=2");
        exit;
    }

    // Check if username and LRN match
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND lrn_number = ?");
    $stmt->bind_param("ss", $username, $lrn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);

        if ($update_stmt->execute()) {
            header("Location: ../../auth/forgotPassword.php?success=1");
            exit;
        } else {
            header("Location: ../../auth/forgotPassword.php?error=3");
            exit;
        }
    } else {
        header("Location: ../../auth/forgotPassword.php?error=1");
        exit;
    }
}
?>
