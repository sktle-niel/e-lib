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

// Check if request is POST and required fields exist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['firstName']) && isset($_POST['lastName'])) {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    
    // Validate inputs
    if (empty($firstName) || empty($lastName)) {
        echo json_encode(['success' => false, 'message' => 'First name and last name are required']);
        exit;
    }
    
    // Validate name length
    if (strlen($firstName) > 50 || strlen($lastName) > 50) {
        echo json_encode(['success' => false, 'message' => 'Names must be less than 50 characters']);
        exit;
    }
    
    // Validate name format (only letters, spaces, hyphens, and apostrophes)
    if (!preg_match("/^[a-zA-Z\s\-']+$/", $firstName) || !preg_match("/^[a-zA-Z\s\-']+$/", $lastName)) {
        echo json_encode(['success' => false, 'message' => 'Names can only contain letters, spaces, hyphens, and apostrophes']);
        exit;
    }
    
    // Capitalize first letter of each word
    $firstName = ucwords(strtolower($firstName));
    $lastName = ucwords(strtolower($lastName));
    
    // Update name in database
    $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ? WHERE id = ?");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("ssi", $firstName, $lastName, $user_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Name updated successfully']);
        } else {
            // No rows affected - name might be the same
            echo json_encode(['success' => true, 'message' => 'Name updated successfully']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update name: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request - missing required fields']);
}

$conn->close();
?>