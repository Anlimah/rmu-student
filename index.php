<?php
session_start();

require_once('bootstrap.php');

use Src\Core\Base;

//Base::dd($_SESSION);

if (Base::sessionExpire()) {
    echo "<script>alert('Your session expired, logging you out...');</script>";
}

if (!isset($_SESSION["student"]['login']) || $_SESSION["student"]['login'] !== true) header('Location: login.php');
if ($_SESSION["student"]['default_password']) header("Location: create-password.php");

if (isset($_GET['logout'])) Base::logout();

use Src\Controller\Semester;
use Src\Controller\Student;

$config = require('config/database.php');
$student_index = isset($_SESSION["student"]['index_number']) && !empty($_SESSION["student"]["index_number"]) ? $_SESSION["student"]["index_number"] : "";

$studentObj = new Student($config["database"]["mysql"]);
$student_data = $studentObj->fetchData($student_index);

$semster = new Semester($config["database"]["mysql"]);
$current_semester = $semster->currentSemester();

if (!empty($current_semester)) {
    if ($current_semester["semester_name"] == 1)
        $semester = $current_semester["semester_name"] . "<sup>st</sup>";
    elseif ($current_semester["semester_name"] == 2)
        $semester = $current_semester["semester_name"] . "<sup>nd</sup>";
}

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
        .item-card {
            display: flex;
            align-items: center;
            height: 80px;
            padding: 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
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
    </style>
</head>

<body id="body">

    <div id="wrapper">

        <?php require_once("inc/page-nav2.php") ?>

        <main class="container">
            <div class="row">

                <div class="col-md-12">
                    <section id="page_info" style="margin-bottom: 0px !important;">

                        <div class="row mb-4">
                            <div class="col-xxl-12 col-md-12">
                                <div class="profile-card">
                                    <div class="student-img" style="text-align: center; padding-top: 20px;">
                                        <img src="<?= $student_image ?>" alt="<?= $student_data["full_name"] ?>" style="border-radius: 50%; border: 2px solid white; width: 100px; height: 100px;">
                                    </div>

                                    <div class="student-name " style="text-align: center; color: #FFA000; padding-top: 10px; font-weight: 600">
                                        <?= $student_data["full_name"] ?>
                                    </div>

                                    <div class="student-index " style="text-align: center; color: white; font-weight: 600">
                                        <?= $student_index ?>
                                    </div>

                                    <div style="display: flex; justify-content: center; align-items:center; margin-top: 40px; ">
                                        <div class="student-program me-2 " style="color: white; font-weight: 600">
                                            <?= $student_data["program_name"] ?>
                                        </div>
                                        <div class="student-program " style="color: #FFA000; font-weight: 600">
                                            [<?= $student_data["class_code"] ?>]
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <?php if (empty($current_semester) || !$current_semester["reg_open_status"]) { ?>
                            <div class="row mb-4">
                                <div class="col-xxl-12 col-md-12">
                                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                                        <span class="bi bi-exclamation-triangle-fill me-2"></span>
                                        <b><?= $semester ?> semester course registration closed</b>
                                    </div>
                                </div>
                            </div>
                        <?php } else {
                            $registration_end = (new \DateTime($current_semester["reg_end_date"]))->format("l F j, Y");
                        ?>
                            <div class="row mb-4">
                                <div class="col-xxl-12 col-md-12">
                                    <div class="alert alert-success" role="alert">
                                        <h6 class="alert-heading d-flex align-items-center">
                                            <span class="bi bi-exclamation-triangle-fill me-2"></span>
                                            <b class=""><?= $semester ?> semester course registration opened.</b>
                                        </h6>
                                        <hr>
                                        <p class="mb-0 ">Registration ends on <b><?= $registration_end ?> at 11:59 PM.</b></p>
                                        <hr>
                                        <p class="mb-0 d-flex" style="justify-content: right;">
                                            <button class="btn btn-outline-success " id="register-here-btn">Register Here</button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">

                            <div class="col-xxl-4 col-md-6 mb-2">
                                <a href="semester-courses.php?myCoursesTab=SEMESTER_COURSES">
                                    <div class="item-card">
                                        <img src="assets/images/icons8-courses-64.png" alt="Icon">
                                        <p>Semeter Courses</p>
                                        <i class="arrow-link bi bi-box-arrow-in-down-right"></i>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-2">
                                <a href="exam-results.php">
                                    <div class="item-card">
                                        <img src="assets/images/icons8-exam-96.png" alt="Icon">
                                        <p>Exam Results</p>
                                        <i class="arrow-link bi bi-box-arrow-in-down-right"></i>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-2">
                                <a href="timetable.php">
                                    <div class="item-card">
                                        <img src="assets/images/icons8-timetable-94.png" alt="Icon">
                                        <p>Timetable</p>
                                        <i class="arrow-link bi bi-box-arrow-in-down-right"></i>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-2">
                                <a href="#">
                                    <div class="item-card">
                                        <img src="assets/images/icons8-exam-96(1).png" alt="Icon">
                                        <p>Course & Lectruer Evaluation</p>
                                        <i class="arrow-link bi bi-box-arrow-in-down-right"></i>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-2">
                                <a href="#">
                                    <div class="item-card">
                                        <img src="assets/images/icons8-hostel-55.png" alt="Icon">
                                        <p>Hostel & Accomodation</p>
                                        <i class="arrow-link bi bi-box-arrow-in-down-right"></i>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-2">
                                <a href="#">
                                    <div class="item-card">
                                        <img src="assets/images/icons8-books-94.png" alt="Icon">
                                        <p>Library</p>
                                        <i class="arrow-link bi bi-box-arrow-in-down-right"></i>
                                    </div>
                                </a>
                            </div>

                        </div>

                    </section>
                </div>

            </div>
        </main>

        <!-- footer -->

        <?php require_once("inc/app-sections-menu.php"); ?>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/myjs.js"></script>
    <script>
        $(document).ready(function() {
            var incompleteForm = false;
            var itsForm = false;

            $("#register-here-btn").on("click", function() {
                window.location.href = "register-courses.php";
            });

            $(document).on("click", ".logout-btn", function() {
                window.location.href = "?logout";
            });

        });
    </script>
</body>

</html>