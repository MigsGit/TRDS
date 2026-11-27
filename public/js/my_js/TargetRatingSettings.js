$(document).ready(function () {
    // --------------------------------------
    // Cache DOM elements
    // --------------------------------------
    const $table = $('#tblTargetRatings');        
    const $tableTRDetails = $('#tblTargetRatingDetails');        
    const $form = $('#formTargetRating');        
    const $modal = $('#modalAddTargetRating');
    const $addTargetRating = $('#btnAddTargetRating');
    const $addTargetRatingDetails = $('#btnAddTargetRatingDetails');
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
    bindTargetRatingEvents($table, $tableTRDetails, $form, $modal, dtTargetRatings, $addTargetRating, $addTargetRatingDetails, rowIndex);
});

/**
 * Reset a form and clear hidden fields
 * @param {string|jQuery} formSelector - the form element or selector
 */
function resetTargetRatingForm(formSelector, tableTRDetails, rowIndex) {
    const $form = $(formSelector);

    $form[0].reset();
    $form.find('input[type="hidden"]').val('');

    // Remove all rows except template row
    $(tableTRDetails).find('tbody tr:not(.data-row)').remove();

    // Clear template row inputs
    $(tableTRDetails).find('.data-row input').val('');

    const $tbody = $(tableTRDetails).find('tbody');
          $tbody.html(getRow(rowIndex));

    updateRemoveButtons(tableTRDetails);
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

// function renderDetails(field) {
//     return function (data, type) {
//         if (!data || !data.length) return '';

//         // keep search/sort/export working on plain text
//         if (type !== 'display') {
//             return data.map(function (d) { return d[field]; }).join(', ');
//         }

//         return data.map(function (d) {
//             return '<div class="detail-line text-center">' + escapeHtml(d[field]) + '</div>';
//         }).join('');
//     };
// }

/**
 * Initialize DataTable
 */
function initTargetRatingsTable($table, url = 'view_target_ratings') {
    return $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: url },
        order: [[2, 'asc']],                       // column 0 is Action, which can't be ordered
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'status_label' },
            { data: 'section' },
            { data: 'position' },
            { data: 'product_area_info.dropdown_masters_details' },
            { data: 'product_line_label' },
            { data: 'rating_details', name: 'rating_details.target_name',
                orderable: false, render: renderDetails('target_name', false) },
            { data: 'rating_details', name: 'rating_details.rating',
                orderable: false, className: 'text-center', render: renderDetails('rating', true) }
        ]
    });
}

/**
 * Bind events for buttons, forms, etc.
 */
function bindTargetRatingEvents($table, $tableTRDetails, $form, $modal, dtTargetRatings, $addTargetRating , $addTargetRatingDetails, rowIndex){

    $addTargetRating.on('click', function () {
        resetTargetRatingForm($form, $tableTRDetails, rowIndex);

        loadProductArea($('.selectProductArea'));
        loadProductLines($('.selectProductLine'));
        $modal.modal('show');
    });

    $addTargetRatingDetails.on('click', function () {
        console.log('rowIndex vlaue', rowIndex);
        rowIndex = $tableTRDetails.find('.data-row').length;

        const $templateRow = $tableTRDetails.find('.data-row').first();

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
        $tableTRDetails.find('tbody').append(newRow);

        updateRemoveButtons($tableTRDetails);
    });

    // --------------------
    // REMOVE ROW
    // --------------------
    $tableTRDetails.on('click', '.removeRow', function() {
        // if it is template row, DON'T allow delete
        // if ($(this).closest('tr').hasClass('data-row')) return;
        $(this).closest('tr').remove();
        updateRemoveButtons($tableTRDetails);
    });

    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();
        saveProcessStations($form, $modal, dtTargetRatings);
    });

    // Edit button
    $table.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        fetchTargetRatingsById(id, $modal, $tableTRDetails, rowIndex); //clark ongoing
    });

    // Disable button
    $table.on('click', '.btnDisable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to disable this examination?', function () {
            updateStatus(id, dtTargetRatings);
        });
    });

    // Enable button
    $table.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to enable this examination?', function () {
            updateStatus(id, dtTargetRatings);
        });
    });
}

function updateRemoveButtons($tableTRDetails){
    let rowCount = $tableTRDetails.find('.data-row').length;

    if (rowCount <= 1) {
        $tableTRDetails.find('.removeRow').prop('disabled', true);
    } else {
        $tableTRDetails.find('.removeRow').prop('disabled', false);
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
                <input type="text" class="form-control" name="target_name[${index}]" required></input>
            </td>
            <td>
                <input type="number" class="form-control" name="rating[${index}]" required></input>
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
        url: 'add_target_ratings',
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
function fetchTargetRatingsById(id, $modal, tableTRDetails) {
    $.ajax({
        type: 'GET',
        url: 'get_target_rating_by_id',
        data: { id },
        dataType: 'json',
        success: function (response) {
            // Populate modal fields (adjust names per process_stations)
            
            $('#targetRatingId').val(response.id);
            $('#section').val(response.section);
            $('#position').val(response.position);

            loadProductArea($('.selectProductArea'), response.product_area);
            loadProductLines($('.selectProductLine'), response.product_line);

            rowIndex = 0;
            tableTRDetails.find('tbody').empty();
            for(let index = 0; index < response.rating_details.length; index++){
                let rating_details = `
                    <tr class="data-row">
                        <td id="removeRow">
                            <center><button class="btn btn-md btn-danger removeRow" title="Remove Row" type="button"><i class="fa fa-times"></i></button></center>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="target_name[${rowIndex}]" value="${response.rating_details[index].target_name}" />
                        </td>
                        <td>
                            <input type="number" class="form-control" name="rating[${rowIndex}]" value="${response.rating_details[index].rating}" />
                        </td>
                    </tr>
                `;

                rowIndex++;

                tableTRDetails.find('tbody').append(rating_details);
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
        url: 'update_status',
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

function loadProductArea(cboElement, currentProductAreaId = null){
    let result = '<option value="" disabled selected> Select Product Area </option>';
    $.ajax({
        // data: { hr_only },
        method: "get",
        url: "get_product_area_fr_master",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected> -- Loading -- </option>';
        },
        success: function (response) {
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Product Area </option>';
                for (let i = 0; i < response.length; i++) {
                    result += '<option value="' + response[i]['id'] + '">' + response[i]['dropdown_masters_details'] + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }

            cboElement.html(result);

            if(currentProductAreaId != null){
                cboElement.val(currentProductAreaId).trigger('change');
            }
        },
        error: function(data, xhr, status) {
            result = '<option value="0" selected disabled> -- Reload Again -- </option>';
            cboElement.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

function loadProductLines(cboElement, currentProductLineId = null){
    let result = '<option value="" disabled selected> Select Product Line </option>';
    $.ajax({
        // data: { hr_only },
        method: "get",
        url: "get_product_line_fr_master",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected> -- Loading -- </option>';
        },
        success: function (response) {
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Product Line </option>';
                for (let i = 0; i < response.length; i++) {
                    result += '<option value="' + response[i]['id'] + '">' + response[i]['dropdown_masters_details'] + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }

            cboElement.html(result);

            prodLineSplitted = currentProductLineId.split(',');
            prodLineSplitted.forEach(element => {
                cboElement.find('option[value="' + element + '"]').prop('selected', true);
                cboElement.trigger('change');
            });
            
            // if(currentProductLineId != null){
            //     cboElement.val(currentProductLineId).trigger('change');
            // }
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
