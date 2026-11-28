$(document).ready(function () {

    const $GrrExamTable = $('#tblGRRExamGroupings');
    const $form = $('#formGRRExamGroupings');
    const $modal = $('#modalAddGRRExamGroupings');
    //  const $modalViewQuestionnaire = $('#grrExamViewQuestionnaireModal');

     // Other Select2 initialization may happen here
    $('.select2bs5').select2({
        theme: 'bootstrap-5'
    });

    initEmployeeSelect();


    // --------------------------------------
    // Initialize global AJAX setup (once per project)
    // --------------------------------------
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    const dtExamTable = initGrrExamGroupings($GrrExamTable);

    /**
     * Initialize DataTable
     */
    function initGrrExamGroupings($GrrExamTable, url = 'view_grr_exam_groupings') {

        // Check if already initialized
        if ($.fn.DataTable.isDataTable($GrrExamTable[0])) {
            return $GrrExamTable.DataTable();
        }

        return $GrrExamTable.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url
            },
            fixedHeader: true,
            columns: [
                { data: 'action', orderable: false, searchable: false },
                { data: 'ctrl_number' },
                { data: 'grr_no_grr_sample'},
                { data: 'exam_employees' },
                { data: 'description' },
                { data: 'status_label' }
            ]
        });
    }

    bindGrrExamGroupingsEvents($GrrExamTable, $form, $modal, dtExamTable);

})


/**
 * Bind events for buttons, forms, etc.
 */
function bindGrrExamGroupingsEvents($GrrExamTable, $form, $modal, dtExamTable){

    $('#btnShowAddGRRExamGroupingsModal').on('click', function () {
        resetGRRExamGroupingsForm($form);
        $modal.modal('show');
    });

    $form.find('#section').on('change', function() {
        const section = $(this).val();

        fetchGrrNoList(section);
        fetchEmployeesByGrrNo('');
    });

    $form.find('#examGrrNo').on('change', function() {
        const grrNo = $(this).val();
        fetchEmployeesByGrrNo(grrNo, $form.find('#txtGRRExamGroupingsId').val());
    });
    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();

        if($form.find('#examGrrNo').val() && $form.find('#section').val()){
            const selectedEmployees = $form.find('#employee').val() || [];

            if (selectedEmployees.length === 0) {
                showError('Please select 3 employees.');
                return;
            }

            saveExamGroupings($form, $modal, dtExamTable);
        }else{
            showError('Please fill in all required fields.');
        }
    });

    // Edit button
    $GrrExamTable.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        fetchGrrExamGroupingsById(id, $modal);
    });

    // Disable button
    $GrrExamTable.on('click', '.btnDisable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to disable this examination?', function () {
            updateGrrExamGroupingStatus(id, dtExamTable);
        });
    });

    // Enable button
    $GrrExamTable.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to enable this examination?', function () {
            updateGrrExamGroupingStatus(id, dtExamTable);
        });
    });

    // Add Questionnaire button
    $GrrExamTable.on('click', '.btnViewQuestionnaire', function () {
        const id = $(this).data('id');
        const grrNo = $(this).data('grrno');
        const grrSample = $(this).data('grrsample');

        const GrrNoAndSample = grrNo + ' - ' + grrSample;

        $('#grrQuestionnaireSampleId').val(id);
        $('#viewGrrSample').val(GrrNoAndSample);
        $('#viewGrrSampleId').val(id);

        $modalViewQuestionnaire.modal('show');
        dtGrrQuestionnaire.ajax.reload();

    });

}

function fetchGrrNoList(section, selectedId) {
    const $grrNo = $('#examGrrNo');

    $grrNo.empty().append($('<option>', { value: '', text: 'Select One', disabled: true, selected: true }));
    $grrNo.val('').trigger('change.select2');

    if (!section) {
        return $.Deferred().resolve().promise();
    }

    return $.ajax({
        url: 'fetch_grr_no_list',
        type: 'GET',
        data: { section: section }
    }).done(function (response) {
        response.forEach(function (item) {
            $grrNo.append($('<option>', { value: item.id, text: item.grr_no + ' - ' + item.grr_sample }));
        });

        // Select without firing the user "change" handler (it would reload/reset the employees)
        $grrNo.val(selectedId ? String(selectedId) : '').trigger('change.select2');
    }).fail(function () {
        showError('Failed to fetch GRR numbers.');
    });
}

function fetchEmployeesByGrrNo(grrNo, examId, selectedEmployees) {
    const $employee = $('#employee');

    $employee.empty();

    if (!grrNo) {
        $employee.val(null).trigger('change.select2');
        return $.Deferred().resolve().promise();
    }

    return $.ajax({
        url: 'fetch_employees_by_grr_no',
        type: 'GET',
        data: { grr_no: grrNo, exam_id: examId || '' }
    }).done(function (response) {
        response.forEach(function (item) {
            const lastName = (item.LastName || '').replace(/Ã±/g, 'ñ');
            const firstName = (item.FirstName || '').replace(/Ã±/g, 'ñ');

            $employee.append($('<option>', { value: item.EmpNo, text: firstName + ' ' + lastName }));
        });

        // Keep already-assigned examinees selectable even if they are no longer in the HRIS list
        (selectedEmployees || []).forEach(function (emp) {
            if ($employee.find('option').filter(function () { return this.value === String(emp.emp_id); }).length === 0) {
                $employee.append($('<option>', { value: emp.emp_id, text: emp.name || emp.emp_id }));
            }
        });

        $employee.val((selectedEmployees || []).map(function (emp) { return String(emp.emp_id); }));
        $employee.trigger('change.select2');
    }).fail(function () {
        showError('Failed to fetch employees.');
    });
}

/**
 * Fetch grr exam grouping by ID and fill the form
 */
function fetchGrrExamGroupingsById(id, $modal) {
    $.ajax({
        type: 'GET',
        url: 'get_grr_exam_groupings_by_id',
        data: { id },
        dataType: 'json',
        success: function (response) {
            const $form = $('#formGRRExamGroupings');

            // "#section" also exists in the GRR sample modal, so always scope to this form
            $form.find('#txtGRRExamGroupingsId').val(response.id);
            $form.find('#controlNumber').val(response.ctrl_number);
            $form.find('#examDescription').val(response.description);
            $form.find('#examPurpose').val(response.purpose);
            $form.find('#examInstruction').val(response.instructions);
            $form.find('#section').val(response.section).trigger('change.select2');

            fetchGrrNoList(response.section, response.grr_setting_id).always(function () {
                fetchEmployeesByGrrNo(response.grr_setting_id, response.id, response.employees).always(function () {
                    $modal.modal('show');
                });
            });
        },
        error: function (xhr) {
            console.error('Fetch failed:', xhr.responseText);
            showError('Failed to fetch data.');
        }
    });
}

function updateGrrExamGroupingStatus(id, dtExamTable) {
    $.ajax({
        type: 'POST',
        url: 'update_grr_exam_groupings_status',
        data: { id },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showSuccess('Status updated successfully.');
                dtExamTable.draw(false);
            } else {
                Swal.fire({ title: 'Error', text: response.message, icon: 'error' });
            }
        },
        error: function (xhr) {
            console.error('Status update failed:', xhr.responseText);
            showError('Failed to update status.');
        }
    });
}

function initEmployeeSelect() {

    const $employee = $('#employee');

    if ($employee.hasClass('select2-hidden-accessible')) {
        $employee.select2('destroy');
    }

    $employee.select2({
        theme: 'bootstrap-5',
        placeholder: 'Select Employees',
        allowClear: true
    });

    // Maximum of 3 employees
    $employee.on('select2:selecting', function (e) {

        const selectedCount = $(this).val()?.length || 0;

        if (selectedCount >= 3) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                text: 'You can only select a maximum of 3 employees.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}


/**
 * Save (add/update) data
 */
function saveExamGroupings($form, $modal, dtGrrSettings) {
    $.ajax({
        type: 'POST',
        url: 'add_exam_groupings',
        data: $form.serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.result === 1) {
                dtGrrSettings.draw(false);
                $modal.modal('hide');
                $form[0].reset();
                showSuccess('Successfully saved!');
            }
        },
        error: function (xhr) {
            console.error('Save failed:', xhr.responseText);
            showError((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save data.');
        }
    });
}

/**
 * Reset a form and clear hidden fields
 * @param {string|jQuery} formSelector - the form element or selector
 */
function resetGRRExamGroupingsForm(formSelector) {
    const $form = $(formSelector);
    $form[0].reset();
    $form.find('input[type="hidden"]').val('');

    // select2 does not follow form.reset(), so clear the selects explicitly
    $form.find('#section').val('').trigger('change.select2');
    fetchGrrNoList('');
    fetchEmployeesByGrrNo('');
}

/**
 * SweetAlert confirmation
 */
function confirmAction(message, callback) {
    Swal.fire({
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) callback();
    });
}

/**
 * SweetAlert success helper
 */
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        text: message,
        timer: 1500,
        showConfirmButton: false
    });
}

/**
 * SweetAlert error helper
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        text: message,
        timer: 2000,
        showConfirmButton: false
    });
}
