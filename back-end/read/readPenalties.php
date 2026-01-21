<?php
include '../../config/connection.php';

function getPenalties($limit = 15, $offset = 0) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT
            b.id,
            lb.book_title as title,
            b.borrow_date,
            b.expected_return_date as return_date,
            DATEDIFF(CURDATE(), b.expected_return_date) as days_overdue,
            50 as penalty_amount,
            'Overdue' as status,
            CONCAT(u.firstname, ' ', u.lastname) as borrower_name
        FROM borrowed_lib_books b
        JOIN lib_books lb ON b.book_id = lb.id
        JOIN users u ON b.user_id = u.id
        WHERE b.expected_return_date < CURDATE() AND b.status != 'Returned'
        ORDER BY b.expected_return_date ASC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $penalties = [];
        while ($row = $result->fetch_assoc()) {
            $penalties[] = $row;
        }
        $stmt->close();
        return $penalties;
    } else {
        $stmt->close();
        return [];
    }
}

function getPenaltiesCount() {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM borrowed_lib_books WHERE expected_return_date < CURDATE() AND status != 'Returned'");

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'];
    } else {
        $stmt->close();
        return 0;
    }
}

function getTotalPenaltyAmount() {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_lib_books WHERE expected_return_date < CURDATE() AND status != 'Returned'");

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'] ?? 0;
    } else {
        $stmt->close();
        return 0;
    }
}

function getAverageDaysOverdue() {
    global $conn;

    $stmt = $conn->prepare("SELECT AVG(DATEDIFF(CURDATE(), expected_return_date)) as avg_days FROM borrowed_lib_books WHERE expected_return_date < CURDATE() AND status != 'Returned'");

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['avg_days'] ?? 0;
    } else {
        $stmt->close();
        return 0;
    }
}
?>
