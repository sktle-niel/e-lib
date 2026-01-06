<?php
include '../../config/connection.php';

function deleteBook($book_id) {
    global $conn;

    // First, get the file path
    $stmt = $conn->prepare("SELECT cover FROM books WHERE id = ?");
    $stmt->bind_param('i', $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Book not found'];
    }

    $row = $result->fetch_assoc();
    $filePath = $row['cover'];

    // Delete the file from filesystem if it exists
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param('i', $book_id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Book deleted successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to delete book from database'];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

    if ($book_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid book ID']);
        exit;
    }

    $result = deleteBook($book_id);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
