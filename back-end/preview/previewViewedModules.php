<?php
include '../../config/connection.php';

function getRecentViewedModules($limit = 6) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $userId = $_SESSION['user_id'];
    
    $sql = "SELECT pm.previewed_at, m.id, m.title, m.cover, m.file_path 
            FROM preview_modules pm 
            JOIN modules m ON pm.module_id = m.id 
            WHERE pm.user_id = ? 
            ORDER BY pm.previewed_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $modules = [];
    while ($row = $result->fetch_assoc()) {
        $modules[] = [
            'type' => 'module',
            'id' => $row['id'],
            'title' => $row['title'],
            'author' => 'Module', // Placeholder for modules
            'cover' => $row['cover'],
            'file_path' => $row['file_path'],
            'previewed_at' => $row['previewed_at'],
            'available' => file_exists($row['file_path'])
        ];
    }
    
    $stmt->close();
    return $modules;
}
?>
