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
function resetHrMemoApprovalForm(formSelector, dtTraineeDetails) {
    const $formSelector = $(formSelector);
    $formSelector[0].reset();
    $formSelector.find('input[type="hidden"]').val('');

    $formSelector.find('#btnSubmitHrMemoApproval').removeClass('d-none');
    $formSelector.find('#btnApprove').addClass('d-none');
    $formSelector.find('#btnDisapprove').addClass('d-none')

    dtTraineeDetails.clear().draw();
    // updateRemoveButtons(tableImprovementActions);
}

/**
 * Initialize DataTable
 */
function initHrMemoApprovalTable($table, url = 'view_hr_memo') {
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
            { data: 'document_no' },    // customize this per hr_memo_approval
            { data: 'date_filed' },    // customize this per hr_memo_approval
            { data: 'reason_label' },    // customize this per hr_memo_approval
            { data: 'subject' }    // customize this per hr_memo_approval
        ]
    });
}

/**
 * Initialize DataTable
 */
function initTraineeDetailsTable($table1) {
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

                    if(data.status == 1 || data.status == 4 || data.status == 7){ //Add/Edit
                        actionButtons = "<button class='btn btn-md btn-danger removeTDRow mr-1' data-id='" + data.id + "' title='Remove Row' type='button'><i class='fa fa-times'></i></button>";
                        actionButtons += "<button class='btn btn-md btn-secondary editTDRow' data-id='" + data.id + "' type='button'><i class='fas fa-edit'></i></button>";
                    }else{ //View
                        actionButtons = "<button class='btn btn-md btn-secondary viewTDRow' data-id='" + data.id + "' type='button'><i class='fa-solid fas fa-eye'></i></button>";
                    }

                    return actionButtons;
                }
            },
            { data: "emp_no" },
            { data: "emp_name" },
            { data: "position" },
            { data: "department" },
            { data: "section" },
            { data: "training_venue" },
            { data: "endorsement_date" }
        ],
    });
}

/**
 * Bind events for buttons, forms, etc.
 */
function bindEvents($table, $form, $modal, $addButtonMemo, dtHMA, dtTraineeDetails, $tableTD, $modalTD, $formTD, $addButtonTD, $addButtonExam, $tableExam){
    let traineeDetailsArray = [];
    let traineeIdCounter = 1;

    // initial check (on page load)
    // updateRemoveButtons($tableTD);

    $addButtonMemo.on('click', function () {
        traineeDetailsArray = [];
        resetHrMemoApprovalForm($form, dtTraineeDetails);
        traineeIdCounter = 1; //set counter to 1 every new memo

        $addButtonTD.data('counter', traineeIdCounter)
        // $addButtonTD.data('counter', null);

        selectEmailRecipients($('.selectToRecipients'));
        selectEmailRecipients($('.selectCcRecipients'));
        selectEmailRecipients($('.selectNotedBy'), '', true);
        $modal.modal('show');
    });

    // add trainee details button
    $addButtonTD.on('click', function (){
        $formTD[0].reset();
        $formTD.find('input[type="hidden"]').val('');
        // $addButtonTD.data('counter', traineeIdCounter)
        $addButtonTD.data('counter', null);
        console.log('counterNow', $addButtonTD.data('counter'));

        // Remove all rows except template row
        $($tableExam).find('tbody tr:not(.data-row)').remove();

        // Clear template row inputs
        $($tableExam).find('.data-row input').val('');

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
                <td id="removeExamRow">
                    <center>
                        <button class="btn btn-md btn-danger removeExamRow" title="Remove Row" type="button" disabled>
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
                emp_details = response['emp_details'][0];
                training_venue = response['training_venue'];
                console.log('emp_details',emp_details);
                console.log('training_venue',training_venue);

                $formTD.find('#employeeName').val(emp_details.EmpName);
                $formTD.find('#dateHired').val(emp_details.DateHired);
                $formTD.find('#position').val(emp_details.Position);
                // $formTD.find('#trainingVenue').val(training_venue);
                $formTD.find('#department').val(emp_details.Department);
                $formTD.find('#prodAllocation').val(emp_details.Section);

                if(training_venue.length > 0){
                    result = '<option value="" disabled selected> Select Training Venue </option>';

                    for (let i = 0; i < training_venue.length; i++) {
                        result += '<option value="' + training_venue[i].Venue + '">' + training_venue[i].Venue + '</option>';
                    }
                }else{
                    result = '<option value="0" selected disabled> -- No record found -- </option>';
                }

                $formTD.find('#trainingVenue').html(result);
                // if(empId != null){
                    // $formTD.find('#trainingVenue').val(empId).trigger('change');
                // }
                // if(mode == 'view'){
                //     $formTD.find('#trainingVenue').prop('disabled', true).trigger('change.select2');
                // }
            }
        });
    });

    $addButtonExam.on('click', function () {
        const $templateRow = $tableExam.find('.data-row').first();

        // Clone WITHOUT events & select2 bindings
        let newRow = $templateRow.clone(false, false);

        // Clear inputs
        newRow.find('input, select, textarea').val('');
        newRow.find('.removeExamRow').prop('disabled', false);

        // 🔥 Remove select2 generated container inside cloned row
        newRow.find('.select2-container').remove();

        let $newSelectExamTitle = newRow.find('.selectExamTitle');

        // 🔥 Clean select2 plugin traces from cloned select
        $newSelectExamTitle
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id')
            .removeAttr('tabindex')
            .removeAttr('aria-hidden')
            .empty()        // ← THIS clears options
            .val(null);

        // Append new row first
        $tableExam.find('tbody').append(newRow);

        // 🔥 Reinitialize select2 ONLY for the new row
        $newSelectExamTitle.select2({
            theme: 'bootstrap-5',
        });

        // Update button states
        updateRemoveButtons($tableExam, 'Table', '.removeExamRow');
        getExaminations($newSelectExamTitle);
    });

    // Submit form (Add / Edit)
    $form.on('submit', function (e) {
        e.preventDefault();
        saveHrMemoApproval($form, $modal, dtHMA, traineeDetailsArray);
    });

    // Edit button
    $table.on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        traineeDetailsArray = [];
        fetchHrMemoById(id, $modal, dtTraineeDetails, $form, 'edit', traineeDetailsArray);
    });

    // View button
    $table.on('click', '.btnView', function () {
        const id = $(this).data('id');
        const approval = $(this).data('approval');
        traineeDetailsArray = [];
        fetchHrMemoById(id, $modal, dtTraineeDetails, $form, 'view', traineeDetailsArray, approval);
    });

    // Enable button
    $table.on('click', '.btnEnable', function () {
        const id = $(this).data('id');
        let updateStatusTo = 1; //pending
        confirmAction('Are you sure you want to enable this?', function () {
            updateHrMemoApprovalStatus(id, dtHMA, updateStatusTo);
        });
    });

    // Disable button
    $table.on('click', '.btnDisable', function () {
        const id = $(this).data('id');
        let updateStatusTo = 2; //cancelled
        confirmAction('Are you sure you want to disable this?', function () {
            updateHrMemoApprovalStatus(id, dtHMA, updateStatusTo);
        });
    });

    // Final Submit button
    $table.on('click', '.btnFinalSubmit', function () {
        const id = $(this).data('id');
        let updateStatusTo = 3; //for approval
        confirmAction('Are you sure you want to submit this?', function () {
            updateHrMemoApprovalStatus(id, dtHMA, updateStatusTo);
        });
    });

    // Disapprove button
    $form.on('click', '#btnHRDisapprove', function () {
        const id = $form.find('#txtHrMemoId').val();
        let updateStatusTo = 4; //disapproved
        // let forApproval = true;
        confirmAction('Disapprove HR Memo Document?', function () {
            updateHrMemoApprovalStatus(id, dtHMA, updateStatusTo, $modal);
        });
    });

    // HR Disapprove button
    // $form.on('click', '#btnHRDisapprove', function () {
    //     const id = $form.find('#txtHrMemoId').val();
    //     let updateStatusTo = 4; // disapproved

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

    //             updateHrMemoApprovalStatus(id, dtHMA, updateStatusTo, $modal, remarks);
    //         }
    //     });
    // });

    // HR Approve button
    $form.on('click', '#btnHRApprove', function () {
        const id = $form.find('#txtHrMemoId').val();
        let updateStatusTo = 5; //approved
        // let forApproval = true;
        confirmAction('Approve HR Memo Document?', function () {
            updateHrMemoApprovalStatus(id, dtHMA, updateStatusTo, $modal);
        });
    });

    // TU Approve button
    $form.on('click', '#btnTUApprove', function () {
        const id = $form.find('#txtHrMemoId').val();
        let updateStatusTo = 6; //approved
        // let forApproval = true;
        confirmAction('Approve HR Memo Document?', function () {
            updateHrMemoApprovalStatus(id, dtHMA, updateStatusTo, $modal);
        });
    });

    // Disapprove button
    // $form.on('click', '#btnTUDisapprove', function () {
    //     const id = $form.find('#txtHrMemoId').val();
    //     let updateStatusTo = 7; //disapproved
    //     // let forApproval = true;
    //     confirmAction('Disapprove HR Memo Document?', function () {
    //         updateHrMemoApprovalStatus(id, dtHMA, updateStatusTo, $modal);
    //     });
    // });

    // TU Disapprove button
    $form.on('click', '#btnTUDisapprove', function () {
        const id = $form.find('#txtHrMemoId').val();
        let updateStatusTo = 7; // disapproved

        Swal.fire({
            title: 'Disapprove HR Memo',
            input: 'textarea',
            id: 'tuDisapproveRemarks',
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

                updateHrMemoApprovalStatus(id, dtHMA, updateStatusTo, $modal, remarks);
            }
        });
    });

    // --------------------
    // REMOVE ROW
    // --------------------
    $tableExam.on('click', '.removeExamRow', function(){
        $(this).closest('tr').remove();
        updateRemoveButtons($tableExam, 'Table', '.removeExamRow');
    });

    $tableTD.on('click', '.removeTDRow', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        traineeDetailsArray = traineeDetailsArray.filter(item => item.action.id != id);
        dtTraineeDetails.clear().rows.add(traineeDetailsArray).draw();
        updateRemoveButtons($tableTD, 'Array', traineeDetailsArray, '.removeTDRow');
        console.log('Updated traineeDetailsArray after removal', traineeDetailsArray);
    });

    $tableTD.on('click', '.editTDRow', function(e) {
        e.preventDefault();
        let editId = $(this).data("id");
        let trainee = traineeDetailsArray.find(item => item.action.id == editId);

        $addButtonTD.data('counter', editId);
        console.log('counterNow', $addButtonTD.data('counter'));

        selectEmpNo($('.selectEmpNo'), trainee.action.emp_id);
        $formTD.find('#trainingVenue').val(trainee.training_venue).trigger('change');
        $formTD.find('#endorsementDate').val(trainee.endorsement_date);

        $tableExam.find('tbody').empty();
        trainee.exam_details.forEach(function (exam){
            let rowExams = `
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
                        <input type="text" class="form-control form-control-md" name="remarks[]" id="remarks"></input>
                    </td>
                    <td id="removeTDRow">
                        <center>
                            <button class="btn btn-md btn-danger removeTDRow" title="Remove Row" type="button" disabled>
                                <i class="fa fa-times"></i>
                            </button>
                        </center>
                    </td>
                </tr>
            `;

            $tableExam.find('tbody').append(rowExams);
            getExaminations($('#tblExamination tr:last').find('.selectExamTitle'), exam.exam_title);
            $('#tblExamination tr:last').find('#result').val(exam.result);
            $('#tblExamination tr:last').find('#remarks').val(exam.remarks);
        });

        updateRemoveButtons($tableExam, 'Table', '.removeTDRow');
        $modalTD.modal('show');
    });

    $tableTD.on('click', '.viewTDRow', function(e) {
        e.preventDefault();
        $formTD.find('#employeeNumber').prop('disabled', true);
        $formTD.find('#endorsementDate').prop('disabled', true);
        $formTD.find('#btnAddExamination').prop('hidden', true);
        $modalTD.find('#btnAddTraineeDetailsToList').prop('hidden', true);

        let id = $(this).data("id");
        let trainee = traineeDetailsArray.find(item => item.action.id == id);

        selectEmpNo($('.selectEmpNo'), trainee.action.emp_id);
        $formTD.find('#endorsementDate').val(trainee.endorsement_date);

        $tableExam.find('tbody').empty();
        trainee.exam_details.forEach(function (exam){
            let rowExams = `
                <tr class="data-row" data-checkbox-id=''>
                    <td>
                        <select class="form-control form-control-sm select2bs5 selectExamTitle" name="title[]" disabled></select>
                    </td>
                    <td>
                        <select class="form-control form-control-md" name="result[]" id="result" disabled>
                            <option value="" disabled selected>Select Result</option>
                            <option value="1">Passed</option>
                            <option value="2">Failed</option>
                            <option value="3">Complied</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-md" name="remarks[]" id="remarks" disabled></input>
                    </td>
                    <td id="removeTDRow">
                        <center>
                            <button class="btn btn-md btn-danger removeTDRow" title="Remove Row" type="button" disabled>
                                <i class="fa fa-times"></i>
                            </button>
                        </center>
                    </td>
                </tr>
            `;

            $tableExam.find('tbody').append(rowExams);
            getExaminations($('#tblExamination tr:last').find('.selectExamTitle'), exam.exam_title);
            $('#tblExamination tr:last').find('#result').val(exam.result);
            $('#tblExamination tr:last').find('#remarks').val(exam.remarks);
        });

        // updateRemoveButtons($tableExam, 'Table', '.removeTDRow');
        $modalTD.modal('show');
    });

    // Submit formTraineeDetails
    $('#btnAddTraineeDetailsToList').on('click', function (e) {
        e.preventDefault();
        let empId = $formTD.find('#employeeNumber').val();
        let position = $formTD.find('#position').val();
        let department = $formTD.find('#department').val();
        let section = $formTD.find('#prodAllocation').val();
        let trainingVenue = $formTD.find('#trainingVenue').val();
        let endorsementDate = $formTD.find('#endorsementDate').val();
        // let counterNow = $form.find('#btnAddTrainee').data('counter');
        let counterNow = $form.find('#btnAddTrainee').data('counter') || null;
        console.log('counterNow', counterNow);

        if(empId == null){
            showError('Please select an employee.');
            return;
        }

        if(endorsementDate == '' || trainingVenue == ''){
            showError('Please fill up the required fields.');
            return;
        }

        let empType = $formTD.find('#employeeNumber').find('option:selected').data('emp_type');
        let empNumber = $formTD.find('#employeeNumber').find('option:selected').text();
        let empName = $formTD.find('#employeeName').val();
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

        // let traineeDetailsList = {
        //     action: {id: counterNow, emp_id: empId, emp_type: empType, status: 1}, //status 1 means "added/edited but not yet saved to database"

        let isEdit = counterNow !== null;
        let traineeDetailsList = {
            action: {
                id: isEdit ? counterNow : getNextId(traineeDetailsArray),
                emp_id: empId,
                emp_type: empType,
                status: 1
            },
            emp_no: empNumber,
            emp_name : empName,
            position: position,
            department: department,
            section: section,
            training_venue: trainingVenue,
            endorsement_date: endorsementDate,
            exam_details: exam_list
        }
        console.log('traineeDetailsArray', traineeDetailsArray);

        let index = traineeDetailsArray.findIndex(function(item){
            return item.action.id == counterNow;
        });

        if (isEdit && index !== -1) {
            // EDIT existing
            traineeDetailsArray[index] = traineeDetailsList;
        } else {
            // ADD new
            traineeDetailsArray.push(traineeDetailsList);
        }

        // traineeDetailsArray.push(traineeDetailsList);
        // $addButtonTD.data('counter', traineeIdCounter);

        dtTraineeDetails.clear().draw();
        dtTraineeDetails.rows.add(traineeDetailsArray).draw();
        showSuccess('Trainee details added to the list.');
        $modalTD.modal('hide');
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

            // if(mode == 'view'){
            //     cboElement.prop('disabled', true).trigger('change.select2');
            // }
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

/**
 * Save (add/update) hr_memo_approval data
 */
function saveHrMemoApproval($form, $modal, dtHrMemoApproval, appendArray) {
    let form = $form[0];
    let formData = new FormData(form);
    formData.append('trainee_details', JSON.stringify(appendArray));

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
                traineeDetailsArray = [];
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
function fetchHrMemoById(id, $modal, $table, $form, $mode, $traineeDetailsArray, approval = false) {
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

            selectEmailRecipients($('.selectToRecipients'), toIds);
            selectEmailRecipients($('.selectCcRecipients'), ccIds);
            selectEmailRecipients($('.selectNotedBy'), response.noted_by, true);

            traineeIdCounter = 1; //set counter to 1 every new memo

            response.trainee_details.forEach(function(item){
                let exam_list = [];
                let counterNow = traineeIdCounter++;

                if(item.employment_type == 1){ //HRIS
                    empName = item.hris_emp_info.EmpName;
                    position = item.hris_emp_info.Position;
                    department = item.hris_emp_info.Department;
                    section = item.hris_emp_info.Section;
                    trainingVenue = item.hris_emp_info.Venue ?? "N/A";
                }else if(item.employment_type == 2){ //SUBCON
                    empName = item.subcon_emp_info.EmpName;
                    position = item.subcon_emp_info.Position;
                    department = item.subcon_emp_info.Department;
                    section = item.subcon_emp_info.Section;
                    trainingVenue = item.subcon_emp_info.Venue ?? "N/A";
                }

                item.emp_exam_details.forEach(function(exam_item){
                    let exam_title = exam_item.category;
                    let result = exam_item.result;
                    let remarks = exam_item.training_remarks;

                    exam_list.push({exam_title, result, remarks});
                });

                let traineeDetailsList = {
                    action: {id: counterNow, emp_id: item.hris_id, emp_type: item.employment_type, status: response.status},
                    emp_no: item.employee_no,
                    emp_name: empName,
                    position: position,
                    department: department,
                    section: section,
                    training_venue: trainingVenue,
                    endorsement_date: item.endorsement_date,
                    exam_details: exam_list
                }

                $traineeDetailsArray.push(traineeDetailsList);
            });

            $form.find('#btnAddTrainee').data('counter', traineeIdCounter);
            // console.log('btn counter', $form.find('#btnAddTrainee').data('counter'));

            console.log('traineeDetailsArray', $traineeDetailsArray);

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
    console.log('disabled form & buttons');

    $form.find('input, textarea, select').prop('disabled', true);
}

/**
 * Disable or update hr_memo_approval status
 */
function updateHrMemoApprovalStatus(id, dtHMA, updateToStatus, modal = null, remarks = '') {
// function updateHrMemoApprovalStatus(id, dtHMA, updateToStatus, modal = null) {
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

                //temp comment due to testing
                // if(updateToStatus > 2){
                //     SendHrMemoMail(id, updateToStatus);
                // }
                dtHMA.draw();
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
