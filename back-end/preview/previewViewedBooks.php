<?php
include '../../config/connection.php';

function getRecentViewedBooks($limit = 6) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $userId = $_SESSION['user_id'];
    
    $sql = "SELECT pb.previewed_at, b.id, b.title, b.author, b.cover, b.file_path 
            FROM preview_books pb 
            JOIN books b ON pb.book_id = b.id 
            WHERE pb.user_id = ? 
            ORDER BY pb.previewed_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = [
            'type' => 'book',
            'id' => $row['id'],
            'title' => $row['title'],
            'author' => $row['author'],
            'cover' => $row['cover'],
            'file_path' => $row['file_path'],
            'previewed_at' => $row['previewed_at'],
            'available' => file_exists($row['file_path'])
        ];
    }
    
    $stmt->close();
    return $books;
}
?>
