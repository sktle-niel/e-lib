<?php
include '../../auth/sessionCheck.php';
include '../../config/connection.php';

function recordModulePreview($user_id, $module_id) {
    global $conn;
    
    // Generate unique random ID
    do {
        $random_id = rand(100000, 999999);
        $stmt = $conn->prepare("SELECT id FROM preview_modules WHERE id = ?");
        $stmt->bind_param("i", $random_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
    } while ($exists);
    
    // Insert preview record (previewed_at will be set automatically)
    $stmt = $conn->prepare("INSERT INTO preview_modules (id, user_id, module_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $random_id, $user_id, $module_id);
    $stmt->execute();
    $stmt->close();
}

// Optional: Function to get recent previews with timestamps
function getRecentPreviews($user_id, $limit = 10) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT pm.id, pm.user_id, pm.module_id, pm.previewed_at,
               m.title, m.course, m.cover
        FROM preview_modules pm
        JOIN modules m ON pm.module_id = m.id
        WHERE pm.user_id = ?
        ORDER BY pm.previewed_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $previews = [];
    while ($row = $result->fetch_assoc()) {
        $previews[] = $row;
    }
    
    $stmt->close();
    return $previews;
}

// Handle POST request to record preview
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $module_id = (int)$data['module_id'];
    
    recordModulePreview($_SESSION['user_id'], $module_id);
    
    echo json_encode(['success' => true]);
    exit;
}

// Handle GET request to retrieve recent previews
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_recent'])) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $previews = getRecentPreviews($_SESSION['user_id'], $limit);
    
    header('Content-Type: application/json');
    echo json_encode($previews);
    exit;
}
?>