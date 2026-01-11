<?php
// Only start session if one hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../config/connection.php';

function removeDownload($itemId, $type) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'User not logged in'];
    }
    
    $userId = $_SESSION['user_id'];
    
    // Validate input
    if (empty($itemId) || !is_numeric($itemId)) {
        return ['success' => false, 'message' => 'Invalid item ID'];
    }
    
    if ($type !== 'book' && $type !== 'module') {
        return ['success' => false, 'message' => 'Invalid type'];
    }
    
    // Determine table and column
    $table = $type === 'book' ? 'downloaded_books' : 'downloaded_modules';
    $idColumn = $type === 'book' ? 'book_id' : 'module_id';
    
    // Check if the download exists and belongs to the user
    $stmt = $conn->prepare("SELECT id FROM $table WHERE $idColumn = ? AND user_id = ?");
    $stmt->bind_param("ii", $itemId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Download not found or you do not have permission to delete it'];
    }
    $stmt->close();
    
    // Delete the download
    $deleteStmt = $conn->prepare("DELETE FROM $table WHERE $idColumn = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $itemId, $userId);
    
    if ($deleteStmt->execute()) {
        $deleteStmt->close();
        return ['success' => true, 'message' => 'Download removed successfully'];
    } else {
        $deleteStmt->close();
        return ['success' => false, 'message' => 'Failed to remove download: ' . $conn->error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    
    $result = removeDownload($itemId, $type);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>