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

                        <div class="row mb-4">
                            <div class="col-xxl-12 col-md-12">
                                <div class="card profile-card" style="background-color: #003262; border-color: transparent !important; padding: 15px 15px">
                                    <div class="student-img" style="text-align: center; padding-top: 20px;">
                                        <img src="<?= $student_image ?>" alt="<?= $student_data["full_name"] ?>" style="border-radius: 50%; border: 2px solid white; width: 100px; height: 100px;">
                                    </div>

                                    <div class="student-name" style="text-align: center; color: #FFA000; padding-top: 10px; text-transform: uppercase; font-weight: 600">
                                        <?= $student_data["full_name"] ?>
                                    </div>

                                    <div class="student-index" style="text-align: center; color: white; text-transform: uppercase; font-weight: 600">
                                        <?= $student_index ?>
                                    </div>

                                    <div style="display: flex; justify-content: center; align-items:center; margin-top: 40px; ">
                                        <div class="student-program me-2" style="color: white; text-transform: uppercase; font-weight: 600">
                                            <?= $student_data["program_name"] ?>
                                        </div>
                                        <div class="student-program" style="color: #FFA000; text-transform: uppercase; font-weight: 600">
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
                                            <b><?= $semester ?> semester course registration opened.</b>
                                        </h6>
                                        <hr>
                                        <p class="mb-0">Registration ends on <b><?= $registration_end ?> at 11:59 PM.</b></p>
                                        <hr>
                                        <p class="mb-0 d-flex" style="justify-content: right;">
                                            <button class="btn btn-outline-success" id="register-here-btn">Register Here</button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">

                            <div class="col-xxl-4 col-md-6 mb-4">
                                <div class="card info-card sales-card">
                                    <div class="card-body">
                                        <a href="applications.php?t=1&c=MASTERS">
                                            <div class="d-flex align-items-center">
                                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <img src="assets/images/icons8-check-24.png" style="width: 48px;" alt="">
                                                </div>
                                                <div class="ps-3">
                                                    <span class="text-muted small pt-2 ps-1">
                                                        <h5 class="card-title">Courses</h5>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-4 col-md-6 mb-4">
                                <div class="card info-card sales-card">
                                    <div class="card-body">
                                        <a href="applications.php?t=1&c=MASTERS">
                                            <div class="d-flex align-items-center">
                                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <img src="assets/images/icons8-check-24.png" style="width: 48px;" alt="">
                                                </div>
                                                <div class="ps-3">
                                                    <span class="text-muted small pt-2 ps-1">
                                                        <h5 class="card-title">Results</h5>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
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

            $("#register-here-btn").on("click", function() {
                window.location.href = "register-courses.php";
            });

        });
    </script>
</body>

</html>