<?php
// Only start session if one hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../config/connection.php';

function editBook($bookId, $title, $course, $author, $publishDate, $coverImage = null) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'User not logged in'];
    }
    
    $userId = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($bookId) || empty($title) || empty($course) || empty($author) || empty($publishDate)) {
        return ['success' => false, 'message' => 'Book ID, title, course, author, and publish date are required'];
    }
    
    // Check if book exists and belongs to the user
    $checkStmt = $conn->prepare("SELECT id, cover FROM books WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $bookId, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        $checkStmt->close();
        return ['success' => false, 'message' => 'Book not found or you do not have permission to edit it'];
    }
    
    $bookData = $checkResult->fetch_assoc();
    $oldCoverPath = $bookData['cover'];
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
    
    // Update book in database
    $stmt = $conn->prepare("UPDATE books SET title = ?, course = ?, author = ?, publish_date = ?, cover = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssssii", $title, $course, $author, $publishDate, $coverUploadPath, $bookId, $userId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Book updated successfully'];
    } else {
        // If database update fails and new cover was uploaded, delete it
        if ($coverUploadPath !== $oldCoverPath && file_exists($coverUploadPath)) {
            unlink($coverUploadPath);
        }
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to update book: ' . $conn->error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'] ?? '';
    $title = $_POST['book_title'] ?? '';
    $course = $_POST['book_course'] ?? '';
    $author = $_POST['author'] ?? '';
    $publishDate = $_POST['publish_date'] ?? '';
    $coverImage = $_FILES['cover_image'] ?? null;
    
    $result = editBook($bookId, $title, $course, $author, $publishDate, $coverImage);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>