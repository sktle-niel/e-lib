<?php
include '../../config/connection.php';

function getAllBooks($search = '', $course = '', $year = '', $publishYear = '', $limit = null, $offset = 0) {
    global $conn;

    $sql = "SELECT id, title, author, course, publish_date, file_path, cover, created_at FROM books WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR author LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
        $types .= 'ss';
    }

    if (!empty($course)) {
        $sql .= " AND course = ?";
        $params[] = $course;
        $types .= 's';
    }

    if (!empty($year)) {
        $sql .= " AND YEAR(created_at) = ?";
        $params[] = $year;
        $types .= 'i';
    }

    if (!empty($publishYear)) {
        $sql .= " AND YEAR(publish_date) = ?";
        $params[] = $publishYear;
        $types .= 'i';
    }

    $sql .= " ORDER BY created_at DESC";

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
            'id' => $row['id'],
            'title' => $row['title'],
            'author' => $row['author'],
            'course' => $row['course'],
            'publish_date' => $row['publish_date'],
            'file_path' => $row['file_path'],
            'cover' => $row['cover'],
            'created_at' => $row['created_at'],
            'available' => file_exists($row['file_path']) // Check if file exists
        ];
    }

    return $books;
}

function getBooksCount($search = '', $course = '', $year = '') {
    global $conn;

    $sql = "SELECT COUNT(*) as count FROM books WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR author LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
        $types .= 'ss';
    }

    if (!empty($course)) {
        $sql .= " AND course = ?";
        $params[] = $course;
        $types .= 's';
    }

    if (!empty($year)) {
        $sql .= " AND YEAR(publish_date) = ?";
        $params[] = $year;
        $types .= 'i';
    }

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['count'];
}

// Handle AJAX requests for pagination
if (isset($_GET['ajax'])) {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $course = isset($_GET['course']) ? $_GET['course'] : '';
    $year = isset($_GET['year']) ? (int)$_GET['year'] : '';
    $publishYear = isset($_GET['publish_year']) ? (int)$_GET['publish_year'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 12;
    $offset = ($page - 1) * $perPage;

    $books = getAllBooks($search, $course, $year, $publishYear, $perPage, $offset);
    header('Content-Type: application/json');
    echo json_encode($books);
    exit;
}
?>
