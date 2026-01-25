<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/PendingAccounts.php';
include '../../back-end/delete/deleteSuper.php';

$currentPage = 'Approved';

$approvedAccounts = getAllApprovedAccounts();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    header('Content-Type: application/json');
    $result = deleteSuperAccount($_POST['delete_id']);
    echo json_encode($result);
    exit;
}
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
                                        <th>LRN Number</th>
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
                                        <td><?php echo htmlspecialchars($account['lrn_number'] ?: 'N/A'); ?></td>
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

<script>
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
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'delete_id=' + encodeURIComponent(accountIdToDelete)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload to update the table
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the account.');
            })
            .finally(() => {
                deleteModal.hide();
            });
        }
    });
});
</script>

<?php include '../../public/admin/components/deleteModal.php'; ?>
