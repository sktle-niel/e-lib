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
$uploadYearFilter = isset($_GET['upload_year']) ? (int)$_GET['upload_year'] : '';
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$perPage = 15;

// Get books from database with filters
function getFilteredBooksPaginated($search, $course, $publishYear, $uploadYear, $page, $perPage) {
    global $conn;
    
    $offset = ($page - 1) * $perPage;
    $conditions = [];
    $params = [];
    $types = '';
    
    // Build WHERE clause
    if (!empty($search)) {
        $conditions[] = "(book_title LIKE ? OR author LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
    }
    
    if (!empty($course)) {
        $conditions[] = "book_course = ?";
        $params[] = $course;
        $types .= 's';
    }
    
    if (!empty($publishYear)) {
        $conditions[] = "YEAR(publish_date) = ?";
        $params[] = $publishYear;
        $types .= 'i';
    }
    
    if (!empty($uploadYear)) {
        $conditions[] = "YEAR(created_at) = ?";
        $params[] = $uploadYear;
        $types .= 'i';
    }
    
    $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM lib_books {$whereClause}";
    $countStmt = $conn->prepare($countSql);
    
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalBooks = $countResult->fetch_assoc()['total'];
    $countStmt->close();
    
    // Get books
    $sql = "SELECT id, book_title, book_course, author, publish_date, created_at, status
            FROM lib_books
            {$whereClause}
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?";
    
    $params[] = $perPage;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    
    $stmt->close();
    
    return [
        'books' => $books,
        'total' => $totalBooks
    ];
}

$result = getFilteredBooksPaginated($searchQuery, $courseFilter, $publishYearFilter, $uploadYearFilter, $page, $perPage);
$initialBooks = $result['books'];
$totalBooks = $result['total'];
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

    <!-- Search Form -->
    <div class="mb-4">
        <form id="filterForm" method="GET" action="" class="row g-3">
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
                    <option value="">Publish Year</option>
                    <?php for ($y = 2024; $y >= 2000; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo $publishYearFilter === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="upload_year" id="upload_year" class="form-select">
                    <option value="">Upload Year</option>
                    <?php for ($y = 2026; $y >= 2020; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo $uploadYearFilter === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if ($searchQuery || $courseFilter || $publishYearFilter || $uploadYearFilter): ?>
                    <button type="button" id="clearFiltersBtn" class="btn btn-outline-secondary">Clear</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Results count -->
    <div class="mb-3">
        <p class="text-muted">
            Found <strong><?php echo $totalBooks; ?></strong> book<?php echo $totalBooks !== 1 ? 's' : ''; ?>
            <?php if ($searchQuery || $courseFilter || $publishYearFilter || $uploadYearFilter): ?>
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
                    <th>Publish Date</th>
                    <th>Date Added</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="books-table-body">
                <?php if (empty($initialBooks)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No books found</p>
                            <?php if ($searchQuery || $courseFilter || $publishYearFilter || $uploadYearFilter): ?>
                                <button type="button" id="clearFiltersBtn2" class="btn btn-sm btn-outline-primary mt-2">Clear Filters</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($initialBooks as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($book['book_course']); ?></span></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['publish_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['created_at'])); ?></td>
                        <td>
                            <?php if (strtolower($book['status']) === 'available'): ?>
                                <span class="badge bg-success">Available</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Not Available</span>
                            <?php endif; ?>
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
                <a class="page-link pagination-link" href="#" data-page="<?php echo $page - 1; ?>" tabindex="-1">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
            </li>

            <!-- Page numbers -->
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);

            if ($startPage > 1):
            ?>
            <li class="page-item">
                <a class="page-link pagination-link" href="#" data-page="1">1</a>
            </li>
            <?php if ($startPage > 2): ?>
            <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link pagination-link" href="#" data-page="<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>

            <?php if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?>
            <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link pagination-link" href="#" data-page="<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
            </li>
            <?php endif; ?>

            <!-- Next button -->
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link pagination-link" href="#" data-page="<?php echo $page + 1; ?>">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- Borrow Confirmation Modal -->
<div class="modal fade" id="borrowBookModal" tabindex="-1" aria-labelledby="borrowBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="borrowBookModalLabel">Confirm Borrow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to borrow "<span id="borrowBookTitle"></span>"?</p>
                <input type="hidden" id="borrowBookId">
                <div class="mb-3">
                    <label for="returnDate" class="form-label">Expected Return Date</label>
                    <input type="date" class="form-control" id="returnDate" required>
                </div>
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
    const booksTableBody = document.getElementById('books-table-body');
    const filterForm = document.getElementById('filterForm');

    // Set minimum return date to tomorrow
    const returnDateInput = document.getElementById('returnDate');
    if (returnDateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        returnDateInput.min = tomorrow.toISOString().split('T')[0];
        
        // Set default to 7 days from now
        const defaultReturn = new Date();
        defaultReturn.setDate(defaultReturn.getDate() + 7);
        returnDateInput.value = defaultReturn.toISOString().split('T')[0];
    }

    // Handle filter form submission
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Get current URL
        const currentUrl = new URL(window.location.href);

        // Get form data
        const formData = new FormData(this);

        // Update URL parameters
        const search = formData.get('search');
        const course = formData.get('course');
        const publishYear = formData.get('publish_year');
        const uploadYear = formData.get('upload_year');

        if (search) {
            currentUrl.searchParams.set('search', search);
        } else {
            currentUrl.searchParams.delete('search');
        }

        if (course) {
            currentUrl.searchParams.set('course', course);
        } else {
            currentUrl.searchParams.delete('course');
        }

        if (publishYear) {
            currentUrl.searchParams.set('publish_year', publishYear);
        } else {
            currentUrl.searchParams.delete('publish_year');
        }

        if (uploadYear) {
            currentUrl.searchParams.set('upload_year', uploadYear);
        } else {
            currentUrl.searchParams.delete('upload_year');
        }

        // Reset to page 1 when filtering
        currentUrl.searchParams.set('p', '1');

        // Reload with new parameters (preserve existing page parameter)
        window.location.href = currentUrl.toString();
    });

    // Handle clear filters button
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const clearFiltersBtn2 = document.getElementById('clearFiltersBtn2');
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('search');
            currentUrl.searchParams.delete('course');
            currentUrl.searchParams.delete('publish_year');
            currentUrl.searchParams.delete('upload_year');
            currentUrl.searchParams.delete('p');
            window.location.href = currentUrl.toString();
        });
    }
    
    if (clearFiltersBtn2) {
        clearFiltersBtn2.addEventListener('click', function() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('search');
            currentUrl.searchParams.delete('course');
            currentUrl.searchParams.delete('publish_year');
            currentUrl.searchParams.delete('upload_year');
            currentUrl.searchParams.delete('p');
            window.location.href = currentUrl.toString();
        });
    }

    // Handle pagination links
    document.querySelectorAll('.pagination-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.closest('.page-item').classList.contains('disabled')) {
                return;
            }
            
            const pageNum = this.dataset.page;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('p', pageNum);
            window.location.href = currentUrl.toString();
        });
    });

    // Handle borrow button clicks
    booksTableBody.addEventListener('click', function(e) {
        const borrowBtn = e.target.closest('.btn-borrow');
        
        if (borrowBtn) {
            const bookId = borrowBtn.dataset.bookId;
            const bookTitle = borrowBtn.dataset.bookTitle;

            // Populate modal
            document.getElementById('borrowBookId').value = bookId;
            document.getElementById('borrowBookTitle').textContent = bookTitle;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('borrowBookModal'));
            modal.show();
        }
    });

    // Handle confirm borrow
    document.getElementById('confirmBorrowBtn').addEventListener('click', function() {
        const bookId = document.getElementById('borrowBookId').value;
        const returnDate = document.getElementById('returnDate').value;
        const btn = this;
        const originalText = btn.innerHTML;

        if (!returnDate) {
            alert('Please select a return date');
            return;
        }

        // Disable button
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        fetch('../../../back-end/create/borrowBook.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'book_id=' + encodeURIComponent(bookId) + '&return_date=' + encodeURIComponent(returnDate)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Book borrowed successfully!');
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('borrowBookModal'));
                modal.hide();
                // Optionally reload to update availability
                // location.reload();
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
});
</script>