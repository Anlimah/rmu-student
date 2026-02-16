<?php
$page_title = 'Exam Results';
require_once('inc/auth.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Exam Results</title>
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
                    <span class="breadcrumb__current">Exam Results</span>
                </nav>

                <!-- Academic Session Info -->
                <?php if (!empty($current_semester)): ?>
                    <div class="summary-bar mb-6">
                        <div class="summary-bar__item">
                            <div class="summary-bar__value"><?= $current_semester["academic_year_name"] ?> Sem <?= $current_semester["semester_name"] ?></div>
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
                <?php endif; ?>

                <!-- Results Table -->
                <div class="card mb-6">
                    <div class="card__header">
                        <h3 class="card__title">Semester Results</h3>
                    </div>

                    <div id="results-display">
                        <div class="table-wrapper">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Title</th>
                                        <th style="text-align: center;">Credits</th>
                                        <th style="text-align: center;">Grade</th>
                                    </tr>
                                </thead>
                                <tbody id="results-table-body">
                                </tbody>
                            </table>
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
            // Load registered courses for the current semester
            $.ajax({
                type: "GET",
                url: "api/student/registered-semester-courses",
                success: function(result) {
                    if (result.success) {
                        var total_courses = result.message.length;
                        var total_credits = 0;

                        result.message.forEach(function(value) {
                            total_credits += value.credits;
                            var courseHtml = '<tr>' +
                                '<td><span class="font-semibold text-navy">' + value.course_code + '</span></td>' +
                                '<td>' + value.course_name + '</td>' +
                                '<td style="text-align: center;">' + value.credits + '</td>' +
                                '<td style="text-align: center;"><span class="badge badge--gray">Pending</span></td>' +
                                '</tr>';
                            $("#results-table-body").append(courseHtml);
                        });

                        $("#total-registered-courses").text(total_courses);
                        $("#total-registered-credits").text(total_credits);
                    } else {
                        $("#results-table-body").html(
                            '<tr><td colspan="4">' +
                            '<div class="empty-state">' +
                            '<div class="empty-state__icon"><i class="bi bi-clipboard-data"></i></div>' +
                            '<div class="empty-state__title">No Results Available</div>' +
                            '<div class="empty-state__message">' + result.message + '</div>' +
                            '</div></td></tr>'
                        );
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
    </script>
</body>

</html>
