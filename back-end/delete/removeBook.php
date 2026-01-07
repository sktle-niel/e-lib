<?php
include '../../config/connection.php';

function deleteBook($bookId) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'User not logged in'];
    }
    
    $userId = $_SESSION['user_id'];
    
    // Validate input
    if (empty($bookId)) {
        return ['success' => false, 'message' => 'Book ID is required'];
    }
    
    // Get book details and verify ownership
    $stmt = $conn->prepare("SELECT cover, file_path FROM books WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $bookId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Book not found or you do not have permission to delete it'];
    }
    
    $book = $result->fetch_assoc();
    $coverPath = $book['cover'];
    $filePath = $book['file_path'];
    $stmt->close();
    
    // Delete book from database
    $deleteStmt = $conn->prepare("DELETE FROM books WHERE id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $bookId, $userId);
    
    if ($deleteStmt->execute()) {
        // Delete physical files
        if (file_exists($coverPath)) {
            unlink($coverPath);
        }
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $deleteStmt->close();
        return ['success' => true, 'message' => 'Book deleted successfully'];
    } else {
        $deleteStmt->close();
        return ['success' => false, 'message' => 'Failed to delete book: ' . $conn->error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    
    if ($bookId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid book ID']);
        exit;
    }
    
    $result = deleteBook($bookId);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>