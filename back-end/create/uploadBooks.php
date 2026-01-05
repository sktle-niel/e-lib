<?php
include '../../config/connection.php';

function uploadBook($title, $course, $author, $publishDate, $file) {
    global $conn;

    // Validate inputs
    if (empty($title)) {
        return ['success' => false, 'message' => 'Book title is required'];
    }
    if (empty($course)) {
        return ['success' => false, 'message' => 'Course is required'];
    }
    if (empty($author)) {
        return ['success' => false, 'message' => 'Author is required'];
    }
    if (empty($publishDate)) {
        return ['success' => false, 'message' => 'Publish date is required'];
    }
    if (empty($file['name'])) {
        return ['success' => false, 'message' => 'Book file is required'];
    }

    // Check file type (only PDF allowed)
    $allowedTypes = ['application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Only PDF files are allowed'];
    }

    // Check file size (limit to 10MB)
    $maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size must be less than 10MB'];
    }

    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid() . '.' . $fileExtension;
    $uploadDir = '../../uploads/books/';
    $uploadPath = $uploadDir . $uniqueName;

    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => false, 'message' => 'Failed to upload file'];
    }

    // Generate random 7-digit ID
    $bookId = rand(1000000, 9999999);

    // Generate cover from PDF first page
    $coverDir = '../../uploads/covers/';
    if (!is_dir($coverDir)) {
        mkdir($coverDir, 0755, true);
    }
    $coverPath = $coverDir . $bookId . '.jpg';
    $coverWebPath = '/uploads/covers/' . $bookId . '.jpg';

    // Try to convert PDF first page to image using ImageMagick convert command
    $convertCommand = "convert -density 150 -quality 90 \"$uploadPath\"[0] \"$coverPath\" 2>&1";
    $output = [];
    $returnCode = 0;
    exec($convertCommand, $output, $returnCode);

    if ($returnCode === 0 && file_exists($coverPath)) {
        $cover = $coverWebPath;
    } else {
        // Log the error for debugging
        error_log("Convert failed: " . implode("\n", $output) . " Return code: $returnCode");

        // If conversion fails, try with pdftoppm if available
        $ppmPath = $coverDir . $bookId;
        $ppmCommand = "pdftoppm -f 1 -l 1 -scale-to 200 -jpeg \"$uploadPath\" \"$ppmPath\" 2>&1";
        exec($ppmCommand, $output, $returnCode);

        if ($returnCode === 0 && file_exists($ppmPath . '-1.jpg')) {
            rename($ppmPath . '-1.jpg', $coverPath);
            $cover = $coverWebPath;
        } else {
            // Log the error for debugging
            error_log("Pdftoppm failed: " . implode("\n", $output) . " Return code: $returnCode");

            // If both methods fail, use placeholder
            $cover = 'https://via.placeholder.com/150x200/default.jpg';
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO books (id, title, author, publish_date, course, file_path, cover, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issssss", $bookId, $title, $author, $publishDate, $course, $uploadPath, $cover);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Book uploaded successfully'];
    } else {
        // Delete uploaded file if database insert fails
        unlink($uploadPath);
        return ['success' => false, 'message' => 'Failed to save book to database'];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['book_title'] ?? '';
    $course = $_POST['book_course'] ?? '';
    $author = $_POST['author'] ?? '';
    $publishDate = $_POST['publish_date'] ?? '';
    $file = $_FILES['book_file'] ?? [];

    $result = uploadBook($title, $course, $author, $publishDate, $file);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
