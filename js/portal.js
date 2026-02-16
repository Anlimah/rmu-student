/**
 * RMU Student Portal - Core JavaScript
 * Consolidated from myjs.js with shared functionality.
 */

// ============================================
// SIDEBAR & NAVIGATION
// ============================================
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const menuToggle = document.getElementById('menuToggle');
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    // Mobile sidebar toggle
    if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('app__sidebar--open');
            overlay.classList.toggle('sidebar-overlay--open');
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('app__sidebar--open');
            overlay.classList.remove('sidebar-overlay--open');
        });
    }

    // User dropdown menu
    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('header__dropdown--open');
        });

        document.addEventListener('click', function () {
            userDropdown.classList.remove('header__dropdown--open');
        });
    }

    // Alert close buttons
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('alert__close')) {
            var alert = e.target.closest('.alert');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(function () { alert.remove(); }, 200);
            }
        }
    });
});

// ============================================
// UTILITIES
// ============================================

function getURLParam(param) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function validatePassword(password) {
    if (password.length < 8) {
        return { success: false, message: 'Password must be at least 8 characters long!' };
    }
    if (!/[A-Z]/.test(password)) {
        return { success: false, message: 'Password must have at least one uppercase letter!' };
    }
    if (!/[a-z]/.test(password)) {
        return { success: false, message: 'Password must have at least one lowercase letter!' };
    }
    if (!/\d/.test(password)) {
        return { success: false, message: 'Password must have at least one digit!' };
    }
    if (!/[',@,$,#,!,*,+,\-.,,\\]/.test(password)) {
        return { success: false, message: 'Password must have at least one special character!' };
    }
    return { success: true, message: 'Password passed' };
}

function capitalizeEachWord(sentence) {
    var smallWords = ['and', 'to', 'of', 'a', 'the', 'in', 'on', 'at', 'for', 'with', 'by', 'from'];
    return sentence.split(' ').map(function (word, index) {
        if (index === 0 || smallWords.indexOf(word.toLowerCase()) === -1)
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        else return word.toLowerCase();
    }).join(' ');
}

function shortenText(text, max) {
    max = max || 17;
    if (text.length <= max) return text;
    return text.substring(0, max) + '...';
}

// ============================================
// ALERT HELPER
// ============================================

function showAlert(containerId, type, message, dismissible) {
    dismissible = dismissible !== false;
    var icons = {
        success: 'bi-check-circle-fill',
        danger: 'bi-exclamation-triangle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    var icon = icons[type] || icons.info;
    var closeBtn = dismissible
        ? '<button class="alert__close" aria-label="Close">&times;</button>'
        : '';

    var html = '<div class="alert alert--' + type + '">' +
        '<span class="alert__icon bi ' + icon + '"></span>' +
        '<div class="alert__content">' + message + '</div>' +
        closeBtn +
        '</div>';

    var container = document.getElementById(containerId);
    if (container) container.innerHTML = html;
}

// ============================================
// AJAX WRAPPER (with session expiry handling)
// ============================================

function apiRequest(options) {
    var defaults = {
        method: options.type || options.method || 'GET',
        url: options.url,
        data: options.data || null,
        onSuccess: options.success || options.onSuccess || function () { },
        onError: options.error || options.onError || function () { },
        showLoading: options.showLoading !== false
    };

    if (defaults.showLoading) showLoadingOverlay();

    var xhr = new XMLHttpRequest();
    xhr.open(defaults.method, defaults.url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (defaults.showLoading) hideLoadingOverlay();

            if (xhr.status === 401) {
                alert('Your session expired, logging you out...');
                window.location.href = '?logout';
                return;
            }

            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    var result = JSON.parse(xhr.responseText);
                    defaults.onSuccess(result);
                } catch (e) {
                    defaults.onError(xhr, 'parseerror', e);
                }
            } else {
                defaults.onError(xhr, xhr.statusText, xhr.status);
            }
        }
    };

    if (defaults.data instanceof FormData) {
        xhr.send(defaults.data);
    } else if (defaults.data && defaults.method !== 'GET') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        var params = [];
        for (var key in defaults.data) {
            if (defaults.data.hasOwnProperty(key)) {
                params.push(encodeURIComponent(key) + '=' + encodeURIComponent(defaults.data[key]));
            }
        }
        xhr.send(params.join('&'));
    } else {
        xhr.send();
    }
}

// ============================================
// LOADING OVERLAY
// ============================================

function showLoadingOverlay() {
    var existing = document.getElementById('loadingOverlay');
    if (existing) return;

    var overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(overlay);
}

function hideLoadingOverlay() {
    var overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.remove();
}

// ============================================
// COURSE REGISTRATION FUNCTIONS (jQuery based for compatibility)
// ============================================

function semesterCourses() {
    $.ajax({
        type: "GET",
        url: "api/student/semester-courses",
        success: function (result) {
            if (result.success) {
                $("#compulsory-courses-display").html('');
                $("#elective-courses-display").html('');

                $("#compulsory-courses-display").html(
                    '<tr class="table__section-header">' +
                    '<td colspan="3"><strong>Courses For This Semester</strong></td>' +
                    '</tr>'
                );

                result.message.forEach(function (value) {
                    var isRegistered = value.registered ? true : false;
                    var disable = isRegistered ? 'disabled' : '';
                    var checked = isRegistered ? 'checked' : '';
                    var statusClass = isRegistered ? 'course-check:checked' : '';

                    var courseHtml = '<tr>' +
                        '<td>' +
                        '<span class="font-semibold text-navy">[' + value.course_code + ']</span> ' +
                        value.course_name +
                        '</td>' +
                        '<td class="text-center">' +
                        '<span class="badge badge--' + (value.category_name === 'compulsory' ? 'navy' : 'gold') + '">' +
                        value.category_name + '</span>' +
                        '</td>' +
                        '<td style="text-align: right;">' +
                        '<input ' + disable + ' ' + checked + ' name="selected-course[]" value="' + value.course_code +
                        '" type="checkbox" id="btn-check-' + value.course_code +
                        '" class="course-check" autocomplete="off">' +
                        '<label class="course-check-label" for="btn-check-' + value.course_code + '">' +
                        value.credits + '</label>' +
                        '</td>' +
                        '</tr>';

                    if (value.category_name === 'compulsory') {
                        $("#compulsory-courses-display").append(courseHtml);
                    } else if (value.category_name === 'elective') {
                        if ($("#elective-courses-display").children('.table__section-header').length === 0) {
                            $("#elective-courses-display").html(
                                '<tr class="table__section-header">' +
                                '<td colspan="3"><strong>Elective Courses</strong></td>' +
                                '</tr>'
                            );
                        }
                        $("#elective-courses-display").append(courseHtml);
                    }
                });

                $("#courses-register-btn-div").show();
                registrationSummary();
                return;
            }

            $("#course-registration-section").html(
                '<div class="alert alert--danger">' +
                '<span class="alert__icon bi bi-exclamation-triangle-fill"></span>' +
                '<div class="alert__content"><strong>' + result.message + '</strong></div>' +
                '</div>'
            );
        },
        error: function (xhr, status, error) {
            if (xhr.status == 401) {
                alert("Your session expired, logging you out...");
                window.location.href = "?logout";
            }
        }
    });
}

function otherSemesterCourses() {
    $.ajax({
        type: "GET",
        url: "api/student/other-semester-courses",
        success: function (result) {
            if (result.success) {
                $("#other-semester-courses-display").html('');
                $("#other-semester-courses-display").html(
                    '<tr class="table__section-header table__section-header--warning">' +
                    '<td colspan="3"><strong>Unregistered Courses From Previous Semesters</strong></td>' +
                    '</tr>'
                );

                result.message.forEach(function (value) {
                    var isRegistered = value.registered ? true : false;
                    var disable = isRegistered ? 'disabled' : '';
                    var checked = isRegistered ? 'checked' : '';

                    var courseHtml = '<tr>' +
                        '<td>' +
                        '<span class="font-semibold text-navy">[' + value.course_code + ']</span> ' +
                        value.course_name +
                        '</td>' +
                        '<td class="text-center">' +
                        '<span class="badge badge--gray">' + value.category_name + '</span>' +
                        '</td>' +
                        '<td style="text-align: right;">' +
                        '<input ' + disable + ' ' + checked + ' name="selected-course[]" value="' + value.course_code +
                        '" type="checkbox" id="btn-check-' + value.course_code +
                        '" class="course-check" autocomplete="off">' +
                        '<label class="course-check-label" for="btn-check-' + value.course_code + '">' +
                        value.credits + '</label>' +
                        '</td>' +
                        '</tr>';

                    $("#other-semester-courses-display").append(courseHtml);
                });
                return;
            }

            $(".unregistered-courses-disp").html(
                '<div class="alert alert--info">' +
                '<span class="alert__icon bi bi-info-circle-fill"></span>' +
                '<div class="alert__content">' + result.message + '</div>' +
                '</div>'
            );
            $("#save-unreg-courses-btn-area").hide();
        },
        error: function (xhr) {
            if (xhr.status == 401) {
                alert("Your session expired, logging you out...");
                window.location.href = "?logout";
            }
        }
    });
}

function registrationSummary() {
    $.ajax({
        type: "GET",
        url: "api/student/registration-summary",
        success: function (result) {
            if (result.success) {
                var total_course = result.message.total_course || 0;
                var total_credit = result.message.total_credit || 0;
                $("#total-registered-courses").html(total_course);
                $("#total-registered-credits").html(total_credit);
                $(".registration-summary").show();
            } else {
                $(".registration-summary").hide();
            }
        },
        error: function (xhr) {
            if (xhr.status == 401) {
                alert("Your session expired, logging you out...");
                window.location.href = "?logout";
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
            if (result.success) {
                // Populate modal with course info
                var info = result.message;
                var modalBody = document.querySelector('#courseModal .modal__body');
                if (modalBody && info) {
                    var html = '<h2 class="mb-4">' + info.name + '</h2>' +
                        '<div class="detail-list">' +
                        '<div class="detail-item"><div class="detail-item__label">Course Code</div><div class="detail-item__value">' + info.code + '</div></div>' +
                        '<div class="detail-item"><div class="detail-item__label">Department</div><div class="detail-item__value">' + (info.department_name || 'N/A') + '</div></div>' +
                        '<div class="detail-item"><div class="detail-item__label">Credits</div><div class="detail-item__value">' + info.credit_hours + '</div></div>' +
                        '<div class="detail-item"><div class="detail-item__label">Category</div><div class="detail-item__value">' + (info.category_name || 'N/A') + '</div></div>' +
                        '</div>';
                    modalBody.innerHTML = html;
                }
            }
        },
        error: function (xhr) {
            if (xhr.status == 401) {
                alert("Your session expired, logging you out...");
                window.location.href = "?logout";
            }
        }
    });
}

// ============================================
// MODAL HELPERS
// ============================================

function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) modal.classList.add('modal-overlay--open');
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('modal-overlay--open');
}
