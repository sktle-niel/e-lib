<?php
include '../../config/connection.php';

function getDownloadedBooks($userId, $search = '', $type = '', $limit = null, $offset = 0) {
    global $conn;

    $sql = "SELECT db.book_id, MAX(db.downloaded_at) as downloaded_at, b.title, b.author, b.cover, b.course, b.publish_date
            FROM downloaded_books db
            JOIN books b ON db.book_id = b.id
            WHERE db.user_id = ?
            GROUP BY db.book_id, b.title, b.author, b.cover, b.course, b.publish_date";
    $params = [$userId];
    $types = 'i';

    if (!empty($search)) {
        $sql .= " AND (b.title LIKE ? OR b.author LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
        $types .= 'ss';
    }

    if (!empty($type) && $type !== 'all') {
        // Since type is for books, we can filter by course or something, but for now, maybe not needed
        // $sql .= " AND b.course = ?";
        // $params[] = $type;
        // $types .= 's';
    }

    $sql .= " ORDER BY downloaded_at DESC";

    if ($limit !== null) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
    }

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = [
            'id' => $row['book_id'],
            'title' => $row['title'],
            'author' => $row['author'] . ' | ' . date('M d, Y', strtotime($row['publish_date'])),
            'cover' => $row['cover'],
            'downloadDate' => $row['downloaded_at'],
            'type' => 'book'
        ];
    }

    $stmt->close();
    return $books;
}

function getDownloadedBooksCount($userId, $search = '', $type = '') {
    global $conn;

    $sql = "SELECT COUNT(*) as count
            FROM downloaded_books db
            JOIN books b ON db.book_id = b.id
            WHERE db.user_id = ?";
    $params = [$userId];
    $types = 'i';

    if (!empty($search)) {
        $sql .= " AND (b.title LIKE ? OR b.author LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
        $types .= 'ss';
    }

    if (!empty($type) && $type !== 'all') {
        // Similar to above
    }

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();
    return $row['count'];
}
?>
