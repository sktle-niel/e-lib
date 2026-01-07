<?php
include '../../config/connection.php';

function editModule($moduleId, $title, $course, $coverImage = null) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'User not logged in'];
    }
    
    $userId = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($moduleId) || empty($title) || empty($course)) {
        return ['success' => false, 'message' => 'Module ID, title, and course are required'];
    }
    
    // Check if module exists and belongs to the user
    $checkStmt = $conn->prepare("SELECT id, cover FROM modules WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $moduleId, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        $checkStmt->close();
        return ['success' => false, 'message' => 'Module not found or you do not have permission to edit it'];
    }
    
    $moduleData = $checkResult->fetch_assoc();
    $oldCoverPath = $moduleData['cover'];
    $checkStmt->close();
    
    $coverUploadPath = $oldCoverPath; // Keep old cover by default
    
    // If a new cover image is provided
    if ($coverImage && !empty($coverImage['name'])) {
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
        
        // Generate unique filename for cover image
        $coverExtension = pathinfo($coverImage['name'], PATHINFO_EXTENSION);
        $uniqueCoverName = uniqid('cover_') . '.' . $coverExtension;
        $coverUploadDir = '../../uploads/covers/';
        $coverUploadPath = $coverUploadDir . $uniqueCoverName;
        
        // Create upload directory if it doesn't exist
        if (!is_dir($coverUploadDir)) {
            mkdir($coverUploadDir, 0755, true);
        }
        
        // Move uploaded cover image
        if (!move_uploaded_file($coverImage['tmp_name'], $coverUploadPath)) {
            return ['success' => false, 'message' => 'Failed to upload new cover image'];
        }
        
        // Delete old cover image if upload was successful
        if (file_exists($oldCoverPath)) {
            unlink($oldCoverPath);
        }
    }
    
    // Update module in database
    $stmt = $conn->prepare("UPDATE modules SET title = ?, course = ?, cover = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $title, $course, $coverUploadPath, $moduleId, $userId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Module updated successfully'];
    } else {
        // If database update fails and new cover was uploaded, delete it
        if ($coverUploadPath !== $oldCoverPath && file_exists($coverUploadPath)) {
            unlink($coverUploadPath);
        }
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to update module: ' . $conn->error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleId = $_POST['module_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $course = $_POST['course'] ?? '';
    $coverImage = $_FILES['cover_image'] ?? null;
    
    $result = editModule($moduleId, $title, $course, $coverImage);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>