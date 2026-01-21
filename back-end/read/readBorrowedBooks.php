<?php
include '../../config/connection.php';

function getBorrowedBooks($userId, $limit = 15, $offset = 0) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT b.id, lb.book_title, b.borrow_date, b.expected_return_date, b.status
        FROM borrowed_lib_books b
        JOIN lib_books lb ON b.book_id = lb.id
        WHERE b.user_id = ?
        ORDER BY b.borrow_date DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $userId, $limit, $offset);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        $stmt->close();
        return $books;
    } else {
        $stmt->close();
        return [];
    }
}

function getBorrowedBooksCount($userId) {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM borrowed_lib_books WHERE user_id = ?");
    $stmt->bind_param("i", $userId);

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

function getAllBorrowedBooks($limit = 15, $offset = 0) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT b.id, lb.book_title as title, b.borrow_date, b.expected_return_date as return_date, b.status, CONCAT(u.firstname, ' ', u.lastname) as borrower_name
        FROM borrowed_lib_books b
        JOIN lib_books lb ON b.book_id = lb.id
        JOIN users u ON b.user_id = u.id
        ORDER BY b.borrow_date DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        $stmt->close();
        return $books;
    } else {
        $stmt->close();
        return [];
    }
}

function getAllBorrowedBooksCount() {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM borrowed_lib_books");

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
?>
