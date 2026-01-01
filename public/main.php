<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --dark-gradient: linear-gradient(135deg, #0e8074 0%, #2dd468 100%);
            --success: #28a745;
            --text-dark: #333;
            --text-light: #fff;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
        }

        .sidebar {
            width: 260px;
            background: var(--primary-gradient);
            color: var(--text-light);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .logo {
            padding: 24px;
            font-size: 28px;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-item {
            padding: 14px 24px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            cursor: pointer;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            color: var(--text-light);
            border-left-color: var(--text-light);
        }

        .nav-item.active {
            background: rgba(255,255,255,0.15);
            color: var(--text-light);
            border-left-color: var(--text-light);
            font-weight: 600;
        }

        .admin-section {
            position: absolute;
            bottom: 20px;
            left: 24px;
            right: 24px;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            margin-bottom: 12px;
        }

        .admin-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #11998e;
        }

        .logout-btn {
            width: 100%;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: var(--text-light);
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
            color: var(--text-light);
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .icon-green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: var(--text-light);
        }

        .icon-red {
            background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
            color: var(--text-light);
        }

        .icon-orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: var(--text-light);
        }

        .icon-blue {
            background: var(--success);
            color: var(--text-light);
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .tab-custom {
            border-bottom: 2px solid #dee2e6;
        }

        .nav-link-custom {
            color: #6c757d;
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
        }

        .nav-link-custom.active {
            color: #11998e;
            border-bottom-color: #11998e;
            font-weight: 600;
        }

        .notification-item, .activity-item {
            padding: 16px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .notification-item:last-child, .activity-item:last-child {
            border-bottom: none;
        }

        .donut-chart {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: conic-gradient(
                #28a745 0deg 160deg,
                #11998e 160deg 280deg,
                #dc3545 280deg 320deg,
                #ffc107 320deg 360deg
            );
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .donut-inner {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .order-item {
            padding: 16px;
            border-left: 4px solid;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .activity-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .breadcrumb-custom {
            background: none;
            padding: 0;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .page-title {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 24px;
        }

        .select-custom {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 8px 16px;
        }
    </style>
</head>
<body>
    <?php
    $currentPage = 'Dashboard';

    $stats = [
        ['title' => 'Available Books', 'value' => '1200', 'subtitle' => 'Books available for download', 'icon' => 'bi-check-circle', 'iconClass' => 'icon-blue'],
        ['title' => 'Your Downloads', 'value' => '15420', 'subtitle' => 'Books downloaded since launch', 'icon' => 'bi-download', 'iconClass' => 'icon-green'],
        ['title' => 'New Additions', 'value' => '45', 'subtitle' => 'New books added this week', 'icon' => 'bi-plus-circle', 'iconClass' => 'icon-red'],
        ['title' => 'Your Profile', 'value' => '1250', 'subtitle' => 'Books downloaded this month', 'icon' => 'bi-person', 'iconClass' => 'icon-orange']
    ];

    $recentBooks = [
        ['title' => 'Introduction to Algorithms', 'author' => 'Cormen et al.', 'cover' => 'https://via.placeholder.com/150x200/11998e/ffffff?text=Algo', 'available' => true],
        ['title' => 'Computer Networks', 'author' => 'Andrew Tanenbaum', 'cover' => 'https://via.placeholder.com/150x200/38ef7d/000000?text=Networks', 'available' => true],
        ['title' => 'Database System Concepts', 'author' => 'Silberschatz et al.', 'cover' => 'https://via.placeholder.com/150x200/f093fb/000000?text=DB', 'available' => false],
        ['title' => 'Operating Systems', 'author' => 'William Stallings', 'cover' => 'https://via.placeholder.com/150x200/f5576c/ffffff?text=OS', 'available' => true]
    ];

    $borrowedBooks = [
        ['title' => 'Data Structures and Algorithms', 'dueDate' => '2024-01-15', 'status' => 'On Time'],
        ['title' => 'Software Engineering', 'dueDate' => '2024-01-10', 'status' => 'Due Soon']
    ];

    $downloads = [
        ['title' => 'Machine Learning Basics', 'downloadDate' => '2024-01-05', 'size' => '2.5 MB'],
        ['title' => 'Web Development Guide', 'downloadDate' => '2024-01-03', 'size' => '1.8 MB']
    ];
    ?>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="bi bi-book-fill"></i> Libro
        </div>
        <div class="mt-3">
            <div class="nav-item active">
                <i class="bi bi-speedometer2"></i> Dashboard
            </div>
            <div class="nav-item">
                <i class="bi bi-book"></i> Books
            </div>
            <div class="nav-item">
                <i class="bi bi-download"></i> Downloads
            </div>
            <div class="nav-item">
                <i class="bi bi-book-half"></i> Borrowed Books
            </div>
            <div class="nav-item">
                <i class="bi bi-person"></i> Profile
            </div>
        </div>
        
        <div class="admin-section">
            <div class="admin-profile">
                <div class="admin-avatar">S</div>
                <div>
                    <div class="fw-bold">STUDENT</div>
                    <small class="opacity-75">student@libro.com</small>
                </div>
            </div>
            <button class="btn logout-btn">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb" class="breadcrumb-custom">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item text-muted">Items</li>
                        <li class="breadcrumb-item active">Overview</li>
                    </ol>
                </nav>
                <h1 class="page-title"><?php echo $currentPage; ?></h1>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <?php foreach($stats as $stat): ?>
            <div class="col-md-6 col-xl-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="text-muted mb-2"><?php echo $stat['title']; ?></h6>
                                <h2 class="fw-bold mb-1"><?php echo $stat['value']; ?></h2>
                                <small class="text-muted"><?php echo $stat['subtitle']; ?></small>
                            </div>
                            <div class="stat-icon <?php echo $stat['iconClass']; ?>">
                                <i class="<?php echo $stat['icon']; ?>"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>



        <!-- Content Grid -->
        <div class="row g-4">
            <!-- Recent Books -->
            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title fw-bold mb-0">Recent Books</h5>
                        </div>
                        <div class="row g-3">
                            <?php foreach($recentBooks as $book): ?>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="<?php echo $book['cover']; ?>" class="card-img-top" alt="<?php echo $book['title']; ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-3">
                                        <h6 class="card-title fw-bold mb-1"><?php echo $book['title']; ?></h6>
                                        <p class="card-text text-muted small mb-2"><?php echo $book['author']; ?></p>
                                        <div class="d-flex justify-content-end">
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-1" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success me-1" title="Download" <?php echo !$book['available'] ? 'disabled' : ''; ?>>
                                                    <i class="bi bi-download"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Downloads -->
            <div class="col-lg-4">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">Recent Downloads</h5>
                        <?php foreach($downloads as $download): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1"><?php echo $download['title']; ?></h6>
                                <small class="text-muted"><?php echo $download['downloadDate']; ?> • <?php echo $download['size']; ?></small>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>