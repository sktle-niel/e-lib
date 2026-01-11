<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
$currentPage = 'Your Downloads';

include '../../back-end/read/fetchDownloadedBooks.php';
include '../../back-end/read/fetchDownloadedModules.php';
include '../../back-end/delete/removeDownloads.php';

// Get user ID from session
$userId = $_SESSION['user_id'];

// Fetch downloaded books and modules
$downloadedBooks = getDownloadedBooks($userId);
$downloadedModules = getDownloadedModules($userId);

// Combine books and modules into one array
$downloadedItems = array_merge($downloadedBooks, $downloadedModules);

// Sort by download date descending (recent first)
usort($downloadedItems, function($a, $b) {
    return strtotime($b['downloadDate']) - strtotime($a['downloadDate']);
});

// Filter items based on search query and type
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$filteredItems = $downloadedItems;

if ($searchQuery || $typeFilter) {
    $filteredItems = array_filter($downloadedItems, function($item) use ($searchQuery, $typeFilter) {
        $matchesSearch = !$searchQuery || strpos(strtolower($item['title']), $searchQuery) !== false || strpos(strtolower($item['author']), $searchQuery) !== false;
        $matchesType = !$typeFilter || $item['type'] === $typeFilter;
        return $matchesSearch && $matchesType;
    });
}

// Handle AJAX requests for pagination
if (isset($_GET['ajax'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 12; // 3 rows * 4 cards
    $offset = ($page - 1) * $perPage;
    $itemsToShow = array_slice($filteredItems, $offset, $perPage);
    echo json_encode($itemsToShow);
    exit;
}

// For initial load, show first 12 items
$initialItems = array_slice($filteredItems, 0, 12);
$totalItems = count($filteredItems);
$hasMore = $totalItems > 12;
?>

<link rel="stylesheet" href="../../src/css/phoneMediaQuery.css">

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb" class="breadcrumb-custom">
            </nav>
            <h1 class="page-title"><?php echo $currentPage; ?></h1>
        </div>
    </div>

    <!-- Search Form -->
    <div class="mb-4">
        <form method="GET" action="" class="d-flex">
            <input type="hidden" name="page" value="downloads">
            <input type="text" name="search" class="form-control me-2" style="max-width: 300px;" placeholder="Search downloads by title or author..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <select name="type" class="form-select me-2" style="max-width: 150px;">
                <option value="">All Types</option>
                <option value="book" <?php echo $typeFilter === 'book' ? 'selected' : ''; ?>>Books</option>
                <option value="module" <?php echo $typeFilter === 'module' ? 'selected' : ''; ?>>Modules</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
            <?php if ($searchQuery || $typeFilter): ?>
                <a href="?page=downloads" class="btn btn-outline-secondary ms-2">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Downloaded Items Grid -->
    <div id="items-container" class="row g-3">
        <?php foreach($initialItems as $item): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 border-0 shadow-sm">
                <img src="<?php echo $item['cover']; ?>" class="card-img-top" alt="<?php echo $item['title']; ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-<?php echo $item['type'] === 'book' ? 'primary' : 'success'; ?> text-white"><?php echo ucfirst($item['type']); ?></span>
                    </div>
                    <h6 class="card-title fw-bold mb-1"><?php echo $item['title']; ?></h6>
                    <p class="card-text text-muted small mb-1"><?php echo $item['author']; ?></p>
                    <p class="card-text text-muted small mb-2">Downloaded: <?php echo date('M d, Y', strtotime($item['downloadDate'])); ?></p>
                    <div class="d-flex justify-content-end">
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-1 preview-btn" title="Preview" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['type']; ?>">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success me-1 download-btn" title="Download" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['type']; ?>">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger me-1 delete-btn" title="Delete" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['type']; ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Loading indicator -->
    <div id="loading" class="text-center mt-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Load More Button -->
    <div id="load-more-container" class="text-center mt-4" style="display: <?php echo $hasMore ? 'block' : 'none'; ?>;">
        <button id="load-more-btn" class="btn btn-primary">
            <i class="bi bi-arrow-down-circle me-2"></i>Load More Downloads
        </button>
    </div>

    <!-- No more books message -->
    <div id="no-more" class="text-center mt-4" style="display: none;">
        <p class="text-muted">No more downloads to load.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let hasMore = <?php echo $hasMore ? 'true' : 'false'; ?>;
    let searchQuery = '<?php echo addslashes($searchQuery); ?>';
    let typeFilter = '<?php echo addslashes($typeFilter); ?>';

    function loadMoreItems() {
        if (!hasMore) return;

        document.getElementById('loading').style.display = 'block';

        currentPage++;
        const url = `?page=downloads&ajax=1&page=${currentPage}&search=${encodeURIComponent(searchQuery)}&type=${encodeURIComponent(typeFilter)}`;

        fetch(url)
            .then(response => response.json())
            .then(items => {
                document.getElementById('loading').style.display = 'none';

                if (items.length === 0) {
                    hasMore = false;
                    document.getElementById('no-more').style.display = 'block';
                    return;
                }

                const container = document.getElementById('items-container');
                items.forEach(item => {
                    const col = document.createElement('div');
                    col.className = 'col-lg-3 col-md-4 col-sm-6';
                    col.innerHTML = `
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="${item.cover}" class="card-img-top" alt="${item.title}" style="height: 200px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold mb-1">${item.title}</h6>
                                <p class="card-text text-muted small mb-1">${item.author}</p>
                                <p class="card-text text-muted small mb-2">Downloaded: ${new Date(item.downloadDate).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-1 preview-btn" title="Preview" data-id="${item.id}" data-type="${item.type}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success me-1 download-btn" title="Download" data-id="${item.id}" data-type="${item.type}">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger me-1 delete-btn" title="Delete" data-id="${item.id}" data-type="${item.type}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });

                // Check if there are more items
                if (items.length < 12) {
                    hasMore = false;
                    document.getElementById('load-more-container').style.display = 'none';
                    document.getElementById('no-more').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading more downloads:', error);
                document.getElementById('loading').style.display = 'none';
            });
    }

    // Add event listener to Load More button
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            console.log('Load More button clicked');
            loadMoreItems();
        });
    } else {
        console.log('Load More button not found');
    }

    // Add event listeners to preview buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.preview-btn')) {
            const btn = e.target.closest('.preview-btn');
            const id = btn.getAttribute('data-id');
            const type = btn.getAttribute('data-type');
            let url = '';
            if (type === 'book') {
                url = `../../back-end/preview/previewBooks.php?id=${id}`;
            } else if (type === 'module') {
                url = `../../back-end/preview/previewModules.php?id=${id}`;
            }
            if (url) {
                window.open(url, '_blank');
            }
        }
    });

    // Add event listeners to download buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.download-btn')) {
            const btn = e.target.closest('.download-btn');
            const id = btn.getAttribute('data-id');
            const type = btn.getAttribute('data-type');
            let url = '';
            if (type === 'book') {
                url = `../../back-end/download/downloadBooks.php?id=${id}`;
            } else if (type === 'module') {
                url = `../../back-end/download/downloadModules.php?id=${id}`;
            }
            if (url) {
                window.location.href = url;
            }
        }
    });

    // Add event listeners to delete buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.getAttribute('data-id');
            const type = btn.getAttribute('data-type');

            if (confirm('Are you sure you want to delete this download?')) {
                fetch('../../back-end/delete/removeDownloads.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `item_id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the card from the DOM
                        const card = btn.closest('.col-lg-3, .col-md-4, .col-sm-6');
                        if (card) {
                            card.remove();
                        }
                        alert('Download removed successfully');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the download');
                });
            }
        }
    });
});
</script>
