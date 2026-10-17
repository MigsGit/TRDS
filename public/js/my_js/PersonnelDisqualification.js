const DROPDOWN_MASTER_URL = 'get_dropdown_from_master'; // your single endpoint, receives ?type=...
const DROPDOWN_SYSTEMONE_URL = 'get_dropdown_from_systemone'; // your single endpoint, receives ?type=...

// one entry per dropdown; add new ones here
const DROPDOWNSFROMMASTER = {
    modeOfDefect:      { type: 'mode_of_defect',     label: 'Mode of Defect' },
    classification:    { type: 'classification',     label: 'Classification', dataAttrs: { subclass: 'remarks' } },
    subClassification: { type: 'sub_classification', label: 'Sub-Classification', disableIfEmpty: true },
    trainingScope:     { type: 'training_scope',     label: 'Training Scope' },
};

const DROPDOWNSFROMSYSTEMONE = {
    conductedBy:    { type: 'conducted_by',   label: 'Conducted By' },
    trainedBy:      { type: 'trained_by',     label: 'Trained By' },
};

//declare variable outside .ready function to be able to use in function outside .ready
let currentSubClass = null;
let currentStation = null;
let currentSeries = null;

$(document).ready(function () {
    // --------------------------------------
    // Cache DOM elements
    // --------------------------------------
    const $table = $('#tblDisqualification');           
    const $tableTrainings = $('#tblTrainings');           
    const $form = $('#formHrMemoApproval');            
    const $formPart1 = $('#formDisqualiPart1');            
    const $formPart2 = $('#formDisqualiPart2');            
    const $formPart2Trainings = $('#formTrainings');            
    const $modal = $('#modalDisqualification');         
    const $modalTrainings = $('#modalTrainings');         
    const $addDisqualiButton = $('#btnAddDisqualification');
    const $addTrainingsButton = $('#btnAddTraining');
    const dtDisqualification = initDataTable($table);
    const dtTrainings = initDataTableTrainings($tableTrainings, $formPart2);

    // ------       --------------------------------
    // Initialize global AJAX setup (once per project)
    // --------------------------------------
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Apply Select2 to all select elements inside any modal dynamically
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('.select2bs5').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                // dropdownParent: $(this).closest('.modal') // Ensures correct parent modal
            });
        });
    });
    
    // --------------------------------------
    // Bind all event handlers
    // --------------------------------------
    bindEvents($table,
        $tableTrainings,
        $form,
        $formPart1,
        $formPart2,
        $formPart2Trainings,
        $modal,
        $modalTrainings,
        $addDisqualiButton,
        $addTrainingsButton,
        dtDisqualification,
        dtTrainings,
    );
});

/**
 * Reset a form and clear hidden fields
 * @param {string|jQuery} formSelector - the form element or selector
 */
function resetPart1Form(formSelector, dtDisqualification) {
    const $formSelector = $(formSelector);
    $formSelector[0].reset();
    $formSelector.find('input[type="hidden"]').val('');

    $formSelector.find('#subject').prop('disabled', false);
    $formSelector.find('#classification').prop('disabled', false);
    $formSelector.find('#from').prop('disabled', false);
    $formSelector.find('#reason').prop('disabled', false);
    $formSelector.find('#dateFiled').prop('disabled', false);
    $formSelector.find('#selectTo').prop('disabled', false);
    $formSelector.find('#selectCc').prop('disabled', false);
    $formSelector.find('#notedBy').prop('disabled', false);
    $formSelector.find('#btnAddTrainee').prop('disabled', false);
    $formSelector.find('#btnAddTrainee').prop('hidden', false);

    $formSelector.find('#btnSubmitHrMemoApproval').removeClass('d-none');
    $formSelector.find('#btnApprove').addClass('d-none');
    $formSelector.find('#btnDisapprove').addClass('d-none')

    dtDisqualification.clear().draw();
    // updateRemoveButtons(tableImprovementActions);
}

/**
 * Initialize DataTable
 */
function initDataTable($table, url = 'view_disqualification') {
    return $table.DataTable({
        processing: true,
        serverSide: true,
        scrollY: '380px',
        scrollCollapse: true,
        ajax: { url: url },
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'status_label' },
            { data: 'control_no' },    
            { data: 'disqualified_personnel_name' },    
            { data: 'station_label' },    
            { data: 'disqualified_date_label' },    
            { data: 'recertification_date_label' },    
            { data: 'disqualification_reason' },
        ]
    });
}

function initDataTableTrainings($table, $paramForm, url = 'view_trainings') {
    return $table.DataTable({
        processing: true,
        serverSide: true,
        scrollY: '380px',
        scrollCollapse: true,
        ajax: { 
            url: url,
            data: function (param){
                param.part2_id = $paramForm.find('#part2Id').val();
            },
        },
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false, visible: false },
            { data: 'category_label' },    
            { data: 'scope_label' },
            { data: 'trained_by_name' },
            { data: 'training_date' }
        ]
    });
}

/**
 * Bind events for buttons, forms, etc.
 */
function bindEvents($table, $tableTrainings, $form, $formPart1, $formPart2, $formPart2Trainings, $modal, $modalTrainings, $addDisqualiButton, $addTrainingsButton, dtDisqualification, dtTrainings){
    $addDisqualiButton.on('click', function () {
        console.log('Add Disqualification button clicked');
        // resetPart1Form($form, dtDisqualification);

        selectEmailRecipients($('.selectAttentionToRecipients'));
        $modal.modal('show');

        selectEmpDetailsFromCertification($('.selectEmployeeName'));
        loadDropdownFrMaster($('.selectModeofDefect'), 'modeOfDefect');
        loadDropdownFrMaster($('.selectClassification'), 'classification');
    });

    $addTrainingsButton.on('click', function (){
        let part1_id = $(this).data('part1_id');
        let part2_id = $(this).data('id');
        let part2_trainig_status = $(this).data('status');
        console.log('part2_training', part2_trainig_status);
        
        console.log('Add button clicked');
        // resetPart1Form($form, dtDisqualification);
        // selectEmpDetailsFromCertification($('.selectEmployeeName'));
        $formPart2Trainings.find('#part1Id').val(part1_id);
        $formPart2Trainings.find('#part2Id').val(part2_id);
        $formPart2Trainings.find('#part2TrainingStatus').val(part2_trainig_status);
        loadDropdownFrMaster($('.selectTrainingScope'), 'trainingScope');
        loadDropdownFrSystemOne($('.selectTrainedBy'), 'trainedBy');
        $modalTrainings.modal('show');
    });

    // Handle employee number input
    $('#employeeName').on('change', function(){
        let position = $(this).find('option:selected').data('position');
        let section = $(this).find('option:selected').data('section');
        let station = $(this).find('option:selected').data('station');
        let station_name = $(this).find('option:selected').data('station_name');
        let series_name = $(this).find('option:selected').data('series_name');

        let arr_stations = Array.isArray(station)
            ? station
            : String(station || '').split(',');

        let arr_stationNames = Array.isArray(station_name)
            ? station_name
            : String(station_name || '').split(',');

        if (arr_stations.length > 0 && arr_stations[0] !== '') {
            station_result = '<option value="" disabled selected>Select Station</option>';

            for (let i = 0; i < arr_stations.length; i++) {
                station_result += '<option value="' + arr_stations[i].trim() + '">' +
                        (arr_stationNames[i] || '').trim() +
                        '</option>';
            }
        } else {
            station_result = '<option value="0" selected disabled>-- No record found --</option>';
        }

        let arr_seriesNames = Array.isArray(series_name)
            ? series_name
            : String(series_name || '').split(',');

        if (arr_seriesNames.length > 0 && arr_seriesNames[0] !== '') {
            series_result = '<option value="" disabled selected>Select Series</option>';

            for (let i = 0; i < arr_seriesNames.length; i++) {
                series_result += '<option value="' + arr_seriesNames[i].trim() + '">' +
                        (arr_seriesNames[i] || '').trim() +
                        '</option>';
            }
        } else {
            series_result = '<option value="0" selected disabled>-- No record found --</option>';
        }

        $formPart1.find('#personnelDesignation').val(position);
        $formPart1.find('#section').val(section);
        $formPart1.find($('.selectStation')).html(station_result);
        $formPart1.find($('.selectSeriesModelName')).html(series_result);
        
        if(currentStation != null){
            $('.selectStation').val(currentStation).trigger('change');
        }

        if(currentSeries != null){
            $('.selectSeriesModelName').val(currentSeries).trigger('change');
        }
    });

    // Handle employee number input
    $('#classification').on('change', function(){
        let sub_class = $(this).find('option:selected').data('subclass');
        
        // sub-classification, sending sub_class and preselecting a value
        loadDropdownFrMaster($('.selectSubClassification'), 'subClassification', {
          params: { sub_class: sub_class },
          selected: currentSubClass
        });

        // selectSubClassification($('.selectSubClassification'), sub_class);
        // You can add your logic here to handle the change event for classification
    });

    // Handle Reason for Disqualification auto generate data
    $('#modeOfDefect, #seriesModelName, #classification, #subClassification').on('change', function () {
        let modeOfDefectText = $formPart1.find('#modeOfDefect option:selected').text();
        let seriesModelNameText = $formPart1.find('#seriesModelName option:selected').text();
        let classificationText = $formPart1.find('#classification option:selected').text();
        let subClassificationText = $formPart1.find('#subClassification option:selected').text();

        let isModSelected = $formPart1.find('#modeOfDefect option:selected').val() != '' ? true : false;
        let isSeriesSelected = $formPart1.find('#seriesModelName option:selected').val() != '' ? true : false;
        let isClassificationSelected = $formPart1.find('#classification option:selected').val() != '' ? true : false;
        let isSubClassRequired = $formPart1.find('#classification option:selected').data('subclass') != null ? true : false;
        let isSubClassificationSelected = $formPart1.find('#subClassification option:selected').val() > 0 ? true : false;
        
        if (isModSelected && isSeriesSelected && isClassificationSelected) {
            if(isSubClassRequired && isSubClassificationSelected){
                let reasonForDisqualification = 'Recertification of disqualified personnel involved on '+classificationText+'('+subClassificationText+') '+ seriesModelNameText+' '+modeOfDefectText+'.';
                $formPart1.find('#reasonForDisqualification').val(reasonForDisqualification);
            }else{
                let reasonForDisqualification = 'Recertification of disqualified personnel involved on '+classificationText+' '+ seriesModelNameText+' '+modeOfDefectText+'.';
                $formPart1.find('#reasonForDisqualification').val(reasonForDisqualification);
            }
        }
    });

    // Submit form (Add / Edit)
    // $formPart1.on('submit', function (e) {
    //     e.preventDefault();
    //     saveDisqualiPart1($formPart1, $modal, dtDisqualification);
    // });

    // Part 1: closes the modal, resets the form, reloads the table
    $formPart1.on('submit', function (e) {
        e.preventDefault();
        saveDisqualiPart($formPart1, 'add_disquali_part1', {
            modal: $modal,
            table: dtDisqualification,
        });
    });

    // Part 2: stays open so the user can continue to Part 3, no reset
    $formPart2.on('submit', function (e) {
        e.preventDefault();
        saveDisqualiPart($formPart2, 'add_disquali_part2', {
            modal: $modal,
            table: dtDisqualification,
            successMessage: 'Part 2 saved. Continue to Part 3.',
            onSuccess: (response) => {
                // e.g. expand the next collapse card
                // $('#collapsePart3').collapse('show');
            },
        });
    });

    // Part 2 (Training): stays open so the user can continue to Part 3, no reset
    $formPart2Trainings.on('submit', function (e) {
        e.preventDefault();
        saveDisqualiPart($formPart2Trainings, 'add_part2_training', {
            modal: $modalTrainings,
            table: dtTrainings,
            addModal: $modal,
            addTable: dtDisqualification,
            addForm: $formPart2,
            addButton: $formPart2.find('#btnAddTraining'),
            successMessage: 'Training saved.',
            onSuccess: (response, addForm, addButton) => {
                // addForm.find('#btnAddTraining').data('status', response.new_status);
                addButton.data('status', response.new_status);
                // if(response.new_status == 4){
                //     addButton.prop('disabled', true);
                //     addButton.prop('hidden', true);
                // }
                // e.g. expand the next collapse card
                // $('#collapsePart3').collapse('show');
            },
        });
    });

    // Edit button
    $table.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        fetchDisqualificationById(id, $modal, dtTrainings, $formPart1, $formPart2, 'edit');
    });

    // View button
    $table.on('click', '.btnView', function () {
        const id = $(this).data('id');
        const approval = $(this).data('approval');
        fetchDisqualificationById(id, $modal, dtTrainings, $formPart1, $formPart2, 'view', approval);
    });

    // View Disapproval Remarks button
    $table.on('click', '.btnViewRemarks', function () {
        const remarks = $(this).data('remarks');
        showMessage(remarks, 'Disapproval Remarks', function(){});
    });

    // reusable: bind a click handler that confirms, then updates the status
    function bindStatusAction({ $root, selector, status, message, getId, modal = null, requireRemarks = false }) {
        $root.on('click', selector, function () {
            const id = getId(this);

            const submit = (remarks) =>
                updateHrMemoApprovalStatus(id, dtDisqualification, status, modal, remarks);

            if (requireRemarks) {
                Swal.fire({
                    title: message,
                    input: 'textarea',
                    inputLabel: 'Remarks',
                    inputPlaceholder: 'Enter reason...',
                    inputAttributes: { 'aria-label': 'Enter remarks' },
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => (!value ? 'Remarks is required!' : undefined),
                }).then((result) => {
                    if (result.isConfirmed) submit(result.value);
                });
            } else {
                confirmAction(message, () => submit());
            }
        });
    }

    // where the id comes from
    const idFromRow  = (el) => $(el).data('id');
    const idFromForm = () => $form.find('#txtHrMemoId').val();

    // one entry per action; add new statuses here
    const STATUS_ACTIONS = [
        { $root: $table, selector: '.btnEnable',      status: 1, message: 'Are you sure you want to enable this?', getId: idFromRow },
        { $root: $table, selector: '.btnDisable',     status: 5, message: 'Are you sure you want to disable this?', getId: idFromRow },
        { $root: $table, selector: '.btnFinalSubmit', status: 2, message: 'Are you sure you want to submit this?', getId: idFromRow },
        // { $root: $form,  selector: '#btnHRDisapprove', status: 4, message: 'Disapprove HR Memo', getId: idFromForm, modal: $modal, requireRemarks: true },
        // { $root: $form,  selector: '#btnHRApprove',    status: 5, message: 'Approve HR Memo Document?', getId: idFromForm, modal: $modal },
    ];

    STATUS_ACTIONS.forEach(bindStatusAction);

    // // Enable button
    // $table.on('click', '.btnEnable', function () {
    //     const id = $(this).data('id');
    //     let updateStatusTo = 1; //pending
    //     confirmAction('Are you sure you want to enable this?', function () {
    //         updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo);
    //     });
    // });

    // // Disable button
    // $table.on('click', '.btnDisable', function () {
    //     const id = $(this).data('id');
    //     let updateStatusTo = 2; //cancelled
    //     confirmAction('Are you sure you want to disable this?', function () {
    //         updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo);
    //     });
    // });

    // // Final Submit button
    // $table.on('click', '.btnFinalSubmit', function () {
    //     const id = $(this).data('id');
    //     let updateStatusTo = 3; //for approval
    //     confirmAction('Are you sure you want to submit this?', function () {
    //         updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo);
    //     });
    // });

    // // Disapprove button
    // $form.on('click', '#btnHRDisapprove', function () {
    //     const id = $form.find('#txtHrMemoId').val();
    //     let updateStatusTo = 4; //disapproved

    //     Swal.fire({
    //         title: 'Disapprove HR Memo',
    //         input: 'textarea',
    //         id: 'hrDisapproveRemarks',
    //         inputLabel: 'Remarks',
    //         inputPlaceholder: 'Enter reason for disapproval...',
    //         inputAttributes: {
    //             'aria-label': 'Enter remarks'
    //         },
    //         showCancelButton: true,
    //         confirmButtonText: 'Submit',
    //         cancelButtonText: 'Cancel',
    //         inputValidator: (value) => {
    //             if (!value) {
    //                 return 'Remarks is required!';
    //             }
    //         }
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             let remarks = result.value;
    //             updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo, $modal, remarks);
    //         }
    //     });
    // });

    // // HR Approve button
    // $form.on('click', '#btnHRApprove', function () {
    //     const id = $form.find('#txtHrMemoId').val();
    //     let updateStatusTo = 5; //approved
    //     // let forApproval = true;
    //     confirmAction('Approve HR Memo Document?', function () {
    //         updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo, $modal);
    //     });
    // });

    // Edit button
    $tableTrainings.on('click', '.btnEditTraining', function () {
        const id = $(this).data('id');
        
        // fetchDisqualificationById(id, $modal, dtTrainings, $formPart1, $formPart2, 'edit');
    });
}

function updateRemoveButtons($table, measure_type, array = null, cboElement){
    let length = 0;
    if(measure_type == 'Table'){
        length = $table.find('.data-row').length;
    }else{
        // measure_type = 'Array';
            length = array.length;
    }

    if (length == 1){
        $table.find(cboElement).prop('disabled', true);
    } else {
        $table.find(cboElement).prop('disabled', false);
    }
}

function selectEmailRecipients(cboElement, rapidxId = null, hr_only = false){
    let result = '<option value="" disabled selected> Select Name/s </option>';
    $.ajax({
        data: { hr_only },
        method: "get",
        url: "get_email_recipients_dropdown_details",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected>--Loading--</option>';
        },
        success: function (response) {
            if(response.length > 0){
                    // result = '<option value="" disabled selected> Select Name/s </option>';
                    result = '';
                for (let i = 0; i < response.length; i++) {
                    result += '<option value="' + response[i]['id'] + '" data-email="' + response[i]['email'] + '">' + response[i]['name'] + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }

            cboElement.html(result);

            if(rapidxId != null){
                cboElement.val(rapidxId).trigger('change');
            }
        },
        error: function(data, xhr, status) {
            result = '<option value="0" selected disabled> -- Reload Again -- </option>';
            cboElement.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

function selectEmpDetailsFromCertification(cboElement, empId = null, mode = null){
    let result = '<option value="" disabled selected> Select One </option>';
    $.ajax({
        method: "get",
        url: "get_emp_details_from_certification",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected>--Loading--</option>';
        },
        success: function (response) {
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Employee Name </option>';

                for (let i = 0; i < response.length; i++) {

                    let stations = '';
                    let station_names = '';
                    let series_name = '';

                    response[i]['stations'].forEach(element => {
                        stations += element.station_to + ',';
                        station_names += element.station_to_name + ',';
                    });

                    response[i]['series_name'].forEach(element => {
                        series_name += element.series_name + ',';
                    });

                    stations = stations.slice(0, -1); // Remove the trailing comma
                    station_names = station_names.slice(0, -1); // Remove the trailing comma
                    series_name = series_name.slice(0, -1); // Remove the trailing comma
                    
                    result += '<option value="' + response[i]['employee_no'] + '" ';
                    result += ' data-position="' + response[i]['position'] + '" ';
                    result += ' data-section="' + response[i]['section'] + '" ';
                    result += ' data-station="' + stations + '" ';
                    result += ' data-station_name="' + station_names + '" ';
                    result += ' data-series_name="' + series_name + '" ';
                    result += '>' + response[i]['employee_name'] + '</option> ';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }
            cboElement.html(result);
            if(empId != null){
                cboElement.val(empId).trigger('change');
            }

            if(mode == 'view'){
                cboElement.prop('disabled', true).trigger('change.select2');
            }
        },
        error: function(data, xhr, status) {
            result = '<option value="0" selected disabled> -- Reload Again -- </option>';
            cboElement.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

function loadDropdownFrMaster($el, key, { selected = null, params = {} } = {}) {
    const cfg = DROPDOWNSFROMMASTER[key];
    const setHtml = (text, value = '') =>
        $el.html($('<option>', { value, text, disabled: true, selected: true }));

    setHtml('-- Loading --');

    return $.ajax({
        method: 'get',
        url: DROPDOWN_MASTER_URL,
        data: { type: cfg.type, ...params },
        dataType: 'json',
    })
    .done(function (response) {
        if (!response.length) {
            if (cfg.disableIfEmpty) $el.prop('disabled', true);
            return setHtml('-- No record found --', 0);
        }

        if (cfg.disableIfEmpty) $el.prop('disabled', false);

        $el.empty().append($('<option>', { value: '', text: 'Select ' + cfg.label, disabled: true, selected: true }));

        response.forEach(row => {
            const $opt = $('<option>', { value: row.id, text: row.dropdown_masters_details });

            // e.g. { subclass: 'remarks' } -> data-subclass="<row.remarks>"
            $.each(cfg.dataAttrs || {}, (attr, field) => $opt.attr('data-' + attr, row[field]));

            $el.append($opt);
        });

        if (selected != null) $el.val(selected).trigger('change');

    })
    .fail(function (xhr, status, error) {
        setHtml('-- Reload Again --', 0);
        console.log('Status: ' + status + '\nError: ' + error);
    });
}

function loadDropdownFrSystemOne($el, key, { selected = null, params = {} } = {}) {
    const cfg = DROPDOWNSFROMSYSTEMONE[key];
    const setHtml = (text, value = '') =>
        $el.html($('<option>', { value, text, disabled: true, selected: true }));

    setHtml('-- Loading --');

    return $.ajax({
        method: 'get',
        url: DROPDOWN_SYSTEMONE_URL,
        data: { type: cfg.type, ...params },
        dataType: 'json',
    })
    .done(function (response) {
        if (!response.length) {
            if (cfg.disableIfEmpty) $el.prop('disabled', true);
            return setHtml('-- No record found --', 0);
        }

        if (cfg.disableIfEmpty) $el.prop('disabled', false);

        $el.empty().append($('<option>', { value: '', text: 'Select ' + cfg.label, disabled: true, selected: true }));

        response.forEach(row => {
            const $opt = $('<option>', { value: row.EmpNo, text: row.empname });

            // e.g. { subclass: 'remarks' } -> data-subclass="<row.remarks>"
            $.each(cfg.dataAttrs || {}, (attr, field) => $opt.attr('data-' + attr, row[field]));

            $el.append($opt);
        });

        if (selected != null) $el.val(selected).trigger('change');

    })
    .fail(function (xhr, status, error) {
        setHtml('-- Reload Again --', 0);
        console.log('Status: ' + status + '\nError: ' + error);
    });
}

function selectModeofDefect(cboElement, rapidxId = null){
    let result = '<option value="" disabled selected> Select Mode of Defects </option>';
    $.ajax({
        // data: { hr_only },
        method: "get",
        url: "get_mode_of_defect",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected> -- Loading -- </option>';
        },
        success: function (response) {
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Mode of Defect </option>';
                for (let i = 0; i < response.length; i++) {
                    result += '<option value="' + response[i]['id'] + '">' + response[i]['dropdown_masters_details'] + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }

            cboElement.html(result);

            if(rapidxId != null){
                cboElement.val(rapidxId).trigger('change');
            }
        },
        error: function(data, xhr, status) {
            result = '<option value="0" selected disabled> -- Reload Again -- </option>';
            cboElement.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

function selectClassification(cboElement, rapidxId = null){
    let result = '<option value="" disabled selected> Select Classification </option>';
    $.ajax({
        // data: { hr_only },
        method: "get",
        url: "get_classification",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected> -- Loading -- </option>';
        },
        success: function (response) {
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Classification </option>';
                for (let i = 0; i < response.length; i++) {
                    result += '<option value="' + response[i]['id'] + '" data-subclass="' + response[i]['remarks'] + '">' + response[i]['dropdown_masters_details'] + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }

            cboElement.html(result);

            if(rapidxId != null){
                cboElement.val(rapidxId).trigger('change');
            }
        },
        error: function(data, xhr, status) {
            result = '<option value="0" selected disabled> -- Reload Again -- </option>';
            cboElement.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

function selectDisqualiExam(cboElement, employeeNo, currentSubClass = null){
    let result = '<option value="" disabled selected> Select Disqualification Exam </option>';
    $.ajax({
        data: {employee_no: employeeNo },
        method: "get",
        url: "get_examination",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected> -- Loading -- </option>';
        },
        success: function (response) {
            if(response.length > 0){
                    // Enable the sub-classification dropdown since records are available
                    cboElement.prop('disabled', false);

                    result = '<option value="" disabled selected> Select Disqualification Exam </option>';
                for (let i = 0; i < response.length; i++) {
                    result += '<option value="' + response[i]['exam_result_details_info'][0]['id'] + '">' + response[i]['training_request_ctrl_no'] + '</option>';
                }
            }else{
                // Disable the sub-classification dropdown since no records are available
                cboElement.prop('disabled', true);
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }

            cboElement.html(result);

            if(currentSubClass != null){
                cboElement.val(currentSubClass).trigger('change');
            }
        },
        error: function(data, xhr, status) {
            result = '<option value="0" selected disabled> -- Reload Again -- </option>';
            cboElement.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

//Reusable Save Function
function saveDisqualiPart($form, url, { modal = null, table = null, resetForm = true, successMessage = 'Successfully saved!', onSuccess = null, addModal = null, addTable = null, addForm = null, addButton = null } = {}) {
    const formData = new FormData($form[0]);

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        contentType: false,   // required for file upload
        processData: false,   // required for file upload
        cache: false,
        beforeSend: function () {
            console.log('Submitting...');
        },
        success: function (response) {
            console.log('raw response:', response, typeof response, typeof response.result);
            console.log('SUCCESS fired', response);
            if (response.result === 1) {
                if (table) table.draw(false);
                if (modal) modal.modal('hide');
                if (addTable) addTable.draw(false);
                if (addModal) addModal.modal('hide');
                if (resetForm) $form[0].reset();

                showSuccess(successMessage);

                // if (typeof onSuccess === 'function'){
                    onSuccess(response, addForm, addButton);
                // }else{
                //     console.log('not trigger success');
                // }
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
            showError('Failed to save data.', xhr.responseText);
        },
    });
}

/**
 * Save (add/update) hr_memo_approval data
 */
// function saveDisqualiPart1($form, $modal, dtDisqualification) {
//     let form = $form[0];
//     let formData = new FormData(form);
//     // formData.append('trainee_details', JSON.stringify(appendArray));

//     $.ajax({
//         url: 'add_disqualification',
//         method: 'POST',
//         data: formData,
//         contentType: false,   // required for file upload
//         processData: false,   // required for file upload
//         cache: false,
//         beforeSend: function() {
//             console.log("Submitting...");
//         },
//         success: function (response) {
//             if (response.result === 1) {
//                 dtDisqualification.draw(false);
//                 $modal.modal('hide');
//                 $form[0].reset();
//                 traineeDetailsArray = [];
//                 showSuccess('Successfully saved!');
//             }
//             // else{
//             //     console.error('Save failed:', response);
//             //     showError('Failed: ' + response.error);
//             // }
//         },
//         error: function (xhr) {
//             if (xhr.status === 401) {
//                 alert('Your session has expired.');
//                 window.location.href = '../';
//                 return;
//             }
            
//             console.error('Save failed:', xhr.responseText);
//             showError('Failed to save data.', xhr.responseText);
//         }
//     });
// }

/**
 * Fetch hr_memo_approval data by ID
 */
function fetchDisqualificationById(id, $modal, dtTrainings, $formPart1, $formPart2, $mode, approval = false) {
    $.ajax({
        type: 'GET',
        url: 'get_disqualification_by_id',
        data: { id },
        dataType: 'json',
        success: function (response){
            if($mode == 'view'){
                disableForm($formPart1, response.status);
            }

            // if (response.status == 3 && approval == true){ //HR APPROVAL
            //     console.log('hide save & TU approval, show HR approval');

            //     $formPart1.find('#btnSubmitHrMemoApproval').addClass('d-none');
            //     $formPart1.find('#btnHRApprove').removeClass('d-none');
            //     $formPart1.find('#btnHRDisapprove').removeClass('d-none');
            //     $formPart1.find('#hrDisapproveRemarks').prop('disabled', false);
            //     $formPart1.find('#btnTUApprove').addClass('d-none');
            //     $formPart1.find('#btnTUDisapprove').addClass('d-none');

            //     $formPart1.find('#btnAddTrainee').prop('disabled', true);
            //     $formPart1.find('#btnAddTrainee').prop('hidden', true);
            // }else if (response.status == 5 && approval == true){ //TU Receiving
            //     console.log('hide save & HR approval, show TU approval');

            //     $formPart1.find('#btnSubmitHrMemoApproval').addClass('d-none');
            //     $formPart1.find('#btnHRApprove').addClass('d-none');
            //     $formPart1.find('#btnHRDisapprove').addClass('d-none');
            //     $formPart1.find('#btnTUApprove').removeClass('d-none');
            //     $formPart1.find('#btnTUDisapprove').removeClass('d-none');
            //     $formPart1.find('#tuDisapproveRemarks').prop('disabled', false);

            //     $formPart1.find('#btnAddTrainee').prop('disabled', true);
            //     $formPart1.find('#btnAddTrainee').prop('hidden', true);
            // }else if(response.status == 1 || response.status == 4 || response.status == 7){ //Pending, HR & TU Disapproved
            //     console.log('show save, hide ALL approval');

            //     $formPart1.find('#btnSubmitHrMemoApproval').removeClass('d-none');
            //     $formPart1.find('#btnHRApprove').addClass('d-none');
            //     $formPart1.find('#btnHRDisapprove').addClass('d-none');
            //     $formPart1.find('#btnTUApprove').addClass('d-none');
            //     $formPart1.find('#btnTUDisapprove').addClass('d-none');

            //     $formPart1.find('#btnAddTrainee').prop('disabled', false);
            //     $formPart1.find('#btnAddTrainee').prop('hidden', false);
            //     $formPart1.find('input, textarea, select').prop('disabled', false);
            // }else{
            //     console.log('hide all');
            //     $formPart1.find('#btnSubmitHrMemoApproval').addClass('d-none');
            //     $formPart1.find('#btnHRApprove').addClass('d-none');
            //     $formPart1.find('#btnHRDisapprove').addClass('d-none');
            //     $formPart1.find('#btnTUApprove').addClass('d-none');
            //     $formPart1.find('#btnTUDisapprove').addClass('d-none');

            //     $formPart1.find('#btnAddTrainee').prop('disabled', true);
            //     $formPart1.find('#btnAddTrainee').prop('hidden', true);
            // }

            // Populate modal fields
            $formPart1.find('#part1Id').val(response.id);
            $formPart1.find('#controlNo').val(response.control_no);
            $formPart1.find('#reasonForDisqualification').val(response.disqualification_reason);
            $formPart1.find('#disqualificationDate').val(response.disqualification_date);
            $formPart1.find('#recertificationDate').val(response.recertification_date);
            $formPart1.find('#deviceName').val(response.device_name);

            let toIds = [];
            response.email_recipients.forEach(function(item){
                if(item.type === 'to'){
                    toIds.push(item.rapidx_id);
                }
            });

            selectEmailRecipients($('.selectAttentionToRecipients'), toIds);
            selectEmpDetailsFromCertification($('.selectEmployeeName'), response.disqualified_personnel);

            //required for passing data to onchange functions
            currentSubClass = response.c1_subclass;
            currentStation = response.station;
            currentSeries = response.series_name;

            loadDropdownFrMaster($('.selectModeofDefect'), 'modeOfDefect', { selected: response.mode_of_defect });
            loadDropdownFrMaster($('.selectClassification'), 'classification', { selected: response.classification });

            //form Part 2
            if(response.status >= 2){
                $formPart2.find('#part1Id').val(response.id);
                loadDropdownFrSystemOne($('.selectConductedBy'), 'conductedBy');
                
                let p2_status = '';
                // let personnel_pos = response.disqualified_personnel_info.qc_slip.position_category;
                if(response.part2_info != null){
                    
                    p2_id = response.part2_info.id;
                    p2_status = response.part2_info.status;

                    $formPart2.find('#part2Id').val(p2_id);
                    loadDropdownFrSystemOne($('.selectConductedBy'), 'conductedBy', { selected: response.part2_info.conducted_by });
                    $formPart2.find('#counsellingDate').val(response.part2_info.counselling_date);
                    
                    // $('#btnSubmitDisqualificationPart2').prop('disabled', false);
                    // $('#btnSubmitDisqualificationPart2').prop('hidden', false);

                    if(p2_status > 1){ //p2 status is 1 - 4, enable add training, disable save button
                        $formPart2.find('#btnAddTraining').data('part1_id', response.id);
                        $formPart2.find('#btnAddTraining').data('id', p2_id);
                        $formPart2.find('#btnAddTraining').data('status', p2_status);
                        $('#btnAddTraining').prop('disabled', false);
                        $('#btnAddTraining').prop('hidden', false);
                        $('#btnSubmitDisqualificationPart2').prop('disabled', true);
                        $('#btnSubmitDisqualificationPart2').prop('hidden', true);

                        if(p2_status == 4){
                            $('#btnAddTraining').prop('disabled', true);
                            $('#btnAddTraining').prop('hidden', true);
                        }

                    }else{
                        $('#btnAddTraining').prop('disabled', true);
                        $('#btnAddTraining').prop('hidden', true);
                        $('#btnSubmitDisqualificationPart2').prop('disabled', false);
                        $('#btnSubmitDisqualificationPart2').prop('hidden', false);
                    }
                    
                }else{
                    p2_status = 1;
                    $('#btnAddTraining').prop('disabled', true);
                    $('#btnAddTraining').prop('hidden', true);
                }

                dtTrainings.draw();
            }

            $modal.modal('show');
        },
        error: function (xhr) {
            console.error('Fetch failed:', xhr.responseText);
            showError('Failed to fetch data.');
        }
    });
}

function disableForm($form, status = null){
    $form.find('input, textarea, select').prop('disabled', true);
}

/**
 * Disable or update disqualification status
 */
function updateHrMemoApprovalStatus(id, dtDisqualification, updateToStatus, modal = null, remarks = '') {
    $.ajax({
        type: 'POST',
        url: 'update_disqualification_status',
        data: {
            id: id,
            new_status: updateToStatus,
            remarks: remarks
        },
        dataType: 'json',
        success: function (response) {
            if(response.success == true) {
                showSuccess('Status updated successfully.');
                if(modal != null){
                    modal.modal('hide');
                }

                //temp comment when testing
                if(updateToStatus > 2){
                    // SendHrMemoMail(id, updateToStatus);
                }
                dtDisqualification.draw();
            }
        },
        error: function (xhr) {
            console.error('Status update failed:', xhr.responseText);
            showError('Failed to update status.');
        }
    });
}

function SendDisqualificationMail(hr_memo_id, updateToStatus){
	$.ajax({
		url: "send_disquali_mail",
		method: "get",
		data:
		{
			hr_memo_id: hr_memo_id,
			status: updateToStatus
		},
		dataType: "json",
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				toastr.success('Mail Sent to Recipients!');
			}else{
				toastr.error('Error Sending Mail!');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
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
function showMessage(message, title = 'Information') {
    Swal.fire({
        icon: 'info',
        title: title,
        // html: message,
        html: `
           <div style="
                border:1px solid #ccc;
                padding:15px;
                border-radius:5px;
                text-align:left;
                white-space:pre-wrap;
                max-height:500px;
                overflow-y:auto;
            ">
                ${message}
            </div>
        `,
        width: '800px', // or '60%', '70%', '1000px'
        showConfirmButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'CLOSE'
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
