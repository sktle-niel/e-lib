<?php
include '../../auth/sessionCheck.php';
include '../../config/connection.php';

function recordBookPreview($user_id, $book_id) {
    global $conn;
    
    // Check if the record already exists
    $check_sql = "SELECT id FROM preview_books WHERE user_id = ? AND book_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $book_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update the existing record
        $update_sql = "UPDATE preview_books SET previewed_at = NOW() WHERE user_id = ? AND book_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $user_id, $book_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Generate unique random ID
        do {
            $random_id = rand(100000, 999999);
            $stmt = $conn->prepare("SELECT id FROM preview_books WHERE id = ?");
            $stmt->bind_param("i", $random_id);
            $stmt->execute();
            $check_result = $stmt->get_result();
            $exists = $check_result->num_rows > 0;
            $stmt->close();
        } while ($exists);
        
        // Insert new record
        $insert_sql = "INSERT INTO preview_books (id, user_id, book_id, previewed_at) VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iii", $random_id, $user_id, $book_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    
    $check_stmt->close();
    return true;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $book_id = isset($input['book_id']) ? (int)$input['book_id'] : null;
    
    if ($book_id && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $success = recordBookPreview($user_id, $book_id);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
    }
    exit;
}
?>