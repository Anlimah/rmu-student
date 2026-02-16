<?php
session_start();
require_once('bootstrap.php');

if (!isset($_SESSION["_start_forgot_password"])) {
    $rstrong = true;
    $_SESSION["_start_forgot_password"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Forgot Password</title>
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
                <img src="assets/images/icons8-forgot-password-96(1).png" alt="Forgot Password" class="login-card__icon">
                <h1 class="login-card__heading">Forgot Password</h1>

                <div class="login-card__divider"></div>

                <div id="loginMsgDisplay"></div>

                <form id="appLoginForm">
                    <div class="form-group">
                        <input class="form-input form-input--underline" type="email" id="email-address" name="email-address" placeholder="Enter email address">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn--primary btn--block">Submit</button>
                    </div>

                    <input type="hidden" name="_cfeToken" value="<?= $_SESSION['_start_forgot_password'] ?>">
                </form>

                <a href="login.php" class="login-card__link">Back to Sign In</a>
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
                        if (result.success) {
                            showAlert('loginMsgDisplay', 'success', result.message);
                        } else {
                            showAlert('loginMsgDisplay', 'danger', result.message);
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
