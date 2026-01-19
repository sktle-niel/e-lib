<?php
session_start();
include '../../config/connection.php';

function generateUniqueReturnId() {
    global $conn;
    do {
        $returnId = rand(1000000, 9999999);
        $stmt = $conn->prepare("SELECT id FROM book_return_history WHERE return_id = ?");
        $stmt->bind_param("i", $returnId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $returnId;
}

function markAsReturned($borrowId) {
    global $conn;

    // Start transaction
    $conn->begin_transaction();

    try {
        // First, get the borrow details
        $stmt = $conn->prepare("
            SELECT b.book_id, b.user_id, b.borrow_date, b.expected_return_date, lb.book_title
            FROM borrowed_lib_books b
            JOIN lib_books lb ON b.book_id = lb.id
            WHERE b.id = ?
        ");
        $stmt->bind_param("i", $borrowId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $bookId = $row['book_id'];
            $userId = $row['user_id'];
            $borrowDate = $row['borrow_date'];
            $expectedReturnDate = $row['expected_return_date'];
            $bookTitle = $row['book_title'];
            $stmt->close();

            // Generate unique return ID
            $returnId = generateUniqueReturnId();

            // Insert into history table
            $stmt4 = $conn->prepare("
                INSERT INTO book_return_history
                (return_id, borrow_id, book_id, user_id, book_title, borrow_date, expected_return_date, processed_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $processedBy = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $stmt4->bind_param("iiiisssi", $returnId, $borrowId, $bookId, $userId, $bookTitle, $borrowDate, $expectedReturnDate, $processedBy);

            if (!$stmt4->execute()) {
                throw new Exception("Failed to save to history");
            }
            $stmt4->close();

            // Delete the record from borrowed_lib_books
            $stmt2 = $conn->prepare("DELETE FROM borrowed_lib_books WHERE id = ?");
            $stmt2->bind_param("i", $borrowId);

            if (!$stmt2->execute()) {
                throw new Exception("Failed to delete borrowed record");
            }
            $stmt2->close();

            // Update the status in lib_books to 'available'
            $stmt3 = $conn->prepare("UPDATE lib_books SET status = 'available' WHERE id = ?");
            $stmt3->bind_param("i", $bookId);

            if (!$stmt3->execute()) {
                throw new Exception("Failed to update book status");
            }
            $stmt3->close();

            // Commit transaction
            $conn->commit();
            return true;
        } else {
            $stmt->close();
            throw new Exception("Borrow ID not found");
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error in markAsReturned: " . $e->getMessage());
        return false;
    }
}

// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_id'])) {
    $borrowId = (int)$_POST['borrow_id'];
    
    if ($borrowId > 0) {
        if (markAsReturned($borrowId)) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>