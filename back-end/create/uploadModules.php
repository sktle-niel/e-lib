<?php
include '../../config/connection.php';

function uploadModule($title, $course, $moduleFile, $coverImage) {
    global $conn;
    
    // Validate inputs
    if (empty($title) || empty($course) || empty($moduleFile['name']) || empty($coverImage['name'])) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    // Check module file type (only PDF allowed)
    $allowedModuleTypes = ['application/pdf'];
    if (!in_array($moduleFile['type'], $allowedModuleTypes)) {
        return ['success' => false, 'message' => 'Only PDF files are allowed for modules'];
    }
    
    // Check module file size (limit to 10MB)
    $maxModuleSize = 10 * 1024 * 1024; // 10MB in bytes
    if ($moduleFile['size'] > $maxModuleSize) {
        return ['success' => false, 'message' => 'Module file size must be less than 10MB'];
    }
    
    // Check cover image type (only images allowed)
    $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $coverFileType = strtolower($coverImage['type']);
    if (!in_array($coverFileType, $allowedImageTypes)) {
        return ['success' => false, 'message' => 'Only JPG, PNG, or GIF images are allowed for cover'];
    }
    
    // Check cover image size (limit to 5MB)
    $maxCoverSize = 5 * 1024 * 1024; // 5MB in bytes
    if ($coverImage['size'] > $maxCoverSize) {
        return ['success' => false, 'message' => 'Cover image size must be less than 5MB'];
    }
    
    // Generate unique filename for module
    $moduleExtension = pathinfo($moduleFile['name'], PATHINFO_EXTENSION);
    $uniqueModuleName = uniqid('module_') . '.' . $moduleExtension;
    $moduleUploadDir = '../../uploads/modules/';
    $moduleUploadPath = $moduleUploadDir . $uniqueModuleName;
    
    // Generate unique filename for cover image
    $coverExtension = pathinfo($coverImage['name'], PATHINFO_EXTENSION);
    $uniqueCoverName = uniqid('cover_') . '.' . $coverExtension;
    $coverUploadDir = '../../uploads/covers/';
    $coverUploadPath = $coverUploadDir . $uniqueCoverName;
    
    // Create upload directories if they don't exist
    if (!is_dir($moduleUploadDir)) {
        mkdir($moduleUploadDir, 0755, true);
    }
    if (!is_dir($coverUploadDir)) {
        mkdir($coverUploadDir, 0755, true);
    }
    
    // Move uploaded module file
    if (!move_uploaded_file($moduleFile['tmp_name'], $moduleUploadPath)) {
        return ['success' => false, 'message' => 'Failed to upload module file'];
    }
    
    // Move uploaded cover image
    if (!move_uploaded_file($coverImage['tmp_name'], $coverUploadPath)) {
        // Delete module file if cover upload fails
        unlink($moduleUploadPath);
        return ['success' => false, 'message' => 'Failed to upload cover image'];
    }
    
    // Generate random 7-digit ID
    $moduleId = rand(1000000, 9999999);
    
    // Check if ID already exists (very unlikely but good practice)
    $checkStmt = $conn->prepare("SELECT id FROM modules WHERE id = ?");
    $checkStmt->bind_param("i", $moduleId);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    // If ID exists, generate a new one
    while ($checkStmt->num_rows > 0) {
        $moduleId = rand(1000000, 9999999);
        $checkStmt->bind_param("i", $moduleId);
        $checkStmt->execute();
        $checkStmt->store_result();
    }
    $checkStmt->close();
    
    // Insert into database
    $uploadedDate = date('Y-m-d');
    $stmt = $conn->prepare("INSERT INTO modules (id, title, uploadedDate, course, cover, file_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $moduleId, $title, $uploadedDate, $course, $coverUploadPath, $moduleUploadPath);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Module uploaded successfully', 'module_id' => $moduleId];
    } else {
        // Delete uploaded files if database insert fails
        unlink($moduleUploadPath);
        unlink($coverUploadPath);
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to save module to database: ' . $conn->error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $course = $_POST['course'] ?? '';
    $moduleFile = $_FILES['module_file'] ?? [];
    $coverImage = $_FILES['cover_image'] ?? [];
    
    $result = uploadModule($title, $course, $moduleFile, $coverImage);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>