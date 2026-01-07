<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/profileData.php';

$stats = [
    ['title' => 'Available Books', 'value' => '1200', 'subtitle' => 'Books available for download', 'icon' => 'bi-check-circle', 'iconClass' => 'icon-blue'],
    ['title' => 'Your Downloads', 'value' => '15420', 'subtitle' => 'Books downloaded since launch', 'icon' => 'bi-download', 'iconClass' => 'icon-green'],
    ['title' => 'New Additions', 'value' => '45', 'subtitle' => 'New books added this week', 'icon' => 'bi-plus-circle', 'iconClass' => 'icon-red'],
    ['title' => 'Your Profile', 'value' => htmlspecialchars($username), 'subtitle' => 'Books downloaded this month', 'icon' => 'bi-person', 'iconClass' => 'icon-orange']
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

<link rel="stylesheet" href="../../src/css/dashboard.css">

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
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