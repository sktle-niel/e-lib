<?php
include '../../config/connection.php';

function getBookPenaltiesCount() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM borrowed_lib_books WHERE expected_return_date < DATE_SUB(CURDATE(), INTERVAL 3 DAY)";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}
?>
