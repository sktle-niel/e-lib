<?php
include '../../config/connection.php';

function updateModule($module_id, $title, $course, $coverImage = null) {
    global $conn;
    
    // Validate inputs
    if (empty($title) || empty($course)) {
        return ['success' => false, 'message' => 'Title and course are required'];
    }
    
    // Get current module data
    $stmt = $conn->prepare("SELECT cover FROM modules WHERE id = ?");
    $stmt->bind_param('i', $module_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Module not found'];
    }
    
    $currentModule = $result->fetch_assoc();
    $oldCoverPath = $currentModule['cover'];
    $stmt->close();
    
    $newCoverPath = $oldCoverPath; // Keep existing cover by default
    
    // If a new cover image is uploaded
    if ($coverImage && !empty($coverImage['name']) && $coverImage['error'] === UPLOAD_ERR_OK) {
        // Validate cover image type
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
        
        // Generate unique filename for new cover
        $coverExtension = pathinfo($coverImage['name'], PATHINFO_EXTENSION);
        $uniqueCoverName = uniqid('cover_') . '.' . $coverExtension;
        $coverUploadDir = '../../uploads/covers/';
        $newCoverPath = $coverUploadDir . $uniqueCoverName;
        
        // Create directory if it doesn't exist
        if (!is_dir($coverUploadDir)) {
            mkdir($coverUploadDir, 0755, true);
        }
        
        // Move uploaded cover image
        if (!move_uploaded_file($coverImage['tmp_name'], $newCoverPath)) {
            return ['success' => false, 'message' => 'Failed to upload new cover image'];
        }
        
        // Delete old cover image if it exists and is different from new one
        if (!empty($oldCoverPath) && file_exists($oldCoverPath) && $oldCoverPath !== $newCoverPath) {
            unlink($oldCoverPath);
        }
    }
    
    // Update module in database
    $sql = "UPDATE modules SET title = ?, course = ?, cover = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $title, $course, $newCoverPath, $module_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Module updated successfully'];
    } else {
        // If database update fails and new cover was uploaded, delete it
        if ($newCoverPath !== $oldCoverPath && file_exists($newCoverPath)) {
            unlink($newCoverPath);
        }
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to update module: ' . $conn->error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id = isset($_POST['module_id']) ? (int)$_POST['module_id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $course = isset($_POST['course']) ? $_POST['course'] : '';
    $coverImage = isset($_FILES['cover_image']) ? $_FILES['cover_image'] : null;
    
    if ($module_id <= 0 || empty($title) || empty($course)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }
    
    $result = updateModule($module_id, $title, $course, $coverImage);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>