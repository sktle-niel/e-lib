
<?php
include '../../config/connection.php';

function getRecentLibBooks($limit = 10) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, book_title, book_course, author, publish_date, created_at, status FROM lib_books ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("i", $limit);

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

function getFilteredBooks($search, $course, $publishYear, $limit = 20) {
    global $conn;

    $conditions = [];
    $params = [];
    $types = '';

    // Build WHERE clause
    if (!empty($search)) {
        $conditions[] = "(book_title LIKE ? OR author LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
    }

    if (!empty($course)) {
        $conditions[] = "book_course = ?";
        $params[] = $course;
        $types .= 's';
    }

    if (!empty($publishYear)) {
        $conditions[] = "YEAR(publish_date) = ?";
        $params[] = $publishYear;
        $types .= 'i';
    }

    $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

    $sql = "SELECT id, book_title, book_course, author, publish_date, created_at, status FROM lib_books {$whereClause} ORDER BY created_at DESC LIMIT ?";

    $params[] = $limit;
    $types .= 'i';

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

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
?>
