<?php
include '../../back-end/read/sidebarProfile.php';
?>

<!-- Sidebar -->
<link rel="stylesheet" href="../../src/css/sidebar.css">
<div class="sidebar d-flex flex-column" id="sidebar">
    <div class="logo d-flex align-items-center">
        <button class="btn btn-link text-white p-0 me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarContent" aria-expanded="true" aria-controls="sidebarContent" style="font-size: 35px;">
        <i class="bi bi-list"></i>
        </button>
        <span class="logo-content">
            <i class="bi bi-book-fill"></i>
            <span class="ms-2 logo-text">PTCI</span>
        </span>
    </div>

    <div class="flex-grow-1">
        <a href="?page=dashboard" class="nav-item <?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i> <span class="nav-text">Dashboard</span>
        </a>
        <a href="?page=recent" class="nav-item <?php echo $currentPage == 'recent' ? 'active' : ''; ?>">
            <i class="bi bi-book-half"></i> <span class="nav-text">Recent</span>
        </a>
        <a href="?page=modules" class="nav-item <?php echo $currentPage == 'modules' ? 'active' : ''; ?>">
            <i class="bi bi-journal"></i> <span class="nav-text">Modules</span>
        </a>
        <a href="?page=books" class="nav-item <?php echo $currentPage == 'books' ? 'active' : ''; ?>">
            <i class="bi bi-book"></i> <span class="nav-text">Books</span>
        </a>
        <a href="?page=downloads" class="nav-item <?php echo $currentPage == 'downloads' ? 'active' : ''; ?>">
            <i class="bi bi-download"></i> <span class="nav-text">Downloads</span>
        </a>
        <a href="?page=profile" class="nav-item <?php echo $currentPage == 'profile' ? 'active' : ''; ?>">
            <i class="bi bi-person"></i> <span class="nav-text">Profile</span>
        </a>
    </div>

    <div class="collapse show mt-3" id="sidebarContent">
        <div class="d-flex flex-column h-100">
            <div class="profile-section mt-auto">
                <div class="profile-profile">
                    <div class="profile-avatar">
                        <?php if ($profilePicture): ?>
                            <img src="../../src/profile/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($_SESSION['firstname'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="fw-bold" style="text-transform: uppercase;"><?php echo $_SESSION['user_type']; ?></div>
                        <small class="opacity-75"><?php echo $_SESSION['firstname']; ?></small>
                    </div>
                </div>
                <button class="btn logout-btn" id="logoutBtn">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const sidebarCollapse = document.getElementById('sidebarContent');

    // Listen for Bootstrap collapse events
    sidebarCollapse.addEventListener('shown.bs.collapse', function() {
        sidebar.classList.remove('collapsed');
        if (mainContent) {
            mainContent.style.marginLeft = '260px';
        }
    });

    sidebarCollapse.addEventListener('hidden.bs.collapse', function() {
        sidebar.classList.add('collapsed');
        if (mainContent) {
            mainContent.style.marginLeft = '80px';
        }
    });

    // Logout functionality
    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', function() {
        window.location.href = '../../auth/logout.php';
    });
});
</script>
