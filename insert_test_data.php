<?php
include 'config/connection.php';

try {
    // Insert test overdue books
    $stmt = $conn->prepare('INSERT INTO borrowed_lib_books (user_id, book_id, borrow_date, expected_return_date, status) VALUES (?, ?, ?, ?, ?)');

    // First test book - overdue
    $user_id = 1457203; // Niel Penlas
    $book_id = 1544353; // awd
    $borrow_date = '2024-01-01';
    $return_date = '2024-01-15'; // Past date
    $status = 'Borrowed';
    $stmt->bind_param('iisss', $user_id, $book_id, $borrow_date, $return_date, $status);
    $stmt->execute();

    // Second test book - overdue
    $user_id = 2211586; // Sample Sample
    $book_id = 3668070; // awd
    $borrow_date = '2024-01-05';
    $return_date = '2024-01-20'; // Past date
    $status = 'Borrowed';
    $stmt->bind_param('iisss', $user_id, $book_id, $borrow_date, $return_date, $status);
    $stmt->execute();

    echo "Test data inserted successfully!\n";
    echo "Now you can test the penalties page to see borrower names.\n";

} catch (Exception $e) {
    echo "Error inserting test data: " . $e->getMessage() . "\n";
}
?>
