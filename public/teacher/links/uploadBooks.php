<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
$currentPage = 'Upload Books';

include '../../back-end/create/uploadBooks.php';
include '../../back-end/read/readBooks.php';

// Get filter parameters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$yearFilter = isset($_GET['year']) ? (int)$_GET['year'] : '';

// Get total count for pagination
$totalBooks = getBooksCount($searchQuery, $courseFilter, $yearFilter);
$hasMore = $totalBooks > 12;

// For initial load, show first 12 books
$initialBooks = getAllBooks($searchQuery, $courseFilter, $yearFilter, 12, 0);
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

    <!-- Upload Book Form -->
    <div class="mb-4">
        <form id="uploadBookForm" method="POST" action="" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-2">
                <label for="book_title" class="form-label">Book Title</label>
                <input type="text" name="book_title" id="book_title" class="form-control" placeholder="Enter book title..." required>
            </div>
            <div class="col-md-2">
                <label for="book_course" class="form-label">Course</label>
                <select name="book_course" id="book_course" class="form-select" required>
                    <option value="">Select Course</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSIS">BSIS</option>
                    <option value="ACT">ACT</option>    
                    <option value="SHS">SHS</option>
                    <option value="BSHM">BSHM</option>
                    <option value="BSOA">BSOA</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="author" class="form-label">Author</label>
                <input type="text" name="author" id="author" class="form-control" placeholder="Enter author..." required>
            </div>
            <div class="col-md-2">
                <label for="publish_date" class="form-label">Publish Date</label>
                <input type="date" name="publish_date" id="publish_date" class="form-control" placeholder="Publish date..." required>
            </div>
            <div class="col-md-2">
                <label for="book_file" class="form-label">Book File</label>
                <input type="file" name="book_file" id="book_file" class="form-control" accept=".pdf" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-upload me-2"></i>Upload
                </button>
            </div>
        </form>
    </div>

    <hr>

    <!-- Search Form -->
    <div class="mb-4">
        <form method="GET" action="" class="d-flex">
            <input type="hidden" name="page" value="upload_books">
            <input type="text" name="search" class="form-control me-2" style="max-width: 300px;" placeholder="Search books by title or author..." value="<?php echo htmlspecialchars($searchQuery); ?>">
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
                <a href="?page=upload_books" class="btn btn-outline-secondary ms-2">Clear</a>
            <?php endif; ?>
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

    // Handle form submission
    const uploadForm = document.getElementById('uploadBookForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Uploading...';

            fetch('../../back-end/create/uploadBooks.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Book uploaded successfully!');
                    // Reset form
                    uploadForm.reset();
                    // Optionally reload the page or update the books list
                    location.reload();
                } else {
                    alert('Upload failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during upload.');
            })
            .finally(() => {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

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
