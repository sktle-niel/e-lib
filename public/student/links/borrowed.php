<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/readBorrowedBooks.php';
$currentPage = 'Borrowed Books';

$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$perPage = 15;
$userId = $_SESSION['user_id'];

// Get total count of borrowed books for this user
$totalBooks = getBorrowedBooksCount($userId);
$totalPages = ceil($totalBooks / $perPage);
$offset = ($page - 1) * $perPage;

// Get borrowed books for this user with pagination
$borrowedBooks = getBorrowedBooks($userId, $perPage, $offset);
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

    <!-- Borrowed Books Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Book Name</th>
                    <th>Date Borrowed</th>
                    <th>Date of Return</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="borrowed-books-table-body">
                <?php if (empty($borrowedBooks)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <i class="bi bi-book" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No borrowed books found</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($borrowedBooks as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['expected_return_date'])); ?></td>
                        <td>
                            <span class="badge <?php
                                echo $book['status'] === 'returned' ? 'bg-success' :
                                     ($book['status'] === 'overdue' ? 'bg-danger' : 'bg-warning text-dark');
                            ?>">
                                <?php echo htmlspecialchars(ucfirst($book['status'])); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Books pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- Previous button -->
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=borrowed&p=<?php echo $page - 1; ?>" tabindex="-1">
                    Previous
                </a>
            </li>

            <!-- Page numbers -->
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);

            for ($i = $startPage; $i <= $endPage; $i++):
            ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=borrowed&p=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>

            <!-- Next button -->
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=borrowed&p=<?php echo $page + 1; ?>">
                    Next
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

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