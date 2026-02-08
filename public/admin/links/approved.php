<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/PendingAccounts.php';

$currentPage = 'Approved';

$approvedAccounts = getAllApprovedAccounts();
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
    <span id="success-text">Account deleted successfully!</span>
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
                        <h5 class="card-title fw-bold mb-0">Approved Teacher & Librarian Accounts</h5>
                    </div>
                    <?php if (empty($approvedAccounts)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No approved accounts yet.</p>
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
                                        <th>User Type</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($approvedAccounts as $account): ?>
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
                                        <td>
                                            <span class="badge bg-<?php echo $account['user_type'] == 'teacher' ? 'primary' : 'success'; ?>">
                                                <?php echo ucfirst($account['user_type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($account['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo htmlspecialchars($account['id']); ?>" data-name="<?php echo htmlspecialchars($account['firstname'] . ' ' . $account['lastname']); ?>">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<?php include '../../public/admin/components/deleteModal.php'; ?>

<script>
// Success and Error Message Functions
function showSuccessMessage(message = 'Account deleted successfully!') {
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
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const accountNameSpan = document.getElementById('accountName');
    let accountIdToDelete = null;

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            accountIdToDelete = this.getAttribute('data-id');
            const accountName = this.getAttribute('data-name');
            accountNameSpan.textContent = accountName;
            deleteModal.show();
        });
    });

    confirmDeleteBtn.addEventListener('click', function() {
        if (accountIdToDelete) {
            // Show loading state
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

            fetch('../../back-end/delete/deleteLibAdmin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'delete_id=' + encodeURIComponent(accountIdToDelete)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                // Log the raw response text for debugging
                return response.text().then(text => {
                    console.log('Raw response:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    showSuccessMessage(data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showErrorMessage(data.message || 'Failed to delete account');
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.innerHTML = 'Delete';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Network error: ' + error.message);
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.innerHTML = 'Delete';
            })
            .finally(() => {
                if (!deleteModal._isShown) {
                    deleteModal.hide();
                }
            });
        }
    });
});
</script>
