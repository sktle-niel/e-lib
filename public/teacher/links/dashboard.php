<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/profileData.php';
include '../../back-end/read/readModules.php';
include '../../back-end/read/readBooks.php';
include '../../back-end/read/readStudents.php';

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

$uploadedBooks = [
    ['title' => 'Introduction to Algorithms', 'author' => 'Cormen et al.', 'cover' => 'https://via.placeholder.com/150x200/11998e/ffffff?text=Algo', 'available' => true],
    ['title' => 'Computer Networks', 'author' => 'Andrew Tanenbaum', 'cover' => 'https://via.placeholder.com/150x200/38ef7d/000000?text=Networks', 'available' => true],
    ['title' => 'Database System Concepts', 'author' => 'Silberschatz et al.', 'cover' => 'https://via.placeholder.com/150x200/f093fb/000000?text=DB', 'available' => false],
    ['title' => 'Operating Systems', 'author' => 'William Stallings', 'cover' => 'https://via.placeholder.com/150x200/f5576c/ffffff?text=OS', 'available' => true]
];
?>

<link rel="stylesheet" href="../../src/css/dashboard.css">

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
        <!-- Uploaded Books -->
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Uploaded Books</h5>
                    </div>
                    <div class="row g-3">
                        <?php foreach($uploadedBooks as $book): ?>
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="<?php echo $book['cover']; ?>" class="card-img-top" alt="<?php echo $book['title']; ?>" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h6 class="card-title fw-bold mb-1"><?php echo $book['title']; ?></h6>
                                    <p class="card-text text-muted small mb-2"><?php echo $book['author']; ?></p>
                                    <div class="d-flex justify-content-end">
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary me-1" title="View">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success me-1" title="Download" <?php echo !$book['available'] ? 'disabled' : ''; ?>>
                                                <i class="bi bi-download"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>