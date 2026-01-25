<?php
include '../../config/connection.php';

function getAllStudents($limit = 10, $offset = 0) {
    global $conn;
    
    $sql = "SELECT id, username, password, firstname, lastname, program, lrn_number, user_type, profile_picture, created_at 
            FROM users 
            WHERE user_type = 'student' 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        return $students;
    } else {
        return [];
    }
}

function getTotalStudents() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as total FROM users WHERE user_type = 'student'";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['total'];
    } else {
        return 0;
    }
}
?>