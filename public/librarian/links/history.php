<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/returnedBookHistory.php';
$currentPage = 'Returned Books History';

$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$perPage = 15;

$totalBooks = getReturnedBooksHistoryCount();
$totalPages = ceil($totalBooks / $perPage);
$offset = ($page - 1) * $perPage;

$returnedBooks = getReturnedBooksHistory($perPage, $offset);
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

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title"><?php echo $currentPage; ?></h1>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Return ID</th>
                    <th>Book Name</th>
                    <th>Borrower Name</th>
                    <th>Borrow Date</th>
                    <th>Expected Return Date</th>
                    <th>Actual Return Date</th>
                    <th>Processed By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($returnedBooks)): ?>
                <?php foreach ($returnedBooks as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['id']) ?></td>
                    <td><?= htmlspecialchars($book['book_title']) ?></td>
                    <td><?= htmlspecialchars($book['borrower_name']) ?></td>
                    <td><?= date('M d, Y', strtotime($book['borrow_date'])) ?></td>
                    <td><?= date('M d, Y', strtotime($book['expected_return_date'])) ?></td>
                    <td><?= $book['actual_return_date'] ? date('M d, Y', strtotime($book['actual_return_date'])) : 'N/A' ?></td>
                    <td><?= htmlspecialchars($book['processed_by'] ?? 'N/A') ?></td>
                    <td>
                        <button class="btn btn-sm btn-danger abort-return-btn"
                            data-return-id="<?= $book['id'] ?>"
                            data-book-id="<?= $book['book_id'] ?>"
                            data-user-id="<?= $book['user_id'] ?>"
                            data-book-title="<?= htmlspecialchars($book['book_title']) ?>"
                            data-borrow-date="<?= $book['borrow_date'] ?>"
                            data-expected-return-date="<?= $book['expected_return_date'] ?>">
                            Abort
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">No returned books history found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmAbortModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Abort</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Abort return for "<span id="abort-book-title"></span>"?
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" id="confirm-abort-btn">Abort</button>
            </div>
        </div>
    </div>
</div>

<div id="abort-success-message" class="success-message" style="display:none;">
    Book return aborted successfully!
</div>

<script>
document.addEventListener('click', e => {
    if (e.target.classList.contains('abort-return-btn')) {
        document.getElementById('abort-book-title').textContent =
            e.target.dataset.bookTitle;

        const btn = document.getElementById('confirm-abort-btn');
        // Fixed: Using underscore naming to match PHP expectations
        btn.dataset.return_id = e.target.dataset.returnId;
        btn.dataset.book_id = e.target.dataset.bookId;
        btn.dataset.user_id = e.target.dataset.userId;
        btn.dataset.borrow_date = e.target.dataset.borrowDate;
        btn.dataset.expected_return_date = e.target.dataset.expectedReturnDate;

        new bootstrap.Modal(document.getElementById('confirmAbortModal')).show();
    }
});

document.getElementById('confirm-abort-btn').addEventListener('click', function () {
    fetch('../../back-end/update/abortReturned.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(this.dataset)
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === 'success') {
            const successMsg = document.getElementById('abort-success-message');
            successMsg.style.display = 'block';
            successMsg.style.opacity = '1';
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            alert(data);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});
</script>