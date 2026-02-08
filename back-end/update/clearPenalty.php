<?php
// Prevent any output before our response
ob_start();

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
include '../../config/connection.php';

// Clear any previous output
ob_clean();

header('Content-Type: application/json');



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

            // Generate unique IDs
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
                throw new Exception("Failed to save to history: " . $stmt4->error);
            }
            $stmt4->close();

            // Delete the record from borrowed_lib_books
            $stmt2 = $conn->prepare("DELETE FROM borrowed_lib_books WHERE id = ?");
            $stmt2->bind_param("i", $borrowId);

            if (!$stmt2->execute()) {
                throw new Exception("Failed to delete borrowed record: " . $stmt2->error);
            }
            $stmt2->close();

            // Update the status in lib_books to 'available'
            $stmt3 = $conn->prepare("UPDATE lib_books SET status = 'available' WHERE id = ?");
            $stmt3->bind_param("i", $bookId);

            if (!$stmt3->execute()) {
                throw new Exception("Failed to update book status: " . $stmt3->error);
            }
            $stmt3->close();

            return true;

        } else {
            $stmt->close();
            throw new Exception("Borrow ID not found");
        }

    } catch (Exception $e) {
        error_log("Error in markAsReturned: " . $e->getMessage());
        throw $e;
    }
}

// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_id'])) {
    $transactionStarted = false;
    
    try {
        $borrowId = (int)$_POST['borrow_id'];
        $clearedBy = isset($_POST['cleared_by']) ? (int)$_POST['cleared_by'] : null;
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

        if ($borrowId <= 0) {
            throw new Exception('Invalid borrow ID');
        }

        // Start transaction
        $conn->begin_transaction();
        $transactionStarted = true;

        // First, get borrow details to calculate penalty
        $stmt = $conn->prepare("
            SELECT b.book_id, b.user_id, b.expected_return_date
            FROM borrowed_lib_books b
            WHERE b.id = ?
        ");
        $stmt->bind_param("i", $borrowId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $bookId = $row['book_id'];
            $userId = $row['user_id'];
            $expectedReturnDate = $row['expected_return_date'];
            $stmt->close();

            // Calculate days overdue and penalty amount (fixed at 50)
            $expectedDate = new DateTime($expectedReturnDate);
            $currentDate = new DateTime();
            $interval = $expectedDate->diff($currentDate);
            $daysOverdue = max(0, (int)$interval->days);
            $penaltyAmount = 50;

            // *** THIS IS WHERE IT INSERTS INTO penalty_clear_log ***
            error_log("Attempting to insert into penalty_clear_log: borrow_id=$borrowId, book_id=$bookId, user_id=$userId, penalty_amount=$penaltyAmount, days_overdue=$daysOverdue, cleared_by=$clearedBy, notes=$notes");

            if ($clearedBy === null) {
                $stmt2 = $conn->prepare("
                    INSERT INTO penalty_clear_log
                    (borrow_id, book_id, user_id, penalty_amount, days_overdue, cleared_at, notes)
                    VALUES (?, ?, ?, ?, ?, NOW(), ?)
                ");
                if (!$stmt2) {
                    throw new Exception('Failed to prepare penalty_clear_log insert: ' . $conn->error);
                }
                $stmt2->bind_param("iiiiis", $borrowId, $bookId, $userId, $penaltyAmount, $daysOverdue, $notes);
            } else {
                $stmt2 = $conn->prepare("
                    INSERT INTO penalty_clear_log
                    (borrow_id, book_id, user_id, penalty_amount, days_overdue, cleared_by, cleared_at, notes)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)
                ");
                if (!$stmt2) {
                    throw new Exception('Failed to prepare penalty_clear_log insert: ' . $conn->error);
                }
                $stmt2->bind_param("iiiiisi", $borrowId, $bookId, $userId, $penaltyAmount, $daysOverdue, $clearedBy, $notes);
            }

            if (!$stmt2->execute()) {
                throw new Exception('Failed to log penalty clearance: ' . $stmt2->error);
            }

            // Get the inserted ID for logging
            $penaltyLogId = $conn->insert_id;
            error_log("Penalty cleared successfully - Log ID: $penaltyLogId, Borrow ID: $borrowId, Amount: $penaltyAmount");

            $stmt2->close();

            // Now mark as returned (this also moves to book_return_history)
            markAsReturned($borrowId);

            // Commit transaction
            $conn->commit();
            $transactionStarted = false;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Penalty cleared successfully',
                'penalty_log_id' => $penaltyLogId,
                'penalty_amount' => $penaltyAmount,
                'days_overdue' => $daysOverdue
            ]);
            exit;

        } else {
            $stmt->close();
            throw new Exception('Borrow record not found');
        }

    } catch (Exception $e) {
        // Rollback transaction on error if it was started
        if ($transactionStarted) {
            $conn->rollback();
        }
        error_log("Error in clearPenalty: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}
?>