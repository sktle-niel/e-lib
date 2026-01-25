<?php
function deleteSuperAccount($id) {
    // Remove session_start() since session is already started in sessionCheck.php
    include '../../config/connection.php';
    
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        return ['success' => false, 'message' => 'Unauthorized access'];
    }
    
    // Check if the account is teacher or librarian
    $checkUserStmt = $conn->prepare("SELECT user_type FROM users WHERE id = ? AND (user_type = 'teacher' OR user_type = 'librarian')");
    $checkUserStmt->bind_param("i", $id);
    $checkUserStmt->execute();
    $userResult = $checkUserStmt->get_result();
    
    if ($userResult->num_rows == 0) {
        $checkUserStmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Account not found or not authorized for deletion'];
    }
    
    $userRow = $userResult->fetch_assoc();
    $userType = $userRow['user_type'];
    $checkUserStmt->close();
    
    // Check for dependencies: uploaded books
    $checkBooksStmt = $conn->prepare("SELECT COUNT(*) as count FROM books WHERE uploaded_by = ?");
    $checkBooksStmt->bind_param("i", $id);
    $checkBooksStmt->execute();
    $booksResult = $checkBooksStmt->get_result();
    $booksRow = $booksResult->fetch_assoc();
    $checkBooksStmt->close();
    
    // Check for dependencies: uploaded modules
    $checkModulesStmt = $conn->prepare("SELECT COUNT(*) as count FROM modules WHERE uploaded_by = ?");
    $checkModulesStmt->bind_param("i", $id);
    $checkModulesStmt->execute();
    $modulesResult = $checkModulesStmt->get_result();
    $modulesRow = $modulesResult->fetch_assoc();
    $checkModulesStmt->close();
    
    if ($booksRow['count'] > 0 || $modulesRow['count'] > 0) {
        $message = 'Cannot delete ' . $userType . ' account. They have ';
        if ($booksRow['count'] > 0) $message .= $booksRow['count'] . ' uploaded book(s)';
        if ($booksRow['count'] > 0 && $modulesRow['count'] > 0) $message .= ' and ';
        if ($modulesRow['count'] > 0) $message .= $modulesRow['count'] . ' uploaded module(s)';
        $message .= '. Please remove uploads first.';
        $conn->close();
        return ['success' => false, 'message' => $message];
    }
    
    // Delete the user account
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND (user_type = 'teacher' OR user_type = 'librarian')");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => ucfirst($userType) . ' account deleted successfully!'];
        } else {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'No account found with that ID'];
        }
    } else {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Failed to delete account'];
    }
}
?>