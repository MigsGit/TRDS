$(document).ready(function () {
    // --------------------------------------
    // Cache DOM elements
    // --------------------------------------
    const $table = $('#tblProcessStation');        
    const $form = $('#formProcessStation');        
    const $modal = $('#modalAddProcessStation');
    const $addProductLine = $('#btnAddProductLine');

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
    const dtProcessStation = initProcessStationsTable($table);

    // --------------------------------------
    // Bind all event handlers
    // --------------------------------------
    bindProcessStationsEvents($table, $form, $modal, dtProcessStation, $addProductLine);
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
function initProcessStationsTable($table, url = 'view_process_stations') {
    return $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: url },
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'section' },    // customize this per process_stations
            { data: 'skill_category' },    // customize this per process_stations
            { data: 'process_order' },    // customize this per process_stations
            { data: 'process_station' },    // customize this per process_stations
            { data: 'product_line' },    // customize this per process_stations
            { data: 'status_label' }
        ]
    });
}

/**
 * Bind events for buttons, forms, etc.
 */
function bindProcessStationsEvents($table, $form, $modal, dtProcessStation, $addProductLine){

    $('#btnShowAddProcessStationModal').on('click', function () {
        resetExaminationForm($form);
        $modal.modal('show');
    });

    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();

        if($form.find('#naProductLineCheckbox').is(":checked")){
            saveProcessStations($form, $modal, dtProcessStation);
        }else if($form.find('#productLine').val() != ""){
            saveProcessStations($form, $modal, dtProcessStation);
        }else{
            showError('Failed to save data, Please Select Product Line');
        }
    });

    // Edit button
    $table.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        fetchProcessStationsById(id, $modal);
    });

    // Disable button
    $table.on('click', '.btnDisable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to disable this examination?', function () {
            updateProcessStationsStatus(id, dtProcessStation);
        });
    });

    // Enable button
    $table.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to enable this examination?', function () {
            updateProcessStationsStatus(id, dtProcessStation);
        });
    });

    $form.on('change', '#naProductLineCheckbox', function (e) {
        if ($(this).is(":checked")){
            $("#productLine").val('N/A').prop("disabled", true).trigger('change');
                // .val("N/A")
                // .prop("readonly", true)
                // .prop("required", false)
                // .attr("placeholder", "Not Applicable");
        }else{
            $("#productLine").val('').prop("disabled", false).trigger('change');
                // .val("")
                // .prop("readonly", false)
                // .prop("required", true)
                // .attr("placeholder", "Enter a value");
        }
    });

    $form.on('change', '#skillCategory, #section', function (e) {
        let section = $form.find('#section').val();
        let skill_category = $form.find('#skillCategory').val();
        $.ajax({
            type: 'GET',
            url: 'get_process_count_per_category',
            data: { skill_category, section },
            dataType: 'json',
            success: function (response) {
                let next_count = response.count + 1;
                $('#processOrder').val(next_count);
            },
            error: function (xhr) {
                console.error('Fetch failed:', xhr.responseText);
                showError('Failed to fetch data.');
            }
        });
    });
}

/**
 * Save (add/update) process_stations data
 */
function saveProcessStations($form, $modal, dtProcessStation) {
    $.ajax({
        type: 'POST',
        url: 'add_process_station',
        data: $form.serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.result === 1) {
                dtProcessStation.draw(false);
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
function fetchProcessStationsById(id, $modal) {
    $.ajax({
        type: 'GET',
        url: 'get_process_station_by_id',
        data: { id },
        dataType: 'json',
        success: function (response) {
            // Populate modal fields (adjust names per process_stations)
            
            $('#txtProcessId').val(response.id);
            $('#section').val(response.section);
            $('#processOrder').val(response.process_order);
            $('#skillCategory').val(response.skill_category);
            $('#processStation').val(response.process_station);

            let selected_product_line = response.product_line.split(",");
            $('#productLine').val(selected_product_line).trigger("change");

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
function updateProcessStationsStatus(id, dtProcessStation) {
    $.ajax({
        type: 'POST',
        url: 'update_process_station_status',
        data: { id },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showSuccess('Status updated successfully.');
                dtProcessStation.draw(false);
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
