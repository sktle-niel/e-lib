<?php
include '../../config/connection.php';

function updateModule($module_id, $title, $course) {
    global $conn;

    $sql = "UPDATE modules SET title = ?, course = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $title, $course, $module_id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Module updated successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to update module'];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id = isset($_POST['module_id']) ? (int)$_POST['module_id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $course = isset($_POST['course']) ? $_POST['course'] : '';

    if ($module_id <= 0 || empty($title) || empty($course)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $result = updateModule($module_id, $title, $course);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
