<?php
// CRITICAL: No whitespace or output before this line
include '../../auth/sessionCheck.php';
include '../../config/connection.php';
include '../recent/downloadedBooks.php';

if (!isset($_GET['id'])) {
    die('Invalid request');
}

$bookId = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT file_path, title FROM books WHERE id = ?");
$stmt->bind_param("i", $bookId);
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
recordBookDownload($_SESSION['user_id'], $bookId);

// Clean any output buffers
if (ob_get_level()) {
    ob_end_clean();
}

// Detect file type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filePath);
finfo_close($finfo);

// Set headers
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . basename($title) . '.pdf"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output file
readfile($filePath);
exit;
// No closing PHP tag to prevent accidental whitespace