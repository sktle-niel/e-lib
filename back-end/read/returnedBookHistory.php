<?php
function getReturnedBooksHistory($limit = 15, $offset = 0) {
    global $conn;

    $stmt = $conn->prepare("
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
        ORDER BY brh.actual_return_date DESC, brh.created_at DESC
        LIMIT ? OFFSET ?
    ");

    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    $stmt->close();
    return $books;
}

function getReturnedBooksHistoryCount() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM book_return_history");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['total'];
}
?>