<?php
include '../../config/connection.php';

function getPendingAccounts($user_type = null) {
    global $conn;

    $sql = "SELECT id, username, user_type, firstname, lastname, created_at FROM pending_accounts";
    if ($user_type) {
        $sql .= " WHERE user_type = ?";
    }
    $sql .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    if ($user_type) {
        $stmt->bind_param("s", $user_type);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $accounts = [];
    while ($row = $result->fetch_assoc()) {
        $accounts[] = $row;
    }

    $stmt->close();
    return $accounts;
}

function getPendingTeachers() {
    return getPendingAccounts('teacher');
}

function getPendingLibrarians() {
    return getPendingAccounts('librarian');
}

function getAllPendingAccounts() {
    return getPendingAccounts();
}

function getApprovedAccounts($user_type = null) {
    global $conn;

    $sql = "SELECT id, profile_picture, firstname, lastname, username, password, program, lrn_number, user_type, created_at FROM users WHERE user_type IN ('teacher', 'librarian')";
    if ($user_type) {
        $sql .= " AND user_type = ?";
    }
    $sql .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    if ($user_type) {
        $stmt->bind_param("s", $user_type);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $accounts = [];
    while ($row = $result->fetch_assoc()) {
        $accounts[] = $row;
    }

    $stmt->close();
    return $accounts;
}

function getApprovedTeachers() {
    return getApprovedAccounts('teacher');
}

function getApprovedLibrarians() {
    return getApprovedAccounts('librarian');
}

function getAllApprovedAccounts() {
    return getApprovedAccounts();
}
?>
