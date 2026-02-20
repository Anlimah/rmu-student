<?php
$page_title = 'Timetable';
require_once('inc/auth.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Timetable</title>
    <?php require_once("inc/head.php") ?>
</head>

<body>
    <div class="app">

        <?php require_once("inc/sidebar.php") ?>

        <div class="app__main">
            <?php require_once("inc/header.php") ?>

            <main class="app__content">

                <nav class="breadcrumb">
                    <a href="index.php" class="breadcrumb__item">Dashboard</a>
                    <span class="breadcrumb__separator"><i class="bi bi-chevron-right"></i></span>
                    <span class="breadcrumb__current">Timetable</span>
                </nav>

                <!-- Academic Session Info -->
                <?php if (!empty($current_semester)): ?>
                    <div class="summary-bar mb-6">
                        <div class="summary-bar__item">
                            <div class="summary-bar__icon"><i class="bi bi-calendar3"></i></div>
                            <div class="summary-bar__value"><?= $current_semester["academic_year_name"] ?></div>
                            <div class="summary-bar__label">Academic Year</div>
                        </div>
                        <div class="summary-bar__item">
                            <div class="summary-bar__icon"><i class="bi bi-mortarboard"></i></div>
                            <div class="summary-bar__value">Semester <?= $current_semester["semester_name"] ?></div>
                            <div class="summary-bar__label">Current Semester</div>
                        </div>
                        <div class="summary-bar__item">
                            <div class="summary-bar__icon"><i class="bi bi-journal-bookmark"></i></div>
                            <div class="summary-bar__value" id="total-scheduled">0</div>
                            <div class="summary-bar__label">Scheduled Courses</div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Loading state -->
                <div id="timetable-loading" class="card mb-6">
                    <div class="card__body">
                        <div class="empty-state">
                            <div class="empty-state__icon"><i class="bi bi-arrow-repeat spin"></i></div>
                            <div class="empty-state__title">Loading Timetable</div>
                            <div class="empty-state__message">Fetching your schedule for the current semester...</div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Grid (populated via JS if schedule data exists) -->
                <div id="schedule-section" style="display: none;">
                    <div class="card mb-6">
                        <div class="card__header">
                            <h3 class="card__title"><i class="bi bi-calendar-week"></i> Weekly Schedule</h3>
                            <button id="btn-print-schedule" class="btn btn--sm btn--outline">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        </div>

                        <div class="table-wrapper">
                            <table class="table timetable-grid" id="schedule-grid">
                                <thead>
                                    <tr>
                                        <th class="timetable-grid__day-header">Day</th>
                                        <th>Time</th>
                                        <th>Course</th>
                                        <th>Venue</th>
                                    </tr>
                                </thead>
                                <tbody id="schedule-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Timetable PDF Files (populated via JS if files exist) -->
                <div id="files-section" style="display: none;">
                    <div class="card mb-6">
                        <div class="card__header">
                            <h3 class="card__title"><i class="bi bi-file-earmark-pdf"></i> Timetable Documents</h3>
                        </div>
                        <div id="timetable-files-list" class="timetable-files">
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div id="timetable-empty" class="card mb-6" style="display: none;">
                    <div class="card__body">
                        <div class="empty-state">
                            <div class="empty-state__icon"><i class="bi bi-calendar-x"></i></div>
                            <div class="empty-state__title">No Timetable Available</div>
                            <div class="empty-state__message" id="empty-message">No timetable has been published for the current semester yet.</div>
                        </div>
                    </div>
                </div>

            </main>

            <?php require_once("inc/footer.php") ?>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/portal.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $.ajax({
                type: "GET",
                url: "api/student/timetable",
                success: function(result) {
                    $('#timetable-loading').hide();

                    if (result.success) {
                        var data = result.message;
                        var schedule = data.schedule;
                        var files = data.files;

                        // Render schedule grid
                        if (schedule && schedule.length > 0) {
                            renderSchedule(schedule);
                            $('#schedule-section').show();
                        }

                        // Render timetable files
                        if (files && files.length > 0) {
                            renderFiles(files);
                            $('#files-section').show();
                        }

                        // If neither schedule nor files, show empty (shouldn't happen since API returns error)
                        if ((!schedule || schedule.length === 0) && (!files || files.length === 0)) {
                            $('#timetable-empty').show();
                        }
                    } else {
                        $('#empty-message').text(result.message);
                        $('#timetable-empty').show();
                    }
                },
                error: function(xhr) {
                    $('#timetable-loading').hide();
                    if (xhr.status == 401) {
                        alert("Your session expired, logging you out...");
                        window.location.href = "?logout";
                    } else {
                        $('#empty-message').text('Failed to load timetable. Please try again.');
                        $('#timetable-empty').show();
                    }
                }
            });

            function escapeHtml(str) {
                if (!str) return '';
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(str));
                return div.innerHTML;
            }

            function renderSchedule(schedule) {
                var $body = $('#schedule-body');
                var days = {};
                var courseSet = new Set();

                // Group by day
                schedule.forEach(function(item) {
                    var day = item.day_of_week;
                    if (!days[day]) days[day] = [];
                    days[day].push(item);
                    courseSet.add(item.course_code);
                });

                $('#total-scheduled').text(courseSet.size);

                var dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

                dayOrder.forEach(function(day) {
                    if (!days[day]) return;
                    var entries = days[day];

                    entries.forEach(function(entry, index) {
                        var startTime = formatTime(entry.start_time);
                        var endTime = formatTime(entry.end_time);
                        var venue = escapeHtml(entry.room_number) || '—';
                        if (entry.room_location) venue += ' (' + escapeHtml(entry.room_location) + ')';

                        var html = '<tr>';
                        if (index === 0) {
                            html += '<td class="timetable-grid__day" rowspan="' + entries.length + '">' +
                                '<span class="timetable-grid__day-name">' + escapeHtml(day) + '</span>' +
                                '</td>';
                        }
                        html += '<td class="timetable-grid__time">' + startTime + ' – ' + endTime + '</td>' +
                            '<td>' +
                            '<span class="font-semibold text-navy">' + escapeHtml(entry.course_code) + '</span>' +
                            '<div class="text-sm text-muted">' + escapeHtml(entry.course_name) + '</div>' +
                            '</td>' +
                            '<td><i class="bi bi-geo-alt"></i> ' + venue + '</td>' +
                            '</tr>';
                        $body.append(html);
                    });
                });
            }

            function renderFiles(files) {
                var $list = $('#timetable-files-list');

                files.forEach(function(file) {
                    var uploadDate = new Date(file.uploaded_at).toLocaleDateString('en-GB', {
                        day: 'numeric', month: 'short', year: 'numeric'
                    });

                    var safeTitle = escapeHtml(file.title);
                    var safePath = encodeURI(file.file_path);

                    var html = '<div class="timetable-file">' +
                        '<div class="timetable-file__info">' +
                        '<i class="bi bi-file-earmark-pdf-fill timetable-file__icon"></i>' +
                        '<div>' +
                        '<div class="timetable-file__title">' + safeTitle + '</div>' +
                        '<div class="timetable-file__date">Uploaded: ' + uploadDate + '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="timetable-file__actions">' +
                        '<a href="' + safePath + '" target="_blank" class="btn btn--sm btn--primary">' +
                        '<i class="bi bi-eye"></i> View</a>' +
                        '<a href="' + safePath + '" download class="btn btn--sm btn--outline">' +
                        '<i class="bi bi-download"></i> Download</a>' +
                        '</div>' +
                        '</div>';
                    $list.append(html);
                });
            }

            function formatTime(timeStr) {
                if (!timeStr) return '—';
                var parts = timeStr.split(':');
                var hours = parseInt(parts[0]);
                var minutes = parts[1];
                var ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                return hours + ':' + minutes + ' ' + ampm;
            }

            // Print schedule
            $('#btn-print-schedule').on('click', function() {
                var semesterLabel = <?= json_encode(
                    ($current_semester["academic_year_name"] ?? '') . ' — Semester ' . ($current_semester["semester_name"] ?? '')
                ) ?>;
                var studentName = <?= json_encode($student_data["full_name"] ?? '') ?>;
                var studentIndex = <?= json_encode($student_index ?? '') ?>;
                var programName = <?= json_encode($student_data["program_name"] ?? '') ?>;

                var printWindow = window.open('', '_blank');
                var tableHtml = $('#schedule-grid').clone().wrap('<div>').parent().html();

                var html = '<!DOCTYPE html><html><head><title>Timetable - ' + studentName + '</title>' +
                    '<style>' +
                    'body { font-family: "Times New Roman", serif; margin: 40px; color: #333; }' +
                    '.header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #003262; padding-bottom: 15px; }' +
                    '.header h1 { color: #003262; font-size: 20px; margin: 0 0 5px; }' +
                    '.header h2 { color: #003262; font-size: 16px; margin: 0 0 10px; font-weight: normal; }' +
                    '.student-info { margin-bottom: 20px; }' +
                    '.student-info table { width: 100%; border: none; }' +
                    '.student-info td { padding: 3px 10px; border: none; font-size: 13px; }' +
                    '.student-info .label { font-weight: bold; width: 150px; }' +
                    'table { width: 100%; border-collapse: collapse; font-size: 12px; }' +
                    'th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; }' +
                    'th { background: #003262; color: #fff; }' +
                    '.timetable-grid__day { background: #f0f4f8; font-weight: bold; vertical-align: top; }' +
                    '.text-sm { font-size: 11px; }' +
                    '.text-muted { color: #666; }' +
                    '.font-semibold { font-weight: 600; }' +
                    '.text-navy { color: #003262; }' +
                    '.footer { margin-top: 40px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #ccc; padding-top: 10px; }' +
                    '@media print { body { margin: 20px; } }' +
                    '</style></head><body>' +
                    '<div class="header">' +
                    '<h1>REGIONAL MARITIME UNIVERSITY</h1>' +
                    '<h2>Weekly Timetable</h2>' +
                    '</div>' +
                    '<div class="student-info"><table>' +
                    '<tr><td class="label">Student Name:</td><td>' + studentName + '</td>' +
                    '<td class="label">Index Number:</td><td>' + studentIndex + '</td></tr>' +
                    '<tr><td class="label">Programme:</td><td>' + programName + '</td>' +
                    '<td class="label">Semester:</td><td>' + semesterLabel + '</td></tr>' +
                    '</table></div>' +
                    tableHtml +
                    '<div class="footer">Generated from RMU Student Portal on ' +
                    new Date().toLocaleDateString('en-GB', {day: 'numeric', month: 'long', year: 'numeric'}) +
                    '</div></body></html>';

                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.onload = function() {
                    printWindow.print();
                };
            });
        });
    </script>
</body>

</html>
