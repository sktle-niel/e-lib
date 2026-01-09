<?php
include '../../config/connection.php';

function getBooksCount() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM books";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}
?>
