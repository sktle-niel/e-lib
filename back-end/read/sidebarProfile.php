<?php
include '../../config/connection.php';
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profilePicture = $user['profile_picture'] ?? null;
$stmt->close();
?>
