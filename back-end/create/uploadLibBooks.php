<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $result = ['success' => false, 'message' => 'User not logged in'];
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

include '../../config/connection.php';

function addBook($title, $author, $publishDate) {
    global $conn;

    // Validate inputs
    if (empty($title)) {
        return ['success' => false, 'message' => 'Book title is required'];
    }
    if (empty($author)) {
        return ['success' => false, 'message' => 'Author is required'];
    }
    if (empty($publishDate)) {
        return ['success' => false, 'message' => 'Publish date is required'];
    }
    
    // Generate unique random 7-digit ID
    do {
        $bookId = rand(1000000, 9999999);
        $stmt = $conn->prepare("SELECT id FROM lib_books WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);
    $stmt->close();
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO lib_books (id, book_title, author, publish_date, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $bookId, $title, $author, $publishDate);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Book added successfully'];
    } else {
        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to add book to database: ' . $error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['book_title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $publishDate = trim($_POST['publish_date'] ?? '');

    $result = addBook($title, $author, $publishDate);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
?>