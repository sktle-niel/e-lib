<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/fetchStudents.php';

$currentPage = 'Students';

$limit = 10;
$page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$offset = ($page - 1) * $limit;

$students = getAllStudents($limit, $offset);
$totalStudents = getTotalStudents();
$totalPages = ceil($totalStudents / $limit);
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

.success-message {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border-radius: 5px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    font-size: 16px;
    z-index: 1000;
    display: flex;
    align-items: center;
    gap: 10px;
}

.success-message.show {
    opacity: 1;
}

.error-message {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    border-radius: 5px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    font-size: 16px;
    z-index: 1000;
    display: flex;
    align-items: center;
    gap: 10px;
}

.error-message.show {
    opacity: 1;
}
</style>

<!-- Success Message -->
<div id="success-message" class="success-message">
    <i class="bi bi-check-circle-fill"></i>
    <span id="success-text">Student account deleted successfully!</span>
</div>

<!-- Error Message -->
<div id="error-message" class="error-message">
    <i class="bi bi-exclamation-circle-fill"></i>
    <span id="error-text">An error occurred</span>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $currentPage; ?> Accounts</h1>
        </div>
    </div>

    <!-- Approved Accounts Table -->
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">All Student Accounts</h5>
                    </div>
                    <?php if (empty($students)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No student accounts yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Profile</th>
                                        <th>Username</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Program</th>
                                        <th>LRN Number</th>
                                        <th>User Type</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($students as $account): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($account['id']); ?></td>
                                        <td>
                                            <?php if ($account['profile_picture']): ?>
                                                <img src="../../src/profile/<?php echo htmlspecialchars($account['profile_picture']); ?>" alt="Profile Picture" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                            <?php else: ?>
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #11998e; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                    <?php echo strtoupper(substr($account['firstname'], 0, 1)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($account['username']); ?></td>
                                        <td><?php echo htmlspecialchars($account['firstname']); ?></td>
                                        <td><?php echo htmlspecialchars($account['lastname']); ?></td>
                                        <td><?php echo htmlspecialchars($account['program'] ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($account['lrn_number'] ?: 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo ucfirst($account['user_type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($account['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo htmlspecialchars($account['id']); ?>" data-name="<?php echo htmlspecialchars($account['firstname'] . ' ' . $account['lastname']); ?>">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=students&page_num=<?php echo $page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <!-- Page Numbers -->
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=students&page_num=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Next Button -->
                                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=students&page_num=<?php echo $page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../public/admin/components/deleteModal.php'; ?>

    <script>
    // Success and Error Message Functions
    function showSuccessMessage(message = 'Student account deleted successfully!') {
        const successMsg = document.getElementById("success-message");
        const successText = document.getElementById("success-text");
        successText.textContent = message;
        successMsg.classList.add('show');
        
        setTimeout(function() {
            successMsg.classList.remove('show');
        }, 3000);
    }

    function showErrorMessage(message = 'An error occurred') {
        const errorMsg = document.getElementById("error-message");
        const errorText = document.getElementById("error-text");
        errorText.textContent = message;
        errorMsg.classList.add('show');
        
        setTimeout(function() {
            errorMsg.classList.remove('show');
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const studentNameElement = document.getElementById('studentName');
        let studentIdToDelete = null;

        // Handle delete button clicks
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');
                const studentName = this.getAttribute('data-name');
                studentIdToDelete = studentId;
                studentNameElement.textContent = studentName;
            });
        });

        // Handle confirm delete
        confirmDeleteBtn.addEventListener('click', function() {
            if (studentIdToDelete) {
                // Close the modal
                const modalInstance = bootstrap.Modal.getInstance(deleteModal);
                modalInstance.hide();
                
                fetch('../../back-end/delete/deleteAccount.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + encodeURIComponent(studentIdToDelete)
                })
                .then(response => {
                    // Log the response for debugging
                    console.log('Response status:', response.status);
                    return response.text();
                })
                .then(text => {
                    // Log the raw response
                    console.log('Response text:', text);
                    
                    // Try to parse as JSON
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            showSuccessMessage(data.message);
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            showErrorMessage(data.message || 'Failed to delete account');
                            console.error('Delete failed:', data);
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response was:', text);
                        showErrorMessage('Server returned invalid response');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showErrorMessage('Network error: ' + error.message);
                });
            }
        });
    });
    </script>
</div>