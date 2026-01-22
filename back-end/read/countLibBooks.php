<?php
include '../../config/connection.php';

function getLibBooksCount() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM lib_books";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}
?>
