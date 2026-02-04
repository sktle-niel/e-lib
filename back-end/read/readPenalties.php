<?php
include '../../config/connection.php';

function getPenalties($user_id = null, $limit = 15, $offset = 0) {
    global $conn;

    // Modified to show only books that are 1+ days overdue
    $whereClause = "DATEDIFF(CURDATE(), b.expected_return_date) >= 1 AND b.status != 'Returned'";

    if ($user_id !== null) {
        $whereClause .= " AND b.user_id = ?";
    }

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
        WHERE $whereClause
        ORDER BY b.expected_return_date ASC
        LIMIT ? OFFSET ?
    ");

    if ($user_id !== null) {
        $stmt->bind_param("iii", $user_id, $limit, $offset);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }

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

function getPenaltiesCount($user_id = null) {
    global $conn;

    // Modified to count only books that are 1+ days overdue
    $whereClause = "DATEDIFF(CURDATE(), expected_return_date) >= 1 AND status != 'Returned'";

    if ($user_id !== null) {
        $whereClause .= " AND user_id = ?";
    }

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM borrowed_lib_books WHERE $whereClause");

    if ($user_id !== null) {
        $stmt->bind_param("i", $user_id);
    }

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

    // Modified to calculate penalty only for books 1+ days overdue
    $stmt = $conn->prepare("
        SELECT SUM(DATEDIFF(CURDATE(), expected_return_date) * 50) as total
        FROM borrowed_lib_books
        WHERE DATEDIFF(CURDATE(), expected_return_date) >= 1
        AND status != 'Returned'
    ");
    
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

function getAverageDaysOverdue($user_id = null) {
    global $conn;

    $whereClause = "DATEDIFF(CURDATE(), expected_return_date) >= 1 AND status != 'Returned'";
    
    if ($user_id !== null) {
        $whereClause .= " AND user_id = ?";
    }
    
    $stmt = $conn->prepare("SELECT AVG(DATEDIFF(CURDATE(), expected_return_date)) as avg_days FROM borrowed_lib_books WHERE $whereClause");
    
    if ($user_id !== null) {
        $stmt->bind_param("i", $user_id);
    }
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return round($row['avg_days'] ?? 0, 1);
    } else {
        $stmt->close();
        return 0;
    }
}
?>