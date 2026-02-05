<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/readPenalties.php';
include '../../back-end/read/readClearedPenalties.php';
$currentPage = 'Penalty';

$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$perPage = 10;

// Initialize search/filter variables (not used in penalties but keeping for consistency)
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$publishYearFilter = isset($_GET['publish_year']) ? $_GET['publish_year'] : '';
$uploadYearFilter = isset($_GET['upload_year']) ? $_GET['upload_year'] : '';
$hasMore = false; // Not used in penalties page

// Get total count of penalties
$totalBooks = getPenaltiesCount();
$totalPages = ceil($totalBooks / $perPage);
$offset = ($page - 1) * $perPage;

// Get penalties with pagination (user_id = null for all users, limit, offset)
$overdueBooks = getPenalties(null, $perPage, $offset);

// Debug: Check if data is retrieved
echo "<!-- DEBUG: Found " . count($overdueBooks) . " penalty records -->";
if (count($overdueBooks) > 0) {
    echo "<!-- DEBUG: First record: " . htmlspecialchars($overdueBooks[0]['title']) . " -->";
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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

    <!-- Penalty Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Book Name</th>
                    <th>Borrower Name</th>
                    <th>Date Borrowed</th>
                    <th>Date of Return</th>
                    <th>Days Overdue</th>
                    <th>Penalty Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="penalty-table-body">
                <?php foreach($overdueBooks as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['borrower_name']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></td>
                    <td><?php echo date('M d, Y', strtotime($book['return_date'])); ?></td>
                    <td><?php echo htmlspecialchars($book['days_overdue']); ?> days</td>
                    <td>₱<?php echo number_format($book['penalty_amount'], 2); ?></td>
                    <td>
                        <span class="badge bg-danger">
                            <?php echo htmlspecialchars($book['status']); ?>
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-success btn-clear-penalty"
                                data-borrow-id="<?php echo htmlspecialchars($book['id']); ?>"
                                data-book-title="<?php echo htmlspecialchars($book['title']); ?>"
                                data-borrower-name="<?php echo htmlspecialchars($book['borrower_name']); ?>"
                                data-penalty-amount="<?php echo htmlspecialchars($book['penalty_amount']); ?>"
                                title="Clear Penalty">
                            <i class="bi bi-check-circle"></i> Clear
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>



    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Books pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- Previous button -->
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=borrow_book&p=<?php echo $page - 1; ?><?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?><?php echo $courseFilter ? '&course=' . urlencode($courseFilter) : ''; ?><?php echo $publishYearFilter ? '&publish_year=' . $publishYearFilter : ''; ?><?php echo $uploadYearFilter ? '&upload_year=' . $uploadYearFilter : ''; ?>" tabindex="-1">
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
                <a class="page-link" href="?page=borrow_book&p=<?php echo $i; ?><?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?><?php echo $courseFilter ? '&course=' . urlencode($courseFilter) : ''; ?><?php echo $publishYearFilter ? '&publish_year=' . $publishYearFilter : ''; ?><?php echo $uploadYearFilter ? '&upload_year=' . $uploadYearFilter : ''; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>

            <!-- Next button -->
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=borrow_book&p=<?php echo $page + 1; ?><?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?><?php echo $courseFilter ? '&course=' . urlencode($courseFilter) : ''; ?><?php echo $publishYearFilter ? '&publish_year=' . $publishYearFilter : ''; ?><?php echo $uploadYearFilter ? '&upload_year=' . $uploadYearFilter : ''; ?>">
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

<!-- Success Message -->
<div id="success-message" class="success-message">Penalty cleared successfully!</div>

<!-- Clear Penalty Confirmation Modal -->
<div class="modal fade" id="clearPenaltyModal" tabindex="-1" aria-labelledby="clearPenaltyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearPenaltyModalLabel">Clear Penalty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to clear this penalty?</p>
                <div class="alert alert-info">
                    <strong>Book:</strong> <span id="modal-book-title"></span><br>
                    <strong>Borrower:</strong> <span id="modal-borrower-name"></span><br>
                    <strong>Penalty Amount:</strong> ₱<span id="modal-penalty-amount"></span>
                </div>
                <div class="mb-3">
                    <label for="clear-notes" class="form-label">Notes (optional)</label>
                    <textarea class="form-control" id="clear-notes" rows="3" placeholder="Add any notes about clearing this penalty..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirm-clear-btn">Clear Penalty</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');

    // Clear Penalty functionality
    let currentBorrowId = null;

    // Handle clear penalty button clicks
    document.addEventListener('click', function(e) {
        console.log('Click detected:', e.target);
        if (e.target.closest('.btn-clear-penalty')) {
            console.log('Clear penalty button clicked');
            const btn = e.target.closest('.btn-clear-penalty');
            currentBorrowId = btn.dataset.borrowId;
            console.log('Borrow ID:', currentBorrowId);

            // Populate modal with penalty details
            document.getElementById('modal-book-title').textContent = btn.dataset.bookTitle;
            document.getElementById('modal-borrower-name').textContent = btn.dataset.borrowerName;
            document.getElementById('modal-penalty-amount').textContent = btn.dataset.penaltyAmount;
            document.getElementById('clear-notes').value = '';

            console.log('Modal elements populated');

            // Check if Bootstrap is available
            if (typeof bootstrap !== 'undefined') {
                console.log('Bootstrap available');
                const modal = new bootstrap.Modal(document.getElementById('clearPenaltyModal'));
                modal.show();
                console.log('Modal show() called');
            } else {
                console.error('Bootstrap not available');
                alert('Bootstrap not loaded. Please refresh the page.');
            }
        }
    });

    // Handle confirm clear penalty
    document.getElementById('confirm-clear-btn').addEventListener('click', function() {
        if (!currentBorrowId) return;

        const confirmBtn = this;
        const originalText = confirmBtn.innerHTML;

        // Disable button and show loading
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Clearing...';

        const notes = document.getElementById('clear-notes').value.trim();

        // Send AJAX request to clear penalty
        fetch('../../back-end/update/clearPenalty.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                borrow_id: currentBorrowId,
                cleared_by: '<?php echo isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 1; ?>', // Default to 1 if not set
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('clearPenaltyModal'));
                modal.hide();

                // Show success message
                const msg = document.getElementById('success-message');
                msg.style.opacity = "1";
                setTimeout(function() {
                    msg.style.opacity = "0";
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }, 3000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while clearing the penalty.');
        })
        .finally(() => {
            // Re-enable button
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
