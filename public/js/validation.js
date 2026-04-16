/**
 * validation.js — Form Validation & AJAX Utilities
 *
 * Provides:
 *  - initAjaxForm(formId, rules)          — attach validation + AJAX submit
 *  - initEmailUniquenessCheck(formId, checkUrl, excludeUserId)
 *  - showAlert(type, message)             — Bootstrap alert
 *  - deleteViaAjax(url, confirmMsg)       — AJAX DELETE with confirmation
 */
(function () {
    'use strict';

    /* ------------------------------------------------------------------ */
    /*  Constants                                                          */
    /* ------------------------------------------------------------------ */
    var EMAIL_REGEX = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */
    function getCsrfToken() {
        var metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : '';
    }

    function escapeHtml(text) {
        var tempDiv = document.createElement('div');
        tempDiv.textContent = text;
        return tempDiv.innerHTML;
    }

    function getFieldLabel(form, fieldName) {
        var field = form.querySelector('[name="' + fieldName + '"]');
        if (!field) return fieldName;
        var label = form.querySelector('label[for="' + field.id + '"]');
        if (label) return label.textContent.trim().replace(/\s*\(.*\)\s*$/, '').trim();
        return fieldName.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    }

    /* ------------------------------------------------------------------ */
    /*  Alert                                                              */
    /* ------------------------------------------------------------------ */
    window.showAlert = function (type, message) {
        var container = document.getElementById('ajax-alert-container');
        if (!container) return;

        container.innerHTML =
            '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                escapeHtml(message) +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';

        window.scrollTo({ top: 0, behavior: 'smooth' });

        if (type === 'success') {
            setTimeout(function () {
                var alertEl = container.querySelector('.alert');
                if (alertEl) {
                    alertEl.classList.remove('show');
                    setTimeout(function () { alertEl.remove(); }, 200);
                }
            }, 4000);
        }
    };

    /* ------------------------------------------------------------------ */
    /*  Field-level error helpers                                          */
    /* ------------------------------------------------------------------ */
    function clearFormErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
        form.querySelectorAll('.invalid-feedback.js-error').forEach(function (el) { el.remove(); });
    }

    function showFieldError(form, fieldName, message) {
        var field = form.querySelector('[name="' + fieldName + '"]');
        if (!field) return;

        // Radio buttons — append error to the parent .mb-3 container
        if (field.type === 'radio') {
            var radioContainer = field.closest('.mb-3');
            if (radioContainer) {
                var existingRadioFeedback = radioContainer.querySelector('.invalid-feedback.js-error');
                if (existingRadioFeedback) existingRadioFeedback.remove();
                var radioFeedback = document.createElement('div');
                radioFeedback.className = 'invalid-feedback js-error d-block';
                radioFeedback.textContent = message;
                radioContainer.appendChild(radioFeedback);
            }
            return;
        }

        field.classList.add('is-invalid');
        var existingFeedback = field.parentNode.querySelector('.invalid-feedback.js-error');
        if (existingFeedback) existingFeedback.remove();

        var feedbackDiv = document.createElement('div');
        feedbackDiv.className = 'invalid-feedback js-error';
        feedbackDiv.textContent = message;
        field.after(feedbackDiv);
    }

    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        var jsFeedback = field.parentNode.querySelector('.invalid-feedback.js-error');
        if (jsFeedback) jsFeedback.remove();
    }

    /* ------------------------------------------------------------------ */
    /*  Single-field validation                                            */
    /* ------------------------------------------------------------------ */
    function validateField(form, fieldName, rules) {
        var field = form.querySelector('[name="' + fieldName + '"]');
        if (!field) return null;

        var label = getFieldLabel(form, fieldName);

        // --- Radio group ---
        if (field.type === 'radio') {
            var radioButtons = form.querySelectorAll('[name="' + fieldName + '"]');
            var isChecked = Array.from(radioButtons).some(function (r) { return r.checked; });
            if (rules.indexOf('required') !== -1 && !isChecked) {
                return 'Please select a ' + label.toLowerCase() + '.';
            }
            return null;
        }

        // --- Select ---
        if (field.tagName === 'SELECT') {
            if (rules.indexOf('required') !== -1 && !field.value) {
                return label + ' is required.';
            }
            return null;
        }

        // --- Text / email / password ---
        var value = field.value.trim();

        for (var i = 0; i < rules.length; i++) {
            var rule = rules[i];

            if (rule === 'required' && !value) {
                return label + ' is required.';
            }
            if (rule === 'email' && value && !EMAIL_REGEX.test(value)) {
                return 'Please enter a valid email (e.g. user@example.com).';
            }
            if (typeof rule === 'string' && rule.indexOf('min:') === 0 && value) {
                var minLen = parseInt(rule.split(':')[1], 10);
                if (value.length < minLen) {
                    return label + ' must be at least ' + minLen + ' characters.';
                }
            }
            if (typeof rule === 'string' && rule.indexOf('match:') === 0 && value) {
                var matchFieldName = rule.split(':')[1];
                var matchField = form.querySelector('[name="' + matchFieldName + '"]');
                if (matchField && value !== matchField.value) {
                    return 'Passwords do not match.';
                }
            }
        }

        return null;
    }

    /* ------------------------------------------------------------------ */
    /*  Full-form validation                                               */
    /* ------------------------------------------------------------------ */
    function validateForm(form, rules) {
        clearFormErrors(form);
        var isValid = true;

        for (var fieldName in rules) {
            if (!rules.hasOwnProperty(fieldName)) continue;
            var errorMessage = validateField(form, fieldName, rules[fieldName]);
            if (errorMessage) {
                showFieldError(form, fieldName, errorMessage);
                isValid = false;
            }
        }

        return isValid;
    }

    /* ------------------------------------------------------------------ */
    /*  AJAX form submission                                               */
    /* ------------------------------------------------------------------ */
    function submitFormViaAjax(form) {
        var submitButton = form.querySelector('[type="submit"]');
        var originalButtonHtml = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { status: response.status, ok: response.ok, data: data };
            });
        })
        .then(function (result) {
            if (result.ok && result.data.success) {
                showAlert('success', result.data.message);
                if (result.data.redirect) {
                    setTimeout(function () { window.location.href = result.data.redirect; }, 1500);
                }
            } else if (result.status === 422 && result.data.errors) {
                clearFormErrors(form);
                for (var fieldName in result.data.errors) {
                    if (result.data.errors.hasOwnProperty(fieldName)) {
                        showFieldError(form, fieldName, result.data.errors[fieldName][0]);
                    }
                }
                showAlert('danger', result.data.message || 'Please fix the errors below.');
            } else {
                showAlert('danger', result.data.message || 'Something went wrong. Please try again.');
            }
        })
        .catch(function () {
            showAlert('danger', 'Network error. Please check your connection.');
        })
        .finally(function () {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonHtml;
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Public: initAjaxForm                                               */
    /* ------------------------------------------------------------------ */
    window.initAjaxForm = function (formId, rules) {
        var form = document.getElementById(formId);
        if (!form) return null;

        // Blur / input listeners for real-time feedback
        for (var fieldName in rules) {
            if (!rules.hasOwnProperty(fieldName)) continue;
            (function (name) {
                var field = form.querySelector('[name="' + name + '"]');
                if (field && field.type !== 'radio') {
                    // field.addEventListener('blur', function () {
                    //     clearFieldError(this);
                    //     var err = validateField(form, name, rules[name]);
                    //     if (err) showFieldError(form, name, err);
                    // });
                    field.addEventListener('input', function () {
                        clearFieldError(this);
                    });
                }
            })(fieldName);
        }

        // Submit handler
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (validateForm(form, rules)) {
                submitFormViaAjax(form);
            } else {
                showAlert('danger', 'Please fix the errors in the form.');
            }
        });

        return form;
    };

    /* ------------------------------------------------------------------ */
    /*  Public: initEmailUniquenessCheck                                   */
    /* ------------------------------------------------------------------ */
    window.initEmailUniquenessCheck = function (formId, checkUrl, excludeUserId) {
        var form = document.getElementById(formId);
        if (!form) return;

        var emailInput = form.querySelector('[name="email"]');
        if (!emailInput) return;

        var debounceTimer = null;

        emailInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var emailValue = this.value.trim();

            if (!emailValue || !EMAIL_REGEX.test(emailValue)) return;

            debounceTimer = setTimeout(function () {
                var url = checkUrl + '?email=' + encodeURIComponent(emailValue);
                if (excludeUserId) url += '&exclude_id=' + excludeUserId;

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.exists) {
                        showFieldError(form, 'email', 'This email is already registered.');
                    } else {
                        clearFieldError(emailInput);
                    }
                })
                .catch(function () { /* silent */ });
            }, 500);
        });
    };

    /* ------------------------------------------------------------------ */
    /*  Public: deleteViaAjax                                              */
    /* ------------------------------------------------------------------ */
    window.deleteViaAjax = function (url, confirmMessage) {
        if (!confirm(confirmMessage || 'Are you sure you want to delete this?')) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: '_method=DELETE',
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                showAlert('success', data.message);
                if (data.redirect) {
                    setTimeout(function () { window.location.href = data.redirect; }, 1200);
                } else {
                    setTimeout(function () { location.reload(); }, 1200);
                }
            } else {
                showAlert('danger', data.message || 'Failed to delete.');
            }
        })
        .catch(function () {
            showAlert('danger', 'Network error. Please try again.');
        });
    };

})();
