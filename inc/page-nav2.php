<nav class="fp-header">
    <div class="container" style="display: flex; justify-content: space-between; align-items:center">
        <div class="items logo-board col-md-10">
            <img src="assets/images/rmu-logo-small.png" alt="RMU logo" style="width: 60px;">
            <div class="flex-column justify-center" style="height: 100% !important; line-height: 1.2">
                <span class="rmu-logo-letter logo-letter">REGIONAL MARITIME UNIVERSITY</span>
                <span class="rmu-logo-letter description">STUDENT PORTAL</span>
            </div>
        </div>
        <div class="col-md-2 sign-out-1" style="display: flex; align-items: center; justify-content:space-between">
            <div id="sign-out-1" style="text-align:center; display: relative">
                <a href="?logout=true" style="color: #fff !important">Sign Out</a>
            </div>

            <span class="open-sections-menu dropdown bi bi-list dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="font-size: 28px; color: #fff">
                <ul class="dropdown-menu">
                    <?php if (isset($_SESSION["student"]["default_password"]) && !$_SESSION["student"]["default_password"]) { ?>
                        <li>
                            <a class="dropdown-item" href="">
                                <span class="bi bi-person-circle"></span> Your Profile
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    <?php } ?>
                    <li>
                        <a class="dropdown-item" href="?logout=true">
                            <span class="bi bi-box-arrow-right"></span> Sign Out
                        </a>
                    </li>
                </ul>
            </span>
        </div>
    </div>
</nav>