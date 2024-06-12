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

        .sunken-border-d {
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

        .item-card {
            display: flex;
            align-items: center;
            height: 100%;
            padding: 5px 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            /* Add shadow here */
        }

        .item-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .item-card img {
            width: 50px;
            height: 50px;
            margin-right: 20px;
        }

        .item-card p {
            color: #003262;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .arrow-link {
            margin-left: auto;
            color: #003262;
            text-decoration: none;
            padding-left: 10px;
            font-size: 18px;
        }

        .transform-text {
            text-transform: uppercase !important;
        }

        .profile-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            background-color: #003262 !important;
            border-radius: 5px !important;
            border-color: transparent !important;
            padding: 15px 15px !important;
        }

        .title-pill {
            padding: 6px 12px;
            border-radius: 25px;
            background-color: #fff;
            color: #353535;
            border-radius: 25px;
            margin-bottom: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            margin-right: 8px;
            border: 1px solid #aaa;
        }

        .title-pill:hover {
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .title-pill.active {
            background-color: #353535;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: #fff !important;
            font-weight: 500 !important;
        }

        .title-pill.active:hover {
            background-color: #003262 !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: #fff !important;
            font-weight: 500 !important;
            border: 1px solid #aaa !important;
        }
    </style>
</head>

<body id="body">

    <div id="wrapper">

        <?php require_once("inc/page-nav2.php") ?>

        <main class="container">

            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Courses</li>
                </ol>
            </nav>

            <div class="row sunken-border-d mb-4">
                <div class="col-xxl-12 col-md-12">

                    <h1 class="mt-4" style="font-size: 18px !important; font-weight:bold">My Courses</h1>

                    <div class="row">

                        <div class="col-xxl-12 col-md-12 mb-4">
                            <a style="text-transform: uppercase; " id="SEMESTER_COURSES" class="col title-pill <?= isset($_GET["myCoursesTab"]) && $_GET["myCoursesTab"] == 'SEMESTER_COURSES' ? 'active' : '' ?>" href="?myCoursesTab=SEMESTER_COURSES">This Semester</a>
                            <a style="text-transform: uppercase; " id="REGISTERED_COURSES" class="col title-pill <?= isset($_GET["myCoursesTab"]) && $_GET["myCoursesTab"] == 'REGISTERED_COURSES' ? 'active' : '' ?>" href="?myCoursesTab=REGISTERED_COURSES">Registered</a>
                        </div>

                        <div class="col-xxl-12 col-md-12 mb-4">
                            <div style="padding: 0px 5px; background: #ddd; width: 100%">
                                <span style="text-transform: uppercase;">
                                    Total: <span style="font-size: large;" id="total_courses"></span> | Credits: <span style="font-size: large;" id="total_credits"></span>
                                </span>
                            </div>
                        </div>
                        <div id="semester-courses" style="display: none;"></div>
                        <div id="registered-courses" style="display: none;"></div>


                    </div>
                </div>
            </div>

            <!-- Modal for adding a course for registration -->
            <div class="modal fade" id="aboutCourseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="aboutCourseModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="aboutCourseModalLabel">About Course</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h1>Introduction to Web Software Architecture</h1>
                            <div>
                                <h2>Department</h2>
                                <p>ICT</p>
                            </div>
                            <div>
                                <h2>Level</h2>
                                <p>100</p>
                            </div>
                            <div>
                                <h2>Semester</h2>
                                <p>1</p>
                            </div>
                            <div>
                                <h2>Learning Objectives</h2>
                                <ul>
                                    <li>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quaerat vitae quos ipsa, harum at commodi cumque, eius in deserunt distinctio cum velit dolor totam minima debitis magni nulla. Aliquam, consectetur!</li>
                                    <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatibus eum non dicta perspiciatis odio, qui sapiente necessitatibus? Qui cupiditate, a id assumenda voluptatum nam illum commodi optio, ullam eos recusandae!</li>
                                    <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum ad, mollitia aspernatur natus, iusto cupiditate ab distinctio culpa et in aliquid temporibus, sunt itaque labore fuga a? Molestiae, repellendus tenetur.</li>
                                </ul>
                            </div>
                            <div>
                                <h2>Resources</h2>
                                <h3>Books</h3>
                                <ul>
                                    <li>Quaerat vitae quos ipsa, harum at commodi cumque</li>
                                    <li>Qui cupiditate, a id assumenda voluptatum nam illum</li>
                                    <li>Illum ad, mollitia aspernatur natus, iusto cupiditate</li>
                                </ul>
                                <h3>Links</h3>
                                <ul>
                                    <li><a href="#">Quaerat vitae quos ipsa, harum at commodi cumque</a></li>
                                    <li><a href="#">Qui cupiditate, a id assumenda voluptatum nam illum</a></li>
                                    <li><a href="#">Illum ad, mollitia aspernatur natus, iusto cupiditate</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <?php require_once("inc/app-sections-menu.php"); ?>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/myjs.js"></script>
    <script>
        jQuery(document).ready(function($) {

            $(document).on("click", ".item-card", function() {
                $cid = $(this).attr("id");
                courseInfo($cid);
                $("#aboutCourseModal").modal("show");
            });

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
                    // Prevent the checkbox from being toggled
                    return false;
                }
            });

            switch (getURLParam('myCoursesTab')) {
                case 'SEMESTER_COURSES':
                    $.ajax({
                        type: "GET",
                        url: "api/student/semester-courses",
                        success: function(result) {
                            console.log(result);

                            if (result.success) {
                                $("#registered-courses").hide();
                                $("#semester-courses").html('');

                                total_courses = result.message.length;
                                total_credits = 0;
                                result.message.forEach(function(value) {
                                    total_credits += value.credits;
                                    var courseHtml = '<div class="col-xxl-6 col-md-12 mb-3">' +
                                        '<div class="item-card" id="' + value.course_code + '">' +
                                        '<img src="assets/images/icons8-course-assign-96.png" alt="Icon">' +
                                        '<div style="text-transform: uppercase;">' +
                                        '<p>' + value.course_name + '</p>' +
                                        '<div>Level: ' + value.level + ' | Semester: ' + value.semester + ' | Credits: ' + value.credits + '</div>' +
                                        '</div>' +
                                        '<i class="arrow-link bi bi-caret-right-fill"></i>' +
                                        '</div>' +
                                        '</div>';
                                    $("#semester-courses").append(courseHtml);
                                });
                                $("#total_courses").text(total_courses);
                                $("#total_credits").text(total_credits);
                                $("#semester-courses").show();
                                return;
                            }
                        },
                        error: function(xhr, status, error) {
                            if (xhr.status == 401) {
                                alert("Your session expired, logging you out...");
                                window.location.href = "?logout";
                            } else console.log("Error: " + status + " - " + error);
                        }
                    });
                    break;

                case 'REGISTERED_COURSES':
                    $.ajax({
                        type: "GET",
                        url: "api/student/registered-semester-courses",
                        success: function(result) {
                            console.log(result);

                            if (result.success) {
                                $("#semester-courses").hide('');
                                $("#registered-courses").html();

                                total_courses = result.message.length;
                                total_credits = 0;
                                result.message.forEach(function(value) {
                                    total_credits += value.credits;
                                    var courseHtml = '<div class="col-xxl-6 col-md-12 mb-3">' +
                                        '<div class="item-card" id="' + value.course_code + '">' +
                                        '<img src="assets/images/icons8-course-registered-96.png" alt="Icon">' +
                                        '<div style="text-transform: uppercase;">' +
                                        '<p>' + value.course_name + '</p>' +
                                        '<div>Level: ' + value.level + ' | Semester: ' + value.semester + ' | Credits: ' + value.credits + '</div>' +
                                        '</div>' +
                                        '<i class="arrow-link bi bi-caret-right-fill"></i>' +
                                        '</div>' +
                                        '</div>';
                                    $("#registered-courses").append(courseHtml);
                                });
                                $("#total_courses").text(total_courses);
                                $("#total_credits").text(total_credits);
                                $("#registered-courses").show();
                                return;
                            }
                        },
                        error: function(xhr, status, error) {
                            if (xhr.status == 401) {
                                alert("Your session expired, logging you out...");
                                window.location.href = "?logout";
                            } else console.log("Error: " + status + " - " + error);
                        }
                    });
                    break;

                default:
                    break;
            };

        });
    </script>
    <script src="js/loadingoverlay.min.js"></script>
    <script>
        $(document).ready(function() {
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