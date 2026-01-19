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

function borrowBook($bookId, $userId) {
    global $conn;

    // Validate inputs
    if (empty($bookId) || !is_numeric($bookId)) {
        return ['success' => false, 'message' => 'Invalid book ID'];
    }

    // Calculate return date as 3 days from now
    $returnDate = date('Y-m-d', strtotime('+3 days'));

    // Check if book is available
    $stmt = $conn->prepare("SELECT status FROM lib_books WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();

    if (!$book) {
        return ['success' => false, 'message' => 'Book not found'];
    }

    if ($book['status'] !== 'available') {
        return ['success' => false, 'message' => 'Book is not available for borrowing'];
    }

    // Generate unique random 7-digit ID
    do {
        $borrowId = rand(1000000, 9999999);
        $stmt = $conn->prepare("SELECT id FROM borrowed_lib_books WHERE id = ?");
        $stmt->bind_param("i", $borrowId);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);
    $stmt->close();

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update book status to 'not available'
        $stmt = $conn->prepare("UPDATE lib_books SET status = 'not available' WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $stmt->close();

        // Insert into borrowed_lib_books table
        $stmt = $conn->prepare("INSERT INTO borrowed_lib_books (id, book_id, user_id, borrow_date, expected_return_date) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->bind_param("iiis", $borrowId, $bookId, $userId, $returnDate);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        return ['success' => true, 'message' => 'Book borrowed successfully'];
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        return ['success' => false, 'message' => 'Failed to borrow book: ' . $e->getMessage()];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = trim($_POST['book_id'] ?? '');
    $userId = $_SESSION['user_id'];

    $result = borrowBook($bookId, $userId);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
?>
