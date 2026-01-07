<?php
include '../../config/connection.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 12;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$course = isset($_GET['course']) ? $_GET['course'] : '';
$publishYear = isset($_GET['publish_year']) ? (int)$_GET['publish_year'] : '';
$uploadYear = isset($_GET['year']) ? (int)$_GET['year'] : '';

// Build query with filters and user_id condition
$query = "SELECT id, title, author, course, publish_date, file_path, cover, created_at FROM books WHERE user_id = ?";
$params = [$userId];
$types = "i";

// Add search filter
if (!empty($search)) {
    $query .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

// Add course filter
if (!empty($course)) {
    $query .= " AND course = ?";
    $params[] = $course;
    $types .= "s";
}

// Add publish year filter
if (!empty($publishYear)) {
    $query .= " AND YEAR(publish_date) = ?";
    $params[] = $publishYear;
    $types .= "i";
}

// Add upload year filter
if (!empty($uploadYear)) {
    $query .= " AND YEAR(created_at) = ?";
    $params[] = $uploadYear;
    $types .= "i";
}

// Add ordering and limit
$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
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

// Check if there are more books
$countQuery = "SELECT COUNT(*) as total FROM books WHERE user_id = ?";
$countParams = [$userId];
$countTypes = "i";

if (!empty($search)) {
    $countQuery .= " AND (title LIKE ? OR author LIKE ?)";
    $countParams[] = "%$search%";
    $countParams[] = "%$search%";
    $countTypes .= "ss";
}

if (!empty($course)) {
    $countQuery .= " AND course = ?";
    $countParams[] = $course;
    $countTypes .= "s";
}

if (!empty($publishYear)) {
    $countQuery .= " AND YEAR(publish_date) = ?";
    $countParams[] = $publishYear;
    $countTypes .= "i";
}

if (!empty($uploadYear)) {
    $countQuery .= " AND YEAR(created_at) = ?";
    $countParams[] = $uploadYear;
    $countTypes .= "i";
}

$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param($countTypes, ...$countParams);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalCount = $countResult->fetch_assoc()['total'];
$countStmt->close();

$hasMore = ($offset + count($books)) < $totalCount;

echo json_encode([
    'success' => true,
    'books' => $books,
    'hasMore' => $hasMore
]);
?>