<?php
// Clean any previous output
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

include '../../config/connection.php';

function deleteLibBook($bookId) {
    global $conn;
    
    // Validate input
    if (empty($bookId) || !is_numeric($bookId)) {
        return ['success' => false, 'message' => 'Invalid book ID'];
    }
    
    // Check if book exists
    $stmt = $conn->prepare("SELECT id FROM lib_books WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Book not found'];
    }
    $stmt->close();
    
    // Delete the book
    $stmt = $conn->prepare("DELETE FROM lib_books WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Book deleted successfully'];
    } else {
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to delete book: ' . $error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'] ?? '';
    
    $result = deleteLibBook($bookId);
    
    ob_end_clean(); // Clear buffer before sending headers
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else {
    ob_end_clean(); // Clear buffer before sending headers
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
?>