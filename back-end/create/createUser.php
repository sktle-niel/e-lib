<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = $_POST['user_type'];

    if ($_POST['password'] !== $_POST['confirm_password']) {
        header("Location: ../../public/signup.php?error=password_mismatch");
        exit;
    }

    $id = rand(1000000, 9999999);
    $stmt = $conn->prepare("INSERT INTO users (id, firstname, lastname, username, password, user_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $id, $firstname, $lastname, $username, $password, $user_type);
    try {
        if ($stmt->execute()) {
            header("Location: ../../public/signup.php?success=1");
            exit;
        } else {
            header("Location: ../../public/signup.php?error=username_taken");
            exit;
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            header("Location: ../../public/signup.php?error=username_taken");
            exit;
        } else {
            header("Location: ../../public/signup.php?error=general");
            exit;
        }
    }
    $stmt->close();
}
$conn->close();
?>
