<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];

    // Validate type
    if ($type !== 'teacher' && $type !== 'librarian') {
        header("Location: ../../public/admin/main.php?page=dashboard&error=invalid_type");
        exit;
    }

    // Get pending account details
    $stmt = $conn->prepare("SELECT username, password, user_type, firstname, lastname FROM pending_accounts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $account = $result->fetch_assoc();

        // Insert into users table
        $insertStmt = $conn->prepare("INSERT INTO users (id, firstname, lastname, username, password, user_type) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("isssss", $id, $account['firstname'], $account['lastname'], $account['username'], $account['password'], $account['user_type']);

        if ($insertStmt->execute()) {
            // Delete from pending_accounts
            $deleteStmt = $conn->prepare("DELETE FROM pending_accounts WHERE id = ?");
            $deleteStmt->bind_param("i", $id);
            $deleteStmt->execute();
            $deleteStmt->close();

            header("Location: ../../public/admin/main.php?page=" . $type . "s&success=approved");
        } else {
            header("Location: ../../public/admin/main.php?page=" . $type . "s&error=approval_failed");
        }

        $insertStmt->close();
    } else {
        header("Location: ../../public/admin/main.php?page=" . $type . "s&error=account_not_found");
    }

    $stmt->close();
} else {
    header("Location: ../../public/admin/main.php?page=dashboard");
}

$conn->close();
?>
