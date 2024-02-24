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

                        <h1 class="mb-4 mt-4">L<?= $student_level ?> <?= $semester ?> Semester </h1>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-xxl-12 col-md-12">
                                <h4 style="text-transform: uppercase;">Select semester courses for registration</h4>
                                <div class="alert alert-warning">COMPULSORY COURSES FOR THIS SEMESTER REGISTRATION</div>
                                <table class="table table-borderless">
                                    <thead>
                                        <th>COURSE TITLE</th>
                                        <th>CREDITS</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="display: flex; justify-content: flex-start; align-items:center">
                                                <span class="me-2">
                                                    <img src="assets/images/icons8-checkmark-24.png" alt="" style="width: 24px;">
                                                </span>
                                                <span><?= $program ?></span>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="btn-check" id="btn-check<?= $course_id ?>" autocomplete="off">
                                                <label class="btn btn-light btn-outline-success w-100" for="btn-check<?= $course_id ?>">3</label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-warning">ELECTIVE COURSES FOR THIS SEMESTER REGISTRATION</div>
                                <table class="table table-borderless">
                                    <thead>
                                        <th>COURSE TITLE</th>
                                        <th>CREDITS</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="display: flex; justify-content: flex-start; align-items:center">
                                                <span class="me-2">
                                                    <img src="assets/images/icons8-checkmark-24.png" alt="" style="width: 24px;">
                                                </span>
                                                <span><?= $program ?></span>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="btn-check" id="btn-check<?= $course_id ?>" autocomplete="off">
                                                <label class="btn btn-light btn-outline-success w-100" for="btn-check<?= $course_id ?>">3</label>
                                            </td>
                                        </tr>
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

            (() => {
                'use strict'

                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                const forms = document.querySelectorAll('.needs-validation')

                // Loop over them and prevent submission
                Array.from(forms).forEach(form => {
                    form.addEventListener('submit', event => {
                        event.preventDefault()
                        if (!form.checkValidity()) {
                            event.stopPropagation()
                            incompleteForm = true;
                            $("#page_info_text").removeClass("hide");
                            $("#page_info_text").addClass("display");
                            window.location.href = "#body";
                        } else {
                            incompleteForm = false;
                            itsForm = true;
                            $("#page_info_text").removeClass("display");
                            $("#page_info_text").addClass("hide");
                        }

                        form.classList.add('was-validated')
                    }, false)
                })

            })();

            $(".prev-uni-rec").click(function() {
                if ($('#prev-uni-rec-yes').is(':checked')) {
                    $("#prev-uni-rec-list").removeClass("hide");
                } else if ($('#prev-uni-rec-no').is(':checked')) {
                    $("#prev-uni-rec-list").addClass("hide");
                }
            });

            $(".completed-prev-uni").click(function() {
                if ($('#completed-prev-uni-yes').is(':checked')) {
                    $("#date-completed-uni").removeClass("hide");
                    $("#uni-not-completed").addClass("hide");
                } else if ($('#completed-prev-uni-no').is(':checked')) {
                    $("#uni-not-completed").removeClass("hide");
                    $("#date-completed-uni").addClass("hide");
                }
            });

            $(".awaiting-result").click(function() {
                if ($('#awaiting-result-yes').is(':checked')) {
                    //$("#not-waiting").addClass("hide");
                    $("#not-waiting").slideUp(200);
                    $("#awaiting_result_value").attr("value", 1);
                }
                if ($('#awaiting-result-no').is(':checked')) {
                    //$("#not-waiting").removeClass("hide");
                    $("#not-waiting").slideDown(200);
                    $("#awaiting_result_value").attr("value", 0);
                }

                if ($('#edit-awaiting-result-yes').is(':checked')) {
                    //$("#edit-not-waiting").addClass("hide");
                    $("#edit-not-waiting").slideUp(200);
                    $("#edit-awaiting_result_value").attr("value", 1);
                }
                if ($('#edit-awaiting-result-no').is(':checked')) {
                    //$("#edit-not-waiting").removeClass("hide");
                    $("#edit-not-waiting").slideDown(200);
                    $("#edit-awaiting_result_value").attr("value", 0);
                }
            });

            $(".form-select").change("blur", function() {
                // For add education background
                if (this.id == "cert-type") {

                    var myArray = ['WASSCE', 'SSSCE', 'NECO', 'GBCE'];
                    let index = $.inArray(this.value, myArray);

                    if (index == -1) {
                        $("#course-studied").slideUp();
                        $("#course-studied option[value='OTHER']").attr('selected', 'selected');
                        $(".other-course-studied").slideDown();
                        $(".waec-course-content").slideUp();

                        if (this.value == "OTHER") $(".sepcific-cert").slideDown();

                        $("#awaiting-result-yes").attr("checked", "checked");
                        $("#awaiting-result-no").attr("checked", "");

                    } else {
                        $("#course-studied").slideDown();
                        $(".other-course-studied").slideUp();
                        $(".waec-course-content").slideDown();
                        $(".sepcific-cert").slideUp();

                        $("#awaiting-result-yes").attr("checked", "");
                        $("#awaiting-result-no").attr("checked", "checked");
                    }
                }

                if (this.id == "course-studied") {
                    if (this.value == "OTHER") {
                        $(".other-course-studied").slideUp(200);
                    } else {
                        $(".other-course-studied").slideDown(200);
                    }
                }

                // For edit education background
                if (this.id == "edit-cert-type") {
                    var myArray = ['WASSCE', 'SSSCE', 'NECO', 'GBCE'];
                    let index = $.inArray(this.value, myArray);

                    if (index == -1) {
                        $("#edit-course-studied").slideUp();
                        $("#edit-course-studied option[value='OTHER']").attr('selected', 'selected');
                        $(".edit-other-course-studied").slideDown();
                        $(".edit-waec-course-content").slideUp();

                        if (this.value == "OTHER") $(".edit-sepcific-cert").slideDown();

                        $("#edit-awaiting-result-yes").attr("checked", "checked");
                        $("#edit-awaiting-result-no").attr("checked", "");

                    } else {
                        $("#edit-course-studied").slideDown();
                        $(".edit-other-course-studied").slideUp();
                        $(".edit-waec-course-content").slideDown();
                        $(".edit-sepcific-cert").slideUp();

                        $("#edit-awaiting-result-yes").attr("checked", "");
                        $("#edit-awaiting-result-no").attr("checked", "checked");
                    }
                }

                if (this.id == "edit-course-studied") {
                    if (this.value == "OTHER") {
                        $(".edit-other-course-studied").slideToggle(200);
                    } else {
                        $(".edit-other-course-studied").slideUp(200);
                    }
                }
            });

            $(".form-select-option").change("blur", function() {
                $.ajax({
                    type: "PUT",
                    url: "api/prev-uni-recs",
                    data: {
                        what: this.name,
                        value: this.value,
                    },
                    success: function(result) {
                        console.log(result);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $(".form-text-input").on("blur", function() {
                $.ajax({
                    type: "PUT",
                    url: "api/prev-uni-recs",
                    data: {
                        what: this.name,
                        value: this.value,
                    },
                    success: function(result) {
                        console.log(result);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $(".form-radio-btn").on("click", function() {

                const inputs = document.querySelectorAll('.required-field');
                const completed = document.querySelectorAll('.completed-uni');
                const not_completed = document.querySelectorAll('.not-completed-uni');

                if (this.id == "prev-uni-rec-yes") {
                    for (const input of inputs) {
                        input.setAttribute('required', '');
                    }
                } else if (this.id == "prev-uni-rec-no") {
                    for (const input of inputs) {
                        input.removeAttribute('required');
                    }
                    for (const inp of completed) {
                        inp.removeAttribute('required', '');
                    }
                    for (const inp of not_completed) {
                        inp.removeAttribute('required', '');
                    }
                }

                if (this.id == "completed-prev-uni-yes") {
                    for (const inp of completed) {
                        inp.setAttribute('required', '');
                    }
                    for (const inp of not_completed) {
                        inp.removeAttribute('required', '');
                    }
                } else if (this.id == "completed-prev-uni-no") {
                    for (const inp of completed) {
                        inp.removeAttribute('required', '');
                    }
                    for (const inp of not_completed) {
                        inp.setAttribute('required', '');
                    }
                }

                $.ajax({
                    type: "PUT",
                    url: "api/prev-uni-recs",
                    data: {
                        what: this.name,
                        value: this.value,
                    },
                    success: function(result) {
                        console.log(result);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $("#cert-type, #edit-cert-type").change("blur", function() {
                var myArray = ['WASSCE', 'SSSCE', 'NECO', 'GBCE'];
                let index = $.inArray(this.value, myArray);

                if (index == -1) return;

                $.ajax({
                    type: "GET",
                    url: "api/grades",
                    data: {
                        what: this.name,
                        value: this.value,
                    },
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
            });

            $("#course-studied").change("blur", function() {
                let value = "technical";
                if (this.value != "TECHNICAL") value = "secondary";
                $.ajax({
                    type: "GET",
                    url: "api/elective-subjects",
                    data: {
                        value: value,
                    },
                    success: function(result) {
                        console.log(result);
                        $(".elective-subjects").html('<option value="Select" hidden>Select</option>');
                        $.each(result, function(index, value) {
                            $(".elective-subjects").append('<option value="' + value.subject + '">' + value.subject + '</option>');
                        });
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $("#appForm").on("submit", function() {
                if (!incompleteForm) {
                    $.ajax({
                        type: "POST",
                        url: "api/validateForm/",
                        data: {
                            form: this.name,
                        },
                        success: function(result) {
                            console.log(result);
                            if (result.success) {
                                window.location.href = "application-step3.php";
                            } else {
                                $("#page_info_text").removeClass("hide");
                                $("#page_info_text").addClass("display");
                                $("#data_info").html("").append(result.message);
                                window.location.href = "#body";
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
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