<?php
include 'config/connection.php';
include 'back-end/read/returnedBookHistory.php';

$year = 2026;
$month = null;

echo "Testing year filter for 2026:\n";
$books = getReturnedBooksHistory(10, 0, $month, $year);
echo "Number of books returned: " . count($books) . "\n";

if (!empty($books)) {
    foreach ($books as $book) {
        echo "Book: " . $book['book_title'] . " - Return Date: " . $book['actual_return_date'] . "\n";
    }
} else {
    echo "No books found for year 2026.\n";
}

$count = getReturnedBooksHistoryCount($month, $year);
echo "Total count: " . $count . "\n";

// Also check raw query
$query = "SELECT COUNT(*) as total FROM book_return_history WHERE YEAR(actual_return_date) = 2026";
$result = $conn->query($query);
$row = $result->fetch_assoc();
echo "Raw count for 2026: " . $row['total'] . "\n";
?>
