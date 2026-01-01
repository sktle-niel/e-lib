<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <i class="bi bi-book-fill"></i> Libro
    </div>
    <div class="mt-3">
        <a href="?page=dashboard" class="nav-item <?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="?page=books" class="nav-item <?php echo $currentPage == 'books' ? 'active' : ''; ?>">
            <i class="bi bi-book"></i> Books
        </a>
        <a href="?page=downloads" class="nav-item <?php echo $currentPage == 'downloads' ? 'active' : ''; ?>">
            <i class="bi bi-download"></i> Downloads
        </a>
        <a href="?page=borrowed" class="nav-item <?php echo $currentPage == 'borrowed' ? 'active' : ''; ?>">
            <i class="bi bi-book-half"></i> Borrowed Books
        </a>
        <a href="?page=profile" class="nav-item <?php echo $currentPage == 'profile' ? 'active' : ''; ?>">
            <i class="bi bi-person"></i> Profile
        </a>
    </div>

    <div class="admin-section">
        <div class="admin-profile">
            <div class="admin-avatar">S</div>
            <div>
                <div class="fw-bold">STUDENT</div>
                <small class="opacity-75">student@libro.com</small>
            </div>
        </div>
        <button class="btn logout-btn">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </button>
    </div>
</div>
