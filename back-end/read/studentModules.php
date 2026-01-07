<?php
include '../../config/connection.php';

function getAllModules($search = '', $course = '', $year = '', $limit = 12, $offset = 0) {
    global $conn;
    
    $sql = "SELECT id, title, author, course, publish_date, file_path, cover, created_at FROM books";
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $sql .= " WHERE (title LIKE ? OR author LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
        $types .= 'ss';
    }
    
    if (!empty($course)) {
        $sql .= (!empty($search) ? " AND" : " WHERE") . " course = ?";
        $params[] = $course;
        $types .= 's';
    }
    
    if (!empty($year)) {
        $sql .= (!empty($search) || !empty($course) ? " AND" : " WHERE") . " YEAR(created_at) = ?";
        $params[] = $year;
        $types .= 'i';
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $modules = [];
    while ($row = $result->fetch_assoc()) {
        $modules[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'uploadedDate' => $row['created_at'],
            'course' => $row['course'],
            'cover' => $row['cover'],
            'file_path' => $row['file_path']
        ];
    }
    
    $stmt->close();
    return $modules;
}

function getModulesCount($search = '', $course = '', $year = '') {
    global $conn;
    
    $sql = "SELECT COUNT(*) as total FROM books";
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $sql .= " WHERE (title LIKE ? OR author LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
        $types .= 'ss';
    }
    
    if (!empty($course)) {
        $sql .= (!empty($search) ? " AND" : " WHERE") . " course = ?";
        $params[] = $course;
        $types .= 's';
    }
    
    if (!empty($year)) {
        $sql .= (!empty($search) || !empty($course) ? " AND" : " WHERE") . " YEAR(created_at) = ?";
        $params[] = $year;
        $types .= 'i';
    }
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    return $row['total'];
}

function getModuleById($moduleId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, title, author, course, publish_date, file_path, cover, created_at FROM books WHERE id = ?");
    $stmt->bind_param("i", $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $module = $result->fetch_assoc();
    if ($module) {
        $module['uploadedDate'] = $module['created_at'];
        unset($module['created_at']);
    }
    $stmt->close();
    
    return $module;
}

// Handle AJAX requests for pagination
if (isset($_GET['ajax'])) {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $course = isset($_GET['course']) ? $_GET['course'] : '';
    $year = isset($_GET['year']) ? (int)$_GET['year'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 12;
    $offset = ($page - 1) * $perPage;
    
    $modules = getAllModules($search, $course, $year, $perPage, $offset);
    header('Content-Type: application/json');
    echo json_encode($modules);
    exit;
}
?>
