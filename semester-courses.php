<?php
$page_title = 'My Courses';
require_once('inc/auth.php');

$activeTab = isset($_GET["myCoursesTab"]) ? $_GET["myCoursesTab"] : 'SEMESTER_COURSES';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | My Courses</title>
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
                    <span class="breadcrumb__current">My Courses</span>
                </nav>

                <!-- Tabs -->
                <div class="tabs">
                    <a href="?myCoursesTab=SEMESTER_COURSES" class="tab <?= $activeTab === 'SEMESTER_COURSES' ? 'tab--active' : '' ?>">
                        This Semester
                    </a>
                    <a href="?myCoursesTab=REGISTERED_COURSES" class="tab <?= $activeTab === 'REGISTERED_COURSES' ? 'tab--active' : '' ?>">
                        Registered
                    </a>
                </div>

                <!-- Course Count Bar -->
                <div class="flex items-center gap-4 mb-6 p-4 bg-gray rounded-lg">
                    <span class="text-sm font-semibold text-uppercase">
                        Total: <span class="text-lg text-navy" id="total_courses">0</span>
                        &nbsp;|&nbsp;
                        Credits: <span class="text-lg text-navy" id="total_credits">0</span>
                    </span>
                </div>

                <!-- Course Lists -->
                <div id="semester-courses" style="display: none;" class="grid grid--2 grid--auto-fit"></div>
                <div id="registered-courses" style="display: none;" class="grid grid--2 grid--auto-fit"></div>

                <!-- Course Info Modal -->
                <div class="modal-overlay" id="courseModal">
                    <div class="modal">
                        <div class="modal__header">
                            <h3 class="modal__title">About Course</h3>
                            <button class="modal__close" onclick="closeModal('courseModal')">&times;</button>
                        </div>
                        <div class="modal__body">
                            <div class="empty-state">
                                <div class="empty-state__icon"><i class="bi bi-book"></i></div>
                                <div class="empty-state__message">Loading course details...</div>
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

            // Open course modal on click
            $(document).on("click", ".course-item", function() {
                var cid = $(this).attr("data-course");
                courseInfo(cid);
                openModal('courseModal');
            });

            var activeTab = '<?= $activeTab ?>';

            if (activeTab === 'SEMESTER_COURSES') {
                $.ajax({
                    type: "GET",
                    url: "api/student/semester-courses",
                    success: function(result) {
                        if (result.success) {
                            $("#registered-courses").hide();
                            $("#semester-courses").html('');

                            var total_courses = result.message.length;
                            var total_credits = 0;

                            result.message.forEach(function(value) {
                                total_credits += value.credits;
                                var courseHtml =
                                    '<div class="course-item" data-course="' + value.course_code + '">' +
                                    '<img src="assets/images/icons8-course-assign-96.png" alt="" class="course-item__icon">' +
                                    '<div class="course-item__info">' +
                                    '<div class="course-item__name">' + value.course_name + '</div>' +
                                    '<div class="course-item__meta">Level: ' + value.level + ' | Semester: ' + value.semester + ' | Credits: ' + value.credits + '</div>' +
                                    '</div>' +
                                    '<i class="course-item__arrow bi bi-chevron-right"></i>' +
                                    '</div>';
                                $("#semester-courses").append(courseHtml);
                            });

                            $("#total_courses").text(total_courses);
                            $("#total_credits").text(total_credits);
                            $("#semester-courses").show();
                        } else {
                            $("#semester-courses").html(
                                '<div class="empty-state">' +
                                '<div class="empty-state__icon"><i class="bi bi-book"></i></div>' +
                                '<div class="empty-state__title">No Courses Found</div>' +
                                '<div class="empty-state__message">' + result.message + '</div>' +
                                '</div>'
                            );
                            $("#semester-courses").show();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 401) {
                            alert("Your session expired, logging you out...");
                            window.location.href = "?logout";
                        }
                    }
                });
            } else if (activeTab === 'REGISTERED_COURSES') {
                $.ajax({
                    type: "GET",
                    url: "api/student/registered-semester-courses",
                    success: function(result) {
                        if (result.success) {
                            $("#semester-courses").hide();
                            $("#registered-courses").html('');

                            var total_courses = result.message.length;
                            var total_credits = 0;

                            result.message.forEach(function(value) {
                                total_credits += value.credits;
                                var courseHtml =
                                    '<div class="course-item" data-course="' + value.course_code + '">' +
                                    '<img src="assets/images/icons8-course-registered-96.png" alt="" class="course-item__icon">' +
                                    '<div class="course-item__info">' +
                                    '<div class="course-item__name">' + value.course_name + '</div>' +
                                    '<div class="course-item__meta">Level: ' + value.level + ' | Semester: ' + value.semester + ' | Credits: ' + value.credits + '</div>' +
                                    '</div>' +
                                    '<i class="course-item__arrow bi bi-chevron-right"></i>' +
                                    '</div>';
                                $("#registered-courses").append(courseHtml);
                            });

                            $("#total_courses").text(total_courses);
                            $("#total_credits").text(total_credits);
                            $("#registered-courses").show();
                        } else {
                            $("#registered-courses").html(
                                '<div class="empty-state">' +
                                '<div class="empty-state__icon"><i class="bi bi-clipboard-check"></i></div>' +
                                '<div class="empty-state__title">No Registered Courses</div>' +
                                '<div class="empty-state__message">' + result.message + '</div>' +
                                '</div>'
                            );
                            $("#registered-courses").show();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 401) {
                            alert("Your session expired, logging you out...");
                            window.location.href = "?logout";
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>
