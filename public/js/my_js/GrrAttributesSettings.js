 $(document).ready(function () {
    // --------------------------------------
    // Cache DOM elements
    // --------------------------------------
    const $GRRSampleTable = $('#tblGRRSamples');        
    const $GRRQuestionnaireTable = $('#tblGrrQuestionnaire');        
    const $form = $('#formGRRSamples');        
    const $modal = $('#modalAddGRRSamples');
    const $formQuestionnaire = $('#formGRRQuestionnaire');        
    const $modalViewQuestionnaire = $('#modalViewQuestionnaire');
    const $modalAddQuestionnaire = $('#modalAddQuestionnaire');

    // --------------------------------------
    // Initialize global AJAX setup (once per project)
    // --------------------------------------
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // --------------------------------------
    // Initialize DataTable
    // --------------------------------------
    const dtGrrSettings = initGrrSampleTable($GRRSampleTable);
    const dtGrrQuestionnaire = initGrrQuestionnaireTable($GRRQuestionnaireTable);

    // --------------------------------------
    // Bind all event handlers
    // --------------------------------------
    bindGrrSampleEvents($GRRSampleTable, $form, $modal, $modalViewQuestionnaire, dtGrrSettings, dtGrrQuestionnaire);
    bindGrrQuestionnaireEvents($GRRQuestionnaireTable, $formQuestionnaire, $modalAddQuestionnaire, dtGrrSettings, dtGrrQuestionnaire);
});

/**
 * Reset a form and clear hidden fields
 * @param {string|jQuery} formSelector - the form element or selector
 */
function resetGRRSamplesForm(formSelector) {
    const $form = $(formSelector);
    $form[0].reset();
    $form.find('input[type="hidden"]').val('');
}

function resetGRRQuestionnaireForm(formSelector) {
    const $form = $(formSelector);
    $form[0].reset();
    $form.find('input[type="hidden"]').val('');
}

/**
 * Initialize DataTable
 */
function initGrrSampleTable($GRRSampleTable, url = 'view_grr_attributes') {

    // Check if already initialized
    if ($.fn.DataTable.isDataTable($GRRSampleTable[0])) {
        return $GRRSampleTable.DataTable();
    }

    return $GRRSampleTable.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: url
        },
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'section' },
            { data: 'grr_no' },
            { data: 'grr_sample' },
            { data: 'status_label' }
        ]
    });
}

function initGrrQuestionnaireTable($GRRQuestionnaireTable, url = 'view_grr_questionnaire') {

    if ($.fn.DataTable.isDataTable($GRRQuestionnaireTable[0])) {
        return $GRRQuestionnaireTable.DataTable();
    }

    return $GRRQuestionnaireTable.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
            data: function (d) {
                d.grr_setting_id = $('#viewGrrSampleId').val();
            }
        },
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'reference' },
            { data: 'defect' },
            { data: 'location' },
            { data: 'status_label' }
        ]
    });
}


/**
 * Bind events for buttons, forms, etc.
 */
function bindGrrSampleEvents($GRRSampleTable, $form, $modal, $modalViewQuestionnaire, dtGrrSettings, dtGrrQuestionnaire){

    $('#btnShowAddGRRSamplesModal').on('click', function () {
        resetGRRSamplesForm($form);
        $modal.modal('show');
    });

    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();

        if($form.find('#grrNo').val() != "" && $form.find('#grrSample').val() != "" && $form.find('#section').val() != ""){
            saveGrrSample($form, $modal, dtGrrSettings, dtGrrQuestionnaire);
        }else{
            showError('Failed to save data, Please Select Product Line');
        }
    });

    // Edit button
    $GRRSampleTable.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        fetchGrrAttributesById(id, $modal);
    });

    // Disable button
    $GRRSampleTable.on('click', '.btnDisable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to disable this sample?', function () {
            updateGrrStatus(id, dtGrrSettings);
        });
    });

    // Enable button
    $GRRSampleTable.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to enable this sample?', function () {
            updateGrrStatus(id, dtGrrSettings);
        });
    });

    // Add Questionnaire button
    $GRRSampleTable.on('click', '.btnViewQuestionnaire', function () {
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

function bindGrrQuestionnaireEvents($GRRQuestionnaireTable,$formQuestionnaire,$modalAddQuestionnaire,dtGrrSettings,dtGrrQuestionnaire) {

    $('#btnShowAddGRRQuestionnaireModal').off('click.grrQuestionnaire').on('click.grrQuestionnaire', function () { 
        resetGRRQuestionnaireForm($formQuestionnaire);
        const sampleId = $('#viewGrrSampleId').val();
        $('#qSampleId').val(sampleId);
        console.log("add button clicked")
        $modalAddQuestionnaire.modal('show');
    });


    $formQuestionnaire.off('submit.grrQuestionnaire').on('submit.grrQuestionnaire', function (e) {
        e.preventDefault();

        if ($formQuestionnaire.find('#grrNo').val() != "" &&$formQuestionnaire.find('#grrSample').val() != "" &&$formQuestionnaire.find('#section').val() != "") {

            saveGrrQuestionnaire($formQuestionnaire,$modalAddQuestionnaire,dtGrrSettings,dtGrrQuestionnaire);

        } else {
            showError('Failed to save data, Please Select Product Line');
        }
    });


    $GRRQuestionnaireTable.off('click.grrQuestionnaire', '.btnEdit').on('click.grrQuestionnaire', '.btnEdit', function () {

        const id = $(this).data('id');
        console.log('id', id);

        fetchGrrQuestionnaireById(
            id,
            $modalAddQuestionnaire
        );
    });


    $GRRQuestionnaireTable.off('click.grrQuestionnaire', '.btnDisable').on('click.grrQuestionnaire', '.btnDisable', function () {
        const id = $(this).data('id');

        confirmAction(
            'Are you sure you want to disable this questionnaire?',
            function () {
                updateQuestionnaireStatus(id, dtGrrQuestionnaire);
            }
        );
    });


    $GRRQuestionnaireTable.off('click.grrQuestionnaire', '.btnEnable').on('click.grrQuestionnaire', '.btnEnable', function () {
        const id = $(this).data('id');
        confirmAction(
            'Are you sure you want to enable this examination?',
            function () {
                updateQuestionnaireStatus(id, dtGrrQuestionnaire);
            }
        );
    });
}
/**
 * Save (add/update) data
 */
function saveGrrSample($form, $modal, dtGrrSettings) {
    $.ajax({
        type: 'POST',
        url: 'add_grr_sample',
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

function saveGrrQuestionnaire($formQuestionnaire, $modalViewQuestionnaire, dtGrrSettings, dtGrrQuestionnaire) {
    $.ajax({
        type: 'POST',
        url: 'add_grr_questionnaire',
        data: $formQuestionnaire.serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.result === 1) {
                dtGrrSettings.draw(false);
                dtGrrQuestionnaire.draw(false);
                $modalViewQuestionnaire.modal('hide');
                $formQuestionnaire[0].reset();
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
 * Fetch grr_attributes by ID
 */
function fetchGrrAttributesById(id, $modal) {
    $.ajax({
        type: 'GET',
        url: 'get_grr_attributes_by_id',
        data: { id },
        dataType: 'json',
        success: function (response) {
            // Populate modal fields (adjust names per grr_attributes)
            console.log(response);
            $('#txtGRRSamplesId').val(response.id);
            $('#section').val(response.section);
            $('#grrNo').val(response.grr_no);
            $('#grrSampleId').val(response.grr_sample);

            $modal.modal('show');
        },
        error: function (xhr) {
            console.error('Fetch failed:', xhr.responseText);
            showError('Failed to fetch data.');
        }
    });
}

function fetchGrrQuestionnaireById(id, $modalViewQuestionnaire) {
    $.ajax({
        type: 'GET',
        url: 'get_grr_questionnaire_by_id',
        data: { id: id },
        dataType: 'json',

        success: function (response) {

            $('#questionnaireId').val(response.id);
            $('#qSampleId').val(response.grr_setting_id);
            $('#reference').val(response.reference);
            $('#defect').val(response.defect);
            $('#location').val(response.location);


            $modalViewQuestionnaire.modal('show');
        },

        error: function (xhr) {
            console.error('Fetch failed:', xhr.responseText);
            showError('Failed to fetch data.');
        }
    });
}


/**
 * Disable or update grr_attributes status
 */
function updateGrrStatus(id, dtGrrSettings) {
    $.ajax({
        type: 'POST',
        url: 'update_grr_status',
        data: { id },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showSuccess('Status updated successfully.');
                dtGrrSettings.draw(false);
            }else {
                // ⚠️ If success is false
                Swal.fire({
                    title: 'Error',
                    text: response.message,
                    icon: 'error'
                });
            }
        },
        error: function (xhr) {
            console.error('Status update failed:', xhr.responseText);
            showError('Failed to update status.');
        }
    });
}

function updateQuestionnaireStatus(id, dtGrrQuestionnaire) {
    $.ajax({
        type: 'POST',
        url: 'update_grr_questionnaire_status',
        data: { id },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showSuccess('Status updated successfully.');
                dtGrrQuestionnaire.draw(false);
            }else {
                // ⚠️ If success is false
                Swal.fire({
                    title: 'Error',
                    text: response.message,
                    icon: 'error'
                });
            }
        },
        error: function (xhr) {
            console.error('Status update failed:', xhr.responseText);
            showError('Failed to update status.');
        }
    });
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
