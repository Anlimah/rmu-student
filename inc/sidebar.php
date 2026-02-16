<?php
// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="app__sidebar" id="sidebar">
    <div class="sidebar__brand">
        <img src="assets/images/rmu-logo-small.png" alt="RMU Logo" class="sidebar__logo">
        <div>
            <div class="sidebar__title">REGIONAL MARITIME UNIVERSITY</div>
            <div class="sidebar__subtitle">Student Portal</div>
        </div>
    </div>

    <nav class="sidebar__nav">
        <div class="sidebar__section-label">Main</div>

        <a href="index.php" class="nav-item <?= $current_page === 'index.php' ? 'nav-item--active' : '' ?>">
            <span class="nav-item__icon"><i class="bi bi-grid-1x2"></i></span>
            Dashboard
        </a>

        <a href="profile.php" class="nav-item <?= $current_page === 'profile.php' ? 'nav-item--active' : '' ?>">
            <span class="nav-item__icon"><i class="bi bi-person"></i></span>
            My Profile
        </a>

        <div class="sidebar__section-label">Academics</div>

        <a href="semester-courses.php?myCoursesTab=SEMESTER_COURSES" class="nav-item <?= $current_page === 'semester-courses.php' ? 'nav-item--active' : '' ?>">
            <span class="nav-item__icon"><i class="bi bi-book"></i></span>
            Semester Courses
        </a>

        <a href="register-courses.php" class="nav-item <?= $current_page === 'register-courses.php' ? 'nav-item--active' : '' ?>">
            <span class="nav-item__icon"><i class="bi bi-pencil-square"></i></span>
            Course Registration
            <?php if (!empty($current_semester) && $current_semester["reg_open_status"]): ?>
                <span class="nav-item__badge">Open</span>
            <?php endif; ?>
        </a>

        <a href="exam-results.php" class="nav-item <?= $current_page === 'exam-results.php' ? 'nav-item--active' : '' ?>">
            <span class="nav-item__icon"><i class="bi bi-clipboard-data"></i></span>
            Exam Results
        </a>

        <a href="timetable.php" class="nav-item <?= $current_page === 'timetable.php' ? 'nav-item--active' : '' ?>">
            <span class="nav-item__icon"><i class="bi bi-calendar-week"></i></span>
            Timetable
        </a>

        <div class="sidebar__section-label">Services</div>

        <a href="#" class="nav-item">
            <span class="nav-item__icon"><i class="bi bi-star"></i></span>
            Course Evaluation
        </a>

        <a href="#" class="nav-item">
            <span class="nav-item__icon"><i class="bi bi-building"></i></span>
            Hostel
        </a>

        <a href="#" class="nav-item">
            <span class="nav-item__icon"><i class="bi bi-journal-bookmark"></i></span>
            Library
        </a>
    </nav>

    <div class="sidebar__footer">
        <a href="profile.php" class="sidebar__user" style="text-decoration: none;">
            <img src="<?= $student_image ?>" alt="<?= $student_data["full_name"] ?? '' ?>" class="sidebar__avatar">
            <div>
                <div class="sidebar__user-name"><?= $student_data["full_name"] ?? '' ?></div>
                <div class="sidebar__user-id"><?= $student_index ?></div>
            </div>
        </a>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
