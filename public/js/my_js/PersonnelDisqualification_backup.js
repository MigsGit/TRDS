$(document).ready(function () {
    // --------------------------------------
    // Cache DOM elements
    // --------------------------------------
    const $table = $('#tblDisqualification');                         //table for hr_memo_approval
    const $form = $('#formHrMemoApproval');                           //form for hr_memo_approval
    const $formPart1 = $('#formDisqualiPart1');                           //form for hr_memo_approval
    const $modal = $('#modalDisqualification');                       //modal for hr_memo_approval
    const $addDisqualiButton = $('#btnShowAddDisqualification');      //button for adding hr_memo_approval
    const dtDisqualification = initDataTable($table);
    const $tableTD = $('#tblTraineeDetails');
    const $modalTD = $('#modalDisqualification');
    const $formTD = $('#formTraineeDetails');
    const $addButtonTD = $('#btnAddTrainee');
    const $addButtonExam = $('#btnAddExamination');
    const $tableExam = $('#tblExamination');

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
                dropdownParent: $(this).closest('.modal') // Ensures correct parent modal
            });
        });
    });

    // --------------------------------------
    // Bind all event handlers
    // --------------------------------------
    bindEvents($table,
        $form,
        $formPart1,
        $modal,
        $addDisqualiButton,
        dtDisqualification,
        $tableTD,
        $modalTD,
        $formTD,
        $addButtonTD,
        $addButtonExam,
        $tableExam
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
            // { data: 'trainee_names', visible: false, searchable: true},
            { data: 'status_label' },
            { data: 'control_no' },    // customize this per hr_memo_approval
            { data: 'disqualification_date' },    // customize this per hr_memo_approval
            { data: 'disqualification_reason' },    // customize this per hr_memo_approval
            // { data: 'subject' },    // customize this per hr_memo_approval
            // { data: 'prepared_by_label' },    // customize this per hr_memo_approval
            // { data: 'received_by_label' }    // customize this per hr_memo_approval
        ]
    });
}

/**
 * Bind events for buttons, forms, etc.
 */
function bindEvents($table, $form, $formPart1, $modal, $addDisqualiButton, dtDisqualification, $formTD){
    let traineeDetailsArray = [];
    let traineeIdCounter = 1;

    $addDisqualiButton.on('click', function () {
        console.log('Add Disqualification button clicked');
        traineeDetailsArray = [];
        // resetPart1Form($form, dtDisqualification);
        traineeIdCounter = 1; //set counter to 1 every new memo

        // $addButtonTD.data('counter', traineeIdCounter)

        selectEmailRecipients($('.selectAttentionToRecipients'));
        $modal.modal('show');

        selectEmpDetailsFromCertification($('.selectEmployeeName'));
        selectModeofDefect($('.selectModeofDefect'));
        selectClassification($('.selectClassification'));
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
    });

    // Handle employee number input
    $('#classification').on('change', function(){
        let sub_class = $(this).find('option:selected').data('subclass');
        selectSubClassification($('.selectSubClassification'), sub_class);
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
        
        // console.log({
        //     'isModSelected': isModSelected,
        //     'isSeriesSelected': isSeriesSelected,
        //     'isClassificationSelected': isClassificationSelected,
        //     'isSubClassRequired': isSubClassRequired,
        //     'isSubClassificationSelected': isSubClassificationSelected
        // });

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
    $formPart1.on('submit', function (e) {
        e.preventDefault();
        saveDisquliPart1($formPart1, $modal, dtDisqualification);
    });

    // Edit button
    $table.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        traineeDetailsArray = [];
        fetchDisqualificationById(id, $modal, dtDisqualification, $formPart1, 'edit');
    });

    // View button
    $table.on('click', '.btnView', function () {
        const id = $(this).data('id');
        const approval = $(this).data('approval');
        traineeDetailsArray = [];
        fetchDisqualificationById(id, $modal, dtDisqualification, $formPart1, 'view', approval);
    });

    // View Disapproval Remarks button
    $table.on('click', '.btnViewRemarks', function () {
        const remarks = $(this).data('remarks');
        showMessage(remarks, 'Disapproval Remarks', function(){});
    });

    // Enable button
    $table.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        let updateStatusTo = 1; //pending
        confirmAction('Are you sure you want to enable this?', function () {
            updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo);
        });
    });

    // Disable button
    $table.on('click', '.btnDisable', function () {
        const id = $(this).data('id');
        let updateStatusTo = 2; //cancelled
        confirmAction('Are you sure you want to disable this?', function () {
            updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo);
        });
    });

    // Final Submit button
    $table.on('click', '.btnFinalSubmit', function () {
        const id = $(this).data('id');
        let updateStatusTo = 3; //for approval
        confirmAction('Are you sure you want to submit this?', function () {
            updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo);
        });
    });

    // Disapprove button
    $form.on('click', '#btnHRDisapprove', function () {
        const id = $form.find('#txtHrMemoId').val();
        let updateStatusTo = 4; //disapproved

        Swal.fire({
            title: 'Disapprove HR Memo',
            input: 'textarea',
            id: 'hrDisapproveRemarks',
            inputLabel: 'Remarks',
            inputPlaceholder: 'Enter reason for disapproval...',
            inputAttributes: {
                'aria-label': 'Enter remarks'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Remarks is required!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let remarks = result.value;
                updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo, $modal, remarks);
            }
        });
    });

    // HR Approve button
    $form.on('click', '#btnHRApprove', function () {
        const id = $form.find('#txtHrMemoId').val();
        let updateStatusTo = 5; //approved
        // let forApproval = true;
        confirmAction('Approve HR Memo Document?', function () {
            updateHrMemoApprovalStatus(id, dtDisqualification, updateStatusTo, $modal);
        });
    });
}

function getNextId(traineeDetailsArray) {
    if (traineeDetailsArray.length === 0) return 1;
    return Math.max(...traineeDetailsArray.map(x => x.action.id || 0)) + 1;
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
                    
                    // console.log('stations', stations);
                    // console.log('station_names', station_names);
                    
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

function selectSubClassification(cboElement, subClass, currentSubClass = null){
    let result = '<option value="" disabled selected> Select Sub-Classification </option>';
    $.ajax({
        data: {sub_class: subClass },
        method: "get",
        url: "get_sub_classification",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected> -- Loading -- </option>';
        },
        success: function (response) {
            if(response.length > 0){
                    // Enable the sub-classification dropdown since records are available
                    cboElement.prop('disabled', false);

                    result = '<option value="" disabled selected> Select Sub-Classification </option>';
                for (let i = 0; i < response.length; i++) {
                    result += '<option value="' + response[i]['id'] + '">' + response[i]['dropdown_masters_details'] + '</option>';
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

function selectEmpNo(cboElement, empId = null, mode = null){
    let result = '<option value="" disabled selected> Select One </option>';
    $.ajax({
        method: "get",
        url: "get_emp_no_dropdown_details",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected>--Loading--</option>';
        },
        success: function (response) {
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Employee Name </option>';

                for (let di = 0; di < response.length; di++) {
                    result += '<option value="' + response[di]['pkid'] + '" data-emp_type="' + response[di]['emp_type'] + '">' + response[di]['EmpNo'] + '</option>';
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

function selectTrainingVenue(cboElement, trainingId = null, mode = null){
    let result = '<option value="" disabled selected> Select One </option>';
    $.ajax({
        method: "get",
        url: "get_training_venue_dropdown_details",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected>--Loading--</option>';
        },
        success: function (response) {
            let venue = response.training_venue;
            if(venue.length > 0){
                    result = '<option value="" disabled selected> Select Training Venue </option>';

                for (let i = 0; i < venue.length; i++) {
                    result += '<option value="' + venue[i].Venue + '">' + venue[i].Venue + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }

            cboElement.html(result);
            if(trainingId != null){
                cboElement.val(trainingId).trigger('change');
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

function getExaminations(cboElement, examId = null, mode = null){
    let result = '<option value="" disabled selected> Select Examination </option>';
    $.ajax({
        method: "get",
        url: "get_examinations",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected>--Loading--</option>';
        },
        success: function (response) {
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Examination </option>';

                if(mode == 'Export'){
                    result += '<option value="ALL"> ALL </option>';
                }

                for (let di = 0; di < response.length; di++) {
                    result += '<option value="' + response[di]['id'] + '" data-objective="' + response[di]['objective'] + '">' + response[di]['examination_name'] + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }
            cboElement.html(result);

            if(examId != null){
                cboElement.val(examId).trigger('change');
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

/**
 * Save (add/update) hr_memo_approval data
 */
function saveDisquliPart1($form, $modal, dtDisqualification) {
    let form = $form[0];
    let formData = new FormData(form);
    // formData.append('trainee_details', JSON.stringify(appendArray));

    $.ajax({
        url: 'add_disqualification',
        method: 'POST',
        data: formData,
        contentType: false,   // required for file upload
        processData: false,   // required for file upload
        cache: false,
        beforeSend: function() {
            console.log("Submitting...");
        },
        success: function (response) {
            if (response.result === 1) {
                dtDisqualification.draw(false);
                $modal.modal('hide');
                $form[0].reset();
                traineeDetailsArray = [];
                showSuccess('Successfully saved!');
            }
            // else{
            //     console.error('Save failed:', response);
            //     showError('Failed: ' + response.error);
            // }
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                alert('Your session has expired.');
                window.location.href = '../';
                return;
            }
            
            console.error('Save failed:', xhr.responseText);
            showError('Failed to save data.', xhr.responseText);
        }
    });
}

/**
 * Fetch hr_memo_approval data by ID
 */
function fetchDisqualificationById(id, $modal, $table, $form, $mode, $traineeDetailsArray, approval = false) {
    $.ajax({
        type: 'GET',
        url: 'get_hr_memo_by_id',
        data: { id },
        dataType: 'json',
        success: function (response){
            if($mode == 'view'){
                disableForm($form, response.status);
            }

            if (response.status == 3 && approval == true){ //HR APPROVAL
                console.log('hide save & TU approval, show HR approval');

                $form.find('#btnSubmitHrMemoApproval').addClass('d-none');
                $form.find('#btnHRApprove').removeClass('d-none');
                $form.find('#btnHRDisapprove').removeClass('d-none');
                $form.find('#hrDisapproveRemarks').prop('disabled', false);
                $form.find('#btnTUApprove').addClass('d-none');
                $form.find('#btnTUDisapprove').addClass('d-none');

                $form.find('#btnAddTrainee').prop('disabled', true);
                $form.find('#btnAddTrainee').prop('hidden', true);
            }else if (response.status == 5 && approval == true){ //TU Receiving
                console.log('hide save & HR approval, show TU approval');

                $form.find('#btnSubmitHrMemoApproval').addClass('d-none');
                $form.find('#btnHRApprove').addClass('d-none');
                $form.find('#btnHRDisapprove').addClass('d-none');
                $form.find('#btnTUApprove').removeClass('d-none');
                $form.find('#btnTUDisapprove').removeClass('d-none');
                $form.find('#tuDisapproveRemarks').prop('disabled', false);

                $form.find('#btnAddTrainee').prop('disabled', true);
                $form.find('#btnAddTrainee').prop('hidden', true);
            }else if(response.status == 1 || response.status == 4 || response.status == 7){ //Pending, HR & TU Disapproved
                console.log('show save, hide ALL approval');

                $form.find('#btnSubmitHrMemoApproval').removeClass('d-none');
                $form.find('#btnHRApprove').addClass('d-none');
                $form.find('#btnHRDisapprove').addClass('d-none');
                $form.find('#btnTUApprove').addClass('d-none');
                $form.find('#btnTUDisapprove').addClass('d-none');

                $form.find('#btnAddTrainee').prop('disabled', false);
                $form.find('#btnAddTrainee').prop('hidden', false);
                $form.find('input, textarea, select').prop('disabled', false);
            }else{
                console.log('hide all');
                $form.find('#btnSubmitHrMemoApproval').addClass('d-none');
                $form.find('#btnHRApprove').addClass('d-none');
                $form.find('#btnHRDisapprove').addClass('d-none');
                $form.find('#btnTUApprove').addClass('d-none');
                $form.find('#btnTUDisapprove').addClass('d-none');

                $form.find('#btnAddTrainee').prop('disabled', true);
                $form.find('#btnAddTrainee').prop('hidden', true);
            }

            // Populate modal fields
            $form.find('#txtHrMemoId').val(response.id);
            $form.find('#documentNo').val(response.document_no);
            $form.find('#subject').val(response.subject);
            $form.find('#reason').val(response.reason);
            $form.find('#classification').val(response.classification);
            $form.find('#dateFiled').val(response.date_filed);
            $form.find('#preparedById').val(response.prepared_by);
            $form.find('#preparedByName').val(response.prepared_by_info.name);

            let toIds = [];
            let ccIds = [];

            response.email_recipients.forEach(function(item){
                if(item.type === 'to'){
                    toIds.push(item.user_id);
                }

                if(item.type === 'cc'){
                    ccIds.push(item.user_id);
                }
            });

            // selectEmailRecipients($('.selectToRecipients'), toIds);
            // selectEmailRecipients($('.selectCcRecipients'), ccIds);
            // selectEmailRecipients($('.selectNotedBy'), response.noted_by, true);

            traineeIdCounter = 1; //set counter to 1 every new memo

            response.trainee_details.forEach(function(item){
                let exam_list = [];
                let counterNow = traineeIdCounter++;

                if(item.employment_type == 1){ //HRIS
                    empName = item.hris_emp_info.EmpName;
                    position = item.hris_emp_info.Position;
                    department = item.hris_emp_info.Department;
                    section = item.hris_emp_info.Section;
                }else if(item.employment_type == 2){ //SUBCON
                    empName = item.subcon_emp_info.EmpName;
                    position = item.subcon_emp_info.Position;
                    department = item.subcon_emp_info.Department;
                    section = item.subcon_emp_info.Section;
                }

                item.emp_exam_details.forEach(function(exam_item){
                    let exam_title = exam_item.category;
                    let objective = exam_item.objective;
                    let result = exam_item.result;
                    let remarks = exam_item.training_remarks;

                    exam_list.push({exam_title, objective, result, remarks});
                });

                let traineeDetailsList = {
                    action: {id: counterNow, emp_id: item.hris_id, emp_type: item.employment_type, status: response.status},
                    emp_no: item.employee_no,
                    emp_name: empName,
                    position: position,
                    department: department,
                    section: section,
                    endorsement_date: item.endorsement_date,
                    training_venue: item.emp_exam_details[0].training_venue,
                    trainor: item.emp_exam_details[0].trainor,
                    date_start: item.emp_exam_details[0].date_start,
                    date_end: item.emp_exam_details[0].date_end,
                    type_of_training: item.emp_exam_details[0].type_of_training,
                    exam_details: exam_list
                }

                $traineeDetailsArray.push(traineeDetailsList);
            });

            $form.find('#btnAddTrainee').data('counter', traineeIdCounter);
            $table.clear().draw();
            $table.rows.add($traineeDetailsArray).draw();
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
 * Disable or update hr_memo_approval status
 */
function updateHrMemoApprovalStatus(id, dtDisqualification, updateToStatus, modal = null, remarks = '') {
    $.ajax({
        type: 'POST',
        url: 'update_hr_memo_status',
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
                    SendHrMemoMail(id, updateToStatus);
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

function SendHrMemoMail(hr_memo_id, updateToStatus){
	$.ajax({
		url: "send_hr_memo_mail",
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
