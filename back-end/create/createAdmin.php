<?php
include '../../config/connection.php';

function createAdmin($username, $password, $firstname, $lastname) {
    global $conn;

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate random 7-digit ID
    $id = rand(1000000, 9999999);

    // Prepare the SQL statement for inserting into users table
    $stmt = $conn->prepare("INSERT INTO users (id, firstname, lastname, username, password, user_type) VALUES (?, ?, ?, ?, ?, 'admin')");
    $stmt->bind_param("issss", $id, $firstname, $lastname, $username, $hashedPassword);

    try {
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Admin account created successfully!'];
        } else {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Failed to create admin account.'];
        }
    } catch (mysqli_sql_exception $e) {
        $stmt->close();
        $conn->close();
        if ($e->getCode() == 1062) {
            return ['success' => false, 'message' => 'Username already exists. Please choose a different username.'];
        } else {
            return ['success' => false, 'message' => 'An error occurred while creating the admin account.'];
        }
    }
}
?>
