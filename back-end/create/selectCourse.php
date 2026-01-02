<?php
session_start();
include '../../config/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course'])) {
    $course = trim($_POST['course']);

    // Validate course
    $validCourses = ['BSIT', 'BSIS', 'ACT', 'SHS', 'BSHM', 'BSOA'];
    if (!in_array($course, $validCourses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid course selected']);
        exit;
    }

    // Update database
    $stmt = $conn->prepare("UPDATE users SET program = ? WHERE id = ?");
    $stmt->bind_param("si", $course, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Course updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>
