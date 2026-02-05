
<?php
include '../../config/connection.php';

function getRecentLibBooks($limit = 10) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, book_title, author, publish_date, created_at, status FROM lib_books ORDER BY created_at DESC LIMIT ?");
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

    $sql = "SELECT id, book_title, author, publish_date, created_at, status FROM lib_books {$whereClause} ORDER BY created_at DESC LIMIT ?";

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


?>
