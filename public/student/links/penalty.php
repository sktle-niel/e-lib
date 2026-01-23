<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/readPenalties.php';

$currentPage = 'Penalty';

// Get user ID from session
$userId = $_SESSION['user_id'];

$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$perPage = 15;

// Pagination
$offset = ($page - 1) * $perPage;
$overdueBooks = getPenalties($userId, $perPage, $offset);
$totalBooks = getPenaltiesCount($userId);
$totalPages = ceil($totalBooks / $perPage);
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

    <!-- Penalty Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Book Name</th>
                    <th>Date Borrowed</th>
                    <th>Date of Return</th>
                    <th>Days Overdue</th>
                    <th>Penalty Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="penalty-table-body">
                <?php foreach($overdueBooks as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></td>
                    <td><?php echo date('M d, Y', strtotime($book['return_date'])); ?></td>
                    <td><?php echo htmlspecialchars($book['days_overdue']); ?> days</td>
                    <td>₱<?php echo number_format($book['penalty_amount'], 2); ?></td>
                    <td>
                        <span class="badge bg-danger">
                            <?php echo htmlspecialchars($book['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Total Penalty Summary -->
    <div class="mt-4 p-3 bg-light rounded">
        <h5>Total Penalty Summary</h5>
        <div class="row">
            <div class="col-md-4">
                <strong>Total Overdue Books:</strong> <?php echo $totalBooks; ?>
            </div>
            <div class="col-md-4">
                <strong>Total Penalty Amount:</strong> ₱<?php
                    $totalPenalty = $totalBooks * 50; // Assuming fixed penalty of 50 per book
                    echo number_format($totalPenalty, 2);
                ?>
            </div>
            <div class="col-md-4">
                <strong>Average Days Overdue:</strong> <?php
                    $avgDays = getAverageDaysOverdue($userId);
                    echo round($avgDays, 1) . ' days';
                ?>
            </div>
        </div>
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

<script>
// No JavaScript needed for penalty page
</script>
