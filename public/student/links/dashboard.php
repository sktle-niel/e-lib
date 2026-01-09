<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/profileData.php';
include '../../back-end/preview/previewViewedBooks.php';
include '../../back-end/preview/previewViewedModules.php';
include '../../back-end/read/fetchDownloadedBooks.php';
include '../../back-end/read/fetchDownloadedModules.php';
include '../../back-end/recent/recentPreviewBooks.php';
include '../../back-end/recent/recentPreviewModules.php';
include '../../back-end/read/modulesCount.php';
include '../../back-end/read/BooksCount.php';
include '../../back-end/read/countDownloads.php';

// Get user ID from session
$userId = $_SESSION['user_id'];

$stats = [
    ['title' => 'Available Books', 'value' => getBooksCount(), 'subtitle' => 'Books available for download', 'icon' => 'bi-book', 'iconClass' => 'icon-green'],
    ['title' => 'Available Modules', 'value' => getModulesCount(), 'subtitle' => 'Total modules available', 'icon' => 'bi-file-earmark-text', 'iconClass' => 'icon-green'],
    ['title' => 'Your Downloads', 'value' => getDownloadsCount($userId), 'subtitle' => 'Books downloaded since launch', 'icon' => 'bi-download', 'iconClass' => 'icon-green'],
    ['title' => 'Your Profile', 'value' => htmlspecialchars($username), 'subtitle' => 'Books downloaded this month', 'icon' => 'bi-person', 'iconClass' => 'icon-green']
];

// Get recent viewed books and modules from database
$recentBooks = getRecentViewedBooks(3); // Get 3 books
$recentModules = getRecentViewedModules(3); // Get 3 modules

// Combine and remove duplicates, keeping only the latest preview for each unique item
$recentViewed = array_merge($recentBooks, $recentModules);
$uniqueViewed = [];
foreach ($recentViewed as $item) {
    $key = $item['type'] . '_' . $item['id'];
    if (!isset($uniqueViewed[$key]) || strtotime($item['previewed_at']) > strtotime($uniqueViewed[$key]['previewed_at'])) {
        $uniqueViewed[$key] = $item;
    }
}
$recentViewed = array_values($uniqueViewed);

// Sort by previewed_at timestamp descending
usort($recentViewed, function($a, $b) {
    return strtotime($b['previewed_at']) - strtotime($a['previewed_at']);
});

// Limit to 6 total items
$recentViewed = array_slice($recentViewed, 0, 6);

$borrowedBooks = [
    ['title' => 'Data Structures and Algorithms', 'dueDate' => '2024-01-15', 'status' => 'On Time'],
    ['title' => 'Software Engineering', 'dueDate' => '2024-01-10', 'status' => 'Due Soon']
];

// Fetch downloaded books and modules
$downloadedBooks = getDownloadedBooks($userId);
$downloadedModules = getDownloadedModules($userId);

// Combine books and modules into one array
$downloads = array_merge($downloadedBooks, $downloadedModules);

// Sort by download date descending (recent first)
usort($downloads, function($a, $b) {
    return strtotime($b['downloadDate']) - strtotime($a['downloadDate']);
});

// Limit to 6 total items
$downloads = array_slice($downloads, 0, 6);
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
        <!-- Recent Viewed -->
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Recent Viewed</h5>
                    </div>
                    
                    <?php if (empty($recentViewed)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-eye-slash" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No recent views yet. Start exploring books and modules!</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach($recentViewed as $item): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="<?php echo htmlspecialchars($item['cover']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-<?php echo $item['type'] === 'book' ? 'primary' : 'success'; ?> text-white"><?php echo ucfirst($item['type']); ?></span>
                                        </div>
                                        <h6 class="card-title fw-bold mb-1" title="<?php echo htmlspecialchars($item['title']); ?>">
                                            <?php echo strlen($item['title']) > 30 ? substr(htmlspecialchars($item['title']), 0, 30) . '...' : htmlspecialchars($item['title']); ?>
                                        </h6>
                                        <p class="card-text text-muted small mb-2">
                                            <?php echo htmlspecialchars($item['author']); ?>
                                        </p>
                                        <small class="text-muted d-block mb-2">
                                            <i class="bi bi-clock"></i> <?php echo date('M d, Y', strtotime($item['previewed_at'])); ?>
                                        </small>
                                        <div class="d-flex justify-content-end">
                                            <div>
                                                <?php if ($item['type'] === 'book'): ?>
                                                    <button class="btn btn-sm btn-outline-primary me-1 btn-preview-book" data-book-id="<?php echo $item['id']; ?>" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <a href="../../back-end/download/downloadBooks.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-success me-1" title="Download" <?php echo !$item['available'] ? 'style="pointer-events: none; opacity: 0.5;"' : ''; ?>>
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-primary me-1 btn-preview-module" data-module-id="<?php echo $item['id']; ?>" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <a href="../../back-end/download/downloadModules.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-success me-1" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Downloads -->
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Recent Downloads</h5>
                    <?php if (empty($downloads)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-download" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No downloads yet. Start downloading books and modules!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($downloads as $download): ?>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="badge bg-<?php echo $download['type'] === 'book' ? 'primary' : 'success'; ?> text-white"><?php echo ucfirst($download['type']); ?></span>
                                </div>
                                <h6 class="mb-1"><?php echo $download['title']; ?></h6>
                                <p class="card-text text-muted small mb-1"><?php echo $download['author']; ?></p>
                                <small class="text-muted">Downloaded: <?php echo date('M d, Y', strtotime($download['downloadDate'])); ?></small>
                            </div>
                            <div class="d-flex">
                                <?php if ($download['type'] === 'book'): ?>
                                    <a href="../../back-end/download/downloadBooks.php?id=<?php echo $download['id']; ?>" class="btn btn-sm btn-outline-success me-1" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="../../back-end/download/downloadModules.php?id=<?php echo $download['id']; ?>" class="btn btn-sm btn-outline-success me-1" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-primary preview-btn" title="Preview" data-id="<?php echo $download['id']; ?>" data-type="<?php echo $download['type']; ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle book preview buttons
    document.addEventListener('click', function(e) {
        const bookPreviewBtn = e.target.closest('.btn-preview-book');
        if (bookPreviewBtn) {
            const bookId = parseInt(bookPreviewBtn.dataset.bookId);
            recordBookPreview(bookId);
        }

        const modulePreviewBtn = e.target.closest('.btn-preview-module');
        if (modulePreviewBtn) {
            const moduleId = parseInt(modulePreviewBtn.dataset.moduleId);
            recordModulePreview(moduleId);
        }

        // Handle download preview buttons
        const previewBtn = e.target.closest('.preview-btn');
        if (previewBtn) {
            const id = previewBtn.dataset.id;
            const type = previewBtn.dataset.type;
            if (type === 'book') {
                window.open(`../../back-end/preview/previewBooks.php?id=${id}`, '_blank');
            } else {
                window.open(`../../back-end/preview/previewModules.php?id=${id}`, '_blank');
            }
        }
    });

    function recordBookPreview(book_id) {
        fetch('../../back-end/recent/recentPreviewBooks.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ book_id: book_id }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.open(`../../back-end/preview/previewBooks.php?id=${book_id}`, '_blank');
            }
        })
        .catch(error => {
            console.error('Error recording book preview:', error);
        });
    }

    function recordModulePreview(module_id) {
        fetch('../../back-end/recent/recentPreviewModules.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ module_id: module_id }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.open(`../../back-end/preview/previewModules.php?id=${module_id}`, '_blank');
            }
        })
        .catch(error => {
            console.error('Error recording module preview:', error);
        });
    }
});
</script>