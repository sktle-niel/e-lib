<?php
include '../../config/connection.php';

function getRecentLibBooks($limit = 10) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, book_title, book_course, author, publish_date, created_at FROM lib_books ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        $stmt->close();
        return $books;
    } else {
        $stmt->close();
        return [];
    }
}
?>
