<?php
include 'config/connection.php';

// Check if penalty data exists
echo "Checking penalty data in database...\n\n";

// Check borrowed_lib_books table for overdue books
$stmt = $conn->prepare("
    SELECT
        b.id,
        lb.book_title as title,
        b.borrow_date,
        b.expected_return_date as return_date,
        DATEDIFF(CURDATE(), b.expected_return_date) as days_overdue,
        (DATEDIFF(CURDATE(), b.expected_return_date) * 50) as penalty_amount,
        'Overdue' as status,
        CONCAT(u.firstname, ' ', u.lastname) as borrower_name
    FROM borrowed_lib_books b
    JOIN lib_books lb ON b.book_id = lb.id
    JOIN users u ON b.user_id = u.id
    WHERE b.expected_return_date < DATE_SUB(CURDATE(), INTERVAL 3 DAY)
    AND b.status != 'Returned'
    ORDER BY b.expected_return_date ASC
");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $penalties = [];
    while ($row = $result->fetch_assoc()) {
        $penalties[] = $row;
    }

    echo "Found " . count($penalties) . " penalty records:\n\n";

    if (count($penalties) > 0) {
        foreach ($penalties as $penalty) {
            echo "Book: {$penalty['title']}\n";
            echo "Borrower: {$penalty['borrower_name']}\n";
            echo "Borrow Date: {$penalty['borrow_date']}\n";
            echo "Return Date: {$penalty['return_date']}\n";
            echo "Days Overdue: {$penalty['days_overdue']}\n";
            echo "Penalty: ₱{$penalty['penalty_amount']}\n";
            echo "Status: {$penalty['status']}\n";
            echo "---\n";
        }
    } else {
        echo "No penalty records found.\n";
        echo "Checking if there are any borrowed books at all...\n";

        // Check all borrowed books
        $allStmt = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_lib_books");
        $allStmt->execute();
        $allResult = $allStmt->get_result();
        $allRow = $allResult->fetch_assoc();
        echo "Total borrowed books: {$allRow['total']}\n";

        // Check overdue books (any overdue, not just 3+ days)
        $overdueStmt = $conn->prepare("SELECT COUNT(*) as overdue FROM borrowed_lib_books WHERE expected_return_date < CURDATE() AND status != 'Returned'");
        $overdueStmt->execute();
        $overdueResult = $overdueStmt->get_result();
        $overdueRow = $overdueResult->fetch_assoc();
        echo "Books overdue (any days): {$overdueRow['overdue']}\n";

        $allStmt->close();
        $overdueStmt->close();
    }

    $stmt->close();
} else {
    echo "Error executing query: " . $stmt->error . "\n";
    $stmt->close();
}

$conn->close();
?>
