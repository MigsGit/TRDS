 $(document).ready(function () {
    // --------------------------------------
    // Cache DOM elements
    // --------------------------------------
    const $GRRSampleTable = $('#tblGRRSamples');
    const $GRRQuestionnaireList = $('#grrQuestionnaireList');
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
    // Initialize DataTable / sortable list
    // --------------------------------------
    const dtGrrSettings = initGrrSampleTable($GRRSampleTable);
    initGrrQuestionnaireSortable($GRRQuestionnaireList);

    // --------------------------------------
    // Bind all event handlers
    // --------------------------------------
    bindGrrSampleEvents($GRRSampleTable, $form, $modal, $modalViewQuestionnaire, dtGrrSettings);
    bindGrrQuestionnaireEvents($modalViewQuestionnaire, $formQuestionnaire, $modalAddQuestionnaire, dtGrrSettings);
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

function initGrrQuestionnaireSortable($list) {
    $list.sortable({
        items: '> .grr-q-card',
        handle: '.grr-q-handle',
        cancel: 'input, textarea, select, option, button, a, .btn',
        axis: 'y',
        tolerance: 'pointer',
        placeholder: 'grr-q-placeholder',
        forcePlaceholderSize: true,
        scroll: true,
        update: function (e, ui) {
            renumberGrrQuestionnaire($list);
            saveGrrQuestionnaireOrder($list);
        }
    });
}

function renumberGrrQuestionnaire($list) {
    const $cards = $list.children('.grr-q-card');
    const inactiveCount = $('#grrQuestionnaireInactiveList').children('.grr-q-card').length;

    $cards.each(function (index) {
        $(this).find('.grr-q-number').text(index + 1);
    });

    $('#grrQuestionnaireCount').text($cards.length);
    $('#grrQuestionnaireInactiveWrap').toggle(inactiveCount > 0);
    $('#grrQuestionnaireEmpty').toggle($cards.length + inactiveCount === 0);
}

function escapeHtml(value) {
    return $('<div>').text(value == null ? '' : value).html();
}

function buildGrrQuestionnaireCard(item) {
    const isActive = Number(item.status) === 0;
    const statusBadge = isActive
        ? '<span class="badge badge-success">Active</span>'
        : '<span class="badge badge-danger">Inactive</span>';
    const toggleButton = isActive
        ? `<button type="button" class="btn btn-danger btn-sm btnDisable" data-id="${item.id}" title="Disable"><i class="fas fa-ban"></i></button>`
        : `<button type="button" class="btn btn-success btn-sm btnEnable" data-id="${item.id}" title="Enable"><i class="fas fa-undo"></i></button>`;
    const handle = isActive
        ? '<div class="grr-q-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></div>'
        : '<div class="grr-q-handle-spacer"><i class="fas fa-ban"></i></div>';
    const number = isActive ? '<div class="grr-q-number"></div>' : '<div class="grr-q-number text-muted">&ndash;</div>';

    return `
        <div class="grr-q-card ${isActive ? '' : 'is-inactive'}" data-id="${item.id}">
            ${handle}
            ${number}
            <div class="grr-q-body">
                <div class="grr-q-field grr-q-field-wide">
                    <span class="grr-q-label">Defect</span>
                    <div class="grr-q-value font-weight-bold">${escapeHtml(item.defect)}</div>
                </div>
                <div class="grr-q-field grr-q-field-wide">
                    <span class="grr-q-label">Location</span>
                    <div class="grr-q-value">${escapeHtml(item.location)}</div>
                </div>
                <div class="grr-q-field">
                    <span class="grr-q-label">Series</span>
                    <div class="grr-q-value">${escapeHtml(item.series ? item.series.series_name : '')}</div>
                </div>
                <div class="grr-q-field">
                    <span class="grr-q-label">Reference</span>
                    <div class="grr-q-value">${escapeHtml(item.reference)}</div>
                </div>
                <div class="grr-q-field">
                    <span class="grr-q-label">Judgement</span>
                    <div class="grr-q-value">${escapeHtml(item.judgement)}</div>
                </div>
                <div class="grr-q-field">
                    <span class="grr-q-label">Status</span>
                    <div class="grr-q-value">${statusBadge}</div>
                </div>
            </div>
            <div class="grr-q-actions">
                <button type="button" class="btn btn-secondary btn-sm btnEdit mr-1" data-id="${item.id}" title="Edit"><i class="fas fa-edit"></i></button>
                ${toggleButton}
            </div>
        </div>`;
}

function loadGrrQuestionnaireList() {
    const $list = $('#grrQuestionnaireList');
    const $loading = $('#grrQuestionnaireLoading');
    const settingId = $('#viewGrrSampleId').val();

    if (!settingId) {
        return;
    }

    $loading.show();

    return $.ajax({
        type: 'GET',
        url: 'view_grr_questionnaire',
        data: { grr_setting_id: settingId },
        dataType: 'json',
        success: function (response) {
            // Ignore stale responses if another sample was opened meanwhile
            if ($('#viewGrrSampleId').val() !== settingId) {
                return;
            }
            const items = response.data || [];
            const isActive = function (item) { return Number(item.status) === 0; };

            $list.html(items.filter(isActive).map(buildGrrQuestionnaireCard).join(''));
            $('#grrQuestionnaireInactiveList').html(
                items.filter(function (item) { return !isActive(item); }).map(buildGrrQuestionnaireCard).join('')
            );
            renumberGrrQuestionnaire($list);
        },
        error: function (xhr) {
            console.error('Load failed:', xhr.responseText);
            showError('Failed to load questionnaire.');
        },
        complete: function () {
            $loading.hide();
        }
    });
}

function saveGrrQuestionnaireOrder($list) {
    const orderedIds = $list.children('.grr-q-card').map(function () {
        return $(this).data('id');
    }).get();

    // Block further drags until the server confirms, so saves can't interleave
    $list.addClass('is-saving').sortable('disable');

    $.ajax({
        type: 'POST',
        url: 'update_grr_questionnaire_order',
        data: {
            grr_setting_id: $('#viewGrrSampleId').val(),
            ordered_ids: orderedIds
        },
        dataType: 'json',
        success: function () {
            toastr.success('Questionnaire order saved.');
        },
        error: function (xhr) {
            console.error('Reorder failed:', xhr.responseText);
            const message = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save the new order.';
            showError(message);
            // Restore the order that is actually stored in the database
            loadGrrQuestionnaireList();
        },
        complete: function () {
            $list.removeClass('is-saving').sortable('enable');
        }
    });
}


/**
 * Bind events for buttons, forms, etc.
 */
function bindGrrSampleEvents($GRRSampleTable, $form, $modal, $modalViewQuestionnaire, dtGrrSettings){

    $('#btnShowAddGRRSamplesModal').on('click', function () {
        resetGRRSamplesForm($form);
        $modal.modal('show');
    });

    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();

        if($form.find('#grrNo').val() != "" && $form.find('#grrSample').val() != "" && $form.find('#section').val() != ""){
            saveGrrSample($form, $modal, dtGrrSettings);
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

        // Clear the previous sample's items so they never flash while loading
        $('#grrQuestionnaireList').empty();
        $('#grrQuestionnaireInactiveList').empty();
        renumberGrrQuestionnaire($('#grrQuestionnaireList'));
        $('#grrQuestionnaireEmpty').hide();

        $modalViewQuestionnaire.modal('show');
        loadGrrQuestionnaireList();

    });

}

function bindGrrQuestionnaireEvents($modalViewQuestionnaire,$formQuestionnaire,$modalAddQuestionnaire,dtGrrSettings) {

    $('#btnShowAddGRRQuestionnaireModal').off('click.grrQuestionnaire').on('click.grrQuestionnaire', function () {
        resetGRRQuestionnaireForm($formQuestionnaire);
        const sampleId = $('#viewGrrSampleId').val();
        $('#qSampleId').val(sampleId);
        loadGrrSeriesOptions(sampleId).always(function () {
            $modalAddQuestionnaire.modal('show');
        });
    });

    // Closing the stacked "add/edit" modal removes Bootstrap's body flag, which breaks scrolling of the modal underneath
    $modalAddQuestionnaire.off('hidden.bs.modal.grrQuestionnaire').on('hidden.bs.modal.grrQuestionnaire', function () {
        if ($('#modalViewQuestionnaire').hasClass('show')) {
            $('body').addClass('modal-open');
        }
    });

    $formQuestionnaire.off('submit.grrQuestionnaire').on('submit.grrQuestionnaire', function (e) {
        e.preventDefault();
        saveGrrQuestionnaire($formQuestionnaire,$modalAddQuestionnaire,dtGrrSettings);
    });


    $modalViewQuestionnaire.off('click.grrQuestionnaire', '.grr-q-card .btnEdit').on('click.grrQuestionnaire', '.grr-q-card .btnEdit', function () {

        const id = $(this).data('id');

        fetchGrrQuestionnaireById(
            id,
            $modalAddQuestionnaire
        );
    });


    $modalViewQuestionnaire.off('click.grrQuestionnaire', '.grr-q-card .btnDisable').on('click.grrQuestionnaire', '.grr-q-card .btnDisable', function () {
        const id = $(this).data('id');

        confirmAction(
            'Are you sure you want to disable this questionnaire?',
            function () {
                updateQuestionnaireStatus(id);
            }
        );
    });


    $modalViewQuestionnaire.off('click.grrQuestionnaire', '.grr-q-card .btnEnable').on('click.grrQuestionnaire', '.grr-q-card .btnEnable', function () {
        const id = $(this).data('id');
        confirmAction(
            'Are you sure you want to enable this examination?',
            function () {
                updateQuestionnaireStatus(id);
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

function saveGrrQuestionnaire($formQuestionnaire, $modalAddQuestionnaire, dtGrrSettings) {
    const $btnSave = $('#btnSaveQuestionnaire').prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: 'add_grr_questionnaire',
        data: $formQuestionnaire.serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.result === 1) {
                dtGrrSettings.draw(false);
                loadGrrQuestionnaireList();
                $modalAddQuestionnaire.modal('hide');
                $formQuestionnaire[0].reset();
                showSuccess('Successfully saved!');
            }
        },
        error: function (xhr) {
            console.error('Save failed:', xhr.responseText);
            const errors = xhr.responseJSON && xhr.responseJSON.errors;
            const first = errors ? Object.values(errors)[0][0] : null;
            showError(first || 'Failed to save data.');
        },
        complete: function () {
            $btnSave.prop('disabled', false);
        }
    });
}

function loadGrrSeriesOptions(sampleId, selectedId) {
    const $select = $('#qSeries');

    return $.ajax({
        type: 'GET',
        url: 'fetch_grr_series_by_sample',
        data: { grr_setting_id: sampleId, selected_id: selectedId || '' },
        dataType: 'json'
    }).done(function (response) {
        const items = response.data || [];
        $select.empty().append('<option value="" disabled selected>Select One</option>');
        items.forEach(function (item) {
            $select.append($('<option>', { value: item.id, text: item.series_name }));
        });
        if (items.length === 0) {
            showError('No active series name found for this section. Add one in the Series Name Settings tab first.');
        }
    }).fail(function (xhr) {
        console.error('Series load failed:', xhr.responseText);
        showError('Failed to load series names.');
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
            $('#judgement').val(response.judgement);

            loadGrrSeriesOptions(response.grr_setting_id, response.series_id).always(function () {
                $('#qSeries').val(response.series_id);
                $modalViewQuestionnaire.modal('show');
            });
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

function updateQuestionnaireStatus(id) {
    $.ajax({
        type: 'POST',
        url: 'update_grr_questionnaire_status',
        data: { id },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showSuccess('Status updated successfully.');
                loadGrrQuestionnaireList();
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
