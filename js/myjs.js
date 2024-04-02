function validatePassword(password) {
    // Password must be at least 8 characters long
    if (password.length < 8) {
        return { success: false, message: 'Password must be at least 8 characters long!' };
    }

    // Password must have at least one uppercase letter
    if (!/[A-Z]/.test(password)) {
        return { success: false, message: 'Password must have at least one uppercase letter!' };
    }

    // Password must have at least one lowercase letter
    if (!/[a-z]/.test(password)) {
        return { success: false, message: 'Password must have at least one lowercase letter!' };
    }

    // Password must have at least one digit
    if (!/\d/.test(password)) {
        return { success: false, message: 'Password must have at least one digit!' };
    }

    // Password must have at least one special character
    if (!/[',@,$,#,!,*,+,\-.,,\,]/.test(password)) {
        return { success: false, message: 'Password must have at least one special character!' };
    }

    return { success: false, message: 'Password passed' };
}

function capitalizeEachWord(sentence) {
    const smallWords = ['and', 'to', 'of', 'a', 'the', 'in', 'on', 'at', 'for', 'with', 'by', 'from'];
    return sentence.split(' ').map((word, index) => {
        if (index === 0 || !smallWords.includes(word.toLowerCase()))
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        else return word.toLowerCase();
    }).join(' ');
}

function shortenText(text, max = 17) {
    if (text.length <= max) return text;
    let shortenedText = text.substring(0, max);
    return shortenedText + '...';
}

function semesterCourses() {
    $.ajax({
        type: "GET",
        url: "api/student/semester-courses",
        success: function (result) {
            console.log(result);

            if (result.success) {

                $("#compulsory-courses-display").html('');
                $("#elective-courses-display").html('');

                $("#compulsory-courses-display").html(
                    '<tr class="alert alert-success">' +
                    '<td colspan="2"><strong>COURSES FOR THIS SEMESTER</strong></td>' +
                    '</tr>'
                );

                // $("#elective-courses-display").html(
                //     '<tr class="alert alert-warning">' +
                //     '<td colspan="2"><strong>ELECTIVE COURSES</strong></td>' +
                //     '</tr>'
                // );

                result.message.forEach(function (value) {
                    var disable = value.reg_status ? 'disabled' : '';
                    var image = value.reg_status ? 'assets/images/icons8-correct-24.png' : 'assets/images/icons8-stop-48.png';
                    var status = value.reg_status ? 'active' : '';

                    var courseHtml = '<tr>' +
                        '<td style="display: flex;">' +
                        '<span class="me-2">' +
                        '<img src="' + image + '" alt="" style="width: 24px !important">' +
                        '</span>' +
                        '<span>[' + value.course_code + '] ' + value.course_name + '</span>' +
                        '</td>' +
                        '<td style="text-align: right">' +
                        '<input ' + disable + ' name="selected-course[]" value="' + value.course_code + '" type="checkbox" id="btn-check-' + value.course_code + '" class="btn-check" autocomplete="off" style="display: none;">' +
                        '<label class="btn btn-light btn-outline-success-dark ' + status + '" style="width: 40px !important; padding: 0px !important" for="btn-check-' + value.course_code + '">' + value.credits + '</label>' +
                        '</td>' +
                        '</tr>';

                    if (value.category_name === 'compulsory') $("#compulsory-courses-display").append(courseHtml);
                    else if (value.category_name === 'elective') $("#elective-courses-display").append(courseHtml);
                });

                $("#courses-register-btn-div").show();
                return;
            }

            $("#course-registration-section").html(
                '<div class="alert alert-danger d-flex align-items-start" role="alert">' +
                '<span class="bi bi-exclamation-triangle-fill me-2"></span>' +
                '<div style="text-transform: uppercase"><b>' + result.message + '</b></div>' +
                '</div>'
            );
        },
        error: function (xhr, status, error) {
            if (xhr.status == 401) {
                alert("Your session expired, logging you out...");
                window.location.href = "?logout";
            } else {
                console.log("Error: " + status + " - " + error);
            }
        }
    });
}

function otherSemesterCourses() {
    $.ajax({
        type: "GET",
        url: "api/student/other-semester-courses",
        success: function (result) {
            console.log(result);

            if (result.success) {

                $("#other-semester-courses-display").html('');

                $("#other-semester-courses-display").html(
                    '<tr class="alert alert-warning">' +
                    '<td colspan="2"><strong>UNREGISTERED COURSES FROM PREVIOUS SEMESTERS</strong></td>' +
                    '</tr>'
                );

                result.message.forEach(function (value) {
                    var disable = value.reg_status ? 'disabled' : '';
                    var image = value.reg_status ? 'assets/images/icons8-correct-24.png' : 'assets/images/icons8-stop-48.png';
                    var status = value.reg_status ? 'active' : '';

                    var courseHtml = '<tr>' +
                        '<td style="display: flex;">' +
                        '<span class="me-2">' +
                        '<img src="' + image + '" alt="" style="width: 24px !important">' +
                        '</span>' +
                        '<span>[' + value.course_code + '] ' + value.course_name + '</span>' +
                        '</td>' +
                        '<td style="text-align: right">' +
                        '<input ' + disable + ' name="selected-course[]" value="' + value.course_code + '" type="checkbox" id="btn-check-' + value.course_code + '" class="btn-check" autocomplete="off" style="display: none;">' +
                        '<label class="btn btn-light btn-outline-success-dark ' + status + '" style="width: 40px !important; padding: 0px !important" for="btn-check-' + value.course_code + '">' + value.credits + '</label>' +
                        '</td>' +
                        '</tr>';

                    $("#other-semester-courses-display").append(courseHtml);
                });
                return;
            }

            $(".unregistered-courses-disp").html(
                '<div class="alert alert-danger d-flex align-items-start" role="alert">' +
                '<span class="bi bi-exclamation-triangle-fill me-2"></span>' +
                '<div style="text-transform: uppercase"><b>' + result.message + '</b></div>' +
                '</div>'
            );

            $("#save-unreg-courses-btn-area").hide();
        },
        error: function (xhr, status, error) {
            if (xhr.status == 401) {
                alert("Your session expired, logging you out...");
                window.location.href = "?logout";
            } else {
                console.log("Error: " + status + " - " + error);
            }
        }
    });
}

function registrationSummary() {
    $.ajax({
        type: "GET",
        url: "api/student/registration-summary",
        success: function (result) {
            console.log(result);

            if (result.success) {
                total_course = result.message.total_course ? result.message.total_course : 0;
                total_credit = result.message.total_credit ? result.message.total_credit : 0;
                $("#total-registered-courses").html(total_course);
                $("#total-registered-credits").html(total_credit);
            } else {
                alert(result.message);
            }
        },
        error: function (xhr, status, error) {
            if (xhr.status == 401) {
                alert("Your session expired, logging you out...");
                window.location.href = "?logout";
            } else {
                console.log("Error: " + status + " - " + error);
            }
        }
    });
}

function courseInfo(course_code) {
    if (!course_code) return;

    $.ajax({
        type: "GET",
        url: "api/course/info?cc=" + course_code,
        success: function (result) {
            console.log(result);

            if (result.success) {
                console.log(result.message);
            } else {
                alert(result.message);
            }
        },
        error: function (xhr, status, error) {
            if (xhr.status == 401) {
                alert("Your session expired, logging you out...");
                window.location.href = "?logout";
            } else {
                console.log("Error: " + status + " - " + error);
            }
        }
    });
}
