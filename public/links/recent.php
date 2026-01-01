<?php
$currentPage = 'Recent';

$recentlyViewedBooks = [
    ['title' => 'Introduction to Algorithms', 'author' => 'Cormen et al.', 'cover' => 'https://via.placeholder.com/150x200/11998e/ffffff?text=Algo', 'viewedDate' => '2023-10-15', 'course' => 'BSIT', 'year' => 2009],
    ['title' => 'Computer Networks', 'author' => 'Andrew Tanenbaum', 'cover' => 'https://via.placeholder.com/150x200/38ef7d/000000?text=Networks', 'viewedDate' => '2023-10-10', 'course' => 'BSIT', 'year' => 2011],
    ['title' => 'Database System Concepts', 'author' => 'Silberschatz et al.', 'cover' => 'https://via.placeholder.com/150x200/f093fb/000000?text=DB', 'viewedDate' => '2023-10-05', 'course' => 'BSIS', 'year' => 2010],
    ['title' => 'Operating Systems', 'author' => 'William Stallings', 'cover' => 'https://via.placeholder.com/150x200/f5576c/ffffff?text=OS', 'viewedDate' => '2023-09-28', 'course' => 'BSIT', 'year' => 2012],
    ['title' => 'Artificial Intelligence', 'author' => 'Stuart Russell', 'cover' => 'https://via.placeholder.com/150x200/28a745/ffffff?text=AI', 'viewedDate' => '2023-09-20', 'course' => 'BSIT', 'year' => 2010],
    ['title' => 'Machine Learning', 'author' => 'Tom Mitchell', 'cover' => 'https://via.placeholder.com/150x200/dc3545/ffffff?text=ML', 'viewedDate' => '2023-09-15', 'course' => 'BSIT', 'year' => 1997],
    ['title' => 'Data Structures', 'author' => 'Mark Allen Weiss', 'cover' => 'https://via.placeholder.com/150x200/ffc107/000000?text=DS', 'viewedDate' => '2023-09-10', 'course' => 'BSIT', 'year' => 2006],
    ['title' => 'Software Engineering', 'author' => 'Ian Sommerville', 'cover' => 'https://via.placeholder.com/150x200/17a2b8/ffffff?text=SE', 'viewedDate' => '2023-09-05', 'course' => 'BSIT', 'year' => 2015],
    ['title' => 'Web Development', 'author' => 'Jon Duckett', 'cover' => 'https://via.placeholder.com/150x200/6f42c1/ffffff?text=Web', 'viewedDate' => '2023-08-30', 'course' => 'BSIT', 'year' => 2014],
    ['title' => 'Cybersecurity', 'author' => 'William Stallings', 'cover' => 'https://via.placeholder.com/150x200/e83e8c/ffffff?text=Cyber', 'viewedDate' => '2023-08-25', 'course' => 'BSIT', 'year' => 2017],
    ['title' => 'Cloud Computing', 'author' => 'Thomas Erl', 'cover' => 'https://via.placeholder.com/150x200/20c997/000000?text=Cloud', 'viewedDate' => '2023-08-20', 'course' => 'BSIS', 'year' => 2013],
    ['title' => 'Big Data', 'author' => 'Viktor Mayer-Schönberger', 'cover' => 'https://via.placeholder.com/150x200/fd7e14/ffffff?text=BigData', 'viewedDate' => '2023-08-15', 'course' => 'BSIS', 'year' => 2013],
    ['title' => 'Blockchain Technology', 'author' => 'Melanie Swan', 'cover' => 'https://via.placeholder.com/150x200/6c757d/ffffff?text=Blockchain', 'viewedDate' => '2023-08-10', 'course' => 'BSIT', 'year' => 2015],
    ['title' => 'Internet of Things', 'author' => 'Adrian McEwen', 'cover' => 'https://via.placeholder.com/150x200/007bff/ffffff?text=IoT', 'viewedDate' => '2023-08-05', 'course' => 'BSIT', 'year' => 2014],
    ['title' => 'Quantum Computing', 'author' => 'Nielsen & Chuang', 'cover' => 'https://via.placeholder.com/150x200/6610f2/ffffff?text=Quantum', 'viewedDate' => '2023-07-30', 'course' => 'BSIT', 'year' => 2010],
    ['title' => 'DevOps Handbook', 'author' => 'Gene Kim', 'cover' => 'https://via.placeholder.com/150x200/28a745/ffffff?text=DevOps', 'viewedDate' => '2023-07-25', 'course' => 'BSIT', 'year' => 2016],
    ['title' => 'Computer Graphics', 'author' => 'Donald Hearn', 'cover' => 'https://via.placeholder.com/150x200/6f42c1/ffffff?text=CG', 'viewedDate' => '2023-07-20', 'course' => 'BSIT', 'year' => 2018],
    ['title' => 'Mobile App Development', 'author' => 'David Mark', 'cover' => 'https://via.placeholder.com/150x200/28a745/ffffff?text=Mobile', 'viewedDate' => '2023-07-15', 'course' => 'BSIT', 'year' => 2019],
    ['title' => 'Data Mining', 'author' => 'Jiawei Han', 'cover' => 'https://via.placeholder.com/150x200/dc3545/ffffff?text=DM', 'viewedDate' => '2023-07-10', 'course' => 'BSIS', 'year' => 2011],
    ['title' => 'Network Security', 'author' => 'Charlie Kaufman', 'cover' => 'https://via.placeholder.com/150x200/f5576c/ffffff?text=NetSec', 'viewedDate' => '2023-07-05', 'course' => 'BSIT', 'year' => 2013],
];

// Sort by viewed date descending (recent first)
usort($recentlyViewedBooks, function($a, $b) {
    return strtotime($b['viewedDate']) - strtotime($a['viewedDate']);
});

// Filter books based on search query, course, and year
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$yearFilter = isset($_GET['year']) ? (int)$_GET['year'] : '';
$filteredBooks = $recentlyViewedBooks;

if ($searchQuery || $courseFilter || $yearFilter) {
    $filteredBooks = array_filter($recentlyViewedBooks, function($book) use ($searchQuery, $courseFilter, $yearFilter) {
        $matchesSearch = !$searchQuery || strpos(strtolower($book['title']), $searchQuery) !== false || strpos(strtolower($book['author']), $searchQuery) !== false;
        $matchesCourse = !$courseFilter || $book['course'] === $courseFilter;
        $matchesYear = !$yearFilter || $book['year'] === $yearFilter;
        return $matchesSearch && $matchesCourse && $matchesYear;
    });
}

// Handle AJAX requests for pagination
if (isset($_GET['ajax'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 12; // 3 rows * 4 cards
    $offset = ($page - 1) * $perPage;
    $booksToShow = array_slice($filteredBooks, $offset, $perPage);
    echo json_encode($booksToShow);
    exit;
}

// For initial load, show first 12 books
$initialBooks = array_slice($filteredBooks, 0, 12);
$totalBooks = count($filteredBooks);
$hasMore = $totalBooks > 12;
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
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
            <?php if ($searchQuery || $courseFilter || $yearFilter): ?>
                <a href="?page=recent" class="btn btn-outline-secondary ms-2">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div id="books-container" class="row g-3">
        <?php foreach($initialBooks as $book): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 border-0 shadow-sm">
                <img src="<?php echo $book['cover']; ?>" class="card-img-top" alt="<?php echo $book['title']; ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold mb-1"><?php echo $book['title']; ?></h6>
                    <p class="card-text text-muted small mb-1"><?php echo $book['author']; ?></p>
                    <p class="card-text text-muted small mb-2">Viewed: <?php echo date('M d, Y', strtotime($book['viewedDate'])); ?></p>
                    <div class="d-flex justify-content-end">
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-1" title="Read">
                                <i class="bi bi-book"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger me-1" title="Delete">
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

    function loadMoreBooks() {
        if (!hasMore) return;

        document.getElementById('loading').style.display = 'block';

        currentPage++;
        const url = `?page=downloads&ajax=1&page=${currentPage}&search=${encodeURIComponent(searchQuery)}&course=${encodeURIComponent(courseFilter)}&year=${yearFilter}`;

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
                                <p class="card-text text-muted small mb-1">${book.author}</p>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Read">
                                            <i class="bi bi-book"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger me-1" title="Delete">
                                            <i class="bi bi-trash"></i>
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
                console.error('Error loading more recent views:', error);
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
