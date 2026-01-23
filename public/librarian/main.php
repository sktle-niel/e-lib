<?php
include '../../auth/sessionCheck.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro - Librarian Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <?php
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
    include 'components/sidebar.php';

    define('MAIN_PAGE', true);
    switch ($currentPage) {
        case 'dashboard':
            include 'links/dashboard.php';
            break;
        case 'add_book':
            include 'links/addBook.php';
            break;
        case 'book_list':
            include 'links/bookList.php';
            break;
        case 'borrowed_list':
            include 'links/borrowedList.php';
            break;
        case 'history':
            include 'links/history.php';
            break;
        case 'penalties':
            include 'links/penaltiesList.php';
            break;
        case 'cleared_penalties':
            include 'links/clearedPenalties.php';
            break;
        case 'profile':
            include 'links/profile.php';
            break;
        default:
            include 'links/dashboard.php';
            break;
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>