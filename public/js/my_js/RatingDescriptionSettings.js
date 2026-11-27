$(document).ready(function () {
    // --------------------------------------
    // Cache DOM elements
    // --------------------------------------
    const $table = $('#tblRatingDescription');        
    const $tableRDDetails = $('#tblRatingDescriptionDetails');        
    const $form = $('#formRatingDescription');        
    const $modal = $('#modalAddTargetRating');
    const $addRatingDescription = $('#btnAddRatingDescription');
    const $addRatingDescriptionDetails = $('#btnAddRatingDescriptionDetails');
    let rowIndex = 0;

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
    const dtTargetRatings = initTargetRatingsTable($table);

    // --------------------------------------
    // Bind all event handlers
    // --------------------------------------
    bindTargetRatingEvents($table, $tableRDDetails, $form, $modal, dtTargetRatings, $addRatingDescription, $addRatingDescriptionDetails, rowIndex);
});

/**
 * Reset a form and clear hidden fields
 * @param {string|jQuery} formSelector - the form element or selector
 */
function resetTargetRatingForm(formSelector, tableRDDetails, rowIndex) {
    const $form = $(formSelector);

    $form[0].reset();
    $form.find('input[type="hidden"]').val('');

    // Remove all rows except template row
    $(tableRDDetails).find('tbody tr:not(.data-row)').remove();

    // Clear template row inputs
    $(tableRDDetails).find('.data-row input').val('');

    const $tbody = $(tableRDDetails).find('tbody');
          $tbody.html(getRow(rowIndex));

    updateRemoveButtons(tableRDDetails);
}

function escapeHtml(s) {
    return $('<div>').text(s == null ? '' : s).html();
}

function renderDetails(field, center) {
    return function (data, type) {
        if (!data || !data.length) return '';

        // keep search/sort/export working on plain text
        if (type !== 'display') {
            return data.map(function (d) { return d[field]; }).join(', ');
        }

        return data.map(function (d, i) {
            var border = i < data.length - 1 ? ' border-bottom' : '';
            return '<div class="detail-line' + border + (center ? ' text-center' : '') + '">'
                 + escapeHtml(d[field]) + '</div>';
        }).join('');
    };
}

/**
 * Initialize DataTable
 */
function initTargetRatingsTable($table, url = 'view_rating_descriptions') {
    return $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: url },
        order: [[2, 'asc']],                       // column 0 is Action, which can't be ordered
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'status_label' },
            { data: 'position' },
            { data: 'designation_label' },
            { data: 'rating_description_details', name: 'rating_description_details.level',
                orderable: false, className: 'text-center', render: renderDetails('level', true) },
            { data: 'rating_description_details', name: 'rating_description_details.description',
                orderable: false, className: 'text-center', render: renderDetails('description', false) },
        ]
    });
}

/**
 * Bind events for buttons, forms, etc.
 */
function bindTargetRatingEvents($table, $tableRDDetails, $form, $modal, dtTargetRatings, $addRatingDescription , $addRatingDescriptionDetails, rowIndex){

    $addRatingDescription.on('click', function () {
        resetTargetRatingForm($form, $tableRDDetails, rowIndex);

        loadProductLines($('.selectDesignation'));
        $modal.modal('show');
    });

    $addRatingDescriptionDetails.on('click', function () {
        console.log('rowIndex vlaue', rowIndex);
        rowIndex = $tableRDDetails.find('.data-row').length;

        const $templateRow = $tableRDDetails.find('.data-row').first();

        let newRow = $templateRow.clone(false, false);

        // Clear inputs
        newRow.find('input, textarea').val('');
        newRow.find('.removeRow').prop('disabled', false);

        // Remove select2 container
        newRow.find('.select2-container').remove();

        // FIX: Update ALL fields (input, textarea, select)
        newRow.find('input, textarea, select').each(function () {
            let name = $(this).attr('name');

            if (name) {
                let updatedName = name.replace(/\[\d+\]/, `[${rowIndex}]`);
                $(this).attr('name', updatedName);
            }
        });

        // Append row
        $tableRDDetails.find('tbody').append(newRow);

        updateRemoveButtons($tableRDDetails);
    });

    // --------------------
    // REMOVE ROW
    // --------------------
    $tableRDDetails.on('click', '.removeRow', function() {
        // if it is template row, DON'T allow delete
        // if ($(this).closest('tr').hasClass('data-row')) return;
        $(this).closest('tr').remove();
        updateRemoveButtons($tableRDDetails);
    });

    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();
        saveProcessStations($form, $modal, dtTargetRatings);
    });

    // Edit button
    $table.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        fetchTargetRatingsById(id, $modal, $tableRDDetails, rowIndex);
    });

    // Disable button
    $table.on('click', '.btnDisable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to disable this?', function () {
            updateStatus(id, dtTargetRatings);
        });
    });

    // Enable button
    $table.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to enable this?', function () {
            updateStatus(id, dtTargetRatings);
        });
    });
}

function updateRemoveButtons($tableRDDetails){
    let rowCount = $tableRDDetails.find('.data-row').length;

    if (rowCount <= 1) {
        $tableRDDetails.find('.removeRow').prop('disabled', true);
    } else {
        $tableRDDetails.find('.removeRow').prop('disabled', false);
    }
}

function getRow(index) {
    return `
        <tr class="data-row">
            <td>
                <center>
                    <button class="btn btn-md btn-danger removeRow" type="button">
                        <i class="fa fa-times"></i>
                    </button>
                </center>
            </td>
            <td>
                <input type="number" class="form-control" name="level[${index}]" required></input>
            </td>
            <td>
                <input type="text" class="form-control" name="description[${index}]" required></input>
            </td>
        </tr>
    `;
}

/**
 * Save (add/update) process_stations data
 */
function saveProcessStations($form, $modal, dtTargetRatings) {
    $.ajax({
        type: 'POST',
        url: 'add_rating_descriptions',
        data: $form.serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.result === 1) {
                dtTargetRatings.draw(false);
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
 * Fetch process_stations data by ID
 */
function fetchTargetRatingsById(id, $modal, tableRDDetails) {
    $.ajax({
        type: 'GET',
        url: 'get_rating_description_by_id',
        data: { id },
        dataType: 'json',
        success: function (response) {
            // Populate modal fields (adjust names per process_stations)
            $('#ratingDescriptionId').val(response.id);
            $('#position').val(response.position);
            // $('#designation').val(response.designation);
            
            loadProductLines($('.selectDesignation'), response.designation);

            rowIndex = 0;
            tableRDDetails.find('tbody').empty();
            for(let index = 0; index < response.rating_description_details.length; index++){
                let rating_details = `
                    <tr class="data-row">
                        <td id="removeRow">
                            <center><button class="btn btn-md btn-danger removeRow" title="Remove Row" type="button"><i class="fa fa-times"></i></button></center>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="level[${rowIndex}]" value="${response.rating_description_details[index].level}" />
                        </td>
                        <td>
                            <input type="text" class="form-control" name="description[${rowIndex}]" value="${response.rating_description_details[index].description}" />
                        </td>
                    </tr>
                `;

                rowIndex++;

                tableRDDetails.find('tbody').append(rating_details);
            }

            $modal.modal('show');
        },
        error: function (xhr) {
            console.error('Fetch failed:', xhr.responseText);
            showError('Failed to fetch data.');
        }
    });
}

/**
 * Disable or update process_stations status
 */
function updateStatus(id, dtTargetRatings) {
    $.ajax({
        type: 'POST',
        url: 'update_rating_description_status',
        data: { id },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showSuccess('Status updated successfully.');
                dtTargetRatings.draw(false);
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

function loadProductLines(cboElement, currentDesignationId = null){
    let result = '<option value="" disabled selected> Select Designation</option>';
    $.ajax({
        method: "get",
        url: "get_designation_fr_master",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected> -- Loading -- </option>';
        },
        success: function (response) {
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Designation</option>';
                for (let i = 0; i < response.length; i++) {
                    result += '<option value="' + response[i]['id'] + '">' + response[i]['dropdown_masters_details'] + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }

            cboElement.html(result);

            designationSplitted = currentDesignationId.split(',');
            designationSplitted.forEach(element => {
                cboElement.find('option[value="' + element + '"]').prop('selected', true);
                cboElement.trigger('change');
            });
        },
        error: function(data, xhr, status) {
            result = '<option value="0" selected disabled> -- Reload Again -- </option>';
            cboElement.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
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
