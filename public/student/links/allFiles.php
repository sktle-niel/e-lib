<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
$currentPage = 'All Modules and Books';

include '../../back-end/read/studentBooks.php';
include '../../back-end/read/studentModules.php';

// Fetch all books and modules
$allBooks = getAllBooks('', '', '', '', null, 0); // Get all books
$allModules = getAllModules('', '', '', PHP_INT_MAX, 0); // Get all modules

// Combine books and modules into one array
$recentItems = [];

// Add books
foreach ($allBooks as $book) {
    $recentItems[] = [
        'id' => $book['id'],
        'title' => $book['title'],
        'author' => $book['author'],
        'course' => $book['course'],
        'cover' => $book['cover'],
        'date' => $book['publish_date'],
        'type' => 'book'
    ];
}

// Add modules
foreach ($allModules as $module) {
    $recentItems[] = [
        'id' => $module['id'],
        'title' => $module['title'],
        'author' => $module['course'], // Use course as author for modules
        'course' => $module['course'],
        'cover' => $module['cover'],
        'date' => $module['uploadedDate'],
        'type' => 'module'
    ];
}

// Sort by date descending (recent first)
usort($recentItems, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Filter items based on search query, course, year, and type
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$yearFilter = isset($_GET['year']) ? (int)$_GET['year'] : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$filteredItems = $recentItems;

if ($searchQuery || $courseFilter || $yearFilter || $typeFilter) {
    $filteredItems = array_filter($recentItems, function($item) use ($searchQuery, $courseFilter, $yearFilter, $typeFilter) {
        $matchesSearch = !$searchQuery || strpos(strtolower($item['title']), $searchQuery) !== false || strpos(strtolower($item['author']), $searchQuery) !== false;
        $matchesCourse = !$courseFilter || $item['course'] === $courseFilter;
        $matchesYear = !$yearFilter || date('Y', strtotime($item['date'])) == $yearFilter;
        $matchesType = !$typeFilter || $item['type'] === $typeFilter;
        return $matchesSearch && $matchesCourse && $matchesYear && $matchesType;
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

<link rel="stylesheet" href="../../src/css/dashboard.css">

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
            <input type="hidden" name="page" value="recent">
            <input type="text" name="search" class="form-control me-2" style="max-width: 300px;" placeholder="Search recent views by title or author..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <select name="course" class="form-select me-2" style="max-width: 150px;">
                <option value="">All Courses</option>
                <option value="BSIT" <?php echo $courseFilter === 'BSIT' ? 'selected' : ''; ?>>BSIT</option>
                <option value="BSIS" <?php echo $courseFilter === 'BSIS' ? 'selected' : ''; ?>>BSIS</option>
                <option value="ACT" <?php echo $courseFilter === 'ACT' ? 'selected' : ''; ?>>ACT</option>
                <option value="SHS" <?php echo $courseFilter === 'SHS' ? 'selected' : ''; ?>>SHS</option>
                <option value="BSHM" <?php echo $courseFilter === 'BSHM' ? 'selected' : ''; ?>>BSHM</option>
                <option value="BSOA" <?php echo $courseFilter === 'BSOA' ? 'selected' : ''; ?>>BSOA</option>
            </select>
            <select name="year" class="form-select me-2" style="max-width: 120px;">
                <option value="">All Years</option>
                <?php for ($y = 2000; $y <= 2026; $y++): ?>
                    <option value="<?php echo $y; ?>" <?php echo $yearFilter === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <select name="type" class="form-select me-2" style="max-width: 120px;">
                <option value="">All Types</option>
                <option value="book" <?php echo isset($_GET['type']) && $_GET['type'] === 'book' ? 'selected' : ''; ?>>Books</option>
                <option value="module" <?php echo isset($_GET['type']) && $_GET['type'] === 'module' ? 'selected' : ''; ?>>Modules</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
            <?php if ($searchQuery || $courseFilter || $yearFilter || $typeFilter): ?>
                <a href="?page=recent" class="btn btn-outline-secondary ms-2">Clear</a>
            <?php endif; ?>
        </form>
    </div>

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
                    <p class="card-text text-muted small mb-1"><?php echo $item['author']; ?> | <?php echo date('M d, Y', strtotime($item['date'])); ?></p>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Preview" onclick="window.open('../../back-end/preview/preview<?php echo ucfirst($item['type']); ?>s.php?id=<?php echo $item['id']; ?>', '_blank')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="../../back-end/download/download<?php echo ucfirst($item['type']); ?>s.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-success" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
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
            <i class="bi bi-arrow-down-circle me-2"></i>Load More Recent Views
        </button>
    </div>

    <!-- No more books message -->
    <div id="no-more" class="text-center mt-4" style="display: none;">
        <p class="text-muted">No more recent views to load.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let hasMore = <?php echo $hasMore ? 'true' : 'false'; ?>;
    let searchQuery = '<?php echo addslashes($searchQuery); ?>';
    let courseFilter = '<?php echo addslashes($courseFilter); ?>';
    let yearFilter = '<?php echo $yearFilter; ?>';
    let typeFilter = '<?php echo addslashes($typeFilter); ?>';

    function loadMoreItems() {
        if (!hasMore) return;

        document.getElementById('loading').style.display = 'block';

        currentPage++;
        const url = `?page=recent&ajax=1&page=${currentPage}&search=${encodeURIComponent(searchQuery)}&course=${encodeURIComponent(courseFilter)}&year=${yearFilter}`;

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
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-${item.type === 'book' ? 'primary' : 'success'} text-white">${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</span>
                                </div>
                                <h6 class="card-title fw-bold mb-1">${item.title}</h6>
                                <p class="card-text text-muted small mb-1">${item.author} | ${new Date(item.date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Preview" onclick="window.open('../../back-end/preview/preview${item.type.charAt(0).toUpperCase() + item.type.slice(1)}s.php?id=${item.id}', '_blank')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="../../back-end/download/download${item.type.charAt(0).toUpperCase() + item.type.slice(1)}s.php?id=${item.id}" class="btn btn-sm btn-outline-success" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
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
                console.error('Error loading more recent views:', error);
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
});
</script>
