<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/profileData.php';
include '../../back-end/read/readModules.php';
include '../../back-end/read/readBooks.php';
include '../../back-end/read/readStudents.php';

$currentPage = 'Dashboard';

$modulesCount = getModulesCount();
$booksCount = getBooksCount();

// Get teacher's programs and student counts
$programs = explode(',', $program);
$studentCounts = getStudentCounts($programs);

$stats = [
    ['title' => 'Modules Uploaded', 'value' => $modulesCount, 'subtitle' => 'Modules you have uploaded', 'icon' => 'bi-check-circle', 'iconClass' => 'icon-blue'],
    ['title' => 'Books Uploaded', 'value' => $booksCount, 'subtitle' => 'Books you have uploaded', 'icon' => 'bi-book', 'iconClass' => 'icon-green'],
    ['title' => 'Students Count', 'value' => '<div style="position:relative;"><div id="student-counts" style="display:inline;"></div><i id="next-programs" class="bi bi-chevron-right" style="cursor:pointer; display:none; position:absolute; right:0; top:50%; transform:translateY(-50%);"></i></div>', 'subtitle' => 'Students in your programs', 'icon' => 'bi-people', 'iconClass' => 'icon-red'],
    ['title' => 'Your Profile', 'value' => htmlspecialchars($username), 'subtitle' => ucfirst($user_type), 'icon' => 'bi-person', 'iconClass' => 'icon-orange']
];

// Fetch recent modules from database (limit to 4 for dashboard display)
$recentModules = getAllModules('', '', '', 4, 0);

// Fetch real books from database (limit to 4 for dashboard display)
$uploadedBooks = getAllBooks('', '', '', '', 4, 0);
?>

<link rel="stylesheet" href="../../src/css/phoneMediaQuery.css">

<style>
#next-programs {
    position: absolute;
    right: -60px !important;
    top: 50%;
    transform: translateY(-50%) !important;
    cursor: pointer;
}
</style>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $currentPage; ?></h1>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentCounts = <?php echo json_encode($studentCounts); ?>;
    const programs = Object.keys(studentCounts);
    let currentIndex = 0;
    const displayDiv = document.getElementById('student-counts');
    const nextButton = document.getElementById('next-programs');

    function displayPrograms() {
        const endIndex = Math.min(currentIndex + 2, programs.length);
        const displayedPrograms = programs.slice(currentIndex, endIndex);
        const formatted = displayedPrograms.map(prog => `${prog} ${studentCounts[prog]}`).join(', ');
        displayDiv.textContent = formatted;

        nextButton.style.display = 'inline-block';
    }

    nextButton.addEventListener('click', function() {
        currentIndex += 2;
        if (currentIndex >= programs.length) {
            currentIndex = 0;
        }
        displayPrograms();
    });

    displayPrograms();
});
</script>

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
        <!-- Recent Modules -->
        <div class="col-lg-12 mb-4">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Recent Uploaded Modules</h5>
                    </div>
                    <?php if (empty($recentModules)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No modules uploaded yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach($recentModules as $module): ?>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="<?php echo htmlspecialchars($module['cover'] ?: 'https://via.placeholder.com/150x200/6c757d/ffffff?text=No+Cover'); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($module['title']); ?>" 
                                         style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-3">
                                        <h6 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($module['title']); ?></h6>
                                        <p class="card-text text-muted small mb-1"><?php echo htmlspecialchars($module['course']); ?></p>
                                        <p class="card-text text-muted small mb-2">
                                            <i class="bi bi-calendar-event"></i> 
                                            <?php echo date('M d, Y', strtotime($module['uploadedDate'])); ?>
                                        </p>
                                        <div class="d-flex justify-content-end">
                                            <div>
                                                <a href="../../back-end/preview/previewModules.php?id=<?php echo $module['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="View" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="../../back-end/download/downloadModules.php?id=<?php echo $module['id']; ?>" class="btn btn-sm btn-outline-success me-1" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Uploaded Books -->
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Recent Uploaded Books</h5>
                    </div>
                    <?php if (empty($uploadedBooks)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-book" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No books uploaded yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach($uploadedBooks as $book): ?>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="<?php echo htmlspecialchars($book['cover'] ?: 'https://via.placeholder.com/150x200/6c757d/ffffff?text=No+Cover'); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                         style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-3">
                                        <h6 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($book['title']); ?></h6>
                                        <p class="card-text text-muted small mb-1">
                                            <?php echo htmlspecialchars($book['course']); ?> - <?php echo date('M d, Y', strtotime($book['created_at'])); ?>
                                        </p>
                                        <p class="card-text text-muted small mb-2">
                                            Author: <?php echo htmlspecialchars($book['author']); ?> | 
                                            Published: <?php echo date('M d, Y', strtotime($book['publish_date'])); ?>
                                        </p>
                                        <div class="d-flex justify-content-end">
                                            <div>
                                                <a href="../../back-end/preview/previewBooks.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="View" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="../../back-end/download/downloadBooks.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-success me-1"
                                                        title="Download"
                                                        <?php echo !$book['available'] ? 'style="pointer-events: none; opacity: 0.5;"' : ''; ?>>
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>