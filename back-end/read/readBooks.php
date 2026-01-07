<?php
include '../../config/connection.php';

function getAllBooks($search = '', $course = '', $publishYear = '', $uploadYear = '', $limit = null, $offset = 0) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $userId = $_SESSION['user_id'];
    
    $sql = "SELECT id, title, author, course, publish_date, file_path, cover, created_at FROM books WHERE user_id = ?";
    $params = [$userId];
    $types = 'i';
    
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
    
    if (!empty($publishYear)) {
        $sql .= " AND YEAR(publish_date) = ?";
        $params[] = $publishYear;
        $types .= 'i';
    }
    
    if (!empty($uploadYear)) {
        $sql .= " AND YEAR(created_at) = ?";
        $params[] = $uploadYear;
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
    
    $stmt->close();
    return $books;
}

function getBooksCount($search = '', $course = '', $publishYear = '', $uploadYear = '') {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    
    $userId = $_SESSION['user_id'];
    
    $sql = "SELECT COUNT(*) as count FROM books WHERE user_id = ?";
    $params = [$userId];
    $types = 'i';
    
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
    
    if (!empty($publishYear)) {
        $sql .= " AND YEAR(publish_date) = ?";
        $params[] = $publishYear;
        $types .= 'i';
    }
    
    if (!empty($uploadYear)) {
        $sql .= " AND YEAR(created_at) = ?";
        $params[] = $uploadYear;
        $types .= 'i';
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

function getBookById($bookId) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT id, title, author, course, publish_date, file_path, cover, created_at FROM books WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $bookId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $book = $result->fetch_assoc();
    $stmt->close();
    
    return $book;
}

// Handle AJAX requests for pagination
if (isset($_GET['ajax'])) {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $course = isset($_GET['course']) ? $_GET['course'] : '';
    $publishYear = isset($_GET['publish_year']) ? (int)$_GET['publish_year'] : '';
    $uploadYear = isset($_GET['upload_year']) ? (int)$_GET['upload_year'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 12;
    $offset = ($page - 1) * $perPage;
    
    $books = getAllBooks($search, $course, $publishYear, $uploadYear, $perPage, $offset);
    header('Content-Type: application/json');
    echo json_encode($books);
    exit;
}
?>