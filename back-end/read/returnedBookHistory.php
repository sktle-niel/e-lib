<?php
function getReturnedBooksHistory($limit = 15, $offset = 0, $month = null, $year = null) {
    global $conn;

    $query = "
        SELECT
            brh.id,
            brh.book_id,
            brh.user_id,
            brh.book_title,
            brh.borrow_date,
            brh.expected_return_date,
            brh.actual_return_date,
            brh.processed_by,
            brh.created_at,
            CONCAT(u.firstname, ' ', u.lastname) as borrower_name
        FROM book_return_history brh
        JOIN users u ON brh.user_id = u.id
    ";

    $params = [];
    $types = "";
    $conditions = [];

    if ($month !== null) {
        $conditions[] = "MONTH(brh.actual_return_date) = ?";
        $params[] = $month;
        $types .= "i";
    }

    if ($year !== null) {
        $conditions[] = "YEAR(brh.actual_return_date) = ?";
        $params[] = $year;
        $types .= "i";
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY brh.actual_return_date DESC, brh.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    $stmt->close();
    return $books;
}

function getReturnedBooksHistoryCount($month = null, $year = null) {
    global $conn;

    $query = "SELECT COUNT(*) as total FROM book_return_history";
    $params = [];
    $types = "";
    $conditions = [];

    if ($month !== null) {
        $conditions[] = "MONTH(actual_return_date) = ?";
        $params[] = $month;
        $types .= "i";
    }

    if ($year !== null) {
        $conditions[] = "YEAR(actual_return_date) = ?";
        $params[] = $year;
        $types .= "i";
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['total'];
}
?>