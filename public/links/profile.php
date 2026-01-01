<?php
$currentPage = 'Profile';
?>

<link rel="stylesheet" href="../../src/css/dashboard.css">

<style>
.profile-container {
    background: linear-gradient(135deg, #0e8074 0%, #2dd468 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.profile-card {
    background: #fff;
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
}

.profile-title {
    color: #333;
    font-weight: 700;
    margin-bottom: 2rem;
}

.section-title {
    color: #11998e;
    font-weight: 600;
    border-bottom: 2px solid #11998e;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.form-label {
    color: #333;
    font-weight: 500;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: border-color 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #11998e;
    box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
}

.btn-custom {
    background: linear-gradient(135deg, #0e8074 0%, #2dd468 100%);
    border: none;
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(14, 128, 116, 0.4);
}

.profile-picture-container {
    position: relative;
    display: inline-block;
}

.profile-picture {
    border: 4px solid #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.upload-section {
    background: rgba(255,255,255,0.9);
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1rem;
}

.text-muted-custom {
    color: #666;
}

hr {
    border: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #11998e, transparent);
}
</style>

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
                        <img src="https://via.placeholder.com/150x150/007bff/ffffff?text=Profile" class="rounded-circle img-fluid" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
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
                                    <input type="text" class="form-control" id="firstName" placeholder="Enter first name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" placeholder="Enter last name">
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
                                    <option value="BSIT">BSIT - Bachelor of Science in Information Technology</option>
                                    <option value="BSIS">BSIS - Bachelor of Science in Information Systems</option>
                                    <option value="ACT">ACT - Associate in Computer Technology</option>
                                    <option value="SHS">SHS - Senior High School</option>
                                    <option value="BSHM">BSHM - Bachelor of Science in Hospitality Management</option>
                                    <option value="BSOA">BSOA - Bachelor of Science in Office Administration</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
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
                                <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password">
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key me-2"></i>Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
