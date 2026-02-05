<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
$currentPage = 'Upload Books';

include '../../back-end/create/uploadBooks.php';
include '../../back-end/read/readBooks.php';
include '../../back-end/update/editBooks.php';
include '../../back-end/delete/removeBook.php';

// Get filter parameters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$yearFilter = isset($_GET['year']) ? (int)$_GET['year'] : '';
$publishYearFilter = isset($_GET['publish_year']) ? (int)$_GET['publish_year'] : '';

// Get total count for pagination
$totalBooks = getBooksCount($searchQuery, $courseFilter, $publishYearFilter, $yearFilter);
$hasMore = $totalBooks > 12;

// For initial load, show first 12 books
$initialBooks = getAllBooks($searchQuery, $courseFilter, $publishYearFilter, $yearFilter, 12, 0);
?>


<style>
/* Optional: Style for the file input to match your design */
.form-control[type="file"] {
    padding: 0.375rem 0.75rem;
}

.form-control[type="file"]::file-selector-button {
    padding: 0.375rem 0.75rem;
    margin-right: 0.75rem;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    cursor: pointer;
}

.form-control[type="file"]::file-selector-button:hover {
    background-color: #e9ecef;
}

.file-info {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

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
                <label for="cover_image" class="form-label">Cover Image</label>
                <input type="file" name="cover_image" id="cover_image" class="form-control" accept=".jpg,.jpeg,.png,.gif" required>
                <div class="file-info">JPG, PNG, or GIF (Max 5MB)</div>
            </div>
            <div class="col-md-2">
                <label for="book_file" class="form-label">Book File</label>
                <input type="file" name="book_file" id="book_file" class="form-control" accept=".pdf" required>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload me-2"></i>Upload Book
                </button>
            </div>
        </form>
    </div>

    <hr>

    <!-- Search Form -->
    <div class="mb-4">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="page" value="upload_books">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search books by title or author..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="col-md-2">
                <label for="courseFilter" class="form-label">Course</label>
                <select name="course" id="courseFilter" class="form-select">
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
                <label for="year" class="form-label">Upload Year</label>
                <select name="year" id="year" class="form-select">
                    <option value="">All Years</option>
                    <?php for ($y = 2000; $y <= 2026; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $yearFilter === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="publish_year" class="form-label">Publish Year</label>
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
                <?php if ($searchQuery || $courseFilter || $yearFilter || $publishYearFilter): ?>
                    <a href="?page=upload_books" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- All Books Grid -->
    <div id="books-container" class="row g-3">
        <?php foreach($initialBooks as $book): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 border-0 shadow-sm" data-book-id="<?php echo $book['id']; ?>">
                <img src="<?php echo htmlspecialchars($book['cover']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($book['title']); ?></h6>
                    <p class="card-text text-muted small mb-2"><?php echo htmlspecialchars($book['course']); ?> - <?php echo date('M d, Y', strtotime($book['created_at'])); ?></p>
                    <p class="card-text text-muted small mb-2">Author: <?php echo htmlspecialchars($book['author']); ?> | Published: <?php echo date('M d, Y', strtotime($book['publish_date'])); ?></p>
                    <div class="d-flex justify-content-end">
                        <div>
                            <a href="../../back-end/preview/previewBooks.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="View" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger me-1" title="Delete">
                                <i class="bi bi-trash"></i>
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

<div id="success-message" class="success-message" style="display:none;">
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBookModal" tabindex="-1" aria-labelledby="deleteBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="deleteBookModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the book "<span id="deleteBookTitle"></span>"?</p>
                <p class="text-muted small">This action cannot be undone. The book file and cover image will also be permanently deleted.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Book</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Book Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBookForm" enctype="multipart/form-data">
                    <input type="hidden" id="editBookId" name="book_id">
                    
                    <!-- Current Cover at the top -->
                    <div class="mb-4 text-center" id="currentCoverPreview">
                        <label class="form-label d-block fw-semibold mb-3">Current Cover</label>
                        <div class="d-flex justify-content-center">
                            <img id="currentCoverImg" src="" alt="Current cover" style="max-width: 250px; max-height: 200px; width: auto; height: auto; border: 1px solid #dee2e6; padding: 8px; border-radius: 8px; background: #f8f9fa;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editBookTitle" class="form-label">Book Title</label>
                        <input type="text" name="book_title" id="editBookTitle" class="form-control" placeholder="Enter book title..." required>
                    </div>
                    <div class="mb-3">
                        <label for="editBookCourse" class="form-label">Course</label>
                        <select name="book_course" id="editBookCourse" class="form-select" required>
                            <option value="">Select Course</option>
                            <option value="BSIT">BSIT</option>
                            <option value="BSIS">BSIS</option>
                            <option value="ACT">ACT</option>
                            <option value="SHS">SHS</option>
                            <option value="BSHM">BSHM</option>
                            <option value="BSOA">BSOA</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editAuthor" class="form-label">Author</label>
                        <input type="text" name="author" id="editAuthor" class="form-control" placeholder="Enter author..." required>
                    </div>
                    <div class="mb-3">
                        <label for="editPublishDate" class="form-label">Publish Date</label>
                        <input type="date" name="publish_date" id="editPublishDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCoverImage" class="form-label">New Cover Image (Optional)</label>
                        <input type="file" name="cover_image" id="editCoverImage" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                        <small class="text-muted d-block mt-1">Leave empty to keep current cover. JPG, PNG, or GIF (Max 5MB)</small>
                    </div>
                    
                    <!-- New Cover Preview -->
                    <div class="mb-3 text-center" id="newCoverPreview" style="display: none;">
                        <label class="form-label d-block fw-semibold mb-3">New Cover Preview</label>
                        <div class="d-flex justify-content-center">
                            <img id="newCoverImg" src="" alt="New cover preview" style="max-width: 250px; max-height: 200px; width: auto; height: auto; border: 1px solid #dee2e6; padding: 8px; border-radius: 8px; background: #f8f9fa;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditBookBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentOffset = 12; // Start after initial 12 books
    let hasMore = <?php echo $hasMore ? 'true' : 'false'; ?>;
    let searchQuery = '<?php echo addslashes($searchQuery); ?>';
    let courseFilter = '<?php echo addslashes($courseFilter); ?>';
    let yearFilter = '<?php echo $yearFilter; ?>';
    let publishYearFilter = '<?php echo $publishYearFilter; ?>';

    // Handle form submission
    const uploadForm = document.getElementById('uploadBookForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Validate file sizes
            const coverImage = document.getElementById('cover_image').files[0];
            if (coverImage && coverImage.size > 5 * 1024 * 1024) {
                alert('Cover image must be less than 5MB');
                return;
            }

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
                    uploadForm.reset();
                    const successMsg = document.getElementById('success-message');
                    successMsg.textContent = 'Book uploaded successfully!';
                    successMsg.style.display = 'block';
                    successMsg.style.opacity = '1';
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    alert('Upload failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during upload.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    function loadMoreBooks() {
        if (!hasMore) return;

        document.getElementById('loading').style.display = 'block';
        document.getElementById('load-more-container').style.display = 'none';

        const url = `../../back-end/read/loadMoreBooks.php?offset=${currentOffset}&search=${encodeURIComponent(searchQuery)}&course=${encodeURIComponent(courseFilter)}&publish_year=${publishYearFilter}&year=${yearFilter}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading').style.display = 'none';

                if (!data.success || data.books.length === 0) {
                    hasMore = false;
                    document.getElementById('no-more').style.display = 'block';
                    return;
                }

                const container = document.getElementById('books-container');
                
                // Escape HTML to prevent XSS
                const escapeHtml = (text) => {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                };
                
                data.books.forEach(book => {
                    const col = document.createElement('div');
                    col.className = 'col-lg-3 col-md-4 col-sm-6';
                    col.innerHTML = `
                        <div class="card h-100 border-0 shadow-sm" data-book-id="${escapeHtml(book.id)}">
                            <img src="${escapeHtml(book.cover)}" class="card-img-top" alt="${escapeHtml(book.title)}" style="height: 200px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold mb-1">${escapeHtml(book.title)}</h6>
                                <p class="card-text text-muted small mb-2">${escapeHtml(book.course)} - ${new Date(book.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                <p class="card-text text-muted small mb-2">Author: ${escapeHtml(book.author)} | Published: ${new Date(book.publish_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <a href="../../back-end/preview/previewBooks.php?id=${escapeHtml(book.id)}" class="btn btn-sm btn-outline-primary me-1" title="View" target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger me-1" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });

                currentOffset += data.books.length;

                // Check if there are more books
                if (data.books.length < 12 || !data.hasMore) {
                    hasMore = false;
                    document.getElementById('no-more').style.display = 'block';
                } else {
                    document.getElementById('load-more-container').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading more books:', error);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('load-more-container').style.display = 'block';
                alert('An error occurred while loading more books.');
            });
    }

    // Add event listener to Load More button
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            console.log('Load More button clicked');
            loadMoreBooks();
        });
    }

    // Handle edit button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-outline-warning')) {
            e.preventDefault();
            const card = e.target.closest('.card');
            const title = card.querySelector('.card-title').textContent;
            const course = card.querySelector('.card-text').textContent.split(' - ')[0];
            const secondLine = card.querySelectorAll('.card-text')[1].textContent;
            const author = secondLine.split(' | ')[0].replace('Author: ', '').trim();
            const publishDateStr = secondLine.split(' | ')[1].replace('Published: ', '').trim();
            const bookId = card.dataset.bookId;
            const coverSrc = card.querySelector('.card-img-top').src;

            // Convert publish date from 'M d, Y' to 'YYYY-MM-DD'
            const dateParts = publishDateStr.split(' ');
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const month = (monthNames.indexOf(dateParts[0]) + 1).toString().padStart(2, '0');
            const day = dateParts[1].replace(',', '').padStart(2, '0');
            const year = dateParts[2];
            const publishDate = `${year}-${month}-${day}`;

            // Populate modal
            document.getElementById('editBookId').value = bookId;
            document.getElementById('editBookTitle').value = title;
            document.getElementById('editBookCourse').value = course;
            document.getElementById('editAuthor').value = author;
            document.getElementById('editPublishDate').value = publishDate;
            
            // Show current cover
            document.getElementById('currentCoverImg').src = coverSrc;
            
            // Clear file input and hide new preview
            document.getElementById('editCoverImage').value = '';
            document.getElementById('newCoverPreview').style.display = 'none';

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editBookModal'));
            modal.show();
        }
    });

    // Handle cover image preview when file is selected
    document.getElementById('editCoverImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const newCoverPreview = document.getElementById('newCoverPreview');
        const newCoverImg = document.getElementById('newCoverImg');
        
        if (file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, or GIF)');
                e.target.value = '';
                newCoverPreview.style.display = 'none';
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Cover image must be less than 5MB');
                e.target.value = '';
                newCoverPreview.style.display = 'none';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(event) {
                newCoverImg.src = event.target.result;
                newCoverPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            newCoverPreview.style.display = 'none';
        }
    });

    // Handle save edit button
    document.getElementById('saveEditBookBtn').addEventListener('click', function() {
        const form = document.getElementById('editBookForm');
        const formData = new FormData(form);
        
        const saveBtn = this;
        const originalText = saveBtn.innerHTML;
        
        // Disable button and show loading
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        fetch('../../back-end/update/editBooks.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const successMsg = document.getElementById('success-message');
                successMsg.textContent = 'Book updated successfully!';
                successMsg.style.display = 'block';
                successMsg.style.opacity = '1';
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the book.');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        });
    });

    // Handle delete button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-outline-danger')) {
            e.preventDefault();
            const card = e.target.closest('.card');
            const bookId = card.dataset.bookId;
            const title = card.querySelector('.card-title').textContent;

            // Populate modal
            document.getElementById('deleteBookTitle').textContent = title;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('deleteBookModal'));
            modal.show();

            // Handle confirm delete button
            document.getElementById('confirmDeleteBtn').onclick = function() {
                modal.hide();

                const deleteBtn = this;
                const originalText = deleteBtn.innerHTML;

                // Disable button and show loading
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

                fetch('../../back-end/delete/removeBook.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'book_id=' + encodeURIComponent(bookId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const successMsg = document.getElementById('success-message');
                        successMsg.textContent = 'Book deleted successfully!';
                        successMsg.style.display = 'block';
                        successMsg.style.opacity = '1';
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the book.');
                })
                .finally(() => {
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = originalText;
                });
            };
        }
    });
});
</script>