<?php
// Prevent any output before our response
ob_start();

session_start();
include '../../config/connection.php';

// Clear any previous output
ob_clean();

function generateUniqueBorrowId() {
    global $conn;
    do {
        $borrowId = rand(1000000, 9999999);
        $stmt = $conn->prepare("SELECT id FROM borrowed_lib_books WHERE id = ?");
        $stmt->bind_param("i", $borrowId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $borrowId;
}

function abortReturned($returnId, $bookId, $userId, $borrowDate, $expectedReturnDate) {
    global $conn;

    // Normalize dates
    $borrowDate = date('Y-m-d', strtotime($borrowDate));
    $expectedReturnDate = !empty($expectedReturnDate) ? date('Y-m-d', strtotime($expectedReturnDate)) : null;

    // Start transaction
    $conn->begin_transaction();

    try {
        // Generate unique borrow ID
        $newBorrowId = generateUniqueBorrowId();

        // Insert into borrowed_lib_books with correct column names
        $stmt = $conn->prepare("
            INSERT INTO borrowed_lib_books
            (id, book_id, user_id, borrow_date, expected_return_date, status)
            VALUES (?, ?, ?, ?, ?, 'borrowed')
        ");
        $stmt->bind_param("iiiiss", $newBorrowId, $bookId, $userId, $borrowDate, $expectedReturnDate);

        if (!$stmt->execute()) {
            throw new Exception("Failed to re-borrow the book: " . $stmt->error);
        }
        $stmt->close();

        // Delete from book_return_history
        $stmt2 = $conn->prepare("DELETE FROM book_return_history WHERE id = ?");
        $stmt2->bind_param("i", $returnId);

        if (!$stmt2->execute()) {
            throw new Exception("Failed to remove from history: " . $stmt2->error);
        }
        $stmt2->close();

        // Update the status in lib_books to 'borrowed'
        $stmt3 = $conn->prepare("UPDATE lib_books SET status = 'borrowed' WHERE id = ?");
        $stmt3->bind_param("i", $bookId);

        if (!$stmt3->execute()) {
            throw new Exception("Failed to update book status: " . $stmt3->error);
        }
        $stmt3->close();

        // Commit transaction
        $conn->commit();
        return true;

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error in abortReturned: " . $e->getMessage());
        return false;
    }
}

// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    $returnId = (int)$_POST['return_id'];
    $bookId = (int)$_POST['book_id'];
    $userId = (int)$_POST['user_id'];
    $borrowDate = $_POST['borrow_date'];
    $expectedReturnDate = $_POST['expected_return_date'];
    
    if ($returnId > 0 && $bookId > 0 && $userId > 0) {
        if (abortReturned($returnId, $bookId, $userId, $borrowDate, $expectedReturnDate)) {
            ob_clean();
            echo 'success';
            exit;
        } else {
            ob_clean();
            echo 'error';
            exit;
        }
    } else {
        ob_clean();
        echo 'error';
        exit;
    }
} else {
    ob_clean();
    echo 'error';
    exit;
}
?>