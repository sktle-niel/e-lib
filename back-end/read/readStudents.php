<?php
include '../../config/connection.php';

function getStudentCounts($programs) {
    global $conn;
    
    if (empty($programs)) {
        return [];
    }
    
    $placeholders = str_repeat('?,', count($programs) - 1) . '?';
    $sql = "SELECT program, COUNT(*) as count FROM users WHERE user_type = 'student' AND program IN ($placeholders) GROUP BY program";
    
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($programs));
    $stmt->bind_param($types, ...$programs);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $counts = [];
    while ($row = $result->fetch_assoc()) {
        $counts[$row['program']] = $row['count'];
    }
    
    $stmt->close();
    return $counts;
}
?>
