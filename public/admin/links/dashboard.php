<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/profileData.php';
include '../../back-end/read/readStudents.php';
include '../../back-end/read/PendingAccounts.php';

$currentPage = 'Dashboard';

$teachersCount = getTeachersCount();
$librariansCount = getLibrariansCount();

// Get pending accounts counts
$pendingTeachers = getPendingTeachers();
$pendingLibrarians = getPendingLibrarians();
$pendingAccountsCount = count($pendingTeachers) + count($pendingLibrarians);

// Get teacher's programs and student counts
$programs = !empty($program) ? explode(',', $program) : [];
$studentCounts = getStudentCounts($programs);

$stats = [
    ['title' => 'Teachers Count', 'value' => $teachersCount, 'subtitle' => 'Total teacher accounts', 'icon' => 'bi-person-badge', 'iconClass' => 'icon-blue', 'link' => 'teachers.php'],
    ['title' => 'Librarian Count', 'value' => $librariansCount, 'subtitle' => 'Total librarian accounts', 'icon' => 'bi-book-half', 'iconClass' => 'icon-green', 'link' => 'librarian.php'],
    ['title' => 'Pending Accounts', 'value' => $pendingAccountsCount, 'subtitle' => 'Accounts awaiting approval', 'icon' => 'bi-clock', 'iconClass' => 'icon-yellow', 'link' => 'teachers.php'],
    ['title' => 'Your Profile', 'value' => htmlspecialchars($username), 'subtitle' => ucfirst($user_type), 'icon' => 'bi-person', 'iconClass' => 'icon-orange', 'link' => 'profile.php']
];


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
            <a href="<?php echo isset($stat['link']) ? $stat['link'] : '#'; ?>" class="text-decoration-none">
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
            </a>
        </div>
        <?php endforeach; ?>
    </div>



    <!-- Admin Rules and Guides -->
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Admin Rules and Guides</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Review and approve pending teacher and librarian accounts promptly.</li>
                        <li class="list-group-item">Monitor system usage and ensure data security and privacy.</li>
                        <li class="list-group-item">Oversee the management of books and modules uploaded by librarians.</li>
                        <li class="list-group-item">Handle user complaints and support requests efficiently.</li>
                        <li class="list-group-item">Maintain accurate records of all library resources and user activities.</li>
                        <li class="list-group-item">Ensure compliance with institutional policies and regulations.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>