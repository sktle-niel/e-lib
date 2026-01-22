<?php
include '../../config/connection.php';

function getBorrowedLibBooksCount() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM borrowed_lib_books WHERE 1";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}
?>
