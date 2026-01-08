<?php
include '../../auth/sessionCheck.php';
include '../../config/connection.php';
include '../recent/downloadedModules.php';

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

// Record the download
recordModuleDownload($_SESSION['user_id'], $moduleId);

if (!file_exists($filePath)) {
    die('File not found on server');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
?>
