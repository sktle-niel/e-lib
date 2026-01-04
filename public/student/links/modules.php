<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
$currentPage = 'Modules';

$modules = [
    ['title' => 'Introduction to Algorithms', 'uploadedDate' => '2023-10-15', 'course' => 'BSIT', 'year' => 2009, 'cover' => 'https://via.placeholder.com/150x200/11998e/ffffff?text=Algo'],
    ['title' => 'Computer Networks', 'uploadedDate' => '2023-10-10', 'course' => 'BSIT', 'year' => 2011, 'cover' => 'https://via.placeholder.com/150x200/38ef7d/000000?text=Networks'],
    ['title' => 'Database System Concepts', 'uploadedDate' => '2023-10-05', 'course' => 'BSIS', 'year' => 2010, 'cover' => 'https://via.placeholder.com/150x200/f093fb/000000?text=DB'],
    ['title' => 'Operating Systems', 'uploadedDate' => '2023-09-28', 'course' => 'BSIT', 'year' => 2012, 'cover' => 'https://via.placeholder.com/150x200/f5576c/ffffff?text=OS'],
    ['title' => 'Artificial Intelligence', 'uploadedDate' => '2023-09-20', 'course' => 'BSIT', 'year' => 2010, 'cover' => 'https://via.placeholder.com/150x200/28a745/ffffff?text=AI'],
    ['title' => 'Machine Learning', 'uploadedDate' => '2023-09-15', 'course' => 'BSIT', 'year' => 1997, 'cover' => 'https://via.placeholder.com/150x200/dc3545/ffffff?text=ML'],
    ['title' => 'Data Structures', 'uploadedDate' => '2023-09-10', 'course' => 'BSIT', 'year' => 2006, 'cover' => 'https://via.placeholder.com/150x200/ffc107/000000?text=DS'],
    ['title' => 'Software Engineering', 'uploadedDate' => '2023-09-05', 'course' => 'BSIT', 'year' => 2015, 'cover' => 'https://via.placeholder.com/150x200/17a2b8/ffffff?text=SE'],
    ['title' => 'Web Development', 'uploadedDate' => '2023-08-30', 'course' => 'BSIT', 'year' => 2014, 'cover' => 'https://via.placeholder.com/150x200/6f42c1/ffffff?text=Web'],
    ['title' => 'Cybersecurity', 'uploadedDate' => '2023-08-25', 'course' => 'BSIT', 'year' => 2017, 'cover' => 'https://via.placeholder.com/150x200/e83e8c/ffffff?text=Cyber'],
    ['title' => 'Cloud Computing', 'uploadedDate' => '2023-08-20', 'course' => 'BSIS', 'year' => 2013, 'cover' => 'https://via.placeholder.com/150x200/20c997/000000?text=Cloud'],
    ['title' => 'Big Data', 'uploadedDate' => '2023-08-15', 'course' => 'BSIS', 'year' => 2013, 'cover' => 'https://via.placeholder.com/150x200/fd7e14/ffffff?text=BigData'],
    ['title' => 'Blockchain Technology', 'uploadedDate' => '2023-08-10', 'course' => 'BSIT', 'year' => 2015, 'cover' => 'https://via.placeholder.com/150x200/6c757d/ffffff?text=Blockchain'],
    ['title' => 'Internet of Things', 'uploadedDate' => '2023-08-05', 'course' => 'BSIT', 'year' => 2014, 'cover' => 'https://via.placeholder.com/150x200/007bff/ffffff?text=IoT'],
    ['title' => 'Quantum Computing', 'uploadedDate' => '2023-07-30', 'course' => 'BSIT', 'year' => 2010, 'cover' => 'https://via.placeholder.com/150x200/6610f2/ffffff?text=Quantum'],
    ['title' => 'DevOps Handbook', 'uploadedDate' => '2023-07-25', 'course' => 'BSIT', 'year' => 2016, 'cover' => 'https://via.placeholder.com/150x200/28a745/ffffff?text=DevOps'],
    ['title' => 'Computer Graphics', 'uploadedDate' => '2023-07-20', 'course' => 'BSIT', 'year' => 2018, 'cover' => 'https://via.placeholder.com/150x200/6f42c1/ffffff?text=CG'],
    ['title' => 'Mobile App Development', 'uploadedDate' => '2023-07-15', 'course' => 'BSIT', 'year' => 2019, 'cover' => 'https://via.placeholder.com/150x200/28a745/ffffff?text=Mobile'],
    ['title' => 'Data Mining', 'uploadedDate' => '2023-07-10', 'course' => 'BSIS', 'year' => 2011, 'cover' => 'https://via.placeholder.com/150x200/dc3545/ffffff?text=DM'],
    ['title' => 'Network Security', 'uploadedDate' => '2023-07-05', 'course' => 'BSIT', 'year' => 2013, 'cover' => 'https://via.placeholder.com/150x200/f5576c/ffffff?text=NetSec'],
];

// Sort by uploaded date descending (recent first)
usort($modules, function($a, $b) {
    return strtotime($b['uploadedDate']) - strtotime($a['uploadedDate']);
});

// Filter modules based on search query, course, and year
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$yearFilter = isset($_GET['year']) ? (int)$_GET['year'] : '';
$filteredModules = $modules;

if ($searchQuery || $courseFilter || $yearFilter) {
    $filteredModules = array_filter($modules, function($module) use ($searchQuery, $courseFilter, $yearFilter) {
        $matchesSearch = !$searchQuery || strpos(strtolower($module['title']), $searchQuery) !== false;
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

    <!-- Search Form -->
    <div class="mb-4">
        <form method="GET" action="" class="d-flex">
            <input type="hidden" name="page" value="modules">
            <input type="text" name="search" class="form-control me-2" style="max-width: 300px;" placeholder="Search modules by title or instructor..." value="<?php echo htmlspecialchars($searchQuery); ?>">
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
                <a href="?page=modules" class="btn btn-outline-secondary ms-2">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Modules Grid -->
    <div id="modules-container" class="row g-3">
        <?php foreach($initialModules as $module): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 border-0 shadow-sm">
                <img src="<?php echo $module['cover']; ?>" class="card-img-top" alt="<?php echo $module['title']; ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold mb-1"><?php echo $module['title']; ?></h6>
                    <p class="card-text text-muted small mb-2">Uploaded: <?php echo date('M d, Y', strtotime($module['uploadedDate'])); ?></p>
                    <div class="d-flex justify-content-end">
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-1" title="View">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success me-1" title="Download">
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
    let currentPage = 1;
    let hasMore = <?php echo $hasMore ? 'true' : 'false'; ?>;
    let searchQuery = '<?php echo addslashes($searchQuery); ?>';
    let courseFilter = '<?php echo addslashes($courseFilter); ?>';
    let yearFilter = '<?php echo $yearFilter; ?>';

    function loadMoreModules() {
        if (!hasMore) return;

        document.getElementById('loading').style.display = 'block';

        currentPage++;
        const url = `?page=modules&ajax=1&page=${currentPage}&search=${encodeURIComponent(searchQuery)}&course=${encodeURIComponent(courseFilter)}&year=${yearFilter}`;

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
                    col.innerHTML = `
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="${module.cover}" class="card-img-top" alt="${module.title}" style="height: 200px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold mb-1">${module.title}</h6>
                                <p class="card-text text-muted small mb-2">Uploaded: ${new Date(module.uploadedDate).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success me-1" title="Download">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
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
