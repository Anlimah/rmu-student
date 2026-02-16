<header class="app__header">
    <div class="header__left">
        <button class="header__menu-btn" id="menuToggle" aria-label="Toggle menu">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="header__page-title"><?= $page_title ?? 'Dashboard' ?></h1>
    </div>

    <div class="header__right">
        <?php if (!empty($current_semester)): ?>
            <span class="badge badge--navy hidden-mobile">
                <?= $current_semester["academic_year_name"] ?> &middot; Semester <?= $current_semester["semester_name"] ?>
            </span>
        <?php endif; ?>

        <div class="header__user-menu">
            <button class="header__user-btn" id="userMenuBtn">
                <img src="<?= $student_image ?>" alt="Profile" class="header__user-avatar">
                <span class="hidden-mobile text-sm font-medium"><?= $student_data["first_name"] ?? '' ?></span>
                <i class="bi bi-chevron-down text-xs text-muted"></i>
            </button>
            <div class="header__dropdown" id="userDropdown">
                <a href="profile.php" class="header__dropdown-item">
                    <i class="bi bi-person-circle"></i>
                    My Profile
                </a>
                <div class="header__dropdown-divider"></div>
                <a href="?logout" class="header__dropdown-item" style="color: var(--rmu-red);">
                    <i class="bi bi-box-arrow-right"></i>
                    Sign Out
                </a>
            </div>
        </div>
    </div>
</header>
