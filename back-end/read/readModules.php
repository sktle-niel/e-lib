<?php
include '../../config/connection.php';

function getAllModules($search = '', $course = '', $year = '', $limit = 12, $offset = 0) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $userId = $_SESSION['user_id'];
    
    // Build query with filters and user_id condition
    $query = "SELECT id, title, uploadedDate, course, cover, file_path FROM modules WHERE user_id = ?";
    $params = [$userId];
    $types = "i";
    
    // Add search filter
    if (!empty($search)) {
        $query .= " AND title LIKE ?";
        $params[] = "%$search%";
        $types .= "s";
    }
    
    // Add course filter
    if (!empty($course)) {
        $query .= " AND course = ?";
        $params[] = $course;
        $types .= "s";
    }
    
    // Add year filter
    if (!empty($year)) {
        $query .= " AND YEAR(uploadedDate) = ?";
        $params[] = $year;
        $types .= "i";
    }
    
    // Add ordering and limit
    $query .= " ORDER BY uploadedDate DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $modules = [];
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }
    
    $stmt->close();
    return $modules;
}

function getModulesCount($search = '', $course = '', $year = '') {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Build query with filters and user_id condition
    $query = "SELECT COUNT(*) as total FROM modules WHERE user_id = ?";
    $params = [$userId];
    $types = "i";
    
    // Add search filter
    if (!empty($search)) {
        $query .= " AND title LIKE ?";
        $params[] = "%$search%";
        $types .= "s";
    }
    
    // Add course filter
    if (!empty($course)) {
        $query .= " AND course = ?";
        $params[] = $course;
        $types .= "s";
    }
    
    // Add year filter
    if (!empty($year)) {
        $query .= " AND YEAR(uploadedDate) = ?";
        $params[] = $year;
        $types .= "i";
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    return $row['total'];
}

function getModuleById($moduleId) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT id, title, uploadedDate, course, cover, file_path FROM modules WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $moduleId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $module = $result->fetch_assoc();
    $stmt->close();
    
    return $module;
}
?>