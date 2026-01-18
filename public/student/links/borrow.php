<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
$currentPage = 'Borrow Books';

// Get filter parameters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$publishYearFilter = isset($_GET['publish_year']) ? (int)$_GET['publish_year'] : '';
$uploadYearFilter = isset($_GET['upload_year']) ? (int)$_GET['upload_year'] : '';
$hasMore = false;

// Dummy data for books
$allBooks = [
    [
        'id' => 1,
        'title' => 'Introduction to Programming',
        'course' => 'BSIT',
        'author' => 'John Doe',
        'publish_date' => '2023-01-15'
    ],
    [
        'id' => 2,
        'title' => 'Data Structures and Algorithms',
        'course' => 'BSIT',
        'author' => 'Jane Smith',
        'publish_date' => '2023-03-20'
    ],
    [
        'id' => 3,
        'title' => 'Database Management Systems',
        'course' => 'BSIS',
        'author' => 'Bob Johnson',
        'publish_date' => '2023-05-10'
    ],
    [
        'id' => 4,
        'title' => 'Web Development Fundamentals',
        'course' => 'BSIT',
        'author' => 'Alice Brown',
        'publish_date' => '2023-07-05'
    ],
    [
        'id' => 5,
        'title' => 'Software Engineering Principles',
        'course' => 'BSIT',
        'author' => 'Charlie Wilson',
        'publish_date' => '2023-09-12'
    ],
    [
        'id' => 6,
        'title' => 'Information Systems Analysis',
        'course' => 'BSIS',
        'author' => 'Diana Davis',
        'publish_date' => '2023-11-08'
    ],
    [
        'id' => 7,
        'title' => 'Computer Networks',
        'course' => 'BSIT',
        'author' => 'Eve Martinez',
        'publish_date' => '2022-09-15'
    ],
    [
        'id' => 8,
        'title' => 'Business Information Systems',
        'course' => 'BSIS',
        'author' => 'Frank Garcia',
        'publish_date' => '2022-11-20'
    ],
    [
        'id' => 9,
        'title' => 'Artificial Intelligence',
        'course' => 'BSIT',
        'author' => 'Grace Lee',
        'publish_date' => '2024-01-10'
    ],
    [
        'id' => 10,
        'title' => 'Data Mining',
        'course' => 'BSIS',
        'author' => 'Henry Taylor',
        'publish_date' => '2024-03-05'
    ]
];

// Filter books based on parameters
$initialBooks = array_filter($allBooks, function($book) use ($searchQuery, $courseFilter, $publishYearFilter, $uploadYearFilter) {
    // Search filter
    if ($searchQuery) {
        $searchLower = strtolower($searchQuery);
        if (strpos(strtolower($book['title']), $searchLower) === false &&
            strpos(strtolower($book['author']), $searchLower) === false) {
            return false;
        }
    }

    // Course filter
    if ($courseFilter && $book['course'] !== $courseFilter) {
        return false;
    }

    // Publish year filter
    if ($publishYearFilter) {
        $bookYear = (int)date('Y', strtotime($book['publish_date']));
        if ($bookYear !== $publishYearFilter) {
            return false;
        }
    }

    // Upload year filter (simulated as publish year for dummy data)
    if ($uploadYearFilter) {
        $bookYear = (int)date('Y', strtotime($book['publish_date']));
        if ($bookYear !== $uploadYearFilter) {
            return false;
        }
    }

    return true;
});
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
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="page" value="borrow_book">
            <div class="col-md-3">
                <input type="text" name="search" id="search" class="form-control" placeholder="Search books by title or author..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="col-md-2">
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
                <select name="publish_year" id="publish_year" class="form-select">
                    <option value="">All Years</option>
                    <?php for ($y = 2000; $y <= 2026; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $publishYearFilter === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
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
                    <a href="?page=borrow_book" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Books Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Book Name</th>
                    <th>Program</th>
                    <th>Author</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="books-table-body">
                <?php foreach($initialBooks as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['course']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($book['publish_date'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-borrow" data-book-id="<?php echo $book['id']; ?>" title="Borrow">
                            <i class="bi bi-bookmark-plus"></i> Borrow
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
    console.log('Script loaded');
    
    let currentPage = 1;
    let hasMore = <?php echo $hasMore ? 'true' : 'false'; ?>;
    let searchQuery = '<?php echo addslashes($searchQuery); ?>';
    let courseFilter = '<?php echo addslashes($courseFilter); ?>';
    let publishYearFilter = '<?php echo $publishYearFilter; ?>';
    let uploadYearFilter = '<?php echo $uploadYearFilter; ?>';

    const booksTableBody = document.getElementById('books-table-body');
    console.log('Books table body found:', booksTableBody);

    // Use event delegation for borrow buttons
    booksTableBody.addEventListener('click', function(e) {
        console.log('Click detected on:', e.target);

        const borrowBtn = e.target.closest('.btn-borrow');
        console.log('Borrow button found:', borrowBtn);

        if (borrowBtn) {
            const bookId = parseInt(borrowBtn.dataset.bookId);
            console.log('Book ID:', bookId);
            // TODO: Implement borrow functionality
            alert('Borrow functionality for book ID ' + bookId + ' will be implemented.');
        }
    });

    function recordPreview(book_id) {
        console.log('recordPreview called with ID:', book_id);
        
        fetch('../../back-end/recent/recentPreviewBooks.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ book_id: book_id }),
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Response text:', text);
            try {
                const data = JSON.parse(text);
                console.log('Parsed data:', data);
                if (data.success) {
                    console.log('Opening preview window...');
                    window.open(`../../back-end/preview/previewBooks.php?id=${book_id}`, '_blank');
                } else {
                    console.error('Preview failed:', data.error);
                }
            } catch (e) {
                console.error('JSON parse error:', e, 'Text was:', text);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    }

    function loadMoreBooks() {
        if (!hasMore) return;

        document.getElementById('loading').style.display = 'block';

        currentPage++;
        const url = `?page=books&ajax=1&page=${currentPage}&search=${encodeURIComponent(searchQuery)}&course=${encodeURIComponent(courseFilter)}&publish_year=${publishYearFilter}&upload_year=${uploadYearFilter}`;

        fetch(url)
            .then(response => response.json())
            .then(books => {
                document.getElementById('loading').style.display = 'none';

                if (books.length === 0) {
                    hasMore = false;
                    document.getElementById('load-more-container').style.display = 'none';
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
                                <p class="card-text text-muted small mb-2">${book.author} | ${new Date(book.publish_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-1 btn-preview" data-book-id="${book.id}" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="../../back-end/download/downloadBooks.php?id=${book.id}" class="btn btn-sm btn-outline-success me-1" title="Download" ${!book.available ? 'style="pointer-events: none; opacity: 0.5;"' : ''}>
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });

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

    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            console.log('Load More button clicked');
            loadMoreBooks();
        });
    }
});
</script>