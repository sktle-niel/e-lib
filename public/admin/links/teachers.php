<?php
if (!defined('MAIN_PAGE')) {
    include '../../auth/sessionCheck.php';
}
include '../../back-end/read/PendingAccounts.php';

$currentPage = 'Teachers';

$pendingTeachers = getPendingTeachers();
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
            <h1 class="page-title"><?php echo $currentPage; ?> Pending Accounts</h1>
        </div>
    </div>

    <!-- Pending Teachers Table -->
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Pending Teacher Accounts</h5>
                    </div>
                    <?php if (empty($pendingTeachers)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No pending teacher accounts.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pendingTeachers as $teacher): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($teacher['id']); ?></td>
                                        <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                                        <td><?php echo htmlspecialchars($teacher['firstname']); ?></td>
                                        <td><?php echo htmlspecialchars($teacher['lastname']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($teacher['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-success me-2" onclick="showApproveModal(<?php echo $teacher['id']; ?>, 'teacher', '<?php echo htmlspecialchars($teacher['firstname'] . ' ' . $teacher['lastname']); ?>')">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="showRejectModal(<?php echo $teacher['id']; ?>, 'teacher', '<?php echo htmlspecialchars($teacher['firstname'] . ' ' . $teacher['lastname']); ?>')">
                                                <i class="bi bi-x-circle"></i> Reject
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

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve the account for <strong id="approveAccountName"></strong>?</p>
                <p class="text-muted">This will activate the account and allow the user to log in.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmApproveBtn">Approve Account</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject the account for <strong id="rejectAccountName"></strong>?</p>
                <p class="text-danger">This action cannot be undone. The account will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRejectBtn">Reject Account</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentAccountId = null;
let currentAccountType = null;

function showApproveModal(id, type, name) {
    currentAccountId = id;
    currentAccountType = type;
    document.getElementById('approveAccountName').textContent = name;
    const modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

function showRejectModal(id, type, name) {
    currentAccountId = id;
    currentAccountType = type;
    document.getElementById('rejectAccountName').textContent = name;
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

document.getElementById('confirmApproveBtn').addEventListener('click', function() {
    if (currentAccountId && currentAccountType) {
        window.location.href = '../../back-end/update/approveAccount.php?id=' + currentAccountId + '&type=' + currentAccountType;
    }
});

document.getElementById('confirmRejectBtn').addEventListener('click', function() {
    if (currentAccountId && currentAccountType) {
        window.location.href = '../../back-end/delete/rejectAccount.php?id=' + currentAccountId + '&type=' + currentAccountType;
    }
});
</script>
