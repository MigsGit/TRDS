$(document).ready(function () {
    // --------------------------------------
    // Cache DOM elements
    // --------------------------------------
    const $table = $('#tblExaminations');        // e.g., #tblExaminations
    const $form = $('#formExaminations');        // e.g., #formExaminations
    const $modal = $('#modalAddExaminations');      // e.g., #modalAddExaminations

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
    const dtExaminations = initExaminationsTable($table);

    // --------------------------------------
    // Bind all event handlers
    // --------------------------------------
    bindExaminationsEvents($table, $form, $modal, dtExaminations);
});

/**
 * Reset a form and clear hidden fields
 * @param {string|jQuery} formSelector - the form element or selector
 */
function resetExaminationForm(formSelector) {
    const $form = $(formSelector);
    $form[0].reset();
    $form.find('input[type="hidden"]').val('');
}

/**
 * Initialize DataTable
 */
function initExaminationsTable($table, url = 'view_examinations') {
    return $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: url },
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'examination_name' },    // customize this per examinations
            { data: 'objective' },    // customize this per examinations
            { data: 'status_label' }
        ]
    });
}

/**
 * Bind events for buttons, forms, etc.
 */
function bindExaminationsEvents($table, $form, $modal, dtExaminations){

    $('#btnShowAddExaminationModal').on('click', function () {
        resetExaminationForm($form);
        $('#modalAddExaminations').modal('show');
    });

    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();
        saveExaminations($form, $modal, dtExaminations);
    });

    // Edit button
    $table.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        fetchExaminationsById(id, $modal);
    });

    // Disable button
    $table.on('click', '.btnDisable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to disable this examination?', function () {
            updateExaminationsStatus(id, dtExaminations);
        });
    });

    // Enable button
    $table.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to enable this examination?', function () {
            updateExaminationsStatus(id, dtExaminations);
        });
    });
}

/**
 * Save (add/update) examinations data
 */
function saveExaminations($form, $modal, dtExaminations) {
    $.ajax({
        type: 'POST',
        url: 'add_examinations',
        data: $form.serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.result === 1) {
                dtExaminations.draw(false);
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
 * Fetch examinations data by ID
 */
function fetchExaminationsById(id, $modal) {
    $.ajax({
        type: 'GET',
        url: 'get_examinations_by_id',
        data: { id },
        dataType: 'json',
        success: function (response) {
            // Populate modal fields (adjust names per examinations)
            $('#txtExaminationId').val(response.id);
            $('#txtExaminationName').val(response.examination_name);
            $('#txtObjective').val(response.objective);
            $modal.modal('show');
        },
        error: function (xhr) {
            console.error('Fetch failed:', xhr.responseText);
            showError('Failed to fetch data.');
        }
    });
}

/**
 * Disable or update examinations status
 */
function updateExaminationsStatus(id, dtExaminations) {
    $.ajax({
        type: 'POST',
        url: 'update_examinations_status',
        data: { id },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showSuccess('Status updated successfully.');
                dtExaminations.draw(false);
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
