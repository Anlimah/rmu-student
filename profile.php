<?php
$page_title = 'My Profile';
require_once('inc/auth.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Profile</title>
    <?php require_once("inc/head.php") ?>
</head>

<body>
    <div class="app">

        <?php require_once("inc/sidebar.php") ?>

        <div class="app__main">
            <?php require_once("inc/header.php") ?>

            <main class="app__content">

                <nav class="breadcrumb">
                    <a href="index.php" class="breadcrumb__item">Dashboard</a>
                    <span class="breadcrumb__separator"><i class="bi bi-chevron-right"></i></span>
                    <span class="breadcrumb__current">My Profile</span>
                </nav>

                <!-- Profile Card -->
                <div class="profile-card mb-6">
                    <img src="<?= $student_image ?>" alt="<?= $student_data["full_name"] ?>" class="profile-card__avatar">
                    <div class="profile-card__name"><?= $student_data["full_name"] ?></div>
                    <div class="profile-card__id"><?= $student_index ?></div>
                    <div class="profile-card__program">
                        <span class="profile-card__program-name"><?= $student_data["program_name"] ?></span>
                        <span class="profile-card__class-code">[<?= $student_data["class_code"] ?>]</span>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="card mb-6">
                    <div class="card__header">
                        <h3 class="card__title"><i class="bi bi-person mr-2"></i> Personal Information</h3>
                    </div>
                    <div class="detail-list">
                        <div class="detail-item">
                            <div class="detail-item__label">Full Name</div>
                            <div class="detail-item__value"><?= $student_data["full_name"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Index Number</div>
                            <div class="detail-item__value"><?= $student_data["index_number"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Application Number</div>
                            <div class="detail-item__value"><?= $student_data["app_number"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Email</div>
                            <div class="detail-item__value"><?= $student_data["email"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Phone Number</div>
                            <div class="detail-item__value"><?= $student_data["phone_number"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Gender</div>
                            <div class="detail-item__value"><?= ucfirst($student_data["gender"] ?? 'N/A') ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Date of Birth</div>
                            <div class="detail-item__value"><?= $student_data["dob"] ? (new DateTime($student_data["dob"]))->format("F j, Y") : 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Nationality</div>
                            <div class="detail-item__value"><?= ucfirst($student_data["nationality"] ?? 'N/A') ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Marital Status</div>
                            <div class="detail-item__value"><?= ucfirst($student_data["marital_status"] ?? 'N/A') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="card mb-6">
                    <div class="card__header">
                        <h3 class="card__title"><i class="bi bi-mortarboard mr-2"></i> Academic Information</h3>
                    </div>
                    <div class="detail-list">
                        <div class="detail-item">
                            <div class="detail-item__label">Programme</div>
                            <div class="detail-item__value"><?= $student_data["program_name"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Department</div>
                            <div class="detail-item__value"><?= $student_data["department_name"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Class</div>
                            <div class="detail-item__value"><?= $student_data["class_code"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Current Level</div>
                            <div class="detail-item__value"><?= $student_level["level"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Current Semester</div>
                            <div class="detail-item__value"><?= $student_level["semester"] ?? 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Date Admitted</div>
                            <div class="detail-item__value"><?= $student_data["date_admitted"] ? (new DateTime($student_data["date_admitted"]))->format("F j, Y") : 'N/A' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Level Admitted</div>
                            <div class="detail-item__value"><?= $student_data["level_admitted"] ?? 'N/A' ?></div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card__header">
                        <h3 class="card__title"><i class="bi bi-gear mr-2"></i> Account</h3>
                    </div>
                    <div class="p-4">
                        <a href="reset-password.php" class="btn btn--outline-primary">
                            <i class="bi bi-key"></i>
                            Change Password
                        </a>
                    </div>
                </div>

            </main>

            <?php require_once("inc/footer.php") ?>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/portal.js"></script>
</body>

</html>
