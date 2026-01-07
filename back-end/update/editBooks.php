<?php
include '../../config/connection.php';

function updateBook($book_id, $title, $course, $author, $publish_date, $coverImage = null) {
    global $conn;
    
    // Get current cover path if we need to delete old cover
    $coverPath = null;
    if ($coverImage && !empty($coverImage['name'])) {
        $stmt = $conn->prepare("SELECT cover FROM books WHERE id = ?");
        $stmt->bind_param('i', $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $coverPath = $row['cover'];
        }
    }
    
    // If new cover image is uploaded
    if ($coverImage && !empty($coverImage['name'])) {
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

        // Upload new cover image
        $coverDir = '../../uploads/covers/';
        if (!is_dir($coverDir)) {
            mkdir($coverDir, 0755, true);
        }

        $coverExtension = pathinfo($coverImage['name'], PATHINFO_EXTENSION);
        $uniqueCoverName = $book_id . '.' . $coverExtension;
        $newCoverPath = $coverDir . $uniqueCoverName;
        $coverWebPath = '/uploads/covers/' . $uniqueCoverName;

        // Delete old cover if exists and is not a placeholder
        if ($coverPath && strpos($coverPath, 'placeholder') === false) {
            $oldCoverFile = '../../' . ltrim($coverPath, '/');
            if (file_exists($oldCoverFile)) {
                unlink($oldCoverFile);
            }
        }

        // Move uploaded cover image
        if (!move_uploaded_file($coverImage['tmp_name'], $newCoverPath)) {
            return ['success' => false, 'message' => 'Failed to upload cover image'];
        }

        // Update with new cover
        $sql = "UPDATE books SET title = ?, course = ?, author = ?, publish_date = ?, cover = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssi', $title, $course, $author, $publish_date, $coverWebPath, $book_id);
    } else {
        // Update without changing cover
        $sql = "UPDATE books SET title = ?, course = ?, author = ?, publish_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $title, $course, $author, $publish_date, $book_id);
    }
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Book updated successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to update book: ' . $stmt->error];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $title = isset($_POST['book_title']) ? trim($_POST['book_title']) : '';
    $course = isset($_POST['book_course']) ? $_POST['book_course'] : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $publish_date = isset($_POST['publish_date']) ? $_POST['publish_date'] : '';
    $coverImage = isset($_FILES['cover_image']) ? $_FILES['cover_image'] : null;
    
    if ($book_id <= 0 || empty($title) || empty($course) || empty($author) || empty($publish_date)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }
    
    $result = updateBook($book_id, $title, $course, $author, $publish_date, $coverImage);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>