<?php
include '../../config/connection.php';

if (!isset($_GET['id'])) {
    die('Invalid request');
}

$moduleId = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT file_path, title FROM modules WHERE id = ?");
$stmt->bind_param("i", $moduleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die('File not found');
}

$row = $result->fetch_assoc();
$filePath = $row['file_path'];
$title = $row['title'];

if (!file_exists($filePath)) {
    die('File not found on server');
}

// Set headers for inline display (preview)
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));

// Output the file
readfile($filePath);
exit;
?>
