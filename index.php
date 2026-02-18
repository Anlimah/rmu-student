<?php
$page_title = 'Dashboard';
require_once('inc/auth.php');

$registration_open = !empty($current_semester) && $current_semester["reg_open_status"];
$registration_end = '';
if ($registration_open) {
    $registration_end = (new \DateTime($current_semester["reg_end_date"]))->format("l F j, Y");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Dashboard</title>
    <?php require_once("inc/head.php") ?>
</head>

<body>
    <div class="app">

        <?php require_once("inc/sidebar.php") ?>

        <div class="app__main">
            <?php require_once("inc/header.php") ?>

            <main class="app__content">

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

                <!-- Registration Banner -->
                <?php if (!$registration_open): ?>
                    <div class="alert alert--danger mb-6">
                        <span class="alert__icon bi bi-exclamation-triangle-fill"></span>
                        <div class="alert__content">
                            <strong><?= $semester_label ?> semester course registration closed</strong>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert--success mb-6">
                        <span class="alert__icon bi bi-check-circle-fill"></span>
                        <div class="alert__content">
                            <strong><?= $semester_label ?> semester course registration is open.</strong>
                            <div class="mt-1 text-sm">Registration ends on <strong><?= $registration_end ?> at 11:59 PM.</strong></div>
                            <div class="mt-4">
                                <a href="register-courses.php" class="btn btn--success btn--sm">Register Now</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Quick Stats -->
                <?php if (!empty($current_semester)): ?>
                    <div class="summary-bar mb-8">
                        <div class="summary-bar__item">
                            <div class="summary-bar__value"><?= $current_semester["academic_year_name"] ?></div>
                            <div class="summary-bar__label">Academic Year</div>
                        </div>
                        <div class="summary-bar__item">
                            <div class="summary-bar__value">Semester <?= $current_semester["semester_name"] ?></div>
                            <div class="summary-bar__label">Current Semester</div>
                        </div>
                        <div class="summary-bar__item">
                            <div class="summary-bar__value">Level <?= $student_level["level"] ?? 'N/A' ?></div>
                            <div class="summary-bar__label">Current Level</div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Quick Access -->
                <h2 class="section-title">Quick Access</h2>
                <div class="grid grid--3 grid--auto-fit mb-8">

                    <a href="semester-courses.php?myCoursesTab=SEMESTER_COURSES" class="feature-card">
                        <img src="assets/images/icons8-courses-64.png" alt="" class="feature-card__icon">
                        <span class="feature-card__label">Semester Courses</span>
                        <i class="feature-card__arrow bi bi-chevron-right"></i>
                    </a>

                    <a href="exam-results.php" class="feature-card">
                        <img src="assets/images/icons8-exam-96.png" alt="" class="feature-card__icon">
                        <span class="feature-card__label">Exam Results</span>
                        <i class="feature-card__arrow bi bi-chevron-right"></i>
                    </a>

                    <a href="timetable.php" class="feature-card">
                        <img src="assets/images/icons8-timetable-94.png" alt="" class="feature-card__icon">
                        <span class="feature-card__label">Timetable</span>
                        <i class="feature-card__arrow bi bi-chevron-right"></i>
                    </a>

                    <a href="#" class="feature-card">
                        <img src="assets/images/icons8-exam-96(1).png" alt="" class="feature-card__icon">
                        <span class="feature-card__label">Course & Lecturer Evaluation</span>
                        <i class="feature-card__arrow bi bi-chevron-right"></i>
                    </a>

                    <a href="#" class="feature-card">
                        <img src="assets/images/icons8-hostel-55.png" alt="" class="feature-card__icon">
                        <span class="feature-card__label">Hostel & Accommodation</span>
                        <i class="feature-card__arrow bi bi-chevron-right"></i>
                    </a>

                    <a href="#" class="feature-card">
                        <img src="assets/images/icons8-books-94.png" alt="" class="feature-card__icon">
                        <span class="feature-card__label">Library</span>
                        <i class="feature-card__arrow bi bi-chevron-right"></i>
                    </a>

                    <a href="#" class="feature-card">
                        <img src="assets/images/icons8-book-and-pencil-96.png" alt="" class="feature-card__icon">
                        <span class="feature-card__label">Practice Quiz</span>
                        <i class="feature-card__arrow bi bi-chevron-right"></i>
                    </a>
                </div>

            </main>

            <?php require_once("inc/footer.php") ?>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/portal.js"></script>
</body>

</html>
