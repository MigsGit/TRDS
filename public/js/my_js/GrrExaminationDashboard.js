$(document).ready(function () {

    const $section = $('#grrDashboardSection');
    const $cards = $('#grrDashboardCards');
    if (!$section.length || !$cards.length) return; // not the examination dashboard page

    const $loading = $('#grrDashboardLoading');
    const $trail = $('#grrDashboardTrail');
    const $trailText = $('#grrDashboardTrailText');
    const TRIAL_BADGE = { 1: 'badge-secondary', 2: 'badge-warning', 3: 'badge-primary' };
    const TRIAL_HEADER = { 1: 'bg-secondary text-white', 2: 'bg-warning', 3: 'bg-primary text-white' };

    let currentRequest = null;
    let grrItems = [];
    let examItems = [];
    let selectedGrr = null;
    let selectedExam = null;
    let employeeItems = [];
    let selectedEmployee = null;
    let currentTrial = 0;

    $section.select2({ theme: 'bootstrap-5' });

    // Selected values for later dashboard filtering.
    window.GrrExaminationDashboard = {
        getFilters: function () {
            return {
                section: $section.val() || '',
                grr_setting_id: selectedGrr ? selectedGrr.id : '',
                exam_grouping_id: selectedExam ? selectedExam.id : '',
                exam_employee_id: selectedEmployee ? selectedEmployee.id : '',
                emp_id: selectedEmployee ? selectedEmployee.emp_id : ''
            };
        }
    };

    function notifyFilterChanged() {
        $(document).trigger('grrDashboardFilterChanged', [window.GrrExaminationDashboard.getFilters()]);
    }

    function renderAlert(text, type) {
        $cards.empty().append(
            $('<div>', { class: 'col-12 text-center' }).append(
                $('<div>', { class: 'alert alert-' + type, text: text })
            )
        );
    }

    function updateTrail() {
        if (!selectedGrr) {
            $trail.hide();
            return;
        }
        let text = 'Section: ' + $section.val() + '  ›  GRR: ' + selectedGrr.label;
        if (selectedExam) text += '  ›  Exam: ' + selectedExam.ctrl_number;
        if (selectedEmployee) text += '  ›  Employee: ' + (selectedEmployee.name || selectedEmployee.emp_id);
        $trailText.text(text);
        $trail.show();
    }

    function cancelRequest() {
        if (currentRequest) {
            currentRequest.abort();
            currentRequest = null;
        }
        $loading.hide();
    }

    // Runs a GET request; stale/aborted responses are ignored.
    function load(url, data, onDone) {
        cancelRequest();
        $loading.show();

        const request = $.ajax({ type: 'GET', url: url, data: data, dataType: 'json' });
        currentRequest = request;

        request.done(function (response) {
            if (currentRequest !== request) return;
            onDone((response && response.data) || []);
        });

        request.fail(function (xhr, textStatus) {
            if (textStatus === 'abort' || currentRequest !== request) return;

            let text = 'Failed to load data. Please try again.';
            if (xhr.status === 401) {
                text = 'Your session has expired. Please log in again.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                text = xhr.responseJSON.message;
            }
            renderAlert(text, 'danger');
        });

        request.always(function () {
            if (currentRequest === request) {
                currentRequest = null;
                $loading.hide();
            }
        });
    }

    function cardColumn($card) {
        return $('<div>', { class: 'col-lg-3 col-md-4 col-sm-6 mt-4' }).append($card);
    }

    function renderGrrCards() {
        if (grrItems.length === 0) {
            renderAlert('No examinations are available for section ' + $section.val() + '.', 'warning');
            return;
        }

        $cards.empty();
        grrItems.forEach(function (item) {
            const $card = $('<div>', { class: 'exam-card is-clickable', 'data-id': item.id })
                .append($('<div>', { class: 'exam-title', text: item.grr_sample }))
                .append($('<div>', { class: 'exam-subtitle', text: item.grr_no }))
                .append(
                    $('<div>', { class: 'exam-meta' }).append(
                        $('<div>').append($('<strong>', { text: 'Section: ' })).append(document.createTextNode(item.section))
                    )
                );
            $cards.append(cardColumn($card));
        });
    }

    function renderExamCards() {
        if (examItems.length === 0) {
            renderAlert('No examination settings are available for this GRR.', 'warning');
            return;
        }

        $cards.empty();
        examItems.forEach(function (item) {
            const $card = $('<div>', { class: 'exam-card is-clickable', 'data-id': item.id })
                .append($('<div>', { class: 'exam-title', text: item.ctrl_number || '-' }))
                .append($('<div>', { class: 'exam-subtitle', text: item.description || '' }))
                .append($('<div>', { class: 'exam-purpose', text: item.purpose || '' }))
                .append(
                    $('<div>', { class: 'exam-meta' }).append(
                        $('<div>').append($('<strong>', { text: 'Examinees: ' })).append(document.createTextNode(item.employees_count))
                    )
                );
            $cards.append(cardColumn($card));
        });
    }

    function renderEmployeeCards() {
        if (employeeItems.length === 0) {
            renderAlert('No employees are available to take this examination.', 'warning');
            return;
        }

        $cards.empty();
        employeeItems.forEach(function (employee) {
            const isDone = employee.exam_status === 2;
            const trial = employee.trial || 1;
            const badgeCss = isDone ? 'badge-success' : (TRIAL_BADGE[trial] || 'badge-secondary');
            const statusText = isDone ? 'Done' : 'Trial ' + trial + (employee.exam_status === 1 ? ' - In Progress' : ' - Not Started');
            const isSelected = selectedEmployee && selectedEmployee.id === employee.id;

            const $card = $('<div>', { class: 'exam-card is-clickable' + (isSelected ? ' is-selected' : ''), 'data-id': employee.id })
                .append($('<div>', { class: 'exam-title', text: employee.name || employee.emp_id }))
                .append($('<div>', { class: 'exam-subtitle', text: 'Employee No.: ' + employee.emp_id }))
                .append(
                    $('<div>', { class: 'text-center' }).append($('<span>', { class: 'badge p-2 ' + badgeCss, text: statusText }))
                );
            $cards.append(cardColumn($card));
        });
    }

    function renderResult(data) {
        const exam = data.exam || {};
        const employee = data.employee || {};
        const rows = data.rows || [];
        const passed = rows.filter(function (row) { return row.score === 'Yes'; }).length;

        const $header = $('<div>', { class: 'card' }).append(
            $('<div>', { class: 'card-body' })
                .append($('<h4>', { class: 'mb-3', text: 'Examination ' + (exam.ctrl_number || '') + ' - Result' }))
                .append($('<span>', { class: 'badge badge-success p-2 mb-3', text: 'Done' }))
                .append($('<div>', { class: 'mb-1' }).append($('<strong>', { text: 'Examinee: ' })).append(document.createTextNode((employee.name || '') + ' (' + (employee.emp_id || '') + ')')))
                .append($('<div>', { class: 'mb-1' }).append($('<strong>', { text: 'Description: ' })).append(document.createTextNode(exam.description || '-')))
                .append($('<div>').append($('<strong>', { text: 'Score: ' })).append(document.createTextNode(passed + ' / ' + rows.length)))
        );

        function answerCell(answer, reference) {
            const $td = $('<td>', { class: 'text-center' });
            if (answer === null || answer === undefined) return $td.text('-');
            return $td.text(answer).addClass(answer === reference ? '' : 'text-danger font-weight-bold');
        }

        const $head = $('<tr>')
            .append($('<th>', { text: 'Sample No' }))
            .append($('<th>', { text: 'Reference' }))
            .append($('<th>', { text: 'Series Name' }))
            .append($('<th>', { text: 'Defect' }))
            .append($('<th>', { text: 'Location' }))
            .append($('<th>', { class: 'text-center ' + TRIAL_HEADER[1], text: 'Trial 1 Answer' }))
            .append($('<th>', { class: 'text-center ' + TRIAL_HEADER[2], text: 'Trial 2 Answer' }))
            .append($('<th>', { class: 'text-center ' + TRIAL_HEADER[3], text: 'Trial 3 Answer' }))
            .append($('<th>', { class: 'text-center', text: 'Score' }));

        const $body = $('<tbody>');
        if (rows.length === 0) {
            $body.append($('<tr>').append($('<td>', { colspan: 9, class: 'text-center text-muted', text: 'No questionnaire items found.' })));
        }
        rows.forEach(function (row) {
            $body.append(
                $('<tr>')
                    .append($('<td>', { text: row.number }))
                    .append($('<td>', { class: 'text-center', text: row.reference || '-' }))
                    .append($('<td>', { text: row.series_name || '-' }))
                    .append($('<td>', { text: row.defect || '-' }))
                    .append($('<td>', { text: row.location || '-' }))
                    .append(answerCell(row.trial_1, row.reference))
                    .append(answerCell(row.trial_2, row.reference))
                    .append(answerCell(row.trial_3, row.reference))
                    .append(
                        $('<td>', { class: 'text-center' }).append(
                            $('<span>', { class: 'badge ' + (row.score === 'Yes' ? 'badge-success' : 'badge-danger'), text: row.score })
                        )
                    )
            );
        });

        const $table = $('<table>', { class: 'table table-bordered table-sm table-hover bg-white mb-0' })
            .append($('<thead>', { class: 'thead-light' }).append($head))
            .append($body);

        $cards.empty().append(
            $('<div>', { class: 'col-12 mt-3' })
                .append($header)
                .append($('<div>', { class: 'card mt-3' }).append($('<div>', { class: 'table-responsive' }).append($table)))
        );
    }

    function renderExamination(data) {
        const exam = data.exam || {};
        const employee = data.employee || {};
        const questions = data.questions || [];
        currentTrial = parseInt(data.trial, 10) || 1;

        function infoRow(label, value) {
            return $('<div>', { class: 'mb-1' })
                .append($('<strong>', { text: label + ': ' }))
                .append(document.createTextNode(value || '-'));
        }

        const $header = $('<div>', { class: 'card' }).append(
            $('<div>', { class: 'card-body' })
                .append($('<h4>', { class: 'mb-3', text: 'Examination ' + (exam.ctrl_number || '') }))
                .append(
                    $('<div>', { class: 'mb-3' }).append(
                        $('<span>', { class: 'badge p-2 ' + (TRIAL_BADGE[currentTrial] || 'badge-secondary'), text: 'Trial ' + currentTrial + ' of 3 - ' + (data.trial_label || '') })
                    )
                )
                .append(infoRow('Examinee', (employee.name || '') + ' (' + (employee.emp_id || '') + ')'))
                .append(infoRow('Description', exam.description))
                .append(infoRow('Purpose', exam.purpose))
                .append(infoRow('Instructions', exam.instructions))
        );

        const $form = $('<form>', { id: 'formGrrExamAnswers', autocomplete: 'off' });

        if (questions.length === 0) {
            $form.append($('<div>', { class: 'alert alert-warning text-center', text: 'No questionnaire items have been set up for this GRR.' }));
        } else {
            questions.forEach(function (question) {
                const inputId = 'grrAnswer' + question.id;

                $form.append(
                    $('<div>', { class: 'card mb-3' }).append(
                        $('<div>', { class: 'card-body' }).append(
                            $('<div>', { class: 'd-flex align-items-start' })
                                .append($('<span>', { class: 'badge badge-dark mr-3 mt-1', text: question.number }))
                                .append(
                                    $('<div>', { class: 'flex-grow-1' })
                                        .append(infoRow('Series Name', question.series_name))
                                        .append(infoRow('Location', question.location))
                                        .append(
                                            $('<div>', { class: 'form-group mt-2 mb-0' })
                                                .append($('<label>', { for: inputId, class: 'font-weight-bold', text: 'Answer' }))
                                                .append(
                                                    $('<select>', {
                                                        id: inputId,
                                                        class: 'form-control grr-answer-input',
                                                        style: 'max-width: 120px;',
                                                        'data-question-id': question.id,
                                                        required: true
                                                    })
                                                        .append($('<option>', { value: '', text: '-- Select --' }))
                                                        .append($('<option>', { value: '0', text: '0' }))
                                                        .append($('<option>', { value: '1', text: '1' }))
                                                )
                                        )
                                )
                        )
                    )
                );
            });

            $form.append(
                $('<button>', { type: 'submit', class: 'btn btn-dark', id: 'btnGrrSaveAnswers' })
                    .append($('<i>', { class: 'fas fa-save' }))
                    .append(document.createTextNode(' Save'))
            );
        }

        $cards.empty().append($('<div>', { class: 'col-12 mt-3' }).append($header).append($form));
    }

    function saveAnswers() {
        const answers = {};
        let missing = false;

        $('#formGrrExamAnswers .grr-answer-input').each(function () {
            const value = $.trim($(this).val());
            if (value === '') missing = true;
            answers[$(this).data('question-id')] = value;
        });

        if (missing) {
            Swal.fire({ icon: 'warning', text: 'Please answer all items before saving.' });
            return;
        }

        const trial = currentTrial;

        Swal.fire({
            text: trial < 3
                ? 'Save your Trial ' + trial + ' answers? You will not be able to change them after saving.'
                : 'Save your Trial 3 answers and complete the examination? You will not be able to change them after saving.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Save'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            const $btn = $('#btnGrrSaveAnswers').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'save_grr_exam_answers',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    exam_grouping_id: selectedExam.id,
                    exam_employee_id: selectedEmployee.id,
                    trial: trial,
                    answers: answers
                },
                dataType: 'json'
            }).done(function (response) {
                Swal.fire({ icon: 'success', text: response.message || 'Answers saved successfully!', timer: 2000, showConfirmButton: false });

                // Return to the examinee list; remaining trials are taken from there.
                selectedEmployee = null;
                currentTrial = 0;
                updateTrail();
                notifyFilterChanged();
                load('fetch_grr_exam_employees_by_exam', { exam_grouping_id: selectedExam.id }, function (items) {
                    employeeItems = items;
                    renderEmployeeCards();
                });
            }).fail(function (xhr) {
                let text = 'Failed to save your answers. Please try again.';
                if (xhr.status === 401) {
                    text = 'Your session has expired. Please log in again.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    text = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', text: text });
                $btn.prop('disabled', false);
            });
        });
    }

    $cards.on('submit', '#formGrrExamAnswers', function (e) {
        e.preventDefault();
        saveAnswers();
    });


    function resetAll() {
        cancelRequest();
        grrItems = [];
        examItems = [];
        employeeItems = [];
        selectedGrr = null;
        selectedExam = null;
        selectedEmployee = null;
        updateTrail();
        renderAlert('Please select a section to display.', 'warning');
    }

    $section.on('change', function () {
        const section = $(this).val();

        resetAll();
        notifyFilterChanged();

        if (!section) return;

        load('fetch_grr_examinations_by_section', { section: section }, function (items) {
            grrItems = items;
            renderGrrCards();
        });
    });

    $cards.on('click', '.exam-card.is-clickable', function () {
        const id = $(this).data('id');

        // Step 1: GRR card -> grr_exam_settings
        if (!selectedGrr) {
            const grr = grrItems.find(function (item) { return item.id === id; });
            if (!grr) return;

            selectedGrr = { id: grr.id, label: grr.label };
            updateTrail();
            notifyFilterChanged();

            load('fetch_grr_exam_settings_by_grr', { grr_setting_id: grr.id }, function (items) {
                examItems = items;
                renderExamCards();
            });
            return;
        }

        // Step 2: exam setting card -> grr_exam_employees (to pick the examinee)
        if (!selectedExam) {
            const exam = examItems.find(function (item) { return item.id === id; });
            if (!exam) return;

            selectedExam = { id: exam.id, ctrl_number: exam.ctrl_number };
            updateTrail();
            notifyFilterChanged();

            load('fetch_grr_exam_employees_by_exam', { exam_grouping_id: exam.id }, function (items) {
                employeeItems = items;
                renderEmployeeCards();
            });
            return;
        }

        // Step 3: employee card -> the employee taking the examination -> show the questionnaire
        if (!selectedEmployee) {
            const employee = employeeItems.find(function (item) { return item.id === id; });
            if (!employee) return;

            selectedEmployee = { id: employee.id, emp_id: employee.emp_id, name: employee.name };
            updateTrail();
            notifyFilterChanged();

            const params = { exam_grouping_id: selectedExam.id, exam_employee_id: employee.id };

            if (employee.exam_status === 2) {
                load('fetch_grr_exam_result', params, function (data) { renderResult(Array.isArray(data) ? {} : data); });
            } else {
                load('fetch_grr_examination_for_employee', params, renderExamination);
            }
        }
    });

    $('#btnGrrDashboardBack').on('click', function () {
        cancelRequest();

        if (selectedEmployee) {
            selectedEmployee = null;
            updateTrail();
            notifyFilterChanged();
            renderEmployeeCards();
        } else if (selectedExam) {
            selectedExam = null;
            selectedEmployee = null;
            employeeItems = [];
            updateTrail();
            notifyFilterChanged();
            renderExamCards();
        } else if (selectedGrr) {
            selectedGrr = null;
            examItems = [];
            updateTrail();
            notifyFilterChanged();
            renderGrrCards();
        }
    });
});
