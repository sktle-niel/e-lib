<?php
include '../../config/connection.php';

function clearPenalty($borrow_id, $cleared_by, $notes = '') {
    global $conn;

    // Start transaction
    $conn->begin_transaction();

    try {
        // First, get the penalty details before clearing
        $stmt = $conn->prepare("
            SELECT
                b.id,
                b.book_id,
                b.user_id,
                lb.book_title,
                CONCAT(u.firstname, ' ', u.lastname) as borrower_name,
                DATEDIFF(CURDATE(), b.expected_return_date) as days_overdue,
                (DATEDIFF(CURDATE(), b.expected_return_date) * 50) as penalty_amount
            FROM borrowed_lib_books b
            JOIN lib_books lb ON b.book_id = lb.id
            JOIN users u ON b.user_id = u.id
            WHERE b.id = ? AND b.status = 'Borrowed'
        ");

        $stmt->bind_param("i", $borrow_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Penalty record not found or already cleared");
        }

        $penaltyData = $result->fetch_assoc();
        $stmt->close();

        // Log the penalty clearing action
        $logStmt = $conn->prepare("
            INSERT INTO penalty_clear_log
            (borrow_id, book_id, user_id, penalty_amount, days_overdue, cleared_by, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $logStmt->bind_param("iiidiss",
            $borrow_id,
            $penaltyData['book_id'],
            $penaltyData['user_id'],
            $penaltyData['penalty_amount'],
            $penaltyData['days_overdue'],
            $cleared_by,
            $notes
        );

        if (!$logStmt->execute()) {
            throw new Exception("Failed to log penalty clearing action");
        }
        $logStmt->close();

        // Mark the book as returned
        $updateStmt = $conn->prepare("
            UPDATE borrowed_lib_books
            SET status = 'Returned'
            WHERE id = ?
        ");

        $updateStmt->bind_param("i", $borrow_id);

        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update book return status");
        }
        $updateStmt->close();

        // Update book availability back to available
        $bookStmt = $conn->prepare("
            UPDATE lib_books
            SET status = 'available'
            WHERE id = ?
        ");

        $bookStmt->bind_param("i", $penaltyData['book_id']);

        if (!$bookStmt->execute()) {
            throw new Exception("Failed to update book availability");
        }
        $bookStmt->close();

        // Commit transaction
        $conn->commit();

        return [
            'success' => true,
            'message' => 'Penalty cleared successfully',
            'data' => [
                'book_title' => $penaltyData['book_title'],
                'borrower_name' => $penaltyData['borrower_name'],
                'penalty_amount' => $penaltyData['penalty_amount'],
                'days_overdue' => $penaltyData['days_overdue']
            ]
        ];

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $borrow_id = isset($_POST['borrow_id']) ? (int)$_POST['borrow_id'] : 0;
    $cleared_by = isset($_POST['cleared_by']) ? (int)$_POST['cleared_by'] : 0;
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    if ($borrow_id <= 0 || $cleared_by <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid borrow ID or user ID'
        ]);
        exit;
    }

    $result = clearPenalty($borrow_id, $cleared_by, $notes);
    echo json_encode($result);
    exit;
}
?>
