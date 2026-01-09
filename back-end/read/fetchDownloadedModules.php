<?php
include '../../config/connection.php';

function getDownloadedModules($userId, $search = '', $type = '', $limit = null, $offset = 0) {
    global $conn;
    
    $sql = "SELECT dm.module_id, MAX(dm.downloaded_at) as downloaded_at, 
                   m.title, m.cover, m.course, m.uploadedDate, m.user_id
            FROM downloaded_modules dm
            JOIN modules m ON dm.module_id = m.id
            WHERE dm.user_id = ?
            GROUP BY dm.module_id, m.title, m.cover, m.course, m.uploadedDate, m.user_id";
    
    $params = [$userId];
    $types = 'i';
    
    if (!empty($search)) {
        $sql .= " AND m.title LIKE ?";
        $params[] = '%' . $search . '%';
        $types .= 's';
    }
    
    if (!empty($type) && $type !== 'all') {
        // Filter by course if needed
        // $sql .= " AND m.course = ?";
        // $params[] = $type;
        // $types .= 's';
    }
    
    $sql .= " ORDER BY downloaded_at DESC";
    
    if ($limit !== null) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
    }
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $modules = [];
    while ($row = $result->fetch_assoc()) {
        $modules[] = [
            'id' => $row['module_id'],
            'title' => $row['title'],
            'author' => $row['course'] . ' | ' . date('M d, Y', strtotime($row['uploadedDate'])),
            'cover' => $row['cover'],
            'downloadDate' => $row['downloaded_at'],
            'type' => 'module'
        ];
    }
    
    $stmt->close();
    return $modules;
}

function getDownloadedModulesCount($userId, $search = '', $type = '') {
    global $conn;
    
    $sql = "SELECT COUNT(DISTINCT dm.module_id) as count
            FROM downloaded_modules dm
            JOIN modules m ON dm.module_id = m.id
            WHERE dm.user_id = ?";
    
    $params = [$userId];
    $types = 'i';
    
    if (!empty($search)) {
        $sql .= " AND m.title LIKE ?";
        $params[] = '%' . $search . '%';
        $types .= 's';
    }
    
    if (!empty($type) && $type !== 'all') {
        // Filter by course if needed
    }
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['count'];
}
?>