<?php
include '../../config/connection.php';

function getModulesCount() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM modules";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}
?>
