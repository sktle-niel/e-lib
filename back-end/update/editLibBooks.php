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

function addBook($title, $course, $author, $publishDate, $userId) {
    global $conn;
    
    // Validate inputs
    if (empty($title)) {
        return ['success' => false, 'message' => 'Book title is required'];
    }
    if (empty($course)) {
        return ['success' => false, 'message' => 'Course is required'];
    }
    if (empty($author)) {
        return ['success' => false, 'message' => 'Author is required'];
    }
    if (empty($publishDate)) {
        return ['success' => false, 'message' => 'Publish date is required'];
    }
    
    // Insert the book
    $stmt = $conn->prepare("INSERT INTO lib_books (book_title, book_course, author, publish_date, created_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssi", $title, $course, $author, $publishDate, $userId);
    
    if ($stmt->execute()) {
        $bookId = $stmt->insert_id;
        $stmt->close();
        return ['success' => true, 'message' => 'Book added successfully', 'book_id' => $bookId];
    } else {
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to add book: ' . $error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['book_title'] ?? '';
    $course = $_POST['book_course'] ?? '';
    $author = $_POST['author'] ?? '';
    $publishDate = $_POST['publish_date'] ?? '';
    $userId = $_SESSION['user_id'];
    
    $result = addBook($title, $course, $author, $publishDate, $userId);
    
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