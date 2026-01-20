<?php
session_start();
require '../../config/connection.php';

function abortReturned($returnId, $bookId, $userId, $borrowDate, $expectedReturnDate) {
    global $conn;
    
    $borrowDate = date('Y-m-d', strtotime($borrowDate));
    $expectedReturnDate = date('Y-m-d', strtotime($expectedReturnDate));
    
    $conn->begin_transaction();
    
    try {
        // Validate return exists
        $stmt = $conn->prepare("SELECT id FROM book_return_history WHERE id=?");
        $stmt->bind_param("i", $returnId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Return record not found");
        }
        $stmt->close();
        
        // Reinsert borrow record with status 'borrowed'
        $status = 'borrowed';
        $stmt = $conn->prepare("
            INSERT INTO borrowed_lib_books
            (book_id, user_id, borrow_date, expected_return_date, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisss", $bookId, $userId, $borrowDate, $expectedReturnDate, $status);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert borrow record: " . $stmt->error);
        }
        $stmt->close();
        
        // Remove return history
        $stmt = $conn->prepare("DELETE FROM book_return_history WHERE id=?");
        $stmt->bind_param("i", $returnId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete return history: " . $stmt->error);
        }
        $stmt->close();
        
        // Update book status to 'not available' (since it's borrowed again)
        $stmt = $conn->prepare("UPDATE lib_books SET status='not available' WHERE id=?");
        $stmt->bind_param("i", $bookId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update book status: " . $stmt->error);
        }
        $stmt->close();
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required POST parameters
    if (!isset($_POST['return_id'], $_POST['book_id'], $_POST['user_id'], 
               $_POST['borrow_date'], $_POST['expected_return_date'])) {
        echo 'error: Missing required parameters';
        exit;
    }
    
    $result = abortReturned(
        (int)$_POST['return_id'],
        (int)$_POST['book_id'],
        (int)$_POST['user_id'],
        $_POST['borrow_date'],
        $_POST['expected_return_date']
    );
    
    echo $result === true ? 'success' : 'error: ' . $result;
}
?>