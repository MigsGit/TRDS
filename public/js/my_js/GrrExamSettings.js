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
        resetGRRExamGroupingsForm($form);;
        $modal.modal('show');
    });

    $form.find('#section').on('change', function() {
        const section = $(this).val();

        fetchGrrNoList(section);
       
    });

    $form.find('#examGrrNo').on('change', function() {
        const grrNo = $(this).val();
        fetchEmployeesByGrrNo(grrNo);
    });
    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();
        
        if($form.find('#grrNo').val() != "" && $form.find('#grrSample').val() != "" && $form.find('#section').val() != ""){
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
        confirmAction('Are you sure you want to disable this sample?', function () {
            updateGrrStatus(id, dtExamTable);
        });
    });

    // Enable button
    $GrrExamTable.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to enable this sample?', function () {
            updateGrrStatus(id, dtExamTable);
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

function fetchGrrNoList(section) {
    if (section != "") {
        $.ajax({
            url: 'fetch_grr_no_list',
            type: 'GET',
            data: {
                section: section
            },
            success: function(response) {

                $('#examGrrNo').empty();

                $('#examGrrNo').append(
                    $('<option>', {
                        value: '',
                        text: 'Select One',
                        disabled: true,
                        selected: true
                    })
                );

                response.forEach(function(item) {
                    $('#examGrrNo').append(
                        $('<option>', {
                            value: item.id,
                            text: item.grr_no + ' - ' + item.grr_sample
                        })
                    );
                });

                // Reset without triggering change event
                $('#examGrrNo').val('');
            },
            error: function() {
                showError('Failed to fetch GRR numbers.');
            }
        });
    }
}

function fetchEmployeesByGrrNo(grrNo) {
    if (grrNo != "") {
        $.ajax({
            url: 'fetch_employees_by_grr_no',
            type: 'GET',
            data: {
                grr_no: grrNo
            },
            success: function(response) {

                // Clear existing employees
                $('#employee').empty();

                console.log(response)

                // Add employees
                response.forEach(function(item) {

                    let lastName = item.LastName.replace(/Ã±/g, 'ñ');
                    let firstName = item.FirstName.replace(/Ã±/g, 'ñ');

                    $('#employee').append(
                        $('<option>', {
                            value: item.EmpNo,
                            text: firstName + ' ' + lastName 
                        })
                    );
                });

                // Reset selection
                $('#employee').val(null).trigger('change');
            },
            error: function() {
                showError('Failed to fetch employees.');
            }
        });
    } else {
        $('#employee').empty();
        $('#employee').val(null).trigger('change');
    }
}

/**
 * Fetch grr_attributes by ID
 */
function fetchGrrExamGroupingsById(id, $modal) {
    $.ajax({
        type: 'GET',
        url: 'get_grr_exam_groupings_by_id',
        data: { id },
        dataType: 'json',
        success: function (response) {
            $('#txtGRRExamGroupingsId').val(response.id);
            $('#controlNumber').val(response.ctrl_number);
            $('#examDescription').val(response.description);
            $('#examPurpose').val(response.purpose);
            $('#examInstruction').val(response.instructions);
            $('#section').val(response.section);


            $modal.modal('show');
        },
        error: function (xhr) {
            console.error('Fetch failed:', xhr.responseText);
            showError('Failed to fetch data.');
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
            showError('Failed to save data.');
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
