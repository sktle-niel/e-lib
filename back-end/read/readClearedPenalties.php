<?php
include '../../config/connection.php';

function getClearedPenalties($user_id = null, $limit = 15, $offset = 0) {
    global $conn;

    $whereClause = '';

    if ($user_id !== null) {
        $whereClause = " WHERE pcl.user_id = ?";
    }

    $stmt = $conn->prepare("
        SELECT
            pcl.id,
            lb.book_title as title,
            b.borrow_date,
            b.expected_return_date as return_date,
            pcl.days_overdue,
            pcl.penalty_amount,
            'Cleared' as status,
            CONCAT(u.firstname, ' ', u.lastname) as borrower_name,
            pcl.cleared_at,
            pcl.notes,
            CONCAT(cleared_user.firstname, ' ', cleared_user.lastname) as cleared_by_name
        FROM penalty_clear_log pcl
        JOIN borrowed_lib_books b ON pcl.borrow_id = b.id
        JOIN lib_books lb ON pcl.book_id = lb.id
        JOIN users u ON pcl.user_id = u.id
        JOIN users cleared_user ON pcl.cleared_by = cleared_user.id
        $whereClause
        ORDER BY pcl.cleared_at DESC
        LIMIT ? OFFSET ?
    ");

    if ($user_id !== null) {
        $stmt->bind_param("iii", $user_id, $limit, $offset);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $clearedPenalties = [];
        while ($row = $result->fetch_assoc()) {
            $clearedPenalties[] = $row;
        }
        $stmt->close();
        return $clearedPenalties;
    } else {
        $stmt->close();
        return [];
    }
}

function getClearedPenaltiesCount($user_id = null) {
    global $conn;

    $whereClause = '';

    if ($user_id !== null) {
        $whereClause = " WHERE user_id = ?";
    }

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM penalty_clear_log $whereClause");

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

function getTotalClearedPenaltyAmount() {
    global $conn;

    $stmt = $conn->prepare("SELECT SUM(penalty_amount) as total FROM penalty_clear_log");

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

function getAverageDaysOverdueCleared($user_id = null) {
    global $conn;

    $whereClause = '';

    if ($user_id !== null) {
        $whereClause = " WHERE user_id = ?";
    }

    $stmt = $conn->prepare("SELECT AVG(days_overdue) as avg_days FROM penalty_clear_log $whereClause");

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
