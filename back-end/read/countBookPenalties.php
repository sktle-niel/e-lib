<?php
include '../../config/connection.php';

function getBookPenaltiesCount() {
    global $conn;

    $sql = "SELECT COUNT(*) as count FROM penalty_clear_log";
    $result = $conn->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}
?>
