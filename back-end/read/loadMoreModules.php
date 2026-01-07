<?php
session_start();
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
$year = isset($_GET['year']) ? (int)$_GET['year'] : '';

// Build query with filters and user_id condition
$query = "SELECT id, title, uploadedDate, course, cover, file_path FROM modules WHERE user_id = ?";
$params = [$userId];
$types = "i";

// Add search filter
if (!empty($search)) {
    $query .= " AND title LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

// Add course filter
if (!empty($course)) {
    $query .= " AND course = ?";
    $params[] = $course;
    $types .= "s";
}

// Add year filter
if (!empty($year)) {
    $query .= " AND YEAR(uploadedDate) = ?";
    $params[] = $year;
    $types .= "i";
}

// Add ordering and limit
$query .= " ORDER BY uploadedDate DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$modules = [];
while ($row = $result->fetch_assoc()) {
    $modules[] = $row;
}

$stmt->close();

// Check if there are more modules
$countQuery = "SELECT COUNT(*) as total FROM modules WHERE user_id = ?";
$countParams = [$userId];
$countTypes = "i";

if (!empty($search)) {
    $countQuery .= " AND title LIKE ?";
    $countParams[] = "%$search%";
    $countTypes .= "s";
}

if (!empty($course)) {
    $countQuery .= " AND course = ?";
    $countParams[] = $course;
    $countTypes .= "s";
}

if (!empty($year)) {
    $countQuery .= " AND YEAR(uploadedDate) = ?";
    $countParams[] = $year;
    $countTypes .= "i";
}

$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param($countTypes, ...$countParams);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalCount = $countResult->fetch_assoc()['total'];
$countStmt->close();

$hasMore = ($offset + count($modules)) < $totalCount;

echo json_encode([
    'success' => true,
    'modules' => $modules,
    'hasMore' => $hasMore
]);
?>