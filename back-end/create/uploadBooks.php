<?php
include '../../config/connection.php';

function uploadBook($title, $course, $author, $publishDate, $file, $coverImage) {
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
    if (empty($coverImage['name'])) {
        return ['success' => false, 'message' => 'Cover image is required'];
    }

    // Check file type (only PDF allowed for book)
    $allowedTypes = ['application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Only PDF files are allowed'];
    }

    // Check file size (limit to 10MB for book)
    $maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size must be less than 10MB'];
    }

    // Validate cover image
    $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($coverImage['type'], $allowedImageTypes)) {
        return ['success' => false, 'message' => 'Cover image must be JPG, PNG, or GIF'];
    }

    // Check cover image size (limit to 5MB)
    $maxImageSize = 5 * 1024 * 1024; // 5MB in bytes
    if ($coverImage['size'] > $maxImageSize) {
        return ['success' => false, 'message' => 'Cover image must be less than 5MB'];
    }

    // Generate random 7-digit ID
    $bookId = rand(1000000, 9999999);

    // Upload book file
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueFileName = uniqid() . '.' . $fileExtension;
    $uploadDir = '../../uploads/books/';
    $uploadPath = $uploadDir . $uniqueFileName;

    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move uploaded book file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => false, 'message' => 'Failed to upload book file'];
    }

    // Upload cover image
    $coverDir = '../../uploads/covers/';
    if (!is_dir($coverDir)) {
        mkdir($coverDir, 0755, true);
    }

    $coverExtension = pathinfo($coverImage['name'], PATHINFO_EXTENSION);
    $uniqueCoverName = $bookId . '.' . $coverExtension;
    $coverPath = $coverDir . $uniqueCoverName;
    $coverWebPath = '/uploads/covers/' . $uniqueCoverName;

    // Move uploaded cover image
    if (!move_uploaded_file($coverImage['tmp_name'], $coverPath)) {
        // Delete book file if cover upload fails
        unlink($uploadPath);
        return ['success' => false, 'message' => 'Failed to upload cover image'];
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO books (id, title, author, publish_date, course, file_path, cover, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issssss", $bookId, $title, $author, $publishDate, $course, $uploadPath, $coverWebPath);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Book uploaded successfully'];
    } else {
        // Delete uploaded files if database insert fails
        unlink($uploadPath);
        unlink($coverPath);
        return ['success' => false, 'message' => 'Failed to save book to database: ' . $stmt->error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['book_title'] ?? '';
    $course = $_POST['book_course'] ?? '';
    $author = $_POST['author'] ?? '';
    $publishDate = $_POST['publish_date'] ?? '';
    $file = $_FILES['book_file'] ?? [];
    $coverImage = $_FILES['cover_image'] ?? [];

    $result = uploadBook($title, $course, $author, $publishDate, $file, $coverImage);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>