<?php
$page_title = 'Exam Results';
require_once('inc/auth.php');

$all_semesters = $semesterObj->allSemesters();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Exam Results</title>
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
                    <span class="breadcrumb__current">Exam Results</span>
                </nav>

                <!-- Semester Filter -->
                <div class="card mb-6">
                    <div class="card__body">
                        <div class="results-filter">
                            <div class="results-filter__group">
                                <label for="semester-select" class="results-filter__label">Select Semester</label>
                                <select id="semester-select" class="form-select">
                                    <option value="">-- Choose Academic Year &amp; Semester --</option>
                                    <?php if (!empty($all_semesters)): ?>
                                        <?php foreach ($all_semesters as $sem): ?>
                                            <option value="<?= $sem['semester_id'] ?>">
                                                <?= htmlspecialchars($sem['academic_year_name']) ?> &mdash; Semester <?= $sem['semester_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button id="btn-load-results" class="btn btn--primary" disabled>
                                <i class="bi bi-search"></i> View Results
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Content -->
                <div id="results-content" style="display: none;">

                    <!-- GPA Summary -->
                    <div class="summary-bar mb-6">
                        <div class="summary-bar__item">
                            <div class="summary-bar__icon"><i class="bi bi-journal-text"></i></div>
                            <div class="summary-bar__value" id="summary-courses">0</div>
                            <div class="summary-bar__label">Courses</div>
                        </div>
                        <div class="summary-bar__item">
                            <div class="summary-bar__icon"><i class="bi bi-stack"></i></div>
                            <div class="summary-bar__value" id="summary-credits">0</div>
                            <div class="summary-bar__label">Total Credits</div>
                        </div>
                        <div class="summary-bar__item">
                            <div class="summary-bar__icon"><i class="bi bi-award"></i></div>
                            <div class="summary-bar__value" id="summary-gpa">0.00</div>
                            <div class="summary-bar__label">Semester GPA</div>
                        </div>
                        <div class="summary-bar__item">
                            <div class="summary-bar__icon"><i class="bi bi-trophy"></i></div>
                            <div class="summary-bar__value" id="summary-cgpa">0.00</div>
                            <div class="summary-bar__label">Cumulative GPA</div>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div class="card mb-6">
                        <div class="card__header">
                            <h3 class="card__title" id="results-heading">Semester Results</h3>
                            <button id="btn-download-pdf" class="btn btn--sm btn--outline">
                                <i class="bi bi-download"></i> Download PDF
                            </button>
                        </div>

                        <div id="results-display">
                            <div class="table-wrapper">
                                <table class="table" id="results-table">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Title</th>
                                            <th style="text-align: center;">Credits</th>
                                            <th style="text-align: center;">CA Score</th>
                                            <th style="text-align: center;">Exam Score</th>
                                            <th style="text-align: center;">Total</th>
                                            <th style="text-align: center;">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody id="results-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Grading Scale Reference (collapsible) -->
                    <div class="card mb-6">
                        <div class="card__header card__header--toggle" id="grading-scale-toggle" style="cursor: pointer; margin-bottom: 0; border-bottom: none;">
                            <h3 class="card__title"><i class="bi bi-info-circle"></i> Grading Scale</h3>
                            <i class="bi bi-chevron-down" id="grading-scale-icon"></i>
                        </div>
                        <div class="table-wrapper" id="grading-scale-body" style="display: none;">
                            <table class="table table--sm">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Score Range</th>
                                        <th style="text-align: center;">Grade</th>
                                        <th style="text-align: center;">Grade Point</th>
                                        <th style="text-align: center;">Interpretation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td style="text-align:center;">80 &ndash; 100</td><td style="text-align:center;"><span class="badge badge--green">A</span></td><td style="text-align:center;">4.00</td><td style="text-align:center;">Excellent</td></tr>
                                    <tr><td style="text-align:center;">75 &ndash; 79</td><td style="text-align:center;"><span class="badge badge--green">A-</span></td><td style="text-align:center;">3.85</td><td style="text-align:center;">Very Good</td></tr>
                                    <tr><td style="text-align:center;">70 &ndash; 74</td><td style="text-align:center;"><span class="badge badge--navy">B+</span></td><td style="text-align:center;">3.00</td><td style="text-align:center;">Good</td></tr>
                                    <tr><td style="text-align:center;">65 &ndash; 69</td><td style="text-align:center;"><span class="badge badge--navy">B</span></td><td style="text-align:center;">2.85</td><td style="text-align:center;">Above Average</td></tr>
                                    <tr><td style="text-align:center;">60 &ndash; 64</td><td style="text-align:center;"><span class="badge badge--gold">C+</span></td><td style="text-align:center;">2.50</td><td style="text-align:center;">Average</td></tr>
                                    <tr><td style="text-align:center;">55 &ndash; 59</td><td style="text-align:center;"><span class="badge badge--gold">C</span></td><td style="text-align:center;">2.00</td><td style="text-align:center;">Below Average</td></tr>
                                    <tr><td style="text-align:center;">50 &ndash; 54</td><td style="text-align:center;"><span class="badge badge--gray">D</span></td><td style="text-align:center;">1.50</td><td style="text-align:center;">Pass</td></tr>
                                    <tr><td style="text-align:center;">45 &ndash; 49</td><td style="text-align:center;"><span class="badge badge--gray">E</span></td><td style="text-align:center;">1.00</td><td style="text-align:center;">Marginal Pass</td></tr>
                                    <tr><td style="text-align:center;">0 &ndash; 44</td><td style="text-align:center;"><span class="badge badge--danger">F</span></td><td style="text-align:center;">0.00</td><td style="text-align:center;">Fail</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Initial empty state (before selection) -->
                <div id="results-empty-state" class="card mb-6">
                    <div class="card__body">
                        <div class="empty-state">
                            <div class="empty-state__icon"><i class="bi bi-clipboard-data"></i></div>
                            <div class="empty-state__title">Select a Semester</div>
                            <div class="empty-state__message">Choose an academic year and semester above to view your exam results.</div>
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
            var $semesterSelect = $('#semester-select');
            var $btnLoad = $('#btn-load-results');
            var $resultsContent = $('#results-content');
            var $emptyState = $('#results-empty-state');
            var $tableBody = $('#results-table-body');

            function escapeHtml(str) {
                if (!str) return '';
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(str));
                return div.innerHTML;
            }

            // Grading scale toggle
            $('#grading-scale-toggle').on('click', function() {
                var $body = $('#grading-scale-body');
                var $icon = $('#grading-scale-icon');
                $body.slideToggle(200);
                $icon.toggleClass('bi-chevron-down bi-chevron-up');
                // Restore header border when open
                if ($body.is(':visible')) {
                    $(this).css({'margin-bottom': 'var(--space-4)', 'border-bottom': '1px solid var(--color-border)', 'padding-bottom': 'var(--space-4)'});
                } else {
                    $(this).css({'margin-bottom': '0', 'border-bottom': 'none', 'padding-bottom': '0'});
                }
            });

            // Enable/disable the button based on selection
            $semesterSelect.on('change', function() {
                $btnLoad.prop('disabled', !$(this).val());
            });

            // Load results
            $btnLoad.on('click', function() {
                var semesterId = $semesterSelect.val();
                if (!semesterId) return;

                var semesterLabel = $semesterSelect.find('option:selected').text().trim();
                $btnLoad.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Loading...');
                $tableBody.empty();

                $.ajax({
                    type: "GET",
                    url: "api/student/exam-results",
                    data: { semester_id: semesterId },
                    success: function(result) {
                        if (result.success) {
                            var data = result.message;
                            var results = data.results;
                            var summary = data.summary;

                            // Populate table
                            results.forEach(function(row) {
                                var badgeClass = getGradeBadge(row.grade);
                                var caScore = parseFloat(row.continues_assessments_score || 0);
                                var examScore = parseFloat(row.exam_score || 0);
                                var finalScore = parseFloat(row.final_score || 0);

                                var html = '<tr>' +
                                    '<td><span class="font-semibold text-navy">' + escapeHtml(row.course_code) + '</span></td>' +
                                    '<td>' + escapeHtml(row.course_name) + '</td>' +
                                    '<td style="text-align:center;">' + row.credit_hours + '</td>' +
                                    '<td style="text-align:center;">' + caScore.toFixed(1) + '</td>' +
                                    '<td style="text-align:center;">' + examScore.toFixed(1) + '</td>' +
                                    '<td style="text-align:center;"><strong>' + finalScore.toFixed(1) + '</strong></td>' +
                                    '<td style="text-align:center;"><span class="badge ' + badgeClass + '">' + row.grade + '</span></td>' +
                                    '</tr>';
                                $tableBody.append(html);
                            });

                            // Update summary (from calculate_gpa_cgpa stored procedure)
                            $('#summary-courses').text(summary.total_courses || 0);
                            $('#summary-credits').text(summary.total_credits || 0);
                            $('#summary-gpa').text(parseFloat(summary.gpa || 0).toFixed(2));
                            $('#summary-cgpa').text(parseFloat(summary.cgpa || 0).toFixed(2));

                            // Update heading
                            $('#results-heading').text('Results: ' + semesterLabel);

                            $emptyState.hide();
                            $resultsContent.show();
                        } else {
                            $resultsContent.hide();
                            $emptyState.find('.empty-state__icon').html('<i class="bi bi-clipboard-x"></i>');
                            $emptyState.find('.empty-state__title').text('No Results Available');
                            $emptyState.find('.empty-state__message').text(result.message);
                            $emptyState.show();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 401) {
                            alert("Your session expired, logging you out...");
                            window.location.href = "?logout";
                        } else {
                            $resultsContent.hide();
                            $emptyState.find('.empty-state__icon').html('<i class="bi bi-exclamation-triangle"></i>');
                            $emptyState.find('.empty-state__title').text('Something Went Wrong');
                            $emptyState.find('.empty-state__message').text('Failed to load results. Please check your connection and try again.');
                            $emptyState.show();
                        }
                    },
                    complete: function() {
                        $btnLoad.prop('disabled', false).html('<i class="bi bi-search"></i> View Results');
                    }
                });
            });

            // PDF download
            $('#btn-download-pdf').on('click', function() {
                var semesterId = $semesterSelect.val();
                if (!semesterId) return;

                var semesterLabel = $semesterSelect.find('option:selected').text().trim();
                var studentName = <?= json_encode($student_data["full_name"] ?? '') ?>;
                var studentIndex = <?= json_encode($student_index ?? '') ?>;
                var programName = <?= json_encode($student_data["program_name"] ?? '') ?>;

                generateResultsPDF(semesterLabel, studentName, studentIndex, programName);
            });

            function getGradeBadge(grade) {
                switch(grade) {
                    case 'A':  return 'badge--green';
                    case 'A-': return 'badge--green';
                    case 'B+': return 'badge--navy';
                    case 'B':  return 'badge--navy';
                    case 'C+': return 'badge--gold';
                    case 'C':  return 'badge--gold';
                    case 'D':  return 'badge--gray';
                    case 'E':  return 'badge--gray';
                    case 'F':  return 'badge--danger';
                    default:   return 'badge--gray';
                }
            }

            function generateResultsPDF(semesterLabel, studentName, studentIndex, programName) {
                var rows = [];
                $('#results-table-body tr').each(function() {
                    var cells = $(this).find('td');
                    rows.push({
                        code: cells.eq(0).text().trim(),
                        title: cells.eq(1).text().trim(),
                        credits: cells.eq(2).text().trim(),
                        ca: cells.eq(3).text().trim(),
                        exam: cells.eq(4).text().trim(),
                        total: cells.eq(5).text().trim(),
                        grade: cells.eq(6).text().trim()
                    });
                });

                var gpa = $('#summary-gpa').text();
                var cgpa = $('#summary-cgpa').text();
                var totalCredits = $('#summary-credits').text();
                var totalCourses = $('#summary-courses').text();

                var printWindow = window.open('', '_blank');
                var html = '<!DOCTYPE html><html><head><title>Exam Results - ' + studentName + '</title>' +
                    '<style>' +
                    'body { font-family: "Times New Roman", serif; margin: 40px; color: #333; }' +
                    '.header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #003262; padding-bottom: 15px; }' +
                    '.header h1 { color: #003262; font-size: 20px; margin: 0 0 5px; }' +
                    '.header h2 { color: #003262; font-size: 16px; margin: 0 0 10px; font-weight: normal; }' +
                    '.student-info { margin-bottom: 20px; }' +
                    '.student-info table { width: 100%; border: none; }' +
                    '.student-info td { padding: 3px 10px; border: none; font-size: 13px; }' +
                    '.student-info .label { font-weight: bold; width: 150px; }' +
                    'table.results { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }' +
                    'table.results th, table.results td { border: 1px solid #ccc; padding: 8px 10px; }' +
                    'table.results th { background: #003262; color: #fff; text-align: center; }' +
                    'table.results td { text-align: center; }' +
                    'table.results td:nth-child(2) { text-align: left; }' +
                    '.summary { margin-top: 20px; text-align: right; font-size: 14px; }' +
                    '.summary strong { color: #003262; }' +
                    '.footer { margin-top: 40px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #ccc; padding-top: 10px; }' +
                    '@media print { body { margin: 20px; } }' +
                    '</style></head><body>' +
                    '<div class="header">' +
                    '<h1>REGIONAL MARITIME UNIVERSITY</h1>' +
                    '<h2>Student Exam Results</h2>' +
                    '</div>' +
                    '<div class="student-info"><table>' +
                    '<tr><td class="label">Student Name:</td><td>' + studentName + '</td>' +
                    '<td class="label">Index Number:</td><td>' + studentIndex + '</td></tr>' +
                    '<tr><td class="label">Programme:</td><td>' + programName + '</td>' +
                    '<td class="label">Semester:</td><td>' + semesterLabel + '</td></tr>' +
                    '</table></div>' +
                    '<table class="results"><thead><tr>' +
                    '<th>Course Code</th><th>Course Title</th><th>Credits</th><th>CA Score</th><th>Exam Score</th><th>Total</th><th>Grade</th>' +
                    '</tr></thead><tbody>';

                rows.forEach(function(row) {
                    html += '<tr>' +
                        '<td>' + row.code + '</td>' +
                        '<td>' + row.title + '</td>' +
                        '<td>' + row.credits + '</td>' +
                        '<td>' + row.ca + '</td>' +
                        '<td>' + row.exam + '</td>' +
                        '<td><strong>' + row.total + '</strong></td>' +
                        '<td>' + row.grade + '</td>' +
                        '</tr>';
                });

                html += '</tbody></table>' +
                    '<div class="summary">' +
                    'Total Courses: <strong>' + totalCourses + '</strong> &nbsp;&nbsp;|&nbsp;&nbsp; ' +
                    'Total Credits: <strong>' + totalCredits + '</strong> &nbsp;&nbsp;|&nbsp;&nbsp; ' +
                    'Semester GPA: <strong>' + gpa + '</strong> &nbsp;&nbsp;|&nbsp;&nbsp; ' +
                    'Cumulative GPA: <strong>' + cgpa + '</strong>' +
                    '</div>' +
                    '<div class="footer">Generated from RMU Student Portal on ' + new Date().toLocaleDateString('en-GB', {day: 'numeric', month: 'long', year: 'numeric'}) + '</div>' +
                    '</body></html>';

                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.onload = function() {
                    printWindow.print();
                };
            }
        });
    </script>
</body>

</html>
