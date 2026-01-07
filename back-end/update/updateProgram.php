<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1);

// Set header for JSON response
header('Content-Type: application/json');

session_start();
include '../../config/connection.php';

// Log function for debugging
function logDebug($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, '../../logs/debug.log');
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    logDebug("User ID: " . $userId);

    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    // Log all POST data for debugging
    logDebug("POST data: " . print_r($_POST, true));

    // Check if programs array exists
    if (!isset($_POST['programs'])) {
        echo json_encode(['success' => false, 'message' => 'Programs data not received']);
        exit;
    }

    $programs = $_POST['programs'];
    
    // Validate that programs is an array
    if (!is_array($programs)) {
        echo json_encode(['success' => false, 'message' => 'Invalid programs data format']);
        exit;
    }
    
    // Validate that programs array is not empty
    if (empty($programs)) {
        echo json_encode(['success' => false, 'message' => 'Please select at least one program']);
        exit;
    }
    
    // Sanitize each program value
    $sanitizedPrograms = array_map(function($program) {
        return trim($program);
    }, $programs);
    
    // Remove any empty values
    $sanitizedPrograms = array_filter($sanitizedPrograms);
    
    if (empty($sanitizedPrograms)) {
        echo json_encode(['success' => false, 'message' => 'Please select at least one valid program']);
        exit;
    }
    
    // Convert array to comma-separated string
    $programString = implode(',', $sanitizedPrograms);
    logDebug("Program string: " . $programString);
    
    // Check database connection
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Update the database
    $stmt = $conn->prepare("UPDATE users SET program = ? WHERE id = ?");
    
    if (!$stmt) {
        logDebug("Prepare failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("si", $programString, $userId);
    
    if ($stmt->execute()) {
        logDebug("Update successful. Affected rows: " . $stmt->affected_rows);
        echo json_encode(['success' => true, 'message' => 'Program updated successfully']);
    } else {
        logDebug("Execute failed: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to update program: ' . $stmt->error]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    logDebug("Exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>