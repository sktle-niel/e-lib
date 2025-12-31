<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user_type = $_SESSION['user_type'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTCI E-Library Portal - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../src/css/global.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .search-bar {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../src/img/ptci/logo.png" alt="PTCI Logo" class="logo" style="height: 40px;">
                PTCI E-Library
            </a>
            <div class="d-flex">
                <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($username); ?> (<?php echo ucfirst($user_type); ?>)</span>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">E-Library Dashboard</h1>
                <div class="search-bar mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search library collection..." id="searchInput">
                        <button class="btn btn-primary" type="button" id="searchBtn">Search</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Books Borrowed</h5>
                        <p class="card-text display-4">5</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Reservations</h5>
                        <p class="card-text display-4">2</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Due Soon</h5>
                        <p class="card-text display-4">1</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Fines</h5>
                        <p class="card-text display-4">$0</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Books</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <img src="../src/img/ptci/book-placeholder.jpg" class="card-img-top" alt="Book Cover" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6 class="card-title">Book Title 1</h6>
                                        <p class="card-text">Author Name</p>
                                        <button class="btn btn-primary btn-sm">Borrow</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <img src="../src/img/ptci/book-placeholder.jpg" class="card-img-top" alt="Book Cover" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6 class="card-title">Book Title 2</h6>
                                        <p class="card-text">Author Name</p>
                                        <button class="btn btn-primary btn-sm">Borrow</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <img src="../src/img/ptci/book-placeholder.jpg" class="card-img-top" alt="Book Cover" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6 class="card-title">Book Title 3</h6>
                                        <p class="card-text">Author Name</p>
                                        <button class="btn btn-primary btn-sm">Borrow</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-outline-primary w-100 mb-2">Reserve a Book</button>
                        <button class="btn btn-outline-success w-100 mb-2">Renew Books</button>
                        <button class="btn btn-outline-info w-100 mb-2">View History</button>
                        <?php if ($user_type == 'teacher'): ?>
                        <button class="btn btn-outline-warning w-100 mb-2">Manage Books</button>
                        <?php endif; ?>
                        <button class="btn btn-outline-secondary w-100">Contact Librarian</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchBtn').addEventListener('click', function() {
            const query = document.getElementById('searchInput').value;
            if (query.trim() !== '') {
                alert('Searching for: ' + query);
                // Implement search functionality
            }
        });
    </script>
</body>
</html>
