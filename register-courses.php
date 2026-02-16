<?php
$page_title = 'Course Registration';
require_once('inc/auth.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Course Registration</title>
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
                    <span class="breadcrumb__current">Course Registration</span>
                </nav>

                <div id="courses-area">

                    <!-- Registration Form -->
                    <div class="card mb-6">
                        <div class="card__header">
                            <h3 class="card__title">Course Registration</h3>
                        </div>

                        <div id="course-registration-section">
                            <div id="course-registration-form-section">

                                <div class="alert alert--info">
                                    <span class="alert__icon bi bi-info-circle-fill"></span>
                                    <div class="alert__content">Select all the courses you want to register for the semester and click the Register button.</div>
                                </div>

                                <form id="register-semester-courses-form" method="post">
                                    <div class="table-wrapper mb-6">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 60%;">Course Title</th>
                                                    <th style="width: 20%; text-align: center;">Category</th>
                                                    <th style="width: 20%; text-align: right;">Credits</th>
                                                </tr>
                                            </thead>
                                            <tbody id="compulsory-courses-display">
                                            </tbody>
                                            <tbody id="elective-courses-display">
                                            </tbody>
                                            <tbody id="other-semester-courses-display">
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="display-message"></div>

                                    <div class="flex justify-between mt-6 mb-4">
                                        <button type="button" class="btn btn--outline-secondary" id="reset-semester-courses-btn">
                                            <i class="bi bi-x-square"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn--primary" id="register-semester-courses-btn">
                                            <i class="bi bi-save"></i> Register
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Summary -->
                    <div class="registration-summary" style="display: none;">
                        <h3 class="section-title">Summary</h3>
                        <div class="summary-bar">
                            <div class="summary-bar__item">
                                <div class="summary-bar__value">
                                    <?= !empty($current_semester) ? $current_semester["academic_year_name"] . ' Sem ' . $current_semester["semester_name"] : 'N/A' ?>
                                </div>
                                <div class="summary-bar__label">Academic Session</div>
                            </div>
                            <div class="summary-bar__item">
                                <div class="summary-bar__value" id="total-registered-courses">0</div>
                                <div class="summary-bar__label">Registered Courses</div>
                            </div>
                            <div class="summary-bar__item">
                                <div class="summary-bar__value" id="total-registered-credits">0</div>
                                <div class="summary-bar__label">Total Credits</div>
                            </div>
                        </div>
                    </div>

                </div>

            </main>

            <?php require_once("inc/footer.php") ?>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/portal.js"></script>
    <script>
        jQuery(document).ready(function($) {

            semesterCourses();
            otherSemesterCourses();

            // Register courses
            $(document).on("submit", "#register-semester-courses-form", function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var btn = $("#register-semester-courses-btn");
                btn.prop("disabled", true).html('<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span> Registering...');

                $.ajax({
                    type: "POST",
                    url: "api/student/register-courses",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        btn.prop("disabled", false).html('<i class="bi bi-save"></i> Register');
                        if (result.success) {
                            showAlert('display-message', 'success', result.message);
                            semesterCourses();
                            otherSemesterCourses();
                        } else {
                            showAlert('display-message', 'danger', result.message);
                        }
                    },
                    error: function(xhr) {
                        btn.prop("disabled", false).html('<i class="bi bi-save"></i> Register');
                        if (xhr.status == 401) {
                            alert("Your session expired, logging you out...");
                            window.location.href = "?logout";
                        }
                    }
                });
            });

            // Reset registration
            $(document).on("click", "#reset-semester-courses-btn", function() {
                if (!confirm("Are you sure you want to reset your course registration?")) return;

                $.ajax({
                    type: "POST",
                    url: "api/student/reset-course-registration",
                    success: function(result) {
                        if (result.success) {
                            showAlert('display-message', 'success', result.message);
                            semesterCourses();
                            otherSemesterCourses();
                        } else {
                            showAlert('display-message', 'danger', result.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 401) {
                            alert("Your session expired, logging you out...");
                            window.location.href = "?logout";
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
