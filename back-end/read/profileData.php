<?php
$currentPage = 'Profile';

include '../../config/connection.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, profile_picture, firstname, lastname, username, password, program, user_type, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $profile_picture = $user['profile_picture'];
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
    $username = $user['username'];
    $program = $user['program'];
    $user_type = $user['user_type'];
    $created_at = $user['created_at'];
} else {
    // Handle error if user not found, but assuming logged in
    $profile_picture = '';
    $firstname = '';
    $lastname = '';
    $username = '';
    $program = '';
    $user_type = '';
    $created_at = '';
}
$stmt->close();
$conn->close();
?>
