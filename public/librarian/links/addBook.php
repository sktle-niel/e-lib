<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
$currentPage = 'Add Book';
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
            <div class="col-md-12">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i>Add Book
                </button>
            </div>
        </form>
    </div>
