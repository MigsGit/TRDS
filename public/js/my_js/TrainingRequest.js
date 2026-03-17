

$(document).ready(function() {    
    // Apply Select2 to all select elements inside any modal dynamically
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('.select2bs5').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this).closest('.modal') // Ensures correct parent modal
            });
        });
    });

    const $trainingRequestTable = $('#tblTrainingRequest');
    const AddTrainingform = $('#formAddTrainingRequest');
    const modalAddTrainingRequest = $('#modalAddTrainingRequest');
    const addRequestTrainingBtn = $('#btnShowModalRequestTraining');
    const selectFilterId = $('#selectFilterId');
    const requestEmployeeBtn = $('#btnAddTrainee');
    const modalAddEmployee = $('#modalAddEmployee');

    const trainingRequestTable = $trainingRequestTable.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get_training_requests',
            type: 'GET',
            data: function(d) {
                d.filter = selectFilterId.val();
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable:false, searchable:false },
            { data: 'status', name: 'status' },
            { data: 'ctrl_number', name: 'ctrl_number' },
            { data: 'date_filed', name: 'date_filed' },
            { data: 'conformance_user', name: 'conformance_user' },
            { data: 'receiving', name: 'receiving' },
            { data: 'tu_head_approval', name: 'tu_head_approval' }
        ]
    });

    const trainingRequestDetailsTable = $('#tblRequestedEmployeeDetails').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get_requested_employee_details',
            type: 'GET',
            // data: function(d) {
            //     d.request_id = $('#documentNo').val(); // Pass the request ID to fetch details for that specific request
            // }
        },
        columns: [
            { data: 'employee_name', name: 'employee_name' },
            { data: 'department', name: 'department' },
            { data: 'section', name: 'section' },
            { data: 'job_function', name: 'job_function' },
            { data: 'area_allocation', name: 'area_allocation' }
        ]
    });

    const tblRequestedEmployeeByMemoDoc = $('#tblRequestedEmployeeByMemoDoc').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'get_memo_doc_employee_details',
            type: 'GET'
        },
        columns: [
            { data: 'emp_no' },
            { data: 'name' },
            { data: 'position' },
            { data: 'department' },
            { data: 'section' },
            { data: 'training_title' },
            { data: 'training_result' },
            { data: 'remarks' },
            { data: 'training_venue' },
            { data: 'training_endorsement_date' }
        ]
    })



    selectFilterId.change(function(){
        trainingRequestTable.ajax.reload();
    });

    addRequestTrainingBtn.on('click', function() {
        $('#btnSubmitTrainingRequest').show();
        AddTrainingform[0].reset();

        modalAddTrainingRequest.modal('show');

        // show employee table
        $('#tblRequestedEmployeeDetails').closest('.tbl').show();

        // hide training details table
        $('#tblRequestedTrainingDetails').closest('.table-responsive').attr('hidden', true);

        modalAddTrainingRequest.find('select').prop('disabled', false);

        getHrisDepartments();
        getHrisSection();
        getUserConformance();
        getRequestor();
    });


    $trainingRequestTable.on('click', '.btnViewTrainingRequest', function () {
        const requestId = $(this).data('id');

        modalAddTrainingRequest.modal('show');
        modalAddTrainingRequest.find('.modal-title').text('Training Request Details');

        // hide add table
        $('#tblRequestedEmployeeDetails').closest('.tbl').hide();

        // show view table
        $('#tblRequestedTrainingDetails').closest('.table-responsive').removeAttr('hidden');

        $.ajax({
            url: 'get_training_request_details',
            method: 'GET',
            data: { id: requestId },
            success: function(response) {

                const created_at = new Date(response.created_at);

                const formattedDate = created_at.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                });

                $('#documentNo').val(response.ctrl_number);
                $('#dateFiled').val(formattedDate);
                $('#txtRequestor').val(response.requestor_name);

                $('#selectDepartment').val(response.department_id).trigger('change');
                $('#selectSection').val(response.section_id).trigger('change');

                $('#selectJobFunction').val(response.job_function).trigger('change').prop('disabled', true);
                $('#selectAreaLine').val(response.area_allocation).trigger('change').prop('disabled', true);
                $('#selectReason').val(response.reason).trigger('change').prop('disabled', true);

                $('#txtTrainingDescription').val(response.training_description);
                $('#btnSubmitTrainingRequest').hide();
            }
        });
    });

    AddTrainingform.on('submit', function(e) {
        e.preventDefault();
        // console.log(e);
       

        $.ajax({
            url: 'add_training_request',
            method: 'POST',
            data: AddTrainingform.serialize(),
            success: function(response) {
                if(response['result'] == 1){
                    toastr.success(response.message);
                    trainingRequestTable.draw(); // Refresh the DataTable
                }else{
                    toastr.error(response.message);

                }
                // Handle success (e.g., show a success message, update the table, etc.)
                // alert('Training request added successfully!');
                // Optionally, you can refresh the training request table here
                // For example, you can make an AJAX call to fetch the updated list of training requests and update the table
            }
        });

        modalAddTrainingRequest.modal('hide');
    });

    requestEmployeeBtn.on('click', function() {
        // console.log('Request Employee button clicked');
        modalAddEmployee.modal('show');
        getMemoDocs();
        tblRequestedEmployeeByMemoDoc.draw();

    });

    $('#selectMemoDocNo').on('change', function(){

        let memoDocId = $(this).val();

        if(memoDocId){

            tblRequestedEmployeeByMemoDoc.ajax.url(
                'get_memo_doc_employee_details?memo_doc_id=' + memoDocId
            ).load();

        }else{

            tblRequestedEmployeeByMemoDoc.clear().draw();

        }

    });


    function getHrisDepartments(){

        $.ajax({
            url: 'get_hris_department', // Update with your actual route
            method: 'GET',
            success: function(response) {
                // Clear existing options
                // console.log(response);
                $('#selectDepartment').empty();
                // Add a default option
                $('#selectDepartment').append('<option value="">Select Department</option>');
                // Populate the dropdown with the response data
                $.each(response, function(index, department) {
                    $('#selectDepartment').append('<option value="' + department.pkid + '">' + department.Department + '</option>');
                });
            }
        });
    }

     function getHrisSection(){

        $.ajax({
            url: 'get_hris_sections', // Update with your actual route
            method: 'GET',
            success: function(response) {
                // Clear existing options
                // console.log(response);
                $('#selectSection').empty();
                // Add a default option
                $('#selectSection').append('<option value="">Select Section</option>');
                // Populate the dropdown with the response data
                $.each(response, function(index, section) {
                    $('#selectSection').append('<option value="' + section.pkid + '">' + section.Section + ' - ' + section.department.Department + '</option>');
                });
            }
        });
    }

    function getUserConformance(){

        $.ajax({
            url: 'get_user_conformance', // Update with your actual route
            method: 'GET',
            // data: { id: id },
            success: function(response) {
                // Handle the response as needed
                // console.log(response);
                $('#selectSectionHead').empty();
                $('#selectSectionHead').append('<option value="">Select Conformance User</option>');
                $.each(response, function(index, user) {
                    $('#selectSectionHead').append('<option value="' + user.users.id + '">' + user.users.name + '</option>');
                });
            }
        });
    }

    function getRequestor(){
        $.ajax({
            url: 'get_requestor', // Update with your actual route
            method: 'GET',
            success: function(response) {
                // Handle the response as needed
                // console.log(response);
                $('#txtRequestor').val(response.requestor_name);
            }
        });
    }

    function getMemoDocs(){
        $.ajax({
            url: 'get_memo_docs', // Update with your actual route
            method: 'GET',
            success: function(response) {
                // Handle the response as needed
                // console.log(response);
                $('#selectMemoDocNo').val(response.document_no);
                $('#selectMemoDocNo').empty();
                $('#selectMemoDocNo').append('<option value="">Select Memo Document</option>');

                $.each(response, function(index, memo) {
                    $('#selectMemoDocNo').append('<option value="' + memo.id + '">' + memo.document_no + '</option>');
                });
            }
        });
    }

});




