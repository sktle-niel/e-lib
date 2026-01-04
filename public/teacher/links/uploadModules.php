<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
$currentPage = 'Upload Modules';

$allModules = [
    ['title' => 'Introduction to Programming', 'subject' => 'Computer Science', 'cover' => 'https://via.placeholder.com/150x200/11998e/ffffff?text=IntroProg', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Data Structures and Algorithms', 'subject' => 'Computer Science', 'cover' => 'https://via.placeholder.com/150x200/38ef7d/000000?text=DSA', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Database Management Systems', 'subject' => 'Information Systems', 'cover' => 'https://via.placeholder.com/150x200/f093fb/000000?text=DBMS', 'available' => false, 'course' => 'BSIS', 'year' => 2023],
    ['title' => 'Web Development Fundamentals', 'subject' => 'Computer Science', 'cover' => 'https://via.placeholder.com/150x200/f5576c/ffffff?text=WebDev', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Software Engineering Principles', 'subject' => 'Computer Science', 'cover' => 'https://via.placeholder.com/150x200/28a745/ffffff?text=SE', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Network Administration', 'subject' => 'Information Technology', 'cover' => 'https://via.placeholder.com/150x200/dc3545/ffffff?text=NetAdmin', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Information Systems Analysis', 'subject' => 'Information Systems', 'cover' => 'https://via.placeholder.com/150x200/ffc107/000000?text=ISA', 'available' => false, 'course' => 'BSIS', 'year' => 2023],
    ['title' => 'Mobile Application Development', 'subject' => 'Computer Science', 'cover' => 'https://via.placeholder.com/150x200/17a2b8/ffffff?text=Mobile', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Cybersecurity Basics', 'subject' => 'Information Technology', 'cover' => 'https://via.placeholder.com/150x200/6f42c1/ffffff?text=Cyber', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Business Process Management', 'subject' => 'Information Systems', 'cover' => 'https://via.placeholder.com/150x200/e83e8c/ffffff?text=BPM', 'available' => true, 'course' => 'BSIS', 'year' => 2023],
    ['title' => 'Artificial Intelligence Concepts', 'subject' => 'Computer Science', 'cover' => 'https://via.placeholder.com/150x200/20c997/000000?text=AI', 'available' => false, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Cloud Computing Technologies', 'subject' => 'Information Technology', 'cover' => 'https://via.placeholder.com/150x200/fd7e14/ffffff?text=Cloud', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Data Analytics', 'subject' => 'Information Systems', 'cover' => 'https://via.placeholder.com/150x200/6c757d/ffffff?text=Analytics', 'available' => true, 'course' => 'BSIS', 'year' => 2023],
    ['title' => 'Internet of Things', 'subject' => 'Computer Science', 'cover' => 'https://via.placeholder.com/150x200/007bff/ffffff?text=IoT', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Digital Marketing', 'subject' => 'Business', 'cover' => 'https://via.placeholder.com/150x200/6610f2/ffffff?text=DigitalMkt', 'available' => false, 'course' => 'BSIS', 'year' => 2023],
    ['title' => 'Project Management', 'subject' => 'Business', 'cover' => 'https://via.placeholder.com/150x200/28a745/ffffff?text=PM', 'available' => true, 'course' => 'BSIS', 'year' => 2023],
    ['title' => 'Computer Graphics', 'subject' => 'Computer Science', 'cover' => 'https://via.placeholder.com/150x200/6f42c1/ffffff?text=CG', 'available' => true, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'E-commerce Systems', 'subject' => 'Information Systems', 'cover' => 'https://via.placeholder.com/150x200/28a745/ffffff?text=Ecom', 'available' => true, 'course' => 'BSIS', 'year' => 2023],
    ['title' => 'Machine Learning', 'subject' => 'Computer Science', 'cover' => 'https://via.placeholder.com/150x200/dc3545/ffffff?text=ML', 'available' => false, 'course' => 'BSIT', 'year' => 2023],
    ['title' => 'Network Security', 'subject' => 'Information Technology', 'cover' => 'https://via.placeholder.com/150x200/f5576c/ffffff?text=NetSec', 'available' => true, 'course' => 'BSIT', 'year' => 2023]
];

// Filter modules based on search query, course, and year
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$yearFilter = isset($_GET['year']) ? (int)$_GET['year'] : '';
$filteredModules = $allModules;

if ($searchQuery || $courseFilter || $yearFilter) {
    $filteredModules = array_filter($allModules, function($module) use ($searchQuery, $courseFilter, $yearFilter) {
        $matchesSearch = !$searchQuery || strpos(strtolower($module['title']), $searchQuery) !== false || strpos(strtolower($module['subject']), $searchQuery) !== false;
        $matchesCourse = !$courseFilter || $module['course'] === $courseFilter;
        $matchesYear = !$yearFilter || $module['year'] === $yearFilter;
        return $matchesSearch && $matchesCourse && $matchesYear;
    });
}

// Handle AJAX requests for pagination
if (isset($_GET['ajax'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 12; // 3 rows * 4 cards
    $offset = ($page - 1) * $perPage;
    $modulesToShow = array_slice($filteredModules, $offset, $perPage);
    echo json_encode($modulesToShow);
    exit;
}

// For initial load, show first 12 modules
$initialModules = array_slice($filteredModules, 0, 12);
$totalModules = count($filteredModules);
$hasMore = $totalModules > 12;
?>

<link rel="stylesheet" href="../../src/css/dashboard.css">

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

    <!-- Upload Module Form -->
    <div class="mb-4">
        <form id="uploadModuleForm" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="title" class="form-control" placeholder="Enter module title..." required>
            </div>
            <div class="col-md-3">
                <select name="course" class="form-select" required>
                    <option value="">Select Course</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSIS">BSIS</option>
                    <option value="ACT">ACT</option>
                    <option value="SHS">SHS</option>
                    <option value="BSHM">BSHM</option>
                    <option value="BSOA">BSOA</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="file" name="module_file" class="form-control" accept=".pdf" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-upload me-2"></i>Upload
                </button>
            </div>
        </form>
    </div>

    <hr>

    <!-- Search Form -->
    <div class="mb-4">
        <form method="GET" action="" class="d-flex">
            <input type="hidden" name="page" value="upload_modules">
            <input type="text" name="search" class="form-control me-2" style="max-width: 300px;" placeholder="Search modules by title or subject..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <select name="course" class="form-select me-2" style="max-width: 150px;">
                <option value="">All Courses</option>
                <option value="BSIT" <?php echo $courseFilter === 'BSIT' ? 'selected' : ''; ?>>BSIT</option>
                <option value="BSIS" <?php echo $courseFilter === 'BSIS' ? 'selected' : ''; ?>>BSIS</option>
                <option value="ACT" <?php echo $courseFilter === 'ACT' ? 'selected' : ''; ?>>ACT</option>
                <option value="SHS" <?php echo $courseFilter === 'SHS' ? 'selected' : ''; ?>>SHS</option>
                <option value="BSHM" <?php echo $courseFilter === 'BSHM' ? 'selected' : ''; ?>>BSHM</option>
                <option value="BSOA" <?php echo $courseFilter === 'BSOA' ? 'selected' : ''; ?>>BSOA</option>
            </select>
            <select name="year" class="form-select me-2" style="max-width: 120px;">
                <option value="">All Years</option>
                <?php for ($y = 2000; $y <= 2026; $y++): ?>
                    <option value="<?php echo $y; ?>" <?php echo $yearFilter === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
            <?php if ($searchQuery || $courseFilter || $yearFilter): ?>
                <a href="?page=upload_modules" class="btn btn-outline-secondary ms-2">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- All Modules Grid -->
    <div id="modules-container" class="row g-3">
        <?php foreach($initialModules as $module): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 border-0 shadow-sm">
                <img src="<?php echo $module['cover']; ?>" class="card-img-top" alt="<?php echo $module['title']; ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold mb-1"><?php echo $module['title']; ?></h6>
                    <p class="card-text text-muted small mb-2"><?php echo $module['subject']; ?></p>
                    <div class="d-flex justify-content-end">
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-1" title="View">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success me-1" title="Download" <?php echo !$module['available'] ? 'disabled' : ''; ?>>
                                <i class="bi bi-download"></i>
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
            <i class="bi bi-arrow-down-circle me-2"></i>Load More Modules
        </button>
    </div>

    <!-- No more modules message -->
    <div id="no-more" class="text-center mt-4" style="display: none;">
        <p class="text-muted">No more modules to load.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle module upload form
    const uploadForm = document.getElementById('uploadModuleForm');
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('../../back-end/create/uploadModules.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Module uploaded successfully!');
                // Reset form
                uploadForm.reset();
                // Optionally reload the page or update the modules list
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while uploading the module.');
        });
    });

    // Load more modules functionality
    let currentPage = 1;
    let hasMore = <?php echo $hasMore ? 'true' : 'false'; ?>;
    let searchQuery = '<?php echo addslashes($searchQuery); ?>';
    let courseFilter = '<?php echo addslashes($courseFilter); ?>';
    let yearFilter = '<?php echo $yearFilter; ?>';

    function loadMoreModules() {
        if (!hasMore) return;

        document.getElementById('loading').style.display = 'block';

        currentPage++;
        const url = `?page=upload_modules&ajax=1&page=${currentPage}&search=${encodeURIComponent(searchQuery)}&course=${encodeURIComponent(courseFilter)}&year=${yearFilter}`;

        fetch(url)
            .then(response => response.json())
            .then(modules => {
                document.getElementById('loading').style.display = 'none';

                if (modules.length === 0) {
                    hasMore = false;
                    document.getElementById('no-more').style.display = 'block';
                    return;
                }

                const container = document.getElementById('modules-container');
                modules.forEach(module => {
                    const col = document.createElement('div');
                    col.className = 'col-lg-3 col-md-4 col-sm-6';
                    col.innerHTML = '<div class="card h-100 border-0 shadow-sm">' +
                        '<img src="' + module.cover + '" class="card-img-top" alt="' + module.title + '" style="height: 200px; object-fit: cover;">' +
                        '<div class="card-body p-3">' +
                            '<h6 class="card-title fw-bold mb-1">' + module.title + '</h6>' +
                            '<p class="card-text text-muted small mb-2">' + module.subject + '</p>' +
                            '<div class="d-flex justify-content-end">' +
                                '<div>' +
                                    '<button class="btn btn-sm btn-outline-primary me-1" title="View">' +
                                        '<i class="bi bi-eye"></i>' +
                                    '</button>' +
                                    '<button class="btn btn-sm btn-outline-success me-1" title="Download" ' + (!module.available ? 'disabled' : '') + '>' +
                                        '<i class="bi bi-download"></i>' +
                                    '</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                    container.appendChild(col);
                });

                // Check if there are more modules
                if (modules.length < 12) {
                    hasMore = false;
                    document.getElementById('load-more-container').style.display = 'none';
                    document.getElementById('no-more').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading more modules:', error);
                document.getElementById('loading').style.display = 'none';
            });
    }

    // Add event listener to Load More button
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            console.log('Load More button clicked');
            loadMoreModules();
        });
    } else {
        console.log('Load More button not found');
    }
});
</script>
