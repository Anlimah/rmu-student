<?php
session_start();

if (!isset($_SESSION['login'])) header('Location: login.php');
if ($_SESSION['login'] !== true) header('Location: login.php');

if (isset($_GET['logout'])) {
    session_destroy();
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    header('Location: login.php');
}


require_once('bootstrap.php');

use Src\Controller\Semester;
use Src\Controller\Student;
use Src\Core\Base;

$config = require('config/database.php');

$studentObj = new Student($config["database"]["mysql"]);
$semster = new Semester($config["database"]["mysql"]);
$student_index = isset($_SESSION['index_number']) && !empty($_SESSION["index_number"]) ? $_SESSION["index_number"] : "";
$student_data = $studentObj->fetchData($student_index);
//Base::dd($student_data);
$current_semester = $semster->currentSemester();

// /Base::dd($current_semester);
if (!empty($current_semester)) $semester = $current_semester["semester_name"] . "<sup>st</sup>";
else $semster = $current_semester["semester_name"] . "<sup>nd</sup>";

$student_level = 100;
$student_image = 'https://admissions.rmuictonline.com/apply/photos/' . $student_data["photo"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Home</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <?php require_once("inc/apply-head-section.php") ?>
</head>

<body id="body">

    <div id="wrapper">

        <?php require_once("inc/page-nav2.php") ?>

        <main class="container">
            <div class="row">

                <div class="col-md-12">
                    <section id="page_info" style="margin-bottom: 0px !important;">

                        <h1 class="mb-4 mt-4">L<?= $student_level ?> <?= $semester ?> SEMESTER </h1>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-xxl-12 col-md-12">
                                <div class="alert alert-info" style="text-transform: uppercase;"><b>SELECT SEMESTER COURSES FOR REGISTRATION</b></div>
                                <table class="table table-borderless">
                                    <colgroup>
                                        <col style="width: 90%">
                                        <col>
                                    </colgroup>

                                    <thead>
                                        <th>COURSE TITLE</th>
                                        <th>CREDITS</th>
                                    </thead>

                                    <tbody>
                                        <tr class="alert alert-warning">
                                            <td colspan="2"><strong>COMPULSORY COURSES</strong></td>
                                        </tr>

                                        <?php
                                        $courses = $studentObj->fetchSemesterCompulsoryCourses($student_index, $current_semester["semester_id"]);
                                        //Base::dd($courses);
                                        foreach ($courses as $course) {
                                        ?>
                                            <tr>
                                                <td style="display: flex;">
                                                    <span class="me-2">
                                                        <img src="assets/images/icons8-stop-48.png" alt="" style="width: 24px !important">
                                                    </span>
                                                    <span><?= $course["course_name"] ?></span>
                                                </td>
                                                <td style="text-align:center">
                                                    <input type="checkbox" id="btn-check-<?= $course["course_name"] ?>" class="btn-check" autocomplete="off" style="display: none;">
                                                    <label class="btn btn-light btn-outline-success-dark" style="width: 50px !important" for="btn-check-<?= $course["course_code"] ?>">3</label>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>

                                        <tr class="alert alert-warning">
                                            <td colspan="2"><strong>ELECTIVE COURSES</strong></td>
                                        </tr>

                                        <?php
                                        $courses = $studentObj->fetchSemesterCompulsoryCourses($student_index, $current_semester["semester_id"]);
                                        //Base::dd($courses);
                                        foreach ($courses as $course) {
                                        ?>
                                            <tr>
                                                <td style="display: flex;">
                                                    <span class="me-2">
                                                        <img src="assets/images/icons8-stop-48.png" alt="" style="width: 24px !important">
                                                    </span>
                                                    <span><?= $course["course_name"] ?></span>
                                                </td>
                                                <td style="text-align:center">
                                                    <input type="checkbox" id="btn-check-<?= $course["course_name"] ?>" class="btn-check" autocomplete="off" style="display: none;">
                                                    <label class="btn btn-light btn-outline-success-dark" style="width: 50px !important" for="btn-check-<?= $course["course_code"] ?>">3</label>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>

                                    </tbody>
                                </table>


                                <div style="display: flex; justify-content:flex-end;margin-top: 30px">
                                    <button class="btn btn-primary"><span class="bi bi-save me-2"></span> SAVE REGISTRATION</button>
                                </div>
                            </div>
                        </div>

                    </section>
                </div>

            </div>
        </main>
        <?php require_once("inc/page-footer.php"); ?>

        <?php require_once("inc/app-sections-menu.php"); ?>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/myjs.js"></script>
    <script>
        $(document).ready(function() {
            var incompleteForm = false;
            var itsForm = false;

            $(".btn-check").change(function() {
                var checkbox = $(this);
                var image = checkbox.closest("td").prev().find("img");

                if (checkbox.prop("checked")) {
                    image.attr("src", "assets/images/icons8-correct-24.png");
                } else {
                    image.attr("src", "assets/images/icons8-stop-48.png");
                }
            });

            let semesterCourses = function() {
                $.ajax({
                    type: "GET",
                    url: "api/student/semester-courses",
                    success: function(result) {
                        console.log(result);
                        $(".edu-mod-grade").html('<option value="Grade" hidden>Grade</option>');
                        $.each(result, function(index, value) {
                            $(".edu-mod-grade").append('<option value="' + value.grade + '">' + value.grade + '</option>');
                        });
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
            semesterCourses();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on({
                ajaxStart: function() {
                    // Show full page LoadingOverlay
                    $.LoadingOverlay("show");
                },
                ajaxStop: function() {
                    // Hide it after 3 seconds
                    $.LoadingOverlay("hide");
                }
            });
        });
    </script>
</body>

</html>