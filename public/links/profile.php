<?php
include '../back-end/read/profileData.php';
?>

<link rel="stylesheet" href="../../src/css/dashboard.css">
<link rel="stylesheet" href="../../src/css/profile.css">

<!-- Success Message -->
<div id="success-message" class="success-message" style="display: none;">Update successful!</div>

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

    <div class="row">
        <!-- Profile Picture Section -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <h5 class="card-title mb-3">Profile Picture</h5>
                    <div class="mb-3">
                        <img src="<?php echo !empty($profile_picture) ? '../../src/profile/' . htmlspecialchars($profile_picture) : 'https://via.placeholder.com/150x150/007bff/ffffff?text=Profile'; ?>" class="rounded-circle img-fluid" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="profilePicture" accept="image/*">
                        <small class="form-text text-muted">Choose a new profile picture (JPG, PNG, GIF)</small>
                    </div>
                    <button type="button" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Upload Picture
                    </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadBtn = document.querySelector('.btn-primary');
    uploadBtn.addEventListener('click', function() {
        const fileInput = document.getElementById('profilePicture');
        const file = fileInput.files[0];
        if (!file) {
            alert('Please select a file');
            return;
        }
        const formData = new FormData();
        formData.append('profilePicture', file);
        fetch('../back-end/create/attachProfile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const msg = document.getElementById('success-message');
                msg.style.display = 'block';
                msg.style.opacity = '1';
                setTimeout(function() {
                    msg.style.opacity = '0';
                    setTimeout(function() {
                        msg.style.display = 'none';
                        location.reload();
                    }, 1000);
                }, 3000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Course selection
    const courseBtn = document.getElementById('updateCourseBtn');
    courseBtn.addEventListener('click', function() {
        const courseSelect = document.getElementById('courseSelect');
        const course = courseSelect.value;
        if (!course) {
            alert('Please select a course');
            return;
        }
        const formData = new FormData();
        formData.append('course', course);
        fetch('../back-end/create/selectCourse.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const msg = document.getElementById('success-message');
                msg.style.display = 'block';
                msg.style.opacity = '1';
                setTimeout(function() {
                    msg.style.opacity = '0';
                    setTimeout(function() {
                        msg.style.display = 'none';
                        location.reload();
                    }, 1000);
                }, 3000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Password visibility toggles
    const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
    const currentPasswordInput = document.getElementById('currentPassword');
    toggleCurrentPassword.addEventListener('click', function() {
        const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        currentPasswordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const newPasswordInput = document.getElementById('newPassword');
    toggleNewPassword.addEventListener('click', function() {
        const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        newPasswordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Change password
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    changePasswordBtn.addEventListener('click', function() {
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!currentPassword || !newPassword || !confirmPassword) {
            alert('Please fill in all fields');
            return;
        }

        if (newPassword !== confirmPassword) {
            alert('New password and confirm password do not match');
            return;
        }

        const formData = new FormData();
        formData.append('currentPassword', currentPassword);
        formData.append('newPassword', newPassword);
        formData.append('confirmPassword', confirmPassword);

        fetch('../back-end/update/changePassword.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const msg = document.getElementById('success-message');
                msg.style.display = 'block';
                msg.style.opacity = '1';
                setTimeout(function() {
                    msg.style.opacity = '0';
                    setTimeout(function() {
                        msg.style.display = 'none';
                        location.reload();
                    }, 1000);
                }, 3000);
                // Clear the form
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>

        <!-- Account Settings -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Account Settings</h5>

                    <!-- Change Name -->
                    <div class="mb-4">
                        <h6 class="mb-3">Change Name</h6>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" placeholder="Enter first name" value="<?php echo htmlspecialchars($firstname); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" placeholder="Enter last name" value="<?php echo htmlspecialchars($lastname); ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Name
                            </button>
                        </form>
                    </div>

                    <hr class="my-4">

                    <!-- Select Course -->
                    <div class="mb-4">
                        <h6 class="mb-3">Select Course</h6>
                        <form>
                            <div class="mb-3">
                                <label for="courseSelect" class="form-label">Course</label>
                                <select class="form-select" id="courseSelect">
                                    <option value="">Select a course</option>
                                    <option value="BSIT" <?php echo ($program == 'BSIT') ? 'selected' : ''; ?>>BSIT - Bachelor of Science in Information Technology</option>
                                    <option value="BSIS" <?php echo ($program == 'BSIS') ? 'selected' : ''; ?>>BSIS - Bachelor of Science in Information Systems</option>
                                    <option value="ACT" <?php echo ($program == 'ACT') ? 'selected' : ''; ?>>ACT - Associate in Computer Technology</option>
                                    <option value="SHS" <?php echo ($program == 'SHS') ? 'selected' : ''; ?>>SHS - Senior High School</option>
                                    <option value="BSHM" <?php echo ($program == 'BSHM') ? 'selected' : ''; ?>>BSHM - Bachelor of Science in Hospitality Management</option>
                                    <option value="BSOA" <?php echo ($program == 'BSOA') ? 'selected' : ''; ?>>BSOA - Bachelor of Science in Office Administration</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary" id="updateCourseBtn">
                                <i class="bi bi-check-circle me-2"></i>Update Course
                            </button>
                        </form>
                    </div>

                    <hr class="my-4">

                    <!-- Change Password -->
                    <div class="mb-4">
                        <h6 class="mb-3">Change Password</h6>
                        <form>
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password">
                                    <span class="input-group-text" id="toggleCurrentPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
                                    <span class="input-group-text" id="toggleNewPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password">
                                    <span class="input-group-text" id="toggleConfirmPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="changePasswordBtn">
                                <i class="bi bi-key me-2"></i>Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>