<?php
// Disable error output to prevent HTML in JSON response
error_reporting(0);
ini_set('display_errors', 0);

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

function updateBook($bookId, $title, $author, $publishDate) {
    global $conn;

    // Validate inputs
    if (empty($bookId)) {
        return ['success' => false, 'message' => 'Book ID is required'];
    }
    if (empty($title)) {
        return ['success' => false, 'message' => 'Book title is required'];
    }
    if (empty($author)) {
        return ['success' => false, 'message' => 'Author is required'];
    }
    if (empty($publishDate)) {
        return ['success' => false, 'message' => 'Publish date is required'];
    }

    // Update the book
    $stmt = $conn->prepare("UPDATE lib_books SET book_title = ?, author = ?, publish_date = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $author, $publishDate, $bookId);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Book updated successfully'];
    } else {
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to update book: ' . $error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'] ?? '';
    $title = $_POST['book_title'] ?? '';
    $author = $_POST['author'] ?? '';
    $publishDate = $_POST['publish_date'] ?? '';

    $result = updateBook($bookId, $title, $author, $publishDate);
    
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