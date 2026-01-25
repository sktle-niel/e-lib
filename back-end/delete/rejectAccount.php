<?php
session_start();
include '../../config/connection.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];

    // Validate type
    if ($type !== 'teacher' && $type !== 'librarian') {
        header("Location: ../../public/admin/main.php?page=" . $type . "&error=invalid_type");
        exit;
    }

    // Delete the account from pending_accounts
    $stmt = $conn->prepare("DELETE FROM pending_accounts WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to the appropriate page with success message
        header("Location: ../../public/admin/main.php?page=" . $type . "&success=account_rejected");
    } else {
        // Redirect back with error message
        header("Location: ../../public/admin/main.php?page=" . $type . "&error=delete_failed");
    }

    $stmt->close();
    $conn->close();
    exit;
} else {
    header("Location: ../../public/admin/main.php");
    exit;
}
?>
