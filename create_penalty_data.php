<?php
include 'config/connection.php';

// Function to get existing books
function getExistingBooks($limit = 5) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, book_title FROM lib_books WHERE status = 'available' LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    $stmt->close();
    return $books;
}

// Function to get existing users
function getExistingUsers($limit = 5) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, firstname, lastname FROM users WHERE user_type = 'student' LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    return $users;
}

// Function to create penalty data
function createPenaltyData() {
    global $conn;

    $books = getExistingBooks(10);
    $users = getExistingUsers(10);

    if (empty($books) || empty($users)) {
        echo "Error: Need existing books and users to create penalty data.\n";
        return false;
    }

    $penaltyRecords = [
        ['days_overdue' => 5, 'book_index' => 0, 'user_index' => 0],
        ['days_overdue' => 8, 'book_index' => 1, 'user_index' => 1],
        ['days_overdue' => 12, 'book_index' => 2, 'user_index' => 2],
        ['days_overdue' => 15, 'book_index' => 3, 'user_index' => 0],
        ['days_overdue' => 20, 'book_index' => 4, 'user_index' => 1],
    ];

    $created = 0;
    foreach ($penaltyRecords as $record) {
        $bookIndex = min($record['book_index'], count($books) - 1);
        $userIndex = min($record['user_index'], count($users) - 1);

        $bookId = $books[$bookIndex]['id'];
        $userId = $users[$userIndex]['id'];
        $daysOverdue = $record['days_overdue'];

        // Calculate dates
        $borrowDate = date('Y-m-d', strtotime("-{$daysOverdue} days -5 days"));
        $expectedReturnDate = date('Y-m-d', strtotime("-{$daysOverdue} days"));

        // Generate unique borrow ID
        $borrowId = rand(1000000, 9999999);

        // Insert borrowed book record
        $stmt = $conn->prepare("INSERT INTO borrowed_lib_books (id, book_id, user_id, borrow_date, expected_return_date, status) VALUES (?, ?, ?, ?, ?, ?)");
        $status = "Borrowed";
        $stmt->bind_param("iiisss", $borrowId, $bookId, $userId, $borrowDate, $expectedReturnDate, $status);

        if ($stmt->execute()) {
            // Update book status to not available
            $updateStmt = $conn->prepare("UPDATE lib_books SET status = 'not available' WHERE id = ?");
            $updateStmt->bind_param("i", $bookId);
            $updateStmt->execute();
            $updateStmt->close();

            $penaltyAmount = $daysOverdue * 50;
            echo "Created penalty: {$books[$bookIndex]['book_title']} - {$users[$userIndex]['firstname']} {$users[$userIndex]['lastname']} - {$daysOverdue} days overdue - ₱{$penaltyAmount}\n";
            $created++;
        } else {
            echo "Failed to create penalty record for book ID {$bookId}\n";
        }
        $stmt->close();
    }

    return $created;
}

// Main execution
echo "Creating penalty data...\n\n";

$books = getExistingBooks();
$users = getExistingUsers();

echo "Found " . count($books) . " available books\n";
echo "Found " . count($users) . " users\n\n";

if (empty($books)) {
    echo "No available books found. Please add some books first.\n";
    exit;
}

if (empty($users)) {
    echo "No users found. Please add some users first.\n";
    exit;
}

$created = createPenaltyData();

if ($created > 0) {
    echo "\n✅ Successfully created {$created} penalty records!\n";
    echo "📊 Penalty calculation: ₱50 per day overdue\n";
    echo "🌐 Visit: http://localhost/e-library/public/librarian/links/penaltiesList.php\n";
} else {
    echo "\n❌ Failed to create penalty records.\n";
}
?>
