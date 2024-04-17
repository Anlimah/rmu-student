<?php
session_start();

require_once('bootstrap.php');

use Src\Core\Base;

if (Base::sessionExpire());

if (!isset($_SESSION["student"]['login']) || $_SESSION["student"]['login'] !== true) header('Location: login.php');
if ($_SESSION["student"]['default_password']) header("Location: create-password.php");

if (isset($_GET['logout'])) Base::logout();
require_once('bootstrap.php');

use Src\Controller\Semester;
use Src\Controller\Student;

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


        @media (min-width: 768px) {
            .cr-card {
                flex-direction: row;
            }

            .cr-card-item-group {
                margin: 0 20px;
            }
        }
    </style>
</head>

<body id="body">

    <div id="wrapper">

        <?php require_once("inc/page-nav2.php") ?>

        <main class="container">

            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" style="text-transform: uppercase;"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page" style="text-transform: uppercase;">Timetable</li>
                </ol>
            </nav>

            <div class="row sunken-border mb-4">
                <div class="col-xxl-12 col-md-12">

                    <h1 class="mt-4" style="text-transform: uppercase; font-size: 18px !important; font-weight:bold">Timetable</h1>

                    <table class="table table-borderless" style="margin-bottom: 30px !important;">
                        <colgroup>
                            <col style="width: 90%">
                            <col>
                        </colgroup>
                        <thead>
                            <tr>
                                <th>COURSE TITLE</th>
                                <th>CREDITS</th>
                            </tr>
                        </thead>
                        <tbody id="compulsory-courses-display">
                        </tbody>
                        <tbody id="elective-courses-display">
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="row">
                <div class="col-xxl-12 col-md-12">
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

            <input type="hidden" name="student" id="student" value="<?= $_SESSION["student"]["index_number"] ?>">
            <input type="hidden" name="semester" id="semester" value="<?= $_SESSION["semester"]["id"] ?>">
        </main>

        <?php require_once("inc/app-sections-menu.php"); ?>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/myjs.js"></script>
    <script>
        jQuery(document).ready(function($) {

            $(document).on("click", ".logout-btn", function() {
                alert("ok")
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
                    // Prevent the checkbox from being toggled
                    return false;
                }
            });

            semesterCourses();
            registrationSummary();

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