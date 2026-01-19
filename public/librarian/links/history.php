<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/returnedBookHistory.php';
$currentPage = 'Returned Books History';

$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$perPage = 15;

// Get total count of all returned books history
$totalBooks = getReturnedBooksHistoryCount();
$totalPages = ceil($totalBooks / $perPage);
$offset = ($page - 1) * $perPage;

// Get all returned books history with pagination
$returnedBooks = getReturnedBooksHistory($perPage, $offset);
?>

<link rel="stylesheet" href="../../src/css/phoneMediaQuery.css">

<!-- Include Success Message Styles -->
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

    <!-- Returned Books History Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Return ID</th>
                    <th>Book Name</th>
                    <th>Borrow Date</th>
                    <th>Expected Return Date</th>
                    <th>Actual Return Date</th>
                    <th>Processed By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="returned-books-table-body">
                <?php if (count($returnedBooks) > 0): ?>
                    <?php foreach($returnedBooks as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['id']); ?></td>
                        <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['expected_return_date'])); ?></td>
                        <td><?php echo $book['actual_return_date'] ? date('M d, Y', strtotime($book['actual_return_date'])) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($book['processed_by'] ?? 'N/A'); ?></td>
                        <td>
                            <button class="btn btn-sm btn-danger abort-return-btn"
                                    data-return-id="<?php echo htmlspecialchars($book['id']); ?>"
                                    data-book-title="<?php echo htmlspecialchars($book['book_title']); ?>"
                                    data-book-id="<?php echo htmlspecialchars($book['book_id']); ?>"
                                    data-user-id="<?php echo htmlspecialchars($book['user_id']); ?>"
                                    data-borrow-date="<?php echo htmlspecialchars($book['borrow_date']); ?>"
                                    data-expected-return-date="<?php echo htmlspecialchars($book['expected_return_date']); ?>">
                                Abort
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No returned books history found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Confirmation Modal for Abort -->
    <div class="modal fade" id="confirmAbortModal" tabindex="-1" aria-labelledby="confirmAbortModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmAbortModalLabel">Confirm Abort</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to abort the return for "<span id="abort-book-title"></span>"? This will re-borrow the book.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-abort-btn">Abort Return</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div id="abort-success-message" class="success-message" style="display: none;">Book return aborted successfully!</div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Books pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- Previous button -->
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?p=<?php echo $page - 1; ?>" tabindex="-1">
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
                <a class="page-link" href="?p=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>

            <!-- Next button -->
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?p=<?php echo $page + 1; ?>">
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
    console.log('History page loaded');

    // Handle Abort button clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('abort-return-btn')) {
            const bookTitle = e.target.getAttribute('data-book-title');
            const returnId = e.target.getAttribute('data-return-id');
            const bookId = e.target.getAttribute('data-book-id');
            const userId = e.target.getAttribute('data-user-id');
            const borrowDate = e.target.getAttribute('data-borrow-date');
            const expectedReturnDate = e.target.getAttribute('data-expected-return-date');

            document.getElementById('abort-book-title').textContent = bookTitle;
            document.getElementById('confirm-abort-btn').setAttribute('data-return-id', returnId);
            document.getElementById('confirm-abort-btn').setAttribute('data-book-id', bookId);
            document.getElementById('confirm-abort-btn').setAttribute('data-user-id', userId);
            document.getElementById('confirm-abort-btn').setAttribute('data-borrow-date', borrowDate);
            document.getElementById('confirm-abort-btn').setAttribute('data-expected-return-date', expectedReturnDate);

            const modal = new bootstrap.Modal(document.getElementById('confirmAbortModal'));
            modal.show();
        }
    });

    // Handle Confirm Abort button click
    document.getElementById('confirm-abort-btn').addEventListener('click', function() {
        const returnId = this.getAttribute('data-return-id');
        const bookId = this.getAttribute('data-book-id');
        const userId = this.getAttribute('data-user-id');
        const borrowDate = this.getAttribute('data-borrow-date');
        const expectedReturnDate = this.getAttribute('data-expected-return-date');

        fetch('../../back-end/update/abortReturned.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'return_id=' + encodeURIComponent(returnId) +
                  '&book_id=' + encodeURIComponent(bookId) +
                  '&user_id=' + encodeURIComponent(userId) +
                  '&borrow_date=' + encodeURIComponent(borrowDate) +
                  '&expected_return_date=' + encodeURIComponent(expectedReturnDate)
        })
        .then(response => response.text())
        .then(data => {
            console.log('Response:', data); // For debugging
            if (data.trim() === 'success') {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmAbortModal'));
                modal.hide();

                // Show success message
                const successMsg = document.getElementById('abort-success-message');
                successMsg.style.display = 'block';
                successMsg.style.opacity = '1';
                setTimeout(function() {
                    successMsg.style.opacity = '0';
                    setTimeout(function() {
                        successMsg.style.display = 'none';
                        // Reload the page to reflect changes
                        location.reload();
                    }, 1000);
                }, 3000);

            } else {
                alert('Error aborting book return. Please try again.');
                console.error('Server response:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>
