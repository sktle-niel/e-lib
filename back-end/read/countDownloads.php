<?php
include '../../config/connection.php';

function getDownloadsCount($user_id) {
    global $conn;

    // Count from downloaded_books table for the user
    $sql_books = "SELECT COUNT(*) as count FROM downloaded_books WHERE user_id = ?";
    $stmt_books = $conn->prepare($sql_books);
    $stmt_books->bind_param("i", $user_id);
    $stmt_books->execute();
    $result_books = $stmt_books->get_result();
    $books_count = 0;
    if ($result_books) {
        $row_books = $result_books->fetch_assoc();
        $books_count = $row_books['count'];
    }
    $stmt_books->close();

    // Count from downloaded_modules table for the user
    $sql_modules = "SELECT COUNT(*) as count FROM downloaded_modules WHERE user_id = ?";
    $stmt_modules = $conn->prepare($sql_modules);
    $stmt_modules->bind_param("i", $user_id);
    $stmt_modules->execute();
    $result_modules = $stmt_modules->get_result();
    $modules_count = 0;
    if ($result_modules) {
        $row_modules = $result_modules->fetch_assoc();
        $modules_count = $row_modules['count'];
    }
    $stmt_modules->close();

    // Return combined count
    return $books_count + $modules_count;
}
?>
