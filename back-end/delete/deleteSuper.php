<?php
// This file handles AJAX delete requests only
// Save this as: back-end/delete/deleteAccount.php

// Prevent any output before JSON
ob_start();

// Start session
session_start();

// Include database connection
require_once __DIR__ . '/../../config/connection.php';

// Clear any accidental output
ob_clean();

// Set JSON header
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if delete_id is set
if (!isset($_POST['delete_id'])) {
    echo json_encode(['success' => false, 'message' => 'No account ID provided']);
    exit;
}

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$id = $_POST['delete_id'];

// Validate ID
if (!is_numeric($id) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid account ID']);
    exit;
}

try {
    // Check if the account exists and is teacher or librarian
    $checkUserStmt = $conn->prepare("SELECT user_type, username FROM users WHERE id = ? AND (user_type = 'teacher' OR user_type = 'librarian')");
    
    if (!$checkUserStmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $checkUserStmt->bind_param("i", $id);
    $checkUserStmt->execute();
    $userResult = $checkUserStmt->get_result();
    
    if ($userResult->num_rows == 0) {
        $checkUserStmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Account not found or not authorized for deletion']);
        exit;
    }
    
    $userRow = $userResult->fetch_assoc();
    $userType = $userRow['user_type'];
    $username = $userRow['username'];
    $checkUserStmt->close();
    
    $bookCount = 0;
    $moduleCount = 0;
    
    // Check for books table and uploaded_by column
    $tablesResult = $conn->query("SHOW TABLES LIKE 'books'");
    if ($tablesResult && $tablesResult->num_rows > 0) {
        $columnsResult = $conn->query("SHOW COLUMNS FROM books LIKE 'uploaded_by'");
        if ($columnsResult && $columnsResult->num_rows > 0) {
            $checkBooksStmt = $conn->prepare("SELECT COUNT(*) as count FROM books WHERE uploaded_by = ?");
            if ($checkBooksStmt) {
                $checkBooksStmt->bind_param("i", $id);
                $checkBooksStmt->execute();
                $booksResult = $checkBooksStmt->get_result();
                $booksRow = $booksResult->fetch_assoc();
                $bookCount = $booksRow['count'];
                $checkBooksStmt->close();
            }
        }
    }
    
    // Check for modules table and uploaded_by column
    $tablesResult = $conn->query("SHOW TABLES LIKE 'modules'");
    if ($tablesResult && $tablesResult->num_rows > 0) {
        $columnsResult = $conn->query("SHOW COLUMNS FROM modules LIKE 'uploaded_by'");
        if ($columnsResult && $columnsResult->num_rows > 0) {
            $checkModulesStmt = $conn->prepare("SELECT COUNT(*) as count FROM modules WHERE uploaded_by = ?");
            if ($checkModulesStmt) {
                $checkModulesStmt->bind_param("i", $id);
                $checkModulesStmt->execute();
                $modulesResult = $checkModulesStmt->get_result();
                $modulesRow = $modulesResult->fetch_assoc();
                $moduleCount = $modulesRow['count'];
                $checkModulesStmt->close();
            }
        }
    }
    
    // If there are dependencies, prevent deletion
    if ($bookCount > 0 || $moduleCount > 0) {
        $message = 'Cannot delete ' . $userType . ' account (' . $username . '). They have ';
        $dependencies = [];
        
        if ($bookCount > 0) {
            $dependencies[] = $bookCount . ' uploaded book' . ($bookCount > 1 ? 's' : '');
        }
        if ($moduleCount > 0) {
            $dependencies[] = $moduleCount . ' uploaded module' . ($moduleCount > 1 ? 's' : '');
        }
        
        $message .= implode(' and ', $dependencies);
        $message .= '. Please remove or reassign these uploads first.';
        
        $conn->close();
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
    
    // Delete the user account
    $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ? AND (user_type = 'teacher' OR user_type = 'librarian')");
    
    if (!$deleteStmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $deleteStmt->bind_param("i", $id);
    
    if ($deleteStmt->execute()) {
        if ($deleteStmt->affected_rows > 0) {
            $deleteStmt->close();
            $conn->close();
            echo json_encode([
                'success' => true, 
                'message' => ucfirst($userType) . ' account (' . $username . ') deleted successfully!'
            ]);
            exit;
        } else {
            $deleteStmt->close();
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'Account not found or already deleted']);
            exit;
        }
    } else {
        $error = $deleteStmt->error;
        $deleteStmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Failed to delete account: ' . $error]);
        exit;
    }
    
} catch (Exception $e) {
    error_log("Delete Account Error: " . $e->getMessage());
    
    if (isset($conn) && $conn) {
        $conn->close();
    }
    
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit;
}
?>