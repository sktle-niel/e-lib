<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/readBorrowedBooks.php';
$currentPage = 'Borrowed Books';

$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$perPage = 15;

// Get total count of all borrowed books
$totalBooks = getAllBorrowedBooksCount();
$totalPages = ceil($totalBooks / $perPage);
$offset = ($page - 1) * $perPage;

// Get all borrowed books with pagination
$borrowedBooks = getAllBorrowedBooks($perPage, $offset);
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

    <!-- Borrowed Books Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Book Name</th>
                    <th>Borrower Name</th>
                    <th>Date Borrowed</th>
                    <th>Date of Return</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="borrowed-books-table-body">
                <?php if (count($borrowedBooks) > 0): ?>
                    <?php foreach($borrowedBooks as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['borrower_name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['return_date'])); ?></td>
                        <td>
                            <span class="badge <?php
                                echo $book['status'] === 'Returned' ? 'bg-success' :
                                     ($book['status'] === 'Overdue' ? 'bg-danger' : 'bg-warning text-dark');
                            ?>">
                                <?php echo htmlspecialchars($book['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success mark-returned-btn"
                                    data-book-title="<?php echo htmlspecialchars($book['title']); ?>"
                                    data-borrow-id="<?php echo htmlspecialchars($book['id']); ?>">
                                Mark as Returned
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No borrowed books found.</td>
                    </tr>
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
                <a class="page-link" href="?page=borrowedList&p=<?php echo $page - 1; ?>" tabindex="-1">
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
                <a class="page-link" href="?page=borrowedList&p=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>

            <!-- Next button -->
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=borrowedList&p=<?php echo $page + 1; ?>">
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

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmReturnModal" tabindex="-1" aria-labelledby="confirmReturnModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmReturnModalLabel">Confirm Return</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to mark "<span id="book-title"></span>" as returned?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirm-return-btn">Mark as Returned</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="success-message" style="display: none;">Book marked as returned successfully!</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Borrowed Books page loaded');

    const borrowedBooksTableBody = document.getElementById('borrowed-books-table-body');

    if (borrowedBooksTableBody) {
        console.log('Borrowed books table body found');
    }

    // Handle Mark as Returned button clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('mark-returned-btn')) {
            const bookTitle = e.target.getAttribute('data-book-title');
            const borrowId = e.target.getAttribute('data-borrow-id');

            document.getElementById('book-title').textContent = bookTitle;
            document.getElementById('confirm-return-btn').setAttribute('data-borrow-id', borrowId);

            const modal = new bootstrap.Modal(document.getElementById('confirmReturnModal'));
            modal.show();
        }
    });

    // Handle Confirm Return button click
    document.getElementById('confirm-return-btn').addEventListener('click', function() {
        const borrowId = this.getAttribute('data-borrow-id');

        fetch('../../back-end/update/markAsReturned.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'borrow_id=' + encodeURIComponent(borrowId)
        })
        .then(response => response.text())
        .then(data => {
            // ADDED: Better debugging
            console.log('Response:', data);
            console.log('Response length:', data.length);
            console.log('Response trimmed:', data.trim());
            console.log('Comparison result:', data.trim() === 'success');
            
            if (data.trim() === 'success') {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmReturnModal'));
                modal.hide();

                // Show success message
                const successMsg = document.getElementById('success-message');
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
                alert('Error marking book as returned. Please try again.');
                console.error('Server response:', data);
                console.error('Expected: "success", Got:', JSON.stringify(data)); // ADDED
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>