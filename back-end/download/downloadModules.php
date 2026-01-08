<?php
// CRITICAL: No whitespace or output before this line
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

if (!file_exists($filePath)) {
    die('File not found on server');
}

// Record the download
recordModuleDownload($_SESSION['user_id'], $moduleId);

// Clean any output buffers
if (ob_get_level()) {
    ob_end_clean();
}

// Set headers
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($title) . '.pdf"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output file
readfile($filePath);
exit;
// No closing PHP tag to prevent accidental whitespace