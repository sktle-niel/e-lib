<?php
include '../../config/connection.php';

function updateBook($book_id, $title, $course, $author, $publish_date) {
    global $conn;

    $sql = "UPDATE books SET title = ?, course = ?, author = ?, publish_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $title, $course, $author, $publish_date, $book_id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Book updated successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to update book'];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $title = isset($_POST['book_title']) ? trim($_POST['book_title']) : '';
    $course = isset($_POST['book_course']) ? $_POST['book_course'] : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $publish_date = isset($_POST['publish_date']) ? $_POST['publish_date'] : '';

    if ($book_id <= 0 || empty($title) || empty($course) || empty($author) || empty($publish_date)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $result = updateBook($book_id, $title, $course, $author, $publish_date);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
