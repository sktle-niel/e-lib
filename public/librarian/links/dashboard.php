<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/countLibBooks.php';
include '../../back-end/read/countBorrowedLibBooks.php';
include '../../back-end/read/countBookPenalties.php';
include '../../back-end/read/countStudents.php';
include '../../back-end/read/readPenalties.php';
include '../../back-end/read/readBorrowedBooks.php';

$stats = [
    ['title' => 'Available Books', 'value' => getLibBooksCount(), 'subtitle' => 'Available Library Books', 'icon' => 'bi-book', 'iconClass' => 'icon-green'],
    ['title' => 'Borrowed Books', 'value' => getBorrowedLibBooksCount(), 'subtitle' => 'Total borrowed books', 'icon' => 'bi-file-earmark-text', 'iconClass' => 'icon-blue'],
    ['title' => 'Overdue Books', 'value' => getBookPenaltiesCount(), 'subtitle' => 'Books overdue by more than 3 days', 'icon' => 'bi-exclamation-triangle', 'iconClass' => 'icon-red'],
    ['title' => 'Total Students', 'value' => getStudentsCount(), 'subtitle' => 'Registered students', 'icon' => 'bi-people', 'iconClass' => 'icon-orange']
];

// Get recent penalties (overdue books)
$recentPenalties = getPenalties(null, 5, 0);

// Get recently borrowed books
$recentBorrowed = getAllBorrowedBooks(5, 0);
?>

<link rel="stylesheet" href="../../src/css/phoneMediaQuery.css">

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $currentPage; ?></h1>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <?php foreach($stats as $stat): ?>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-muted mb-2"><?php echo $stat['title']; ?></h6>
                            <h2 class="fw-bold mb-1"><?php echo $stat['value']; ?></h2>
                            <small class="text-muted"><?php echo $stat['subtitle']; ?></small>
                        </div>
                        <div class="stat-icon <?php echo $stat['iconClass']; ?>">
                            <i class="<?php echo $stat['icon']; ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Content Grid -->
    <div class="row g-4">
        <!-- Recent Penalties -->
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Recent Penalties</h5>
                        <a href="?page=penalties" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>

                    <?php if (empty($recentPenalties)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745;"></i>
                            <p class="text-muted mt-3">No overdue books at the moment!</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Book Title</th>
                                        <th>Borrower</th>
                                        <th>Days Overdue</th>
                                        <th>Penalty Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recentPenalties as $penalty): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($penalty['title']); ?></td>
                                        <td><?php echo htmlspecialchars($penalty['borrower_name']); ?></td>
                                        <td><?php echo htmlspecialchars($penalty['days_overdue']); ?> days</td>
                                        <td>₱<?php echo number_format($penalty['penalty_amount'], 2); ?></td>
                                        <td><span class="badge bg-danger">Overdue</span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Borrowed Books -->
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Recent Borrowed Books</h5>
                    <?php if (empty($recentBorrowed)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-book" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No recent borrowings</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($recentBorrowed as $book): ?>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars($book['title']); ?></h6>
                                <p class="card-text text-muted small mb-1"><?php echo htmlspecialchars($book['borrower_name']); ?></p>
                                <small class="text-muted">Borrowed: <?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></small>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                <span class="badge bg-<?php echo $book['status'] === 'Borrowed' ? 'warning' : 'success'; ?> text-white mb-1">
                                    <?php echo $book['status']; ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


