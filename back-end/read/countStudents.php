<?php
include '../../config/connection.php';

function getStudentsCount() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM users WHERE user_type = 'student'";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}
?>
