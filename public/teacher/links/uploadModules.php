<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/readModules.php';
include '../../back-end/update/editModule.php';

$currentPage = 'Upload Modules';

// Get search and filter parameters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
$yearFilter = isset($_GET['year']) ? (int)$_GET['year'] : '';

// Get initial modules for display
$initialModules = getAllModules($searchQuery, $courseFilter, $yearFilter, 12, 0);
$totalModules = getModulesCount($searchQuery, $courseFilter, $yearFilter);
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
                <label for="title" class="form-label">Module Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Enter module title..." required>
            </div>
            <div class="col-md-3">
                <label for="course" class="form-label">Course</label>
                <select name="course" id="course" class="form-select" required>
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
                <label for="module_file" class="form-label">Module File</label>
                <input type="file" name="module_file" id="module_file" class="form-control" accept=".pdf" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-upload me-2"></i>Upload
                </button>
            </div>
        </form>
    </div>

    <hr>

    <!-- Search Form -->
    <div class="mb-4">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="page" value="upload_modules">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search modules by title..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="col-md-3">
                <label for="course" class="form-label">Course</label>
                <select name="course" id="course" class="form-select">
                    <option value="">All Courses</option>
                    <option value="BSIT" <?php echo $courseFilter === 'BSIT' ? 'selected' : ''; ?>>BSIT</option>
                    <option value="BSIS" <?php echo $courseFilter === 'BSIS' ? 'selected' : ''; ?>>BSIS</option>
                    <option value="ACT" <?php echo $courseFilter === 'ACT' ? 'selected' : ''; ?>>ACT</option>
                    <option value="SHS" <?php echo $courseFilter === 'SHS' ? 'selected' : ''; ?>>SHS</option>
                    <option value="BSHM" <?php echo $courseFilter === 'BSHM' ? 'selected' : ''; ?>>BSHM</option>
                    <option value="BSOA" <?php echo $courseFilter === 'BSOA' ? 'selected' : ''; ?>>BSOA</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Year</label>
                <select name="year" id="year" class="form-select">
                    <option value="">All Years</option>
                    <?php for ($y = 2000; $y <= 2026; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $yearFilter === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if ($searchQuery || $courseFilter || $yearFilter): ?>
                    <a href="?page=upload_modules" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- All Modules Grid -->
    <div id="modules-container" class="row g-3">
        <?php foreach($initialModules as $module): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 border-0 shadow-sm" data-module-id="<?php echo $module['id']; ?>">
                <img src="<?php echo $module['cover']; ?>" class="card-img-top" alt="<?php echo $module['title']; ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold mb-1"><?php echo $module['title']; ?></h6>
                    <p class="card-text text-muted small mb-2"><?php echo $module['course']; ?> - <?php echo date('M d, Y', strtotime($module['uploadedDate'])); ?></p>
                    <div class="d-flex justify-content-end">
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-1" title="View">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger me-1" title="Delete">
                                <i class="bi bi-trash"></i>
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

<!-- Edit Module Modal -->
<div class="modal fade" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModuleModalLabel">Edit Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editModuleForm">
                    <input type="hidden" id="editModuleId" name="module_id">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Module Title</label>
                        <input type="text" name="title" id="editTitle" class="form-control" placeholder="Enter module title..." required>
                    </div>
                    <div class="mb-3">
                        <label for="editCourse" class="form-label">Course</label>
                        <select name="course" id="editCourse" class="form-select" required>
                            <option value="">Select Course</option>
                            <option value="BSIT">BSIT</option>
                            <option value="BSIS">BSIS</option>
                            <option value="ACT">ACT</option>
                            <option value="SHS">SHS</option>
                            <option value="BSHM">BSHM</option>
                            <option value="BSOA">BSOA</option>
                        </select>
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
                    col.innerHTML = '<div class="card h-100 border-0 shadow-sm" data-module-id="' + module.id + '">' +
                        '<img src="' + module.cover + '" class="card-img-top" alt="' + module.title + '" style="height: 200px; object-fit: cover;">' +
                        '<div class="card-body p-3">' +
                            '<h6 class="card-title fw-bold mb-1">' + module.title + '</h6>' +
                            '<p class="card-text text-muted small mb-2">' + module.course + ' - ' + new Date(module.uploadedDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + '</p>' +
                            '<div class="d-flex justify-content-end">' +
                                '<div>' +
                                    '<button class="btn btn-sm btn-outline-primary me-1" title="View">' +
                                        '<i class="bi bi-eye"></i>' +
                                    '</button>' +
                                    '<button class="btn btn-sm btn-outline-warning me-1" title="Edit">' +
                                        '<i class="bi bi-pencil"></i>' +
                                    '</button>' +
                                    '<button class="btn btn-sm btn-outline-danger me-1" title="Delete">' +
                                        '<i class="bi bi-trash"></i>' +
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

    // Handle edit button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-outline-warning')) {
            e.preventDefault();
            const card = e.target.closest('.card');
            const title = card.querySelector('.card-title').textContent;
            const course = card.querySelector('.card-text').textContent.split(' - ')[0];
            const moduleId = card.dataset.moduleId; // Assuming module ID is stored in data attribute

            // Populate modal
            document.getElementById('editModuleId').value = moduleId;
            document.getElementById('editTitle').value = title;
            document.getElementById('editCourse').value = course;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editModuleModal'));
            modal.show();
        }
    });

    // Handle save edit button
    document.getElementById('saveEditBtn').addEventListener('click', function() {
        const form = document.getElementById('editModuleForm');
        const formData = new FormData(form);

        fetch('../../back-end/update/editModule.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Module updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the module.');
        });
    });
});
</script>
