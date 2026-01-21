<?php
include 'config/connection.php';

echo "Current date in MySQL: ";
$result = $conn->query("SELECT CURDATE() as today");
$row = $result->fetch_assoc();
echo $row['today'] . "\n";

echo "\nTest calculations:\n";
$test_dates = ['2024-01-15', '2024-01-20'];

foreach ($test_dates as $date) {
    $result = $conn->query("SELECT DATEDIFF(CURDATE(), '$date') as days_overdue");
    $row = $result->fetch_assoc();
    echo "From $date to today: " . $row['days_overdue'] . " days\n";
}

echo "\nActual overdue books:\n";
$result = $conn->query("
    SELECT
        b.expected_return_date,
        DATEDIFF(CURDATE(), b.expected_return_date) as days_overdue
    FROM borrowed_lib_books b
    WHERE b.expected_return_date < CURDATE() AND b.status != 'Returned'
");
while ($row = $result->fetch_assoc()) {
    echo "Return date: " . $row['expected_return_date'] . " - Days overdue: " . $row['days_overdue'] . "\n";
}
?>
