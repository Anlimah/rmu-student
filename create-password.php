<?php
session_start();

require_once('bootstrap.php');

use Src\Core\Base;

if (Base::sessionExpire()) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION["student"]['login']) || $_SESSION["student"]['login'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['logout'])) {
    Base::logout();
    exit;
}

if (!isset($_SESSION["_start_create_password"])) {
    $rstrong = true;
    $_SESSION["_start_create_password"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Create Password</title>
    <?php require_once("inc/head.php") ?>
</head>

<body>
    <div class="login-page">

        <div class="login-page__header">
            <div class="login-page__brand">
                <img src="assets/images/rmu-logo-small.png" alt="RMU Logo" class="login-page__logo">
                <div>
                    <div class="login-page__title">REGIONAL MARITIME UNIVERSITY</div>
                    <div class="login-page__subtitle">Student Portal</div>
                </div>
            </div>
        </div>

        <main class="login-page__body">
            <div class="login-card">
                <img src="assets/images/icons8-security-user-male-96.png" alt="Create Password" class="login-card__icon">
                <h1 class="login-card__heading">Create New Password</h1>

                <div class="login-card__divider"></div>

                <div id="loginMsgDisplay"></div>

                <form id="appLoginForm">
                    <div class="form-group">
                        <input class="form-input form-input--underline" type="password" id="new-usp-password" name="new-usp-password" placeholder="New Password">
                        <div class="form-hint">8-16 characters with uppercase, lowercase, digit, and special character.</div>
                    </div>

                    <div class="form-group">
                        <input class="form-input form-input--underline" type="password" id="re-usp-password" placeholder="Retype Password">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn--primary btn--block">Create New Password</button>
                    </div>

                    <input type="hidden" name="_cpToken" value="<?= $_SESSION['_start_create_password'] ?>">
                </form>
            </div>
        </main>

        <footer class="app__footer">
            &copy; <?= date('Y') ?> Regional Maritime University. All rights reserved.
        </footer>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/portal.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on("click", ".logout-btn", function() {
                window.location.href = "?logout";
            });

            $("#appLoginForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "api/student/create-password",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        if (!result.success && result.message == "logout") {
                            showAlert('loginMsgDisplay', 'danger', result.message);
                            window.location.href = "?logout=true";
                            return;
                        }

                        if (result.success) {
                            showAlert('loginMsgDisplay', 'success', result.message);

                            setTimeout(function() {
                                showAlert('loginMsgDisplay', 'success', 'Setting up account...');
                                var payload = result.data;

                                $.ajax({
                                    type: "POST",
                                    url: "api/student/setup-account",
                                    data: payload,
                                    success: function(result) {
                                        if (!result.success && result.message == "logout") {
                                            window.location.href = "?logout=true";
                                            return;
                                        }
                                        setTimeout(function() {
                                            if (result.success) {
                                                showAlert('loginMsgDisplay', 'success', result.message);
                                            } else {
                                                showAlert('loginMsgDisplay', 'danger', result.message);
                                            }
                                            setTimeout(function() {
                                                window.location.href = "index.php";
                                            }, 1000);
                                        }, 1000);
                                    },
                                    error: function(xhr) {
                                        if (xhr.status == 401) {
                                            alert("Your session expired, logging you out...");
                                            window.location.href = "?logout";
                                        }
                                    }
                                });
                            }, 2000);
                        } else {
                            showAlert('loginMsgDisplay', 'danger', '<strong>' + result.message + '</strong>');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 401) {
                            alert("Your session expired, logging you out...");
                            window.location.href = "?logout";
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
