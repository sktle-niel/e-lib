<?php
include '../../config/connection.php';

function getAllModules($search = '', $course = '', $year = '', $limit = null, $offset = 0) {
    global $conn;

    $sql = "SELECT id, title, uploadedDate, course, cover FROM modules WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($search)) {
        $sql .= " AND title LIKE ?";
        $params[] = '%' . $search . '%';
        $types .= 's';
    }

    if (!empty($course)) {
        $sql .= " AND course = ?";
        $params[] = $course;
        $types .= 's';
    }

    if (!empty($year)) {
        $sql .= " AND YEAR(uploadedDate) = ?";
        $params[] = $year;
        $types .= 'i';
    }

    $sql .= " ORDER BY uploadedDate DESC";

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
            'id' => $row['id'],
            'title' => $row['title'],
            'uploadedDate' => $row['uploadedDate'],
            'course' => $row['course'],
            'cover' => $row['cover'],
            'available' => true // Assuming all are available since no file_path
        ];
    }

    return $modules;
}

function getModulesCount($search = '', $course = '', $year = '') {
    global $conn;

    $sql = "SELECT COUNT(*) as count FROM modules WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($search)) {
        $sql .= " AND title LIKE ?";
        $params[] = '%' . $search . '%';
        $types .= 's';
    }

    if (!empty($course)) {
        $sql .= " AND course = ?";
        $params[] = $course;
        $types .= 's';
    }

    if (!empty($year)) {
        $sql .= " AND YEAR(uploadedDate) = ?";
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

    return $row['count'];
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
