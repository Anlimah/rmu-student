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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Add shadow here */
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

                                    <div class="student-name transform-text" style="text-align: center; color: #FFA000; padding-top: 10px; font-weight: 600">
                                        <?= $student_data["full_name"] ?>
                                    </div>

                                    <div class="student-index transform-text" style="text-align: center; color: white; font-weight: 600">
                                        <?= $student_index ?>
                                    </div>

                                    <div style="display: flex; justify-content: center; align-items:center; margin-top: 40px; ">
                                        <div class="student-program me-2 transform-text" style="color: white; font-weight: 600">
                                            <?= $student_data["program_name"] ?>
                                        </div>
                                        <div class="student-program transform-text" style="color: #FFA000; font-weight: 600">
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
                                            <b class="transform-text"><?= $semester ?> semester course registration opened.</b>
                                        </h6>
                                        <hr>
                                        <p class="mb-0 transform-text">Registration ends on <b><?= $registration_end ?> at 11:59 PM.</b></p>
                                        <hr>
                                        <p class="mb-0 d-flex" style="justify-content: right;">
                                            <button class="btn btn-outline-success transform-text" id="register-here-btn">Register Here</button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">

                            <div class="col-xxl-4 col-md-6 mb-4">
                                <div class="item-card">
                                    <img src="assets/images/icons8-courses-64.png" alt="Icon">
                                    <p>SEMESTER COURSES</p>
                                    <a href="semester-courses.php" class="arrow-link"><i class="bi bi-box-arrow-in-down-right"></i></a>
                                </div>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-4">
                                <div class="item-card">
                                    <img src="assets/images/icons8-timetable-96(1).png" alt="Icon">
                                    <p>EXAM & CLASS TIMETABLE</p>
                                    <a href="timetable.php" class="arrow-link"><i class="bi bi-box-arrow-in-down-right"></i></a>
                                </div>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-4">
                                <div class="item-card">
                                    <img src="assets/images/icons8-exam-96.png" alt="Icon">
                                    <p>EXAM RESULTS</p>
                                    <a href="exam-results.php" class="arrow-link"><i class="bi bi-box-arrow-in-down-right"></i></a>
                                </div>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-4">
                                <div class="item-card">
                                    <img src="assets/images/icons8-report-card-64(1).png" alt="Icon">
                                    <p>COURSE & LECTURER EVALUATION</p>
                                    <a href="#" class="arrow-link"><i class="bi bi-box-arrow-in-down-right"></i></a>
                                </div>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-4">
                                <div class="item-card">
                                    <img src="assets/images/icons8-hostel-64.png" alt="Icon">
                                    <p>HOSTEL & ACCOMMODATION</p>
                                    <a href="#" class="arrow-link"><i class="bi bi-box-arrow-in-down-right"></i></a>
                                </div>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-4">
                                <div class="item-card">
                                    <img src="assets/images/icons8-books-emoji-96.png" alt="Icon">
                                    <p>LIBRARY</p>
                                    <a href="#" class="arrow-link"><i class="bi bi-box-arrow-in-down-right"></i></a>
                                </div>
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

        });
    </script>
</body>

</html>