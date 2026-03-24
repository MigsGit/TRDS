

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
            { data: 'section_head_user', name: 'section_head_user' },
            { data: 'receiving', name: 'receiving' },
            { data: 'tu_head_approval', name: 'tu_head_approval' }
        ]
    });

    const tblEmployeeListByMemoDoc = $('#tblEmployeeListByMemoDoc').DataTable({
        processing: true,
        serverSide: false,
        autoWidth: false, // Disable default autoWidth
        responsive: true, // Enable responsive behavior
        ajax: {
            url: 'get_employee_list_by_memo_doc',
            type: 'GET',
            data: function(d){
                d.memo_doc_id = $('#selectMemoDocNo').val();
            }
        },
        columns: [
            { data: 'action', orderable: false, searchable: false, width: '50px' },
            { data: 'date_hired', width: '100px' },
            { data: 'emp_no', width: '80px' },
            { data: 'name', width: '150px' },
            { data: 'position', width: '250px' },
            { data: 'training_title', width: '250px' },
            { data: 'training_result', width: '120px' },
            { data: 'remarks', width: '200px' },
            { data: 'training_venue', width: '150px' },
            { data: 'training_endorsement_date', width: '120px' }
        ],
        columnDefs: [
            { targets: '_all', className: 'text-nowrap' } // prevent wrapping, keeps widths based on content
        ]
    });

    


    selectFilterId.change(function(){
        trainingRequestTable.ajax.reload();
    });

    addRequestTrainingBtn.on('click', function() {
        $('#btnSubmitTrainingRequest').show();
        AddTrainingform[0].reset();

        modalAddTrainingRequest.modal('show');

        // show employee table
        $('#tblEmployeeListByMemoDoc').closest('.tbl').show();

        // hide training details table
        $('#tblRequestedTrainingDetails').closest('.table-responsive').attr('hidden', true);

        modalAddTrainingRequest.find('select').prop('disabled', false);

        getHrisDepartments();
        getHrisSection();
        getUserConformance();
        getRequestor();
        getMemoDocs();
    });

    $trainingRequestTable.on('click', '.btnViewTrainingRequest', function () {
        const requestId = $(this).data('id');
        modalAddTrainingRequest.modal('show');
        modalAddTrainingRequest.find('.modal-title').text('View Training Request Details');

        // hide add table
        $('#tblEmployeeListByMemoDoc').closest('.tbl').hide();

        // show view table
        $('#tblRequestedTrainingDetails').closest('.table-responsive').removeAttr('hidden');

        $.ajax({
            url: 'get_training_request_details',
            method: 'GET',
            data: { id: requestId },
            success: function(response) {

                const created_at = new Date(response.created_at);
                const memoDocId = response.training_request_details[0].training_memo_doc_id;

                console.log('clarkyboy', memoDocId);

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
                $('#txtRequestor').val(response.requestor.name);

                getHrisDepartments(response.department_id,true);
                getHrisSection(response.section_id,true);
                getUserConformance(response.section_head_user.id,true);
                getMemoDocs(memoDocId, true);

                $('#selectJobFunction').val(response.job_function).trigger('change').prop('disabled', true);
                $('#selectAreaLine').val(response.area_allocation).trigger('change').prop('disabled', true);
                $('#selectReason').val(response.reason).trigger('change').prop('disabled', true);

                $('#txtTrainingDescription').val(response.training_description);
                $('#btnSubmitTrainingRequest').hide();

                if ($.fn.DataTable.isDataTable('#tblRequestedTrainingDetails')) {
                    // Clear and reload existing table
                    const table = $('#tblRequestedTrainingDetails').DataTable();
                    table.clear().rows.add(response.training_request_details).draw();
                } else {
                    // Initialize DataTable for the first time
                    $('#tblRequestedTrainingDetails').DataTable({
                        data: response.training_request_details,
                        columns: [
                            { data: 'date_hired', name: 'date_hired' },
                            { data: 'emp_no', name: 'emp_no' },
                            { data: 'name', name: 'name' },
                            { 
                                data: 'pos_dept_section', 
                                name: 'pos_dept_section', 
                                render : function(data, type, row) {
                                    return `${row.position} / ${row.department} / ${row.section}`;
                                }
                            }
                        ],
                        responsive: true,
                        paging: false,
                        searching: false,
                        ordering: false,
                        autoWidth: false
                    });
                }
            }
            
        });

    });

    $trainingRequestTable.on('click', '.btnConformTrainingRequest', function () {
        const requestId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to conform this training request?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Example AJAX request to confirm
                $.ajax({
                    url: 'confirm_training_request', // Your route here
                    method: 'POST',
                    data: {
                        id: requestId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.result == 1){
                            Swal.fire(
                                'Confirmed!',
                                response.message,
                                'success'
                            );
                            trainingRequestTable.ajax.reload();
                        } else {
                            Swal.fire(
                                'Failed!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr){
                        Swal.fire(
                            'Error!',
                            'An error occurred while confirming the request.',
                            'error'
                        );
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });

    $trainingRequestTable.on('click', '.btnReceiveTrainingRequest', function () {
        const requestId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to receive this training request?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, receive it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Example AJAX request to confirm
                $.ajax({
                    url: 'receive_training_request', // Your route here
                    method: 'POST',
                    data: {
                        id: requestId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.result == 1){
                            Swal.fire(
                                'Confirmed!',
                                response.message,
                                'success'
                            );
                            trainingRequestTable.ajax.reload();
                        } else {
                            Swal.fire(
                                'Failed!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr){
                        Swal.fire(
                            'Error!',
                            'An error occurred while confirming the request.',
                            'error'
                        );
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });

    $trainingRequestTable.on('click', '.btnApproveTrainingRequest', function () {
        const requestId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to approve this training request?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Example AJAX request to confirm
                $.ajax({
                    url: 'approve_training_request', // Your route here
                    method: 'POST',
                    data: {
                        id: requestId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.result == 1){
                            Swal.fire(
                                'Confirmed!',
                                response.message,
                                'success'
                            );
                            trainingRequestTable.ajax.reload();
                        } else {
                            Swal.fire(
                                'Failed!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr){
                        Swal.fire(
                            'Error!',
                            'An error occurred while confirming the request.',
                            'error'
                        );
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });

    AddTrainingform.on('submit', function(e) {
        e.preventDefault();

        let table = $('#tblEmployeeListByMemoDoc').DataTable();
        let data = table.rows().data().toArray();
        let formData = Object.fromEntries(new FormData($('#formAddTrainingRequest')[0]));

        let employees = data.map(row => ({
            id: row.id,   // make sure your server actually sends an 'id'
            emp_no: row.emp_no,
            date_hired: row.date_hired,
            name: row.name,
            position: row.position,
            training_title: row.training_title,
            training_result: row.training_result,
            remarks: row.remarks,
            training_venue: row.training_venue,
            training_endorsement_date: row.training_endorsement_date
        }));

        $.ajax({
            url: 'add_training_request',
            method: 'POST',
            data: {
                ...formData,   
                employees: employees,
                memo_doc_id: $('#selectMemoDocNo').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.result == 1){
                    toastr.success(response.message);
                    trainingRequestTable.draw(); // Refresh the DataTable
                } else {
                    toastr.error(response.message);
                }
                modalAddTrainingRequest.modal('hide');
            },
            error: function(xhr, status, error){
                console.error(xhr.responseText);
                toastr.error('An error occurred while adding the training request.');
            }
        });

        
    });

    $('#selectMemoDocNo').on('change', function(){

        if($(this).val()){
            tblEmployeeListByMemoDoc.ajax.reload();
        }else{
            tblEmployeeListByMemoDoc.clear().draw();
        }

    });

    $('#tblEmployeeListByMemoDoc').on('click', '.btnRemoveEmployeeFromMemoDoc', function(){
        let table = $('#tblEmployeeListByMemoDoc').DataTable();
        let row = $(this).closest('tr');

        table.row(row).remove().draw(false); // no reload
    });


    function getHrisDepartments(selectedDepartmentId = null, isViewMode = false) {
        $.ajax({
            url: 'get_hris_department',
            method: 'GET',
            success: function(response) {
                const $select = $('#selectDepartment');
                $select.empty();
                $select.append('<option value="" disabled selected>Select Department</option>');

                $.each(response, function(index, department) {
                    $select.append('<option value="' + department.pkid + '">' + department.Department + '</option>');
                });

                // Only set a selected value if provided
                if (selectedDepartmentId) {
                    $select.val(selectedDepartmentId);
                }
                $select.prop('disabled', isViewMode).trigger('change');
            }
        });
    }

    function getHrisSection(selectedSectionId = null, isViewMode = false) {

        $.ajax({
            url: 'get_hris_sections',
            method: 'GET',
            success: function(response) {
                const $select = $('#selectSection');
                $select.empty();
                $select.append('<option value="" disabled selected>Select Section</option>');

                $.each(response, function(index, section) {
                    $select.append('<option value="' + section.pkid + '">' + section.Section + ' - ' + section.department.Department + '</option>');
                });

                if (selectedSectionId) {
                    $select.val(selectedSectionId);
                }

                $select.prop('disabled', isViewMode).trigger('change');
            }
        });
    }

    function getUserConformance(selectedUserId = null, isViewMode = false) {

        $.ajax({
            url: 'get_user_conformance', // Update with your actual route
            method: 'GET',
            // data: { id: id },
            success: function(response) {
                // Handle the response as needed
                // console.log(response);
                const $select = $('#selectSectionHead');
                $select.empty();
                $select.append('<option value="" disabled selected>Select Conformance User</option>');
                $.each(response, function(index, user) {
                    $select.append('<option value="' + user.users.id + '">' + user.users.name + '</option>');
                });

                if (selectedUserId) {
                    $select.val(selectedUserId);
                }

                $select.prop('disabled', isViewMode).trigger('change');
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

    function getMemoDocs(selectedMemoId = null, isViewMode = false) {
        $.ajax({
            url: 'get_memo_docs',
            method: 'GET',
            data: {
                selectedMemoId: selectedMemoId // ✅ send it
            },
            success: function(response) {
                const $select = $('#selectMemoDocNo');
                // console.log('clarkyyyboy', selectedMemoId);
                $select.empty();
                $select.append('<option value="" disabled>Select Memo Document</option>');

                $.each(response, function(index, memo) {
                    $select.append('<option value="' + memo.id + '">' + memo.document_no + '</option>');
                });

                // Set selected only after options are added
                if (selectedMemoId) {
                    $select.val(selectedMemoId);
                } else {
                    $select.val(''); // clear selection in Add mode
                }

                // Enable or disable depending on mode
                $select.prop('disabled', isViewMode).trigger('change');
            }
        });
    }


});




