<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/readLibBooks.php';
$currentPage = 'Add Library Book';
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
        display: none;
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
                <label for="author" class="form-label">Author</label>
                <input type="text" name="author" id="author" class="form-control" placeholder="Enter author..." required>
            </div>
            <div class="col-md-2">
                <label for="publish_date" class="form-label">Publish Date</label>
                <input type="date" name="publish_date" id="publish_date" class="form-control" placeholder="Publish date..." required>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i>Add Book
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Books Table -->
    <div class="mb-4">
        <h3 class="mb-3">Recent Added Books</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Publish Date</th>
                        <th>Added At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recentBooks = getRecentLibBooks(10);
                    if (empty($recentBooks)) {
                        echo '<tr><td colspan="6" class="text-center">No books found</td></tr>';
                    } else {
                        foreach ($recentBooks as $book) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($book['id']) . '</td>';
                            echo '<td>' . htmlspecialchars($book['book_title']) . '</td>';
                            echo '<td>' . htmlspecialchars($book['author']) . '</td>';
                            echo '<td>' . htmlspecialchars($book['publish_date']) . '</td>';
                            echo '<td>' . htmlspecialchars($book['created_at']) . '</td>';
                            echo '<td>';
                            echo '<button class="btn btn-sm btn-outline-warning me-1 edit-btn" title="Edit" data-book-id="' . htmlspecialchars($book['id']) . '" data-book-title="' . htmlspecialchars($book['book_title']) . '" data-author="' . htmlspecialchars($book['author']) . '" data-publish-date="' . htmlspecialchars($book['publish_date']) . '"><i class="bi bi-pencil"></i></button>';
                            echo '<button class="btn btn-sm btn-outline-danger delete-btn" title="Delete" data-book-id="' . htmlspecialchars($book['id']) . '" data-book-title="' . htmlspecialchars($book['book_title']) . '"><i class="bi bi-trash"></i></button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="success-message" class="success-message">Book added successfully!</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBookModal" tabindex="-1" aria-labelledby="deleteBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBookModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the book "<span id="deleteBookTitle"></span>"? This action cannot be undone.</p>
                <input type="hidden" id="deleteBookId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Book</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Book Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBookForm">
                    <input type="hidden" id="edit_book_id" name="book_id">
                    <div class="mb-3">
                        <label for="edit_book_title" class="form-label">Book Title</label>
                        <input type="text" name="book_title" id="edit_book_title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_author" class="form-label">Author</label>
                        <input type="text" name="author" id="edit_author" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_publish_date" class="form-label">Publish Date</label>
                        <input type="date" name="publish_date" id="edit_publish_date" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle upload book form submission
    const uploadForm = document.getElementById('uploadBookForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Adding...';
            
            fetch('../../../back-end/create/uploadLibBooks.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Response:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        const msg = document.getElementById("success-message");
                        msg.textContent = "Book added successfully!";
                        msg.style.display = "block";
                        msg.style.opacity = "1";
                        
                        uploadForm.reset();
                        
                        // Reload page to show new book in table
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response text:', text);
                    alert('Server returned invalid response. Check console for details.');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert('An error occurred: ' + error.message);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Handle edit button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.edit-btn');
            const bookId = btn.dataset.bookId;
            const bookTitle = btn.dataset.bookTitle;
            const author = btn.dataset.author;
            const publishDate = btn.dataset.publishDate;

            // Populate modal
            document.getElementById('edit_book_id').value = bookId;
            document.getElementById('edit_book_title').value = bookTitle;
            document.getElementById('edit_author').value = author;
            document.getElementById('edit_publish_date').value = publishDate;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editBookModal'));
            modal.show();
        }
    });

    // Handle save edit button
    document.getElementById('saveEditBtn').addEventListener('click', function() {
        const form = document.getElementById('editBookForm');
        const formData = new FormData(form);
        const btn = this;
        const originalText = btn.innerHTML;

        // Disable button
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';

        fetch('../../../back-end/update/editLibBooks.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editBookModal'));
                modal.hide();
                
                // Show success message
                const msg = document.getElementById("success-message");
                msg.textContent = "Book updated successfully!";
                msg.style.display = "block";
                msg.style.opacity = "1";
                
                // Reload page to show updated data
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the book: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // Handle delete button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.delete-btn');
            const bookId = btn.dataset.bookId;
            const bookTitle = btn.dataset.bookTitle;

            // Populate modal
            document.getElementById('deleteBookId').value = bookId;
            document.getElementById('deleteBookTitle').textContent = bookTitle;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('deleteBookModal'));
            modal.show();
        }
    });

    // Handle confirm delete button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const bookId = document.getElementById('deleteBookId').value;
        const btn = this;
        const originalText = btn.innerHTML;

        // Disable button
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

        fetch('../../../back-end/delete/deleteLibBook.php', {
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
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteBookModal'));
                modal.hide();
                
                // Show success message
                const msg = document.getElementById("success-message");
                msg.textContent = "Book deleted successfully!";
                msg.style.display = "block";
                msg.style.opacity = "1";
                
                // Remove the row from the table
                const deleteBtn = document.querySelector(`.delete-btn[data-book-id="${bookId}"]`);
                if (deleteBtn) {
                    const row = deleteBtn.closest('tr');
                    row.style.transition = 'opacity 0.5s';
                    row.style.opacity = '0';
                    setTimeout(function() {
                        row.remove();
                        
                        // Check if table is now empty
                        const tbody = document.querySelector('.table tbody');
                        if (tbody.querySelectorAll('tr').length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No books found</td></tr>';
                        }
                    }, 500);
                }
                
                // Hide success message after 3 seconds
                setTimeout(function() {
                    msg.style.opacity = "0";
                    setTimeout(function() {
                        msg.style.display = "none";
                    }, 1000);
                }, 3000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the book: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
});
</script>