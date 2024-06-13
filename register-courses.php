<?php
session_start();

require_once('bootstrap.php');

use Src\Core\Base;
// Base::dd($_SESSION);

if (Base::sessionExpire());

if (!isset($_SESSION["student"]['login']) || $_SESSION["student"]['login'] !== true) header('Location: login.php');
if ($_SESSION["student"]['default_password']) header("Location: create-password.php");

if (isset($_GET['logout'])) Base::logout();

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

        /* Darker Green Outline Button */
        .btn-outline-success-dark {
            --bs-btn-color: #086132;
            --bs-btn-border-color: #086132;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #086132;
            --bs-btn-hover-border-color: #086132;
            --bs-btn-focus-shadow-rgb: 0, 100, 0;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #086132;
            --bs-btn-active-border-color: #086132;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            --bs-btn-disabled-color: #086132;
            --bs-btn-disabled-bg: transparent;
            --bs-btn-disabled-border-color: #086132;
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

            <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
                <symbol id="check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                </symbol>
                <symbol id="info-fill" viewBox="0 0 16 16">
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                </symbol>
                <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                </symbol>
            </svg>

            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Course Registration</li>
                </ol>
            </nav>

            <div class="row sunken-border mb-4">
                <div class="col-xxl-12 col-md-12">

                    <h1 class="mt-4" style="font-size: 18px !important; font-weight:bold">Course Registration</h1>

                    <div id="course-registration-section">
                        <div id="course-registration-form-section">

                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle-fill me-2" role="img"></i>
                                <div> Select all the courses you want to register for the semester and click register button</div>
                            </div>

                            <form id="register-semester-courses-form" method="post" enctype="multipart/form-data">
                                <table class="table" style="margin-bottom: 30px !important;">
                                    <colgroup>
                                        <col style="width: 90%; text-align: left;">
                                        <col style="width: 10%; text-align: right;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th style="text-align: left;">Course Title</th>
                                            <th style="text-align: right;">Credits</th>
                                        </tr>
                                    </thead>
                                    <tbody id="compulsory-courses-display">
                                    </tbody>
                                    <tbody id="elective-courses-display">
                                    </tbody>
                                    <tbody id="other-semester-courses-display">
                                    </tbody>
                                </table>

                                <div style="display: flex; justify-content: space-between; margin-top: 30px; margin-bottom: 30px;">
                                    <button type="button" class="btn btn-outline-secondary-dark" id="reset-semester-courses-btn">
                                        <span class="bi bi-x-square me-2"></span> <b>Reset</b>
                                    </button>

                                    <button class="btn btn-outline-primary-dark" id="register-semester-courses-btn">
                                        <span class="bi bi-save me-2"></span> <b>Register</b>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4 registration-summary" style="display: none;">
                <div class="col-xxl-12 col-md-12">
                    <h1 style="font-size: 18px !important; font-weight:bold">Summary</h1>
                    <div class="cr-card bg-secondary">
                        <div class="cr-card-item-group">
                            <div class="cr-card-item-info">
                                <?= $current_semester["academic_year_name"] ?> Semester <?= $current_semester["semester_name"] ?>
                            </div>
                            <div class="cr-card-item-title">Academic Session</div>
                        </div>
                        <div class="cr-card-item-group">
                            <div class="cr-card-item-info" id="total-registered-courses">0</div>
                            <div class="cr-card-item-title">Registered Courses</div>
                        </div>
                        <div class="cr-card-item-group">
                            <div class="cr-card-item-info" id="total-registered-credits">0</div>
                            <div class="cr-card-item-title">Total Credits</div>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <?php require_once("inc/app-sections-menu.php"); ?>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/myjs.js"></script>
    <script src="js/loadingoverlay.min.js"></script>
    <script>
        jQuery(document).ready(function($) {

            semesterCourses();
            otherSemesterCourses();

            $(document).on("click", ".logout-btn", function() {
                window.location.href = "?logout";
            });

            $(document).on("change", ".btn-check", function() {
                var checkbox = $(this);
                var image = checkbox.closest("td").prev().find("img");
                var label = checkbox.next("label");

                if (!label.hasClass("disabled")) {
                    if (checkbox.prop("checked")) {
                        image.attr("src", "assets/images/icons8-check-30.png");
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
                            otherSemesterCourses();
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
                            otherSemesterCourses();
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

            /*$(document).on({
                ajaxStart: function() {
                    $.LoadingOverlay("show");
                },
                ajaxStop: function() {
                    $.LoadingOverlay("hide");
                }
            });*/
        });
    </script>
</body>

</html>