// Personnel Disqualification - Parts 4 to 7
// Part 4: QC Sample Check Result, Part 5: Monitoring Result,
// Part 6: Inspection Result, Part 7: Overall Disposition.
// Uses showSuccess / showError / confirmAction from PersonnelDisqualification.js.

// Set USE_MOCK to false once the backend endpoints below exist.
const USE_MOCK = true;

// All server endpoints in one place.
const URLS = {
    people: 'get_dropdown_from_systemone', // existing endpoint (EmpNo, empname)
    loadParts: 'get_disquali_parts',       // GET  ?id=<part1_id>
    saveQcSample: 'add_disquali_part4',    // POST
    saveMonitoring: 'add_disquali_part5',  // POST (has file upload)
    saveInspection: 'add_disquali_part6',  // POST
    saveDisposition: 'add_disquali_part7', // POST
};

const MAX_ROWS_PER_DAY = 10;

// Sample record used when USE_MOCK is true (same shape the load endpoint should return).
const MOCK_PARTS = {
    qc_sample: {
        qc_first_ok: 9, qc_first_ng: 1, qc_first_date: '2026-11-10T09:00',
        qc_second_ok: 10, qc_second_ng: 0, qc_second_date: '2026-11-11T09:00',
        qc_overall_result: '1',
    },
    monitoring: {
        employee_quality_card_file: 'employee_quality_card.pdf',
        spot_audit_report_file: 'spot_audit_report.pdf',
        days: {
            1: [
                { date: '2026-11-12', time: '08:30', po_series: 'PO-001 / Series A', sample: '1', accept: 1, reject: 0, mode_of_defect: '', qc_inspector: '' },
                { date: '2026-11-12', time: '09:00', po_series: 'PO-001 / Series A', sample: '2', accept: 0, reject: 1, mode_of_defect: 'Scratch / 1', qc_inspector: '' },
            ],
        },
    },
    inspection: [
        { part_name: 'Part A', lot_no: 'LOT-001', lot_qty_ipqc: 100, sample_iqc_oqc: 20, accept: 20, reject: 0, mode_of_defect: '', qc_inspector: '', qc_date: '2026-11-13' },
    ],
    disposition: {
        disposition: '1', recertified_date: '2026-11-16',
        lqc_supervisor_sign: 'Sample Sign', lqc_supervisor_date: '2026-11-16',
    },
};

$(document).ready(function () {
    const $modal = $('#modalDisqualification');
    const $part4 = $('#formDisqualiPart4');
    const $part5 = $('#formDisqualiPart5');
    const $part6 = $('#formDisqualiPart6');
    const $part7 = $('#formDisqualiPart7');
    const $allForms = $part4.add($part5).add($part6).add($part7);

    let peopleList = [];   // [{ id, text }] loaded once
    let designation = '';  // operator | inspector | mh | technician | ''
    let viewMode = false;  // true when the record is opened with the View button

    resetMonitoringRows();
    bindEvents();

    // ======================================================
    // Events
    // ======================================================
    function bindEvents() {
        // New record
        $('#btnAddDisqualification').on('click', function () {
            resetPartsForms();
            loadPeople(function () {});
        });

        // Edit / View record from the list (same buttons the main script uses)
        $('#tblDisqualification').on('click', '.btnEdit, .btnView', function () {
            const id = $(this).data('id');
            const isView = $(this).hasClass('btnView');

            resetPartsForms();
            viewMode = isView;
            $allForms.find('.js-part1-id').val(id);
            applyViewMode();

            loadPeople(function () {
                loadParts(id);
            });
        });

        // Designation is set by the main script when an employee is picked
        $('#employeeName').on('change', function () {
            const position = $(this).find('option:selected').data('position');
            designation = getDesignationGroup(position);
            applyDesignation();
        });

        // Part 5: add / remove rows and sample counter
        $part5.on('click', '.btnAddRow', function () {
            addMonitoringRow($(this).closest('tbody'));
        });
        $part5.on('click', '.btnRemoveRow', function () {
            const $tbody = $(this).closest('tbody');
            $(this).closest('tr').remove();
            renumberMonitoringRows($tbody);
            updateDayState($tbody);
        });
        $part5.on('input', '[data-field="sample"]', function () {
            updateDayState($(this).closest('tbody'));
        });

        // Part 7: show the date that belongs to the chosen disposition
        $('#dispositionResult').on('change', showDispositionDates);

        // Saves
        $part4.on('submit', function (e) {
            e.preventDefault();
            if (!qcSampleIsValid()) return;
            savePart($part4, URLS.saveQcSample, 'QC Sample Check Result saved.');
        });

        $part5.on('submit', function (e) {
            e.preventDefault();
            saveMonitoring();
        });

        $part6.on('submit', function (e) {
            e.preventDefault();
            savePart($part6, URLS.saveInspection, 'Inspection Result saved.');
        });

        $part7.on('submit', function (e) {
            e.preventDefault();
            // last part: close the modal and go back to the list
            savePart($part7, URLS.saveDisposition, 'Overall Disposition saved.', true);
        });
    }

    // ======================================================
    // Common helpers
    // ======================================================

    // Convert the position text from the employee to one of our groups
    function getDesignationGroup(position) {
        const p = String(position || '').toLowerCase();
        if (p.indexOf('operator') !== -1) return 'operator';
        if (p.indexOf('inspector') !== -1) return 'inspector';
        if (p === 'mh' || p.indexOf('material handler') !== -1) return 'mh';
        if (p.indexOf('technician') !== -1) return 'technician';
        return '';
    }

    // Load the people list once, then fill every people dropdown
    function loadPeople(done) {
        if (peopleList.length > 0) {
            fillAllPeopleSelects();
            done();
            return;
        }

        $.ajax({
            method: 'get',
            url: URLS.people,
            data: { type: 'conducted_by' },
            dataType: 'json',
            success: function (response) {
                peopleList = response.map(function (row) {
                    return { id: String(row.EmpNo), text: row.empname };
                });
                fillAllPeopleSelects();
                done();
            },
            error: function (xhr) {
                console.error('Load people failed:', xhr.responseText);
                showError('Failed to load names.');
            },
        });
    }

    function fillPeopleSelect($select) {
        const current = $select.val() || '';
        $select.empty().append($('<option>', { value: '', text: '-- Select --' }));
        peopleList.forEach(function (p) {
            $select.append($('<option>', { value: p.id, text: p.text }));
        });
        $select.val(current);
    }

    function fillAllPeopleSelects() {
        $modal.find('.js-people').each(function () {
            fillPeopleSelect($(this));
        });
    }

    // Set values on inputs inside $scope. attr is 'name' or 'data-field'.
    function setValues($scope, values, attr) {
        attr = attr || 'name';
        $.each(values || {}, function (key, value) {
            $scope.find('[' + attr + '="' + key + '"]').val(value).trigger('change');
        });
    }

    // Show / hide the sections that depend on the designation
    function applyDesignation() {
        const isOperator = designation === 'operator';
        const isInspector = designation === 'inspector';
        const isMhTech = designation === 'mh' || designation === 'technician';

        $('#cardPart4').toggleClass('d-none', !(isOperator || isInspector));
        $('#cardPart6').toggleClass('d-none', !(isOperator || isInspector));
        $('#cardPart5').toggleClass('d-none', designation === '');

        $('#monitoringOperator').toggleClass('d-none', !isOperator);
        $('#monitoringInspector').toggleClass('d-none', !isInspector);
        $('#monitoringMhTech').toggleClass('d-none', !isMhTech);

        // hidden blocks must not be sent to the server
        $('#monitoringOperator').find('input, select').prop('disabled', viewMode || !isOperator);
        $('#monitoringInspector').find('input').prop('disabled', viewMode || !isInspector);
        $('#monitoringMhTech').find('input').prop('disabled', viewMode || !isMhTech);

        $('#monitoringDesignation').val(designation);
    }

    // Lock the forms when the record is opened with the View button
    function applyViewMode() {
        $allForms.find('input, select').prop('disabled', viewMode);
        $allForms.find('button[type="submit"]').toggleClass('d-none', viewMode);
        applyDesignation();
        $('.monitoring-day').each(function () {
            updateDayState($(this));
        });
    }

    function resetPartsForms() {
        $allForms.each(function () {
            this.reset();
        });
        $allForms.find('select').trigger('change');
        $allForms.find('.js-part1-id').val('');
        $('.js-current-file').text('');

        resetMonitoringRows();
        showDispositionDates();

        designation = '';
        viewMode = false;
        applyViewMode();
    }

    // Save one part with FormData (the same way for every part)
    function savePart($form, url, successMessage, closeAfter) {
        if (!$form.find('.js-part1-id').val()) {
            showError('Please save Part 1 first, then open the record from the list.');
            return;
        }

        const formData = new FormData($form[0]);

        if (USE_MOCK) {
            console.log('MOCK POST ' + url);
            formData.forEach(function (value, key) {
                console.log(key, value);
            });
            afterSave(successMessage, closeAfter);
            return;
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            dataType: 'json',
            success: function (response) {
                if (response.result === 1) {
                    afterSave(successMessage, closeAfter);
                } else {
                    console.error('Save failed:', response);
                    showError('Failed: ' + (response.error || 'Unknown error'));
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    alert('Your session has expired.');
                    window.location.href = '../';
                    return;
                }
                console.error('Save failed:', xhr.responseText);
                showError('Failed to save data.');
            },
        });
    }

    function afterSave(message, closeAfter) {
        showSuccess(message);
        if (closeAfter) {
            $modal.modal('hide');
            $('#tblDisqualification').DataTable().draw(false);
        }
    }

    // Load saved Part 4 to 7 data of a record
    function loadParts(id) {
        if (USE_MOCK) {
            fillParts(MOCK_PARTS);
            return;
        }

        $.ajax({
            method: 'get',
            url: URLS.loadParts,
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                fillParts(response);
            },
            error: function (xhr) {
                console.error('Load parts failed:', xhr.responseText);
                showError('Failed to fetch data.');
            },
        });
    }

    function fillParts(data) {
        if (!data) return;
        fillQcSample(data.qc_sample);
        fillMonitoring(data.monitoring);
        fillInspection(data.inspection);
        fillDisposition(data.disposition);
    }

    // ======================================================
    // Part 4: QC Sample Check Result
    // ======================================================
    function qcSampleIsValid() {
        const first = (Number($part4.find('[name="qc_first_ok"]').val()) || 0) + (Number($part4.find('[name="qc_first_ng"]').val()) || 0);
        const second = (Number($part4.find('[name="qc_second_ok"]').val()) || 0) + (Number($part4.find('[name="qc_second_ng"]').val()) || 0);

        if (first > 10 || second > 10) {
            showError('OK + NG cannot be more than 10 pcs.');
            return false;
        }
        return true;
    }

    function fillQcSample(values) {
        setValues($part4, values);
    }

    // ======================================================
    // Part 5: Monitoring Result
    // ======================================================

    // Each day starts with one empty row
    function resetMonitoringRows() {
        $('.monitoring-day').each(function () {
            $(this).find('tr.day-row').remove();
            addMonitoringRow($(this));
        });
    }

    function addMonitoringRow($tbody, values) {
        if ($tbody.find('tr.day-row').length >= MAX_ROWS_PER_DAY) return;

        const $row = $(
            '<tr class="day-row">' +
                '<td><input type="date" class="form-control form-control-sm" data-field="date"></td>' +
                '<td><input type="time" class="form-control form-control-sm" data-field="time"></td>' +
                '<td><input type="text" class="form-control form-control-sm" data-field="po_series"></td>' +
                '<td><input type="text" class="form-control form-control-sm" data-field="sample"></td>' +
                '<td><input type="number" min="0" class="form-control form-control-sm" data-field="accept"></td>' +
                '<td><input type="number" min="0" class="form-control form-control-sm" data-field="reject"></td>' +
                '<td><input type="text" class="form-control form-control-sm" data-field="mode_of_defect"></td>' +
                '<td><select class="form-control form-control-sm js-people" data-field="qc_inspector"></select></td>' +
                '<td class="text-center"><button type="button" class="btn btn-danger btn-sm btnRemoveRow"><i class="fa fa-trash"></i></button></td>' +
            '</tr>'
        );

        fillPeopleSelect($row.find('select'));
        setValues($row, values, 'data-field');

        $tbody.append($row);
        renumberMonitoringRows($tbody);
        updateDayState($tbody);
    }

    // Field names: monitoring[day][row][field]
    function renumberMonitoringRows($tbody) {
        const day = $tbody.data('day');
        $tbody.find('tr.day-row').each(function (index) {
            $(this).find('[data-field]').each(function () {
                $(this).attr('name', 'monitoring[' + day + '][' + index + '][' + $(this).data('field') + ']');
            });
        });
    }

    // A row counts as a sample when its Sample box is filled
    function countDaySamples($tbody) {
        return $tbody.find('[data-field="sample"]').filter(function () {
            return $.trim($(this).val()) !== '';
        }).length;
    }

    function updateDayState($tbody) {
        const rowCount = $tbody.find('tr.day-row').length;
        const filled = countDaySamples($tbody);

        $tbody.find('.day-count')
            .text(filled + ' / ' + MAX_ROWS_PER_DAY + ' samples')
            .removeClass('bg-danger bg-success')
            .addClass(filled > 0 ? 'bg-success' : 'bg-danger');

        $tbody.find('.btnAddRow').prop('disabled', viewMode || rowCount >= MAX_ROWS_PER_DAY);
        $tbody.find('.btnRemoveRow').prop('disabled', viewMode || rowCount <= 1);
    }

    function saveMonitoring() {
        const emptyDays = [];

        if (designation === 'operator') {
            $('.monitoring-day').each(function () {
                if (countDaySamples($(this)) === 0) {
                    emptyDays.push($(this).data('day'));
                }
            });
        }

        const doSave = function () {
            savePart($part5, URLS.saveMonitoring, 'Monitoring Result saved.');
        };

        if (emptyDays.length > 0) {
            confirmAction('Day ' + emptyDays.join(', ') + ' has no samples entered. Save anyway?', doSave);
        } else {
            doSave();
        }
    }

    function fillMonitoring(monitoring) {
        if (!monitoring) return;

        if (monitoring.employee_quality_card_file) {
            $('#employeeQualityCardCurrent').text('Current file: ' + monitoring.employee_quality_card_file);
        }
        if (monitoring.spot_audit_report_file) {
            $('#spotAuditReportCurrent').text('Current file: ' + monitoring.spot_audit_report_file);
        }

        $('.monitoring-day').each(function () {
            const $tbody = $(this);
            const rows = (monitoring.days && monitoring.days[$tbody.data('day')]) || [];
            if (rows.length === 0) return;

            $tbody.find('tr.day-row').remove();
            rows.forEach(function (rowValues) {
                addMonitoringRow($tbody, rowValues);
            });
        });
    }

    // ======================================================
    // Part 6: Inspection Result
    // ======================================================
    function fillInspection(rows) {
        $('#tblInspection tbody tr').each(function (index) {
            setValues($(this), (rows || [])[index], 'data-field');
        });
    }

    // ======================================================
    // Part 7: Overall Disposition
    // ======================================================

    // 1 = back to normal operation (Re-certified Date), 2 = transfer (Date of Transfer), 3 = failed
    function showDispositionDates() {
        const value = $('#dispositionResult').val();

        $('#recertifiedDateGroup').toggleClass('d-none', value !== '1');
        $('#transferDateGroup').toggleClass('d-none', value !== '2');
        $('#recertifiedDate').prop('required', value === '1');
        $('#transferDate').prop('required', value === '2');

        if (value !== '1') $('#recertifiedDate').val('');
        if (value !== '2') $('#transferDate').val('');
    }

    function fillDisposition(values) {
        if (!values) return;
        $('#dispositionResult').val(values.disposition).trigger('change');
        setValues($part7, values);
    }
});
