<?php
include '../../back-end/read/sidebarProfile.php';
?>

<!-- Sidebar -->
<style>
    :root {
  --primary-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  --dark-gradient: linear-gradient(135deg, #0e8074 0%, #2dd468 100%);
  --success: #28a745;
  --text-dark: #333;
  --text-light: #fff;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  background: #f8f9fa;
  margin: 0;
  padding: 0;
}

.sidebar {
  width: 260px;
  background: var(--primary-gradient);
  color: var(--text-light);
  position: fixed;
  height: 100vh;
  left: 0;
  top: 0;
  overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
  transition: width 0.3s ease;
  z-index: 1050;
}

.sidebar.collapsed {
  width: 80px;
}

.sidebar.collapsed .nav-text {
  display: none;
}

.sidebar.collapsed .nav-item {
  justify-content: center;
  padding: 14px 0;
}

.sidebar.collapsed .logo-text {
  display: none;
}

.sidebar.collapsed .logo {
  justify-content: center;
  padding: 24px 0;
}

.sidebar.collapsed .logo-content {
  display: none;
}

.logo {
  padding: 24px;
  font-size: 28px;
  font-weight: 700;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  gap: 10px;
}

.nav-item {
  padding: 14px 24px;
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: all 0.3s;
  border-left: 3px solid transparent;
  cursor: pointer;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: var(--text-light);
  border-left-color: var(--text-light);
}

.nav-item.active {
  background: rgba(255, 255, 255, 0.15);
  color: var(--text-light);
  border-left-color: var(--text-light);
  font-weight: 600;
}

.profile-section {
  position: absolute;
  bottom: 20px;
  left: 24px;
  right: 24px;
}

.profile-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  margin-bottom: 12px;
}

.profile-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--text-light);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  color: #11998e;
  flex-shrink: 0;
}

.logout-btn {
  width: 100%;
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: var(--text-light);
  padding: 10px;
  border-radius: 8px;
  transition: all 0.3s;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  color: var(--text-light);
}

.main-content {
  margin-left: 260px;
  padding: 30px;
  transition: margin-left 0.3s ease;
  min-height: 100vh;
}

.stat-card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s, box-shadow 0.3s;
  height: 100%;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
}

.icon-green {
  background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  color: var(--text-light);
}

.icon-red {
  background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
  color: var(--text-light);
}

.icon-orange {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: var(--text-light);
}

.icon-blue {
  background: var(--success);
  color: var(--text-light);
}

.card-custom {
  border: none;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.tab-custom {
  border-bottom: 2px solid #dee2e6;
}

.nav-link-custom {
  color: #6c757d;
  padding: 10px 20px;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
}

.nav-link-custom.active {
  color: #11998e;
  border-bottom-color: #11998e;
  font-weight: 600;
}

.notification-item,
.activity-item {
  padding: 16px 0;
  border-bottom: 1px solid #e9ecef;
}

.notification-item:last-child,
.activity-item:last-child {
  border-bottom: none;
}

.donut-chart {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background: conic-gradient(
    #28a745 0deg 160deg,
    #11998e 160deg 280deg,
    #dc3545 280deg 320deg,
    #ffc107 320deg 360deg
  );
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.donut-inner {
  width: 120px;
  height: 120px;
  background: white;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.legend-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
}

.order-item {
  padding: 16px;
  border-left: 4px solid;
  border-radius: 8px;
  margin-bottom: 12px;
}

.activity-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--primary-gradient);
  color: var(--text-light);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  flex-shrink: 0;
}

.breadcrumb-custom {
  background: none;
  padding: 0;
  margin-bottom: 8px;
  font-size: 14px;
}

.page-title {
  color: var(--text-dark);
  font-weight: 700;
  margin-bottom: 24px;
}

.select-custom {
  border-radius: 8px;
  border: 1px solid #dee2e6;
  padding: 8px 16px;
}

/* Mobile Menu Button */
.mobile-menu-btn {
  display: none;
  position: fixed;
  top: 15px;
  left: 15px;
  z-index: 1060;
  background: var(--primary-gradient);
  color: white;
  border: none;
  width: 45px;
  height: 45px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  align-items: center;
  justify-content: center;
  font-size: 24px;
  cursor: pointer;
}

/* Mobile Overlay */
.mobile-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1040;
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
}

.mobile-overlay.show {
  opacity: 1;
  pointer-events: auto;
}

/* ============================================
   MOBILE RESPONSIVE STYLES - SIDEBAR
   ============================================ */

/* Tablet and below (768px) */
@media (max-width: 768px) {
  /* Show mobile menu button */
  .mobile-menu-btn {
    display: flex;
  }

  .mobile-overlay {
    display: block;
  }

  /* Sidebar stays fixed on left but hidden */
  .sidebar {
    width: 280px;
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }

  /* Show sidebar when menu is open */
  .sidebar.mobile-open {
    transform: translateX(0);
  }

  /* Disable desktop collapsed state on mobile */
  .sidebar.collapsed {
    width: 280px;
  }

  .sidebar.collapsed .nav-text,
  .sidebar.collapsed .logo-text,
  .sidebar.collapsed .logo-content {
    display: inline;
  }

  .sidebar.collapsed .nav-item {
    justify-content: flex-start;
    padding: 12px 20px;
  }

  .sidebar.collapsed .logo {
    justify-content: flex-start;
    padding: 20px;
  }

  /* Hide desktop collapse button */
  .logo button {
    display: none !important;
  }

  .logo {
    padding: 20px;
    font-size: 24px;
  }

  .nav-item {
    padding: 12px 20px;
    font-size: 15px;
  }

  .profile-section {
    position: static;
    margin: 15px 20px 20px;
  }

  /* Main content takes full width */
  .main-content {
    margin-left: 0;
    padding: 80px 15px 20px 15px;
  }
}

/* Phone only (576px and below) */
@media (max-width: 576px) {
  .mobile-menu-btn {
    width: 42px;
    height: 42px;
    font-size: 22px;
    top: 12px;
    left: 12px;
  }

  .sidebar {
    width: 260px;
  }

  .logo {
    padding: 16px;
    font-size: 20px;
  }

  .nav-item {
    padding: 10px 16px;
    font-size: 14px;
  }

  .nav-item i {
    font-size: 18px;
  }

  .profile-section {
    margin: 12px 16px 16px;
  }

  .profile-profile {
    padding: 10px;
  }

  .profile-avatar {
    width: 42px;
    height: 42px;
  }

  .logout-btn {
    padding: 8px;
    font-size: 13px;
  }

  .main-content {
    padding: 70px 10px 15px 10px;
  }
}

/* Extra small phones (375px and below) */
@media (max-width: 375px) {
  .sidebar {
    width: 240px;
  }

  .logo {
    padding: 14px;
    font-size: 18px;
  }

  .nav-item {
    padding: 9px 14px;
    font-size: 13px;
  }

  .profile-avatar {
    width: 38px;
    height: 38px;
  }
}

</style>
<!-- Mobile Menu Button (Only visible on mobile) -->
<button class="mobile-menu-btn d-lg-none" id="mobileMenuBtn" type="button">
    <i class="bi bi-list"></i>
</button>

<!-- Mobile Overlay -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column" id="sidebar">
    <div class="logo d-flex align-items-center">
        <button class="btn btn-link text-white p-0 me-2 d-none d-lg-block" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarContent" aria-expanded="true" aria-controls="sidebarContent" style="font-size: 35px;">
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
        <a href="?page=add_book" class="nav-item <?php echo $currentPage == 'add_book' ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle"></i> <span class="nav-text">Add Book</span>
        </a>
        <a href="?page=book_list" class="nav-item <?php echo $currentPage == 'book_list' ? 'active' : ''; ?>">
            <i class="bi bi-bookmark-plus"></i> <span class="nav-text">List of Books</span>
        </a>
        <a href="?page=borrowed_list" class="nav-item <?php echo $currentPage == 'borrowed_list' ? 'active' : ''; ?>">
            <i class="bi bi-bookmark-check"></i> <span class="nav-text">List of Borrowed Books</span>
        </a>
        <a href="?page=history" class="nav-item <?php echo $currentPage == 'history' ? 'active' : ''; ?>">
            <i class="bi bi-clock-history"></i> <span class="nav-text">History</span>
        </a>
        <a href="?page=penalties" class="nav-item <?php echo $currentPage == 'penalties' ? 'active' : ''; ?>">
            <i class="bi bi-exclamation-triangle"></i> <span class="nav-text">List of Penalties</span>
        </a>
        <a href="?page=profile" class="nav-item <?php echo $currentPage == 'profile' ? 'active' : ''; ?>">
            <i class="bi bi-person"></i> <span class="nav-text">Profile</span>
        </a>
    </div>

    <div class="collapse show" id="sidebarContent">
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
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileOverlay = document.getElementById('mobileOverlay');

    // Desktop: Listen for Bootstrap collapse events
    sidebarCollapse.addEventListener('shown.bs.collapse', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('collapsed');
            if (mainContent) {
                mainContent.style.marginLeft = '260px';
            }
        }
    });

    sidebarCollapse.addEventListener('hidden.bs.collapse', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.add('collapsed');
            if (mainContent) {
                mainContent.style.marginLeft = '80px';
            }
        }
    });

    // Mobile: Toggle sidebar
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                mobileOverlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
            }
        });
    }

    // Close sidebar when clicking overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('show');
            document.body.style.overflow = '';
        });
    }

    // Close sidebar when clicking a nav item on mobile
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('mobile-open');
                mobileOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    });

    // Logout functionality
    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', function() {
        window.location.href = '../../auth/logout.php';
    });
});
</script>