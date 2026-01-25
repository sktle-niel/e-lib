<?php
session_start();
include '../../config/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Fetch user_type
    $userStmt = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
    $userStmt->bind_param("i", $id);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    if ($userResult->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'No account found with that ID']);
        $userStmt->close();
        $conn->close();
        exit;
    }
    $userRow = $userResult->fetch_assoc();
    $userType = $userRow['user_type'];
    $userStmt->close();

    // Check if student has borrowed books
    if ($userType == 'student') {
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM borrowed_lib_books WHERE user_id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        $checkStmt->close();

        if ($row['count'] > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Cannot delete user. They have ' . $row['count'] . ' borrowed book record(s). Please remove borrowed books first.'
            ]);
            $conn->close();
            exit;
        }
    }

    // Delete the user account
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Account deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No account found with that ID']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete account']);
    }

    $stmt->close();
    $conn->close();
    exit;

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}
