<?php
include '../../config/connection.php';

function uploadModule($title, $course, $file) {
    global $conn;

    // Validate inputs
    if (empty($title) || empty($course) || empty($file['name'])) {
        return ['success' => false, 'message' => 'All fields are required'];
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
    $uploadDir = '../../uploads/modules/';
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
    $moduleId = rand(1000000, 9999999);

    // Insert into database
    $uploadedDate = date('Y-m-d');
    $stmt = $conn->prepare("INSERT INTO modules (id, title, uploadedDate, course, cover) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $moduleId, $title, $uploadedDate, $course, $uploadPath);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Module uploaded successfully'];
    } else {
        // Delete uploaded file if database insert fails
        unlink($uploadPath);
        return ['success' => false, 'message' => 'Failed to save module to database'];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $course = $_POST['course'] ?? '';
    $file = $_FILES['module_file'] ?? [];

    $result = uploadModule($title, $course, $file);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
