<?php
include '../../config/connection.php';

function deleteModule($module_id) {
    global $conn;

    // First, get the file path
    $stmt = $conn->prepare("SELECT cover FROM modules WHERE id = ?");
    $stmt->bind_param('i', $module_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Module not found'];
    }

    $row = $result->fetch_assoc();
    $filePath = $row['cover'];

    // Delete the file from filesystem if it exists
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM modules WHERE id = ?");
    $stmt->bind_param('i', $module_id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Module deleted successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to delete module from database'];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id = isset($_POST['module_id']) ? (int)$_POST['module_id'] : 0;

    if ($module_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid module ID']);
        exit;
    }

    $result = deleteModule($module_id);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
