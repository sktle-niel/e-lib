<?php
include '../config/connection.php';

function recordModuleDownload($user_id, $module_id) {
    global $conn;
    do {
        $random_id = rand(100000, 999999);
        $stmt = $conn->prepare("SELECT id FROM downloaded_modules WHERE id = ?");
        $stmt->bind_param("i", $random_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
    } while ($exists);

    $stmt = $conn->prepare("INSERT INTO downloaded_modules (id, user_id, module_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $random_id, $user_id, $module_id);
    $stmt->execute();
    $stmt->close();
}
?>
