<?php
// Only start session if one hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../config/connection.php';

function removeModule($moduleId) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'User not logged in'];
    }
    
    $userId = $_SESSION['user_id'];
    
    // Validate input
    if (empty($moduleId)) {
        return ['success' => false, 'message' => 'Module ID is required'];
    }
    
    // Get module details and verify ownership
    $stmt = $conn->prepare("SELECT cover, file_path FROM modules WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $moduleId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Module not found or you do not have permission to delete it'];
    }
    
    $module = $result->fetch_assoc();
    $coverPath = $module['cover'];
    $filePath = $module['file_path'];
    $stmt->close();
    
    // Delete module from database
    $deleteStmt = $conn->prepare("DELETE FROM modules WHERE id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $moduleId, $userId);
    
    if ($deleteStmt->execute()) {
        // Delete physical files
        if (file_exists($coverPath)) {
            unlink($coverPath);
        }
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $deleteStmt->close();
        return ['success' => true, 'message' => 'Module deleted successfully'];
    } else {
        $deleteStmt->close();
        return ['success' => false, 'message' => 'Failed to delete module: ' . $conn->error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleId = $_POST['module_id'] ?? '';
    
    $result = removeModule($moduleId);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>