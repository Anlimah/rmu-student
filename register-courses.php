<?php
session_start();

if (!isset($_SESSION["student"]['login']) || $_SESSION["student"]['login'] !== true) header('Location: login.php');
if ($_SESSION["student"]['default_password']) header("Location: create-password.php");

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

$student_index = isset($_SESSION["student"]["index_number"]) && !empty($_SESSION["student"]["index_number"]) ? $_SESSION["student"]["index_number"] : "";
$student_data = $studentObj->fetchData($student_index);
$current_semester = $semster->currentSemester();

$student_level = 100;
$student_image = 'https://admissions.rmuictonline.com/apply/photos/' . $student_data["photo"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Home</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <?php require_once("inc/apply-head-section.php") ?>
    <style>
        /* Lighter Blue Outline Button */
        .btn-outline-primary-dark {
            --bs-btn-color: #003262;
            --bs-btn-border-color: #003262;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #003262;
            --bs-btn-hover-border-color: #003262;
            --bs-btn-focus-shadow-rgb: 0, 50, 98;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #003262;
            --bs-btn-active-border-color: #003262;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            --bs-btn-disabled-color: #003262;
            --bs-btn-disabled-bg: transparent;
            --bs-btn-disabled-border-color: #003262;
            --bs-gradient: none;
        }

        /* Darker Secondary Outline Button */
        .btn-outline-secondary-dark {
            --bs-btn-color: #444;
            --bs-btn-border-color: #444;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #444;
            --bs-btn-hover-border-color: #444;
            --bs-btn-focus-shadow-rgb: 68, 68, 68;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #444;
            --bs-btn-active-border-color: #444;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            --bs-btn-disabled-color: #444;
            --bs-btn-disabled-bg: transparent;
            --bs-btn-disabled-border-color: #444;
            --bs-gradient: none;
        }

        .sunken-border {
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
            border-bottom: 1px solid #ccc;
            box-shadow: inset 0 1px 0 #fff, inset 0 -1px 0 #fff, inset 1px 0 0 #fff, inset -1px 0 0 #fff, inset 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        .cr-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            border-radius: 5px;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .cr-card-item-group {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .cr-card-item-info {
            font-size: 20px;
            margin: 1px 0;
            font-weight: bolder;
        }

        .cr-card-item-title {
            font-size: 16px;
            margin: 1px 0;
            color: #003262;
            font-weight: bolder;
        }

        .transform-text {
            text-transform: uppercase !important;
        }

        .add-course-search>span {
            padding: 6px 20px;
            font-size: 18px;
        }

        .add-new-course {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        @media (min-width: 768px) {
            .cr-card {
                flex-direction: row;
            }

            .cr-card-item-group {
                margin: 0 20px;
            }
        }

        @media (max-width: 767.99px) {
            .add-new-course>.add-new-course-img {
                width: 40px;
            }

            .add-new-course>.add-new-course-txt {
                display: none;
            }
        }
    </style>
</head>

<body id="body">

    <div id="wrapper">

        <?php require_once("inc/page-nav2.php") ?>

        <main class="container">

            <div class="row sunken-border mb-4">
                <div class="col-xxl-12 col-md-12">
                    <h1 class="mt-4" style="font-size: 18px !important; font-weight:bold">REGISTRATION</h1>
                    <div id="course-registration-section">
                        <div id="course-registration-form-section">
                            <div class="alert alert-info" style="text-transform: uppercase; margin-bottom: 30px !important;"><b>SELECT SEMESTER COURSES FOR REGISTRATION</b></div>

                            <div class="mb-4" style="display:flex; justify-content: flex-end; align-items: flex-end;">
                                <div class="add-new-course">
                                    <img class="add-new-course-img" src="assets/images/icons8-add-48.png" width="30px" alt="add a course">
                                    <span class="add-new-course-txt" style="color:#003262; font-weight:bolder">ADD A COURSE</span>
                                </div>
                            </div>

                            <form id="register-semester-courses-form" method="post" enctype="multipart/form-data">
                                <table class="table" style="margin-bottom: 30px !important;">
                                    <colgroup>
                                        <col style="width: 90%; text-align: left;">
                                        <col style="width: 10%; text-align: right;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th style="text-align: left;">COURSE TITLE</th>
                                            <th style="text-align: right;">CREDITS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="compulsory-courses-display">
                                    </tbody>
                                    <tbody id="elective-courses-display">
                                    </tbody>
                                </table>

                                <div style="display: flex; justify-content: space-between; margin-top: 30px; margin-bottom: 30px;">
                                    <button type="button" class="btn btn-outline-secondary-dark" id="reset-semester-courses-btn">
                                        <span class="bi bi-x-square me-2"></span> <b>RESET</b>
                                    </button>

                                    <button class="btn btn-outline-primary-dark" id="register-semester-courses-btn">
                                        <span class="bi bi-save me-2"></span> <b>REGISTER</b>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-xxl-12 col-md-12">
                    <h1 style="font-size: 18px !important; font-weight:bold">SUMMARY</h1>
                    <div class="cr-card bg-secondary">
                        <div class="cr-card-item-group transform-text">
                            <div class="cr-card-item-info">
                                <?= $current_semester["academic_year_name"] ?> Semester <?= $current_semester["semester_name"] ?>
                            </div>
                            <div class="cr-card-item-title">Academic Session</div>
                        </div>
                        <div class="cr-card-item-group transform-text">
                            <div class="cr-card-item-info" id="total-registered-courses">0</div>
                            <div class="cr-card-item-title">Registered Courses</div>
                        </div>
                        <div class="cr-card-item-group transform-text">
                            <div class="cr-card-item-info" id="total-registered-credits">0</div>
                            <div class="cr-card-item-title">Total Credits</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for adding a course for registration -->
            <div class="modal fade" id="addCourseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="addCourseModalLabel">Add Course(s) to register</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <form id="add-course-search-form" class="mb-4 ">
                                <div style="display: flex; justify-content: right; align-items: center;">
                                    <div class="add-course-search" style="display: flex; justify-content: center; align-items:center">
                                        <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Search...">
                                        <span class="bi bi-search btn"></span>
                                    </div>
                                </div>
                            </form>

                            <table id="search-other-courses-tbl" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">COURSE TITLE</th>
                                        <th style="text-align: right;"></th>
                                    </tr>
                                </thead>
                                <tbody id="other-semester-courses">
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-primary-dark">Save</button>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <?php require_once("inc/app-sections-menu.php"); ?>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/myjs.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>

    <script>
        jQuery(document).ready(function($) {

            semesterCourses();
            registrationSummary();

            $(document).on("click", ".logout-btn", function() {
                window.location.href = "?logout";
            });

            $(document).on("change", ".btn-check", function() {
                var checkbox = $(this);
                var image = checkbox.closest("td").prev().find("img");
                var label = checkbox.next("label");

                if (!label.hasClass("disabled")) {
                    if (checkbox.prop("checked")) {
                        image.attr("src", "assets/images/icons8-correct-24.png");
                    } else {
                        image.attr("src", "assets/images/icons8-stop-48.png");
                    }
                } else {
                    return false;
                }
            });

            $(document).on("submit", "#register-semester-courses-form", function(e) {
                e.preventDefault();
                formData = new FormData(this);

                $.ajax({
                    type: "POST",
                    url: "api/student/register-courses",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            semesterCourses();
                            registrationSummary();
                        }
                        alert(result.message);
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 401) {
                            alert("Your session expired, logging you out...");
                            window.location.href = "?logout";
                        } else {
                            console.log("Error: " + status + " - " + error);
                        }
                    }
                });
            });

            $(document).on("click", "#reset-semester-courses-btn", function() {
                $.ajax({
                    type: "POST",
                    url: "api/student/reset-course-registration",
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            semesterCourses();
                            registrationSummary();
                        } else alert(result.message);
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 401) {
                            alert("Your session expired, logging you out...");
                            window.location.href = "?logout";
                        } else {
                            console.log("Error: " + status + " - " + error);
                        }
                    }
                });
            });

            $(document).on("click", ".add-new-course", function() {
                otherSemesterCourses();
                $("#addCourseModal").modal("show");
            });

            $(document).on("keyup", "#search-input", function() {
                if (this.value && this.value.length >= 3) $("#add-course-search-form").submit();
                if ($(this).val() === '') $('#search-other-courses-tbl tbody tr').show();
            });

            $(document).on("submit", "#add-course-search-form", function(e) {
                e.preventDefault();
                var searchText = $('#search-input').val().toLowerCase();

                $('#search-other-courses-tbl tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    if (searchText === '' || rowText.indexOf(searchText) === -1) $(this).hide();
                    else $(this).show();
                });
            });

            // Add click event handler to the tr
            $('#search-other-courses-tbl').on('click', 'tr', function() {
                var checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked'));
            });


            $(document).on({
                ajaxStart: function() {
                    $.LoadingOverlay("show");
                },
                ajaxStop: function() {
                    $.LoadingOverlay("hide");
                }
            });
        });
    </script>
</body>

</html>