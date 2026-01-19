<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/readLibBooks.php';
$currentPage = 'Borrow Books';

// Get filter parameters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$publishYearFilter = isset($_GET['publish_year']) ? (int)$_GET['publish_year'] : '';

// Get books from database
if ($searchQuery || $courseFilter || $publishYearFilter) {
    $initialBooks = getFilteredBooks($searchQuery, $courseFilter, $publishYearFilter, 20);
} else {
    $initialBooks = getRecentLibBooks(20); // Get 20 recent books for display
}
$totalBooks = count($initialBooks);
?>

<link rel="stylesheet" href="../../src/css/phoneMediaQuery.css">
<style>
    .success-message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-radius: 5px;
        opacity: 0;
        transition: opacity 1s;
        font-size: 16px;
        z-index: 1000;
    }
</style>

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

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if ($searchQuery || $courseFilter || $publishYearFilter): ?>
                    <a href="?page=borrow_book" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Results count -->
    <div class="mb-3">
        <p class="text-muted">
            Found <strong><?php echo $totalBooks; ?></strong> book<?php echo $totalBooks !== 1 ? 's' : ''; ?>
            <?php if ($searchQuery || $courseFilter || $publishYearFilter): ?>
                matching your search
            <?php endif; ?>
        </p>
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
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="books-table-body">
                <?php if (empty($initialBooks)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No books found</p>
                            <?php if ($searchQuery || $courseFilter || $publishYearFilter): ?>
                                <button type="button" id="clearFiltersBtn2" class="btn btn-sm btn-outline-primary mt-2">Clear Filters</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($initialBooks as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($book['book_course']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['publish_date'])); ?></td>
                        <td>
                            <span class="badge <?php echo isset($book['status']) && $book['status'] === 'available' ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo isset($book['status']) ? ucfirst($book['status']) : 'Not Available'; ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-borrow" data-book-id="<?php echo $book['id']; ?>" title="Borrow" <?php echo isset($book['status']) && $book['status'] !== 'available' ? 'disabled' : ''; ?>>
                                <i class="bi bi-bookmark-plus"></i> Borrow
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Loading indicator -->
    <div id="loading" class="text-center mt-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- No more books message -->
    <div id="no-more" class="text-center mt-4" style="display: none;">
        <p class="text-muted">No more books to load.</p>
    </div>
</div>

<div id="success-message" class="success-message">Book borrowed successfully!</div>

<!-- Borrow Confirmation Modal -->
<div class="modal fade" id="borrowBookModal" tabindex="-1" aria-labelledby="borrowBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="borrowBookModalLabel">Confirm Borrow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to borrow "<span id="borrowBookTitle"></span>"? The book must be returned within 3 days.</p>
                <input type="hidden" id="borrowBookId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBorrowBtn">Confirm Borrow</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');

    let currentPage = 1;
    let hasMore = false;
    let searchQuery = '<?php echo addslashes($searchQuery); ?>';
    let courseFilter = '<?php echo addslashes($courseFilter); ?>';
    let publishYearFilter = '<?php echo $publishYearFilter; ?>';


    const booksTableBody = document.getElementById('books-table-body');
    console.log('Books table body found:', booksTableBody);

    // Use event delegation for borrow buttons
    booksTableBody.addEventListener('click', function(e) {
        console.log('Click detected on:', e.target);

        const borrowBtn = e.target.closest('.btn-borrow');
        console.log('Borrow button found:', borrowBtn);

        if (borrowBtn) {
            const bookId = parseInt(borrowBtn.dataset.bookId);
            const bookTitle = borrowBtn.closest('tr').querySelector('td:first-child').textContent.trim();
            console.log('Book ID:', bookId, 'Book Title:', bookTitle);

            // Populate modal
            document.getElementById('borrowBookId').value = bookId;
            document.getElementById('borrowBookTitle').textContent = bookTitle;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('borrowBookModal'));
            modal.show();
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
        const url = `?page=books&ajax=1&page=${currentPage}&search=${encodeURIComponent(searchQuery)}&course=${encodeURIComponent(courseFilter)}&publish_year=${publishYearFilter}`;

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

    // Handle clear filters button
    const clearFiltersBtn2 = document.getElementById('clearFiltersBtn2');
    if (clearFiltersBtn2) {
        clearFiltersBtn2.addEventListener('click', function() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('search');
            currentUrl.searchParams.delete('course');
            currentUrl.searchParams.delete('publish_year');
            window.location.href = currentUrl.toString();
        });
    }

    // Handle confirm borrow
    document.getElementById('confirmBorrowBtn').addEventListener('click', function() {
        const bookId = document.getElementById('borrowBookId').value;
        const btn = this;
        const originalText = btn.innerHTML;

        // Disable button
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        fetch('../../../back-end/create/borrowBook.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'book_id=' + encodeURIComponent(bookId)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const msg = document.getElementById("success-message");
                msg.style.opacity = "1";
                setTimeout(function() {
                    msg.style.opacity = "0";
                    setTimeout(function() {
                        msg.style.display = "none";
                        // Reload to update status after message fades out
                        location.reload();
                    }, 1000);
                }, 3000);
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('borrowBookModal'));
                modal.hide();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while borrowing the book: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            console.log('Load More button clicked');
            loadMoreBooks();
        });
    }
});
</script>