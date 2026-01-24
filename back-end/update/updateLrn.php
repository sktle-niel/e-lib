<?php
// Set header for JSON response
header('Content-Type: application/json');

session_start();
include '../../config/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if request is POST and required field exists
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lrnNumber'])) {
    $lrnNumber = trim($_POST['lrnNumber']);

    // Validate input
    if (empty($lrnNumber)) {
        echo json_encode(['success' => false, 'message' => 'LRN number is required']);
        exit;
    }

    // Validate LRN format (assuming it's numeric, adjust as needed)
    if (!preg_match("/^[0-9]+$/", $lrnNumber)) {
        echo json_encode(['success' => false, 'message' => 'LRN number must contain only numbers']);
        exit;
    }

    // Validate LRN length (6-10 digits)
    if (strlen($lrnNumber) < 6 || strlen($lrnNumber) > 10) {
        echo json_encode(['success' => false, 'message' => 'LRN number must be between 6 and 10 digits']);
        exit;
    }

    // Update LRN number in database
    $stmt = $conn->prepare("UPDATE users SET lrn_number = ? WHERE id = ?");

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("si", $lrnNumber, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'LRN number updated successfully']);
        } else {
            // No rows affected - LRN might be the same
            echo json_encode(['success' => true, 'message' => 'LRN number updated successfully']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update LRN number: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request - missing required field']);
}

$conn->close();
?>
