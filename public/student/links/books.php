<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
$currentPage = 'Books';

include '../../back-end/read/readBooks.php';

// Get search and filter parameters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$publishYearFilter = isset($_GET['publish_year']) ? (int)$_GET['publish_year'] : '';
$uploadYearFilter = isset($_GET['upload_year']) ? (int)$_GET['upload_year'] : '';

// Get total count for pagination
$totalBooks = getBooksCount($searchQuery, $courseFilter, $publishYearFilter, $uploadYearFilter);
$hasMore = $totalBooks > 12;

// Handle AJAX requests for pagination
if (isset($_GET['ajax'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 12;
    $offset = ($page - 1) * $perPage;
    $books = getAllBooks($searchQuery, $courseFilter, $publishYearFilter, $uploadYearFilter, $perPage, $offset);
    header('Content-Type: application/json');
    echo json_encode($books);
    exit;
}

// For initial load, show first 12 books
$initialBooks = getAllBooks($searchQuery, $courseFilter, $publishYearFilter, $uploadYearFilter, 12, 0);
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
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="page" value="books">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search books by title or author..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="col-md-2">
                <label for="course" class="form-label">Course</label>
                <select name="course" id="course" class="form-select">
                    <option value="">All Courses</option>
                    <option value="BSIT" <?php echo $courseFilter === 'BSIT' ? 'selected' : ''; ?>>BSIT</option>
                    <option value="BSIS" <?php echo $courseFilter === 'BSIS' ? 'selected' : ''; ?>>BSIS</option>
                    <option value="ACT" <?php echo $courseFilter === 'ACT' ? 'selected' : ''; ?>>ACT</option>
                    <option value="SHS" <?php echo $courseFilter === 'SHS' ? 'selected' : ''; ?>>SHS</option>
                    <option value="BSHM" <?php echo $courseFilter === 'BSHM' ? 'selected' : ''; ?>>BSHM</option>
                    <option value="BSOA" <?php echo $courseFilter === 'BSOA' ? 'selected' : ''; ?>>BSOA</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="publish_year" class="form-label">Publish Year</label>
                <select name="publish_year" id="publish_year" class="form-select">
                    <option value="">All Years</option>
                    <?php for ($y = 2000; $y <= 2026; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $publishYearFilter === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="upload_year" class="form-label">Upload Year</label>
                <select name="upload_year" id="upload_year" class="form-select">
                    <option value="">All Years</option>
                    <?php for ($y = 2000; $y <= 2026; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $uploadYearFilter === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if ($searchQuery || $courseFilter || $publishYearFilter || $uploadYearFilter): ?>
                    <a href="?page=books" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- All Books Grid -->
    <div id="books-container" class="row g-3">
        <?php foreach($initialBooks as $book): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 border-0 shadow-sm">
                <img src="<?php echo $book['cover']; ?>" class="card-img-top" alt="<?php echo $book['title']; ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold mb-1"><?php echo $book['title']; ?></h6>
                    <p class="card-text text-muted small mb-2"><?php echo $book['course']; ?> - <?php echo date('M d, Y', strtotime($book['created_at'])); ?></p>
                    <p class="card-text text-muted small mb-2">Published: <?php echo date('M d, Y', strtotime($book['publish_date'])); ?></p>
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

    <!-- Loading indicator -->
    <div id="loading" class="text-center mt-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Load More Button -->
    <div id="load-more-container" class="text-center mt-4" style="display: <?php echo $hasMore ? 'block' : 'none'; ?>;">
        <button id="load-more-btn" class="btn btn-primary">
            <i class="bi bi-arrow-down-circle me-2"></i>Load More Books
        </button>
    </div>

    <!-- No more books message -->
    <div id="no-more" class="text-center mt-4" style="display: none;">
        <p class="text-muted">No more books to load.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let hasMore = <?php echo $hasMore ? 'true' : 'false'; ?>;
    let searchQuery = '<?php echo addslashes($searchQuery); ?>';
    let courseFilter = '<?php echo addslashes($courseFilter); ?>';
    let yearFilter = '<?php echo $yearFilter; ?>';

    function loadMoreBooks() {
        if (!hasMore) return;

        document.getElementById('loading').style.display = 'block';

        currentPage++;
        const url = `?page=books&ajax=1&page=${currentPage}&search=${encodeURIComponent(searchQuery)}&course=${encodeURIComponent(courseFilter)}&year=${yearFilter}`;

        fetch(url)
            .then(response => response.json())
            .then(books => {
                document.getElementById('loading').style.display = 'none';

                if (books.length === 0) {
                    hasMore = false;
                    document.getElementById('no-more').style.display = 'block';
                    return;
                }

                const container = document.getElementById('books-container');
                books.forEach(book => {
                    const col = document.createElement('div');
                    col.className = 'col-lg-3 col-md-4 col-sm-6';
                    col.innerHTML = `
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="${book.cover}" class="card-img-top" alt="${book.title}" style="height: 200px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold mb-1">${book.title}</h6>
                                <p class="card-text text-muted small mb-2">${book.course} - ${new Date(book.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                <p class="card-text text-muted small mb-2">Published: ${new Date(book.publish_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success me-1" title="Download" ${!book.available ? 'disabled' : ''}>
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });

                // Check if there are more books
                if (books.length < 12) {
                    hasMore = false;
                    document.getElementById('load-more-container').style.display = 'none';
                    document.getElementById('no-more').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading more books:', error);
                document.getElementById('loading').style.display = 'none';
            });
    }

    // Add event listener to Load More button
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            console.log('Load More button clicked');
            loadMoreBooks();
        });
    } else {
        console.log('Load More button not found');
    }
});
</script>
