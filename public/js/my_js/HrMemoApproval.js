$(document).ready(function () {
    // --------------------------------------
    // Cache DOM elements
    // --------------------------------------
    const $table = $('#tblHrMemoApproval');                                    //table for hr_memo_approval
    const $form = $('#formHrMemoApproval');                                    //form for hr_memo_approval
    const $modal = $('#modalHrMemoApproval');                                  //modal for hr_memo_approval
    const $addButtonMemo = $('#btnShowAddHrMemoApproval');      //button for adding hr_memo_approval
    const dtHMA = initHrMemoApprovalTable($table);
    const $tableTD = $('#tblTraineeDetails');
    const dtTraineeDetails = initTraineeDetailsTable($tableTD);
    const $modalTD = $('#modalTraineeDetails');
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
        $modal,
        $addButtonMemo,
        dtHMA,
        dtTraineeDetails,
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
function resetHrMemoApprovalForm(formSelector, tableImprovementActions) {
    const $formSelector = $(formSelector);
    $formSelector[0].reset();
    $formSelector.find('input[type="hidden"]').val('');

    // hide image preview
    $('#previewImage').hide();

    // clear error fields
    $('.text-danger').text('');

    // Hide Reupload Div & Exisiting Filename
    formSelector.find("#btnReuploadTriggerDiv").addClass('d-none');
    formSelector.find("#btnReuploadTrigger").addClass('d-none');
    formSelector.find("#btnReuploadTrigger").prop('checked', false);
    formSelector.find("#btnReuploadTriggerLabel").addClass('d-none');
    formSelector.find("#illustrationOfDefectFileName").addClass('d-none');

    // Show Upload Attachment section, remove required attribute
    formSelector.find("#illustrationOfDefect").removeClass('d-none');
    formSelector.find("#illustrationOfDefect").prop('required', true);

    // Hide Download Button
    formSelector.find("#downloadFile").addClass('d-none');

    // updateRemoveButtons(tableImprovementActions);
}

/**
 * Initialize DataTable
 */
function initHrMemoApprovalTable($table, url = 'view_hr_memo') {
    return $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: url },
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'status_label' },
            { data: 'document_no' },    // customize this per hr_memo_approval
            { data: 'date_filed' },    // customize this per hr_memo_approval
            { data: 'reason' },    // customize this per hr_memo_approval
            { data: 'subject' }    // customize this per hr_memo_approval
        ]
    });
}

/**
 * Initialize DataTable
 */
function initTraineeDetailsTable($table1, url1 = 'view_trainee_details') {
    return $table1.DataTable({
        ordering: false,
        searching: false,
        paging: false,
        info : false,
        columns: [
            {
                data: "action",
                render: function (data){
                    let actionButtons;
                    actionButtons = "<button class='btn btn-md btn-danger removeRow mr-1' data-id='" + data.id + "' title='Remove Row' type='button'><i class='fa fa-times'></i></button>";
                    actionButtons += "<button class='btn btn-md btn-secondary editRow' data-id='" + data.id + "' type='button'><i class='fas fa-edit'></i></button>";
                    return actionButtons;

                    // return "<td id='removeRow'>
                    //             '<center>'
                    //                 <button class="btn btn-md btn-danger removeRow" title="Remove Row" type="button">
                    //                     <i class="fa fa-times"></i>
                    //                 </button>
                    //             </center>
                    //         </td>";
                    // if(data == 1){
                    //     return "Material Kitting & Issuance";
                    // }else if(data == 2){
                    //     return "Sakidashi Issuance";
                    // }
                }
            },
            { data: "emp_no" },
            { data: "emp_name" },
            { data: "traning_venue" },
            { data: "endorsement_date" }
        ],
    });
}

// function updatePreShipmentTable($tableTD) {
//         let tbody = $tableTD.find('tbody');
//         tbody.empty(); // clear VIEW

//         // ✅ NO RECORDS
//         if (Object.keys(traineeDetailsList).length === 0) {
//             tbody.append(`
//                 <tr class="no-record">
//                     <td colspan="9" class="text-center text-muted">
//                         No records added
//                     </td>
//                 </tr>
//             `);
//             return;
//         }

//         // ✅ HAS DATA
//         $.each(traineeDetailsList, function (id, item) {
//             console.log('traineeDetailsList', traineeDetailsList);

//             // <td>${item.master_carton_no}</td>
//             // <td>${item.item_no}</td>
//             // <td>${item.po_no}</td>
//             // <td>${item.parts_code}</td>
//             // <td>${item.device_name}</td>
//             // <td>${item.lot_no}</td>
//             // <td>${item.qty}</td>
//             // <td>${item.package_category}</td>
//             // <td>${item.package_qty}</td>
//             // <td>${item.remarks ?? ''}</td>

//             tbody.append(`
//                 <tr data-id="${id}" class="text-center">
//                     <td>
//                         <button class="btn btn-danger btn-sm btn-remove">
//                             <i class="fa fa-times"></i>
//                         </button>
//                     </td>
//                     <td style="width: 10%;">Employee No</td>
//                     <td style="width: 10%;">Name</td>
//                     <td style="width: 10%;">Date Hired</td>
//                     <td style="width: 10%;">Position/Dept./Section</td>
//                     <td style="width: 10%;">Training Venue</td>
//                     <td style="width: 10%;">Endorsement Date</td>
//                     <td style="width: 10%;">Department</td>
//                     <td style="width: 10%;">Section</td>
//                 </tr>
//             `);
//         });
//     }

/**
 * Bind events for buttons, forms, etc.
 */
function bindEvents($table, $form, $modal, $addButtonMemo, dtHMA, dtTraineeDetails, $tableTD, $modalTD, $formTD, $addButtonTD, $addButtonExam, $tableExam){
    let traineeDetailsArray = [];
    let traineeDetailsList = {};
    let traineeIdCounter = 1;

    // initial check (on page load)
    // updateRemoveButtons($tableTD);

    $addButtonMemo.on('click', function () {
        resetHrMemoApprovalForm($form, $tableTD);
        traineeDetailsList = {};
        traineeIdCounter = 1; //set counter to 1 every new memo

        // updatePreShipmentTable($tableTD);
        // getSituations($('#selectSituation'));
        // getPic($('#tblTraineeDetails #selectPic'));
        // getPic($('#tblTraineeDetails tr:last').find('.selectPic'));
        // $('#modalHrMemoApproval').modal('show');
        $modal.modal('show');
    });

    $addButtonTD.on('click', function (){

        $formTD[0].reset();
        $formTD.find('input[type="hidden"]').val('');

        // Remove all rows except template row
        $($tableExam).find('tbody tr:not(.data-row)').remove();

        // Clear template row inputs
        $($tableExam).find('.data-row input').val('');

        // <select class="form-control form-control-sm select2bs5 selectTitle" name="title[]" required></select>
        // <select class="form-control form-control-sm select2bs5 selectResult" name="result[]" required></select>
        // Remove any additional rows except the default one
        const defaultExamRow = `
            <tr class="data-row" data-checkbox-id=''>
                <td>
                    <select class="form-control form-control-sm select2bs5 selectExamTitle" name="title[]" required></select>
                </td>
                <td>
                    <select class="form-control form-control-md" name="result[]" id="result" required>
                        <option value="" disabled selected>Select Result</option>
                        <option value="1">Passed</option>
                        <option value="2">Failed</option>
                        <option value="3">Complied</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-md" name="remarks[]" id="remarks" required></input>
                </td>
                <td id="removeRow">
                    <center>
                        <button class="btn btn-md btn-danger removeRow" title="Remove Row" type="button" disabled>
                            <i class="fa fa-times"></i>
                        </button>
                    </center>
                </td>
            </tr>
        `;

        const $tbody = $($tableExam).find('tbody');
        $tbody.html(defaultExamRow);

        getExaminations($('#tblExamination tr:last').find('.selectExamTitle'));
        selectEmpNo($('.selectEmpNo'));
        $modalTD.modal('show');
    });

    // Handle employee number input
    $('#employeeNumber').on('change', function(){
        let empNo = $(this).find('option:selected').text();
        $.ajax({
            type: "GET",
            url: "get_employee_details",
            data: { employee_number: empNo },
            dataType: "json",
            success: function (response) {
                emp_details = response[0];
                console.log('emp_details', emp_details);

                $formTD.find('#employeeName').val(emp_details.EmpName);
                $formTD.find('#dateHired').val(emp_details.DateHired);
                $formTD.find('#position').val(emp_details.Position);
                $formTD.find('#trainingVenue').val(emp_details.Venue);
                // $formTD.find('#endorsementDate').val(emp_details.emp_name);
                $formTD.find('#department').val(emp_details.Department);
                $formTD.find('#prodAllocation').val(emp_details.Section);
            }
        });
    });

    $addButtonExam.on('click', function () {
        const $templateRow = $tableExam.find('.data-row').first();

        // Clone WITHOUT events & select2 bindings
        let newRow = $templateRow.clone(false, false);

        // Clear inputs
        newRow.find('input, select, textarea').val('');
        newRow.find('.removeRow').prop('disabled', false);

        // 🔥 Remove select2 generated container inside cloned row
        newRow.find('.select2-container').remove();

        let $newSelectExamTitle = newRow.find('.selectExamTitle');
        // let $newSelectResult = newRow.find('.selectResult');

        // 🔥 Clean select2 plugin traces from cloned select
        $newSelectExamTitle
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id')
            .removeAttr('tabindex')
            .removeAttr('aria-hidden')
            .empty()        // ← THIS clears options
            .val(null);

        // 🔥 Clean select2 plugin traces from cloned select
        // $newSelectResult
        //     .removeClass('select2-hidden-accessible')
        //     .removeAttr('data-select2-id')
        //     .removeAttr('tabindex')
        //     .removeAttr('aria-hidden')
        //     .empty()        // ← THIS clears options
        //     .val(null);

        // Append new row first
        $tableExam.find('tbody').append(newRow);

        // 🔥 Reinitialize select2 ONLY for the new row
        $newSelectExamTitle.select2({
            theme: 'bootstrap-5',
        });

        // $newSelectResult.select2({
        //     theme: 'bootstrap-5',
        // });

        // Update button states
        updateRemoveButtons($tableExam, 'Table');
        getExaminations($newSelectExamTitle);
    });

    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();
        saveHrMemoApproval($form, $modal, dtHMA);
    });

    // Edit button
    $table.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        fetchHrMemoApprovalById(id, $modal, $tableTD, $form, 'edit');
    });

    // View button
    $table.on('click', '.btnView', function () {
        const id = $(this).data('id');
        fetchHrMemoApprovalById(id, $modal, $tableTD, $form, 'view');
    });

    // Disable button
    $table.on('click', '.btnDisable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to disable this?', function () {
            updateHrMemoApprovalStatus(id, dtHMA);
        });
    });

    // Enable button
    $table.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to enable this?', function () {
            updateHrMemoApprovalStatus(id, dtHMA);
        });
    });

    // --------------------
    // REMOVE ROW
    // --------------------
    $tableExam.on('click', '.removeRow', function(){
        $(this).closest('tr').remove();
        updateRemoveButtons($tableExam, 'Table');
    });

    $tableTD.on('click', '.removeRow', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        traineeDetailsArray = traineeDetailsArray.filter(item => item.action.id != id);
        dtTraineeDetails.clear().rows.add(traineeDetailsArray).draw();
        updateRemoveButtons($tableTD, 'Array', traineeDetailsArray);
        console.log('Updated traineeDetailsArray after removal', traineeDetailsArray);
    });

    $tableTD.on('click', '.editRow', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let trainee = traineeDetailsArray.find(item => item.action.id == id);
        console.log('traineeDetailsArray', trainee);
        $modalTD.modal('show');
    });

    // Submit formTraineeDetails
    $('#btnAddTraineeDetailsToList').on('click', function (e) {
        e.preventDefault();
        let empId = $formTD.find('#employeeNumber').val();
        let endorsementDate = $formTD.find('#endorsementDate').val();

        if(empId == null){
            showError('Please select an employee.');
            return;
        }

        if(endorsementDate == ''){
            showError('Please fill up the Endorsement date.');
            return;
        }

        let empType = $formTD.find('#employeeNumber').find('option:selected').data('emp_type');
        let empNumber = $formTD.find('#employeeNumber').find('option:selected').text();
        let empName = $formTD.find('#employeeName').val();
        let trainingVenue = $formTD.find('#trainingVenue').val();
        let exam_list = [];
        let hasError = false;

        $('.data-row').each(function (){
            let exam_title = $(this).find('.selectExamTitle').val();
            let result = $(this).find('#result').val();
            let remarks = $(this).find('#remarks').val();

            if(exam_title == null || result == null || remarks == ''){
                showError('Please fill in all examination details.');
                hasError = true;
                return false; // stops the .each loop
            }

            exam_list.push({exam_title, result, remarks});
        });

        if(hasError){
            return; // stop the rest of the function
        }

        let counterNow = traineeIdCounter++;

        let traineeDetailsList = {
            action: {id: counterNow, emp_id: empId, emp_type: empType},
            emp_no: empNumber,
            emp_name : empName,
            traning_venue: trainingVenue,
            endorsement_date: endorsementDate,
            exam_detals: exam_list
        }

        traineeDetailsArray.push(traineeDetailsList);
        dtTraineeDetails.clear().draw();
        dtTraineeDetails.rows.add(traineeDetailsArray).draw();
        showSuccess('Trainee details added to the list.');
        $modalTD.modal('hide');
    });
}

function updateRemoveButtons($table, measure_type, array = null){
    let length = 0;
    if(measure_type == 'Table'){
        length = $table.find('.data-row').length;
    }else{
        // measure_type = 'Array';
            length = array.length;
    }

    if (length == 1){
        $table.find('.removeRow').prop('disabled', true);
    } else {
        $table.find('.removeRow').prop('disabled', false);
    }

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
            console.log(response);

            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Employee No </option>';

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
                    result += '<option value="' + response[di]['id'] + '">' + response[di]['examination_name'] + '</option>';
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

function getDeviceName(cboElement, section, deviceName = null, mode = null){
    let result = '<option value="" disabled selected> Select Series Name </option>';
    $.ajax({
        method: "get",
        url: "get_device_name",
        data: { section },
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected>--Loading--</option>';
        },
        success: function (response) {
            console.log('response', response);
            $('#selectDeviceName').prop('disabled', false);
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Series Name </option>';

                if(mode == 'Export'){
                    result += '<option value="ALL"> ALL </option>';
                }

                for (let dni = 0; dni < response.length; dni++) {
                    result += '<option value="' + response[dni]['materials'] + '">' + response[dni]['materials'] + '</option>';
                }
            }else{
                // result = '<option value="0" selected disabled> -- No record found -- </option>';
                result = '<option value="" disabled selected>--Loading--</option>';
            }
            cboElement.html(result);
            if(deviceName != null){
                cboElement.val(deviceName).trigger('change');
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

function getSituations(cboElement, situationId = null, mode = null){
    let result = '<option value="" disabled selected> Select Situation </option>';
    $.ajax({
        method: "get",
        url: "get_situations",
        // data: { section },
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected>--Loading--</option>';
        },
        success: function (response) {
            if(response.length > 0){
                    result = '<option value="" disabled selected> Select Situation </option>';

                if(mode == 'Export'){
                    result += '<option value="ALL"> ALL </option>';
                }

                for (let si = 0; si < response.length; si++){
                    result += '<option value="' + response[si]['id'] + '">' + response[si]['situation_name'] + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }
            cboElement.html(result);
            if(situationId != null){
                cboElement.val(situationId).trigger('change');
            }

            if(mode === 'view'){
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

function getPic(cboElement, picId = null, $mode = null){
    let result = '<option value="" disabled selected> Select Person-In Charge </option>';
    $.ajax({
        method: "get",
        url: "get_users",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="" disabled selected>--Loading--</option>';
        },
        success: function (response) {
            let users = response.users_data;
            if(users.length > 0){
                    result = '<option value="" disabled selected> Select Person-In Charge </option>';

                for (let ui = 0; ui < users.length; ui++) {
                    let id = users[ui]['id'];
                    let name = users[ui]['name'];

                    result += '<option value="'+id+'">' + name + '</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }
            cboElement.html(result);

            if (picId != null) {
                cboElement.val(picId).trigger('change');
            }

            if($mode === 'view'){
                cboElement.prop('disabled', true).trigger('change.select2');
            }
        },
        error: function(data, xhr, status){
            result = '<option value="0" selected disabled> -- Reload Again -- </option>';
            cboElement.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

/**
 * Save (add/update) hr_memo_approval data
 */
function saveHrMemoApproval($form, $modal, dtHrMemoApproval) {
    let form = $form[0];
    let formData = new FormData(form);

    $.ajax({
        url: 'add_hr_memo',
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
                dtHrMemoApproval.draw(false);
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
 * Fetch hr_memo_approval data by ID
 */
function fetchHrMemoApprovalById(id, $modal, $tableTD, $form, $mode) {
    $.ajax({
        type: 'GET',
        url: 'get_hr_memo_by_id',
        data: { id },
        dataType: 'json',
        success: function (response){
            if($mode == 'view'){
                disableForm($form);
            }

            // Show Reupload Div & Exisiting Filename
            $form.find("#btnReuploadTriggerDiv").removeClass('d-none');
            $form.find("#btnReuploadTrigger").removeClass('d-none');
            $form.find("#btnReuploadTrigger").prop('checked', false);
            $form.find("#btnReuploadTriggerLabel").removeClass('d-none');
            $form.find("#illustrationOfDefectFileName").removeClass('d-none');

            // Hide Upload Attachment section, remove required attribute
            $form.find("#illustrationOfDefect").addClass('d-none');
            $form.find("#illustrationOfDefect").removeAttr('required');

            // Populate modal fields (adjust names per hr_memo_approval)
            $form.find('#txtHrMemoApprovalId').val(response.id);
            $form.find('#situation').val(response.situation);
            $form.find('#section').val(response.section);
            $form.find('#dateEncountered').val(response.date_encountered);
            $form.find('#illustrationOfDefectFileName').val(response.defects.illustration_of_defect);

            let download_file ='<a href="download_file/'+response.id+'" target="_blank">';
                download_file +='<button type="button" class="btn btn-primary btn-sm d-none" name="download_file" id="downloadFile">';
                download_file +=     '<i class="fa-solid fa-file-arrow-down"></i>';
                download_file +=         '&nbsp;';
                download_file +=         'See Attachment';
                download_file +='</button>';
                download_file +='</a>';

            $form.find('#attachmentDiv').append(download_file);

            // Show Download Button
            $form.find("#downloadFile").removeClass('d-none');

            // getDeviceName($('#selectDeviceName'), response.section, response.model, $mode);
            // getDefects($('#defectId'), response.defects.defect_id, $mode);
            // getSituations($('#selectSituation'), response.situation, $mode);

            $form.find('#noOfOccurrence').val(response.defects.no_of_occurrence);
            $form.find('#rootCause').val(response.defects.root_cause);

            $tableTD.find('tbody').empty();
            for(let index = 0; index < response.improvements.length; index++){

                let rowImprovements = `
                    <tr class="data-row">
                        <td id="removeRow">
                            <center><button ${$mode === 'view' ? 'disabled' : ''} class="btn btn-md btn-danger removeRow" title="Remove Row" type="button"><i class="fa fa-times"></i></button></center>
                        </td>
                        <td>
                            <textarea ${$mode === 'view' ? 'disabled' : ''} class="form-control form-control-sm" name="factor[]">${response.improvements[index].factor}</textarea>
                        </td>
                        <td>
                            <textarea ${$mode === 'view' ? 'disabled' : ''} class="form-control form-control-sm" name="cause[]">${response.improvements[index].cause}</textarea>
                        </td>
                        <td>
                            <textarea ${$mode === 'view' ? 'disabled' : ''} class="form-control form-control-sm" name="analysis[]">${response.improvements[index].analysis}</textarea>
                        </td>
                        <td>
                            <textarea ${$mode === 'view' ? 'disabled' : ''} class="form-control form-control-sm" name="counter_measure[]">${response.improvements[index].counter_measure}</textarea>
                        </td>
                        <td>
                            <select ${$mode === 'view' ? 'disabled' : ''} class="form-control form-control-lg select2bs5 selectPic" name="pic[]"></select>
                        </td>
                        <td>
                            <input ${$mode === 'view' ? 'disabled' : ''} type="date" class="form-control form-control-lg" name="implementation_date[]" value="${response.improvements[index].implementation_date}">
                        </td>
                    </tr>
                `;

                $tableTD.find('tbody').append(rowImprovements);
                getPic($('#tblTraineeDetails tr:last').find('.selectPic'), response.improvements[index].pic, $mode);
            }

            $modal.modal('show');
        },
        error: function (xhr) {
            console.error('Fetch failed:', xhr.responseText);
            showError('Failed to fetch data.');
        }
    });
}

function disableForm($form){
    $form.find('#btnAddTrainee').prop('disabled', true);
    $form.find('#btnAddTrainee').prop('hidden', true);
    $form.find('#btnSubmitHrMemoApproval').prop('disabled', true);
    $form.find('#btnSubmitHrMemoApproval').prop('hidden', true);
    $form.find('input, textarea, select').prop('disabled', true);
    $form.find('#btnReuploadTrigger').prop('disabled', true);
    $form.find('#btnReuploadTrigger').prop('checked', false);
}

/**
 * Disable or update hr_memo_approval status
 */
function updateHrMemoApprovalStatus(id, dtHMA) {
    $.ajax({
        type: 'POST',
        url: 'update_hr_memo_status',
        data: { id },
        dataType: 'json',
        success: function (response) {
            if(response.success == true) {
                showSuccess('Status updated successfully.');
                dtHMA.draw();
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
