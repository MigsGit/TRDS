if ($.fn.modal) {
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};
}
// ==================== Element References ====================
const modalAddEndorsement = $('#modalAddEndorsement');
const modalAddNotEndorsed = $('#modalAddNotEndorsed');
const formAddEndorsement = $('#formAddEndorsement');
const btnShowModal = $('#btnShowModalAddEndorsement');
let dtForNotEndorsedEmployee;
let requiresHandsOn = false;

// ==================== Main DataTable ====================
const trainingEndorsementTable = $('#tblTrainingEndorsement').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: 'get_training_endorsements',
        type: 'GET'
    },
    columns: [
        { data: 'action', orderable: false, searchable: false },
        { data: 'ctrl_no'},
        { data: 'hr_memo_details.document_no'  },
        { data: 'training_request_details.ctrl_number'  },
        { data: 'date_created' }
    ]
});

// ==================== Modal Employee DataTable ====================
const endorsementEmployeeTable = $('#tblEndorsementEmployees').DataTable({
    processing: true,
    serverSide: false,
    autoWidth: false,
    // paging: false,
    // info: false,
    columns: [
        { data: 'action', orderable: false, searchable: false, width: '90px' },
        { data: 'status', width: '120px' },
        { data: 'date_hired', width: '120px' },
        { data: 'emp_no', width: '100px' },
        { data: 'name', width: '200px' },
        { data: 'rating', width: '200px' },
        { data: 'questionnaire', width: '200px' },
        { data: 'exam_remarks', width: '200px' },
        { data: 'hands_on_attachment', orderable: false, searchable: false, width: '80px', defaultContent: '' }
    ]
});

// ==================== Show Add Modal ====================
btnShowModal.on('click', function () {
    // formAddEndorsement[0].reset();
    // $('#endorsementId').val('');
    // endorsementEmployeeTable.clear().draw();
    modalAddEndorsement.modal('show');
    getCheckedByUsers();
    getApprovedByUsers();
    getAllEmail();
    getPreparedBy();
});

// ==================== View Endorsement ====================
$(document).on('click', '.btnViewEndorsement', function () {
    var id = $(this).data('id');
    var trCtrlNo = $(this).data('tr-ctrl-no');
    $.ajax({
        url: 'get_training_endorsement_by_id',
        method: 'GET',
        data: { 
            id: id,
            tr_ctrl_no: trCtrlNo
        },
        beforeSend: function(){
            $('#btnExportEndorsement').prop('hidden', false);
            $('#selectApprovedBy').val('').trigger('change');
            $('#selectCheckedBy').val('').trigger('change');
        },  
        success: function (response) {
            if (response.result) {
                let data = response.data;
                $('#endorsementId').val(data.id);
                $('#documentNo').val(data.ctrl_no);
                $('#trainingReqCtrl').val(data.training_request_details.ctrl_number);
                $('#hrMemoCtrl').val(data.hr_memo_details.document_no);
                $('#endorsementDate').val(data.date);
                $('#preparedBy').val(data.created_by_user_details.name);

                let attn = data.mail_cc.split(',').map(email => email.trim());
                $('#attn').val(attn).trigger('change');
                console.log(data.approved_by);
                $('#selectApprovedBy').val(data.approved_by).trigger('change');
                $('#selectCheckedBy').val(data.checked_by).trigger('change');
                
                $('#attn').prop('disabled', true);
                $('#trainingReqCtrl').prop('disabled', true);
                $('#btnSubmitEndorsement').hide();
                $('#selectApprovedBy').prop('disabled', true);
                $('#selectCheckedBy').prop('disabled', true);
                
                endorsementEmpList = data.training_endorsement_employees || [];

                var rows =  endorsementEmpList.map(function(detail) {
                    var emp = detail || {};
                    var ratings = '';
                    var remarks = '';
                    var questionnaire = '';
                    var examTitles = '';
                    var examRemarks = '';
                    var handsOnImage = '';
                    var statusHtml = '<span class="badge badge-success">Endorsed</span>';

                    if (Array.isArray(emp.training_request_details_info.employee_exam_details) && emp.training_request_details_info.employee_exam_details.length > 0) {
                        ratings = emp.training_request_details_info.employee_exam_details.map(function(exam) {
                            return exam.exam_result_details_info && exam.exam_result_details_info.rating !== undefined && exam.exam_result_details_info.rating !== null
                                ? exam.exam_result_details_info.rating
                                : '';
                        }).join(' | ');

                        remarks = emp.training_request_details_info.employee_exam_details.map(function(exam) {
                            return exam.exam_result_details_info && exam.exam_result_details_info.remark !== undefined && exam.exam_result_details_info.remark !== null
                                ? exam.exam_result_details_info.remark
                                : '';
                        }).join(' | ');
                        examRemarks = remarks;


                        var questionnaireArr = emp.training_request_details_info.employee_exam_details.map(function(exam) {
                            return exam.exam_result_details_info && exam.exam_result_details_info.questionnaire !== undefined && exam.exam_result_details_info.questionnaire !== null
                                ? exam.exam_result_details_info.questionnaire
                                : null;
                        }).filter(function(q) { return q !== null; });
                        // If the backend returns an array of JSON strings, parse them
                        var parsedQuestionnaires = questionnaireArr.map(function(q) {
                            if (typeof q === 'string') {
                                try {
                                    return JSON.parse(q);
                                } catch (e) {
                                    return null;
                                }
                            }
                            return q;
                        }).filter(function(q) { return q !== null; });
                        questionnaire = JSON.stringify(parsedQuestionnaires);
                        // Extract all exam_title values and join with |
                        try {
                            const qArr = JSON.parse(questionnaire);
                            if (Array.isArray(qArr) && qArr.length > 0) {
                                examTitles = qArr.map(q => q && q.exam_title ? q.exam_title : '').filter(Boolean).join(' | ');
                            }
                        } catch (e) {

                            examTitles = '';
                        }
                    }

                    if(emp.will_endorse == 1){
                        statusHtml = '<span class="badge badge-danger">Not Endorsed</span>';
                    }

                   
                    return Object.assign({
                        action: '--',
                        status: statusHtml,
                        date_hired: emp.training_request_details_info.date_hired || '',
                        emp_no: emp.training_request_details_info.emp_no || '',
                        name: emp.training_request_details_info.name || '',
                        id: emp.training_request_details_info.id || '',
                        rating: ratings,
                        questionnaire: examTitles,
                        exam_remarks: examRemarks,
                        hands_on_attachment: emp.hands_on_filename ? `<button type="button" class="btn btn-primary btn-sm btnViewHandsOnAttachment" title="View Hands-On"><i class="fa fa-eye"></i></button>` : 'N/A',
                        hands_on_filename: emp.hands_on_filename || '',
                        training_endorsement_employee_id: emp.id || ''
                    });
                });
                
                // Load employees into modal table
                endorsementEmployeeTable.clear().rows.add(rows).draw();

                // Set select2 values after modal opens
                modalAddEndorsement.modal('show');
                // getCheckedByUsers(data.checked_by);
                // getApprovedByUsers(data.approved_by);
            } else {
                toastr.error(response.message);
            }
        },
        error: function () {
            toastr.error('Failed to fetch endorsement details.');
        }
    });
});

$(document).on('click', '.btnAddNotEndorsement', function () {
    var id = $(this).data('id');
    var trId = $(this).data('tr-id');
    Swal.fire({
        title: 'Are you sure?',
        html: 'This will allow you to add employees who will not be endorsed for this training endorsement.<br> <em style="font-size: 1rem;">You can specify the reason for not endorsing each employee.</em>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel'
    }).then(function (result) {
        if (result.isConfirmed) {
            drawEmployeeForNotEndorsed(id, trId);
            modalAddNotEndorsed.modal('show');
        }
    });
})

// ==================== Delete Employee Row ====================
$('#tblEndorsementEmployees').on('click', '.btnRemoveEmployee', function () {
    const row = $(this).closest('tr');
    Swal.fire({
        title: 'Are you sure?',
        text: 'This employee will be removed from the endorsement and will be available for selection again.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, remove!'
    }).then(function (result) {
        if (result.isConfirmed) {
            endorsementEmployeeTable.row(row).remove().draw();
        }
    });
});

// ==================== Will Not Be Endorsed Button ====================
$('#tblEndorsementEmployees').on('click', '.btnWillNotEndorse', function () {
    const row = $(this).closest('tr');
    const rowIdx = endorsementEmployeeTable.row(row).index();
    const rowData = endorsementEmployeeTable.row(row).data();
    Swal.fire({
        title: 'Will Not Be Endorsed',
        input: 'textarea',
        inputLabel: 'Remarks',
        inputValue: rowData.remarks || '',
        inputPlaceholder: 'Enter remarks here...',
        showCancelButton: true,
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) {
                return 'Remarks are required!';
            }
        }
    }).then(function (result) {
        if (result.isConfirmed) {
            // Update row data
            rowData.will_not_endorse = true;
            rowData.remarks = result.value;
            rowData.rowClass = 'custom-failed-row'; // Always set rowClass so redraw keeps the style
            rowData.status = '<span class="badge badge-danger">Will Not Be Endorsed</span>';
            endorsementEmployeeTable.row(row).data(rowData).invalidate();
            applyCustomFailedRowBg(); // Reapply background immediately
        }
    });
});

// Remove red highlight if row is removed or reset
endorsementEmployeeTable.on('draw', function () {
    endorsementEmployeeTable.rows().every(function () {
        const data = this.data();
        const node = this.node();
        if (data.will_not_endorse) {
            $(node).css({'background-color': '#e35764', 'color': '#fff'});
        } else {
            $(node).css({'background-color': '', 'color': ''});
        }
    });
});

// ==================== Form Submit ====================
formAddEndorsement.on('submit', function (e) {
    e.preventDefault();
    // Validate: require hands-on image for employees that need it
    var allRows = endorsementEmployeeTable.rows().data().toArray();
    var missingHandsOn = allRows.filter(function(row) {
        return row.requiresHandsOn && !row.hands_on_image && !row.will_not_endorse;
    });
    if (missingHandsOn.length > 0) {
        var names = missingHandsOn.map(function(row) { return row.name || row.emp_no; }).join(', ');
        toastr.error('Hands-on image is required for: ' + names);
        return;
    }

    // Collect employee data from the modal DataTable, including will_not_endorse and remarks
    var employees = endorsementEmployeeTable.rows().data().toArray().map(function (row) {
        return {
            hasExam: row.hasExam,
            hasPassed: row.hasPassed,
            emp_no: row.emp_no,
            name: row.name,
            date_hired: row.date_hired,
            tr_details_id: row.id,
            will_not_endorse: row.will_not_endorse || false,
            remarks: row.remarks || '',
            hands_on_image: row.hands_on_image || null,
            hands_on_file_name: row.hands_on_file_name || null
        };
    });

    var formData = new FormData(this);
    formData.append('employees', JSON.stringify(employees));

    // Append hands-on image files as base64 in the employees JSON
    // The backend can decode them from the employees array

    $.ajax({
        url: 'save_training_endorsement',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if(!response.result){
                toastr.error(response.message);
                return;
            }
            toastr.success(response.message);
            modalAddEndorsement.modal('hide');
            trainingEndorsementTable.ajax.reload();
        },
        error: function (xhr, status, error) {
            if(xhr.status === 422) {
                toastr.error('Please fill up all required fields.');
                handleValidatorErrors(xhr.responseJSON.errors);
                return;
            }
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
            toastr.error('An error occurred while saving the endorsement.');
        }
    });
});

// ==================== Delete Endorsement ====================
$(document).on('click', '.btnDeleteEndorsement', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: 'This endorsement will be deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then(function (result) {
        if (result.isConfirmed) {
            $.ajax({
                url: 'delete_training_endorsement',
                method: 'POST',
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.result == 1) {
                        Swal.fire('Deleted!', response.message, 'success');
                        trainingEndorsementTable.ajax.reload();
                    } else {
                        Swal.fire('Failed!', response.message, 'error');
                    }
                },
                error: function () {
                    toastr.error('An error occurred while deleting.');
                }
            });
        }
    });
});

// ==================== Load Training Request Control Number ====================
$('#trainingReqCtrl').on('keyup', function (e) {
    var trainingReqCtrl = $(this).val();
    var datalist = $('#trainingReqCtrlList');

    $.ajax({
        type: "get",
        url: "get_training_request_controls",
        data: {
            training_req_ctrl: trainingReqCtrl
        },
        dataType: "json",
        beforeSend: function(){
            datalist.empty();
        },
        success: function (response) {
            response.forEach(function(item){
                datalist.append('<option value="' + item.ctrl_number + '">');
            });
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
    

    if(e.key === 'Enter'){
        e.preventDefault();
        $.ajax({
            type: "get",
            url: "get_training_request_ctrl_details",
            data: {
                training_req_ctrl: trainingReqCtrl
            },
            dataType: "json",
            success: function (response) {
                if(!response.result){
                    toastr.error(response.message);
                    return;
                }
                $('#hrMemoId').val(response.hr_memo_id);
                $('#hrMemoCtrl').val(response.hr_memo_document_no);
                $('#trainingReqId').val(response.training_request.id);
                // Build endorsementEmployeeTable using training_request_details
                endorsementEmpList = response.training_request.training_request_details || [];
                let trJobFunctions = response.training_request.job_function;
                var rows = endorsementEmpList.map(function(detail) {
                    var emp = detail || {};
                    // Get ratings from employee_exam_details (array)
                    var ratings = '';
                    var remarks = '';
                    var questionnaire = '';
                    var hasExam = false;
                    var hasPassed = false;
                    var examTitles = '';
                    var examRemarks = '';
                    if (Array.isArray(emp.employee_exam_details) && emp.employee_exam_details.length > 0) {
                        ratings = emp.employee_exam_details.map(function(exam) {
                            return exam.exam_result_details_info && exam.exam_result_details_info.rating !== undefined && exam.exam_result_details_info.rating !== null
                                ? exam.exam_result_details_info.rating
                                : '';
                        }).join(' | ');

                        remarks = emp.employee_exam_details.map(function(exam) {
                            return exam.exam_result_details_info && exam.exam_result_details_info.remark !== undefined && exam.exam_result_details_info.remark !== null
                                ? exam.exam_result_details_info.remark
                                : '';
                        }).join(' | ');
                        examRemarks = remarks;

                        // Check if any remarks contain 'Passed' (case-insensitive)
                        hasPassed = /passed/i.test(remarks);

                        var questionnaireArr = emp.employee_exam_details.map(function(exam) {
                            return exam.exam_result_details_info && exam.exam_result_details_info.questionnaire !== undefined && exam.exam_result_details_info.questionnaire !== null
                                ? exam.exam_result_details_info.questionnaire
                                : null;
                        }).filter(function(q) { return q !== null; });
                        // If the backend returns an array of JSON strings, parse them
                        var parsedQuestionnaires = questionnaireArr.map(function(q) {
                            if (typeof q === 'string') {
                                try {
                                    return JSON.parse(q);
                                } catch (e) {
                                    return null;
                                }
                            }
                            return q;
                        }).filter(function(q) { return q !== null; });
                        questionnaire = JSON.stringify(parsedQuestionnaires);
                        // Extract all exam_title values and join with |
                        try {
                            const qArr = JSON.parse(questionnaire);
                            if (Array.isArray(qArr) && qArr.length > 0) {
                                hasExam = true;

                                examTitles = qArr.map(q => q && q.exam_title ? q.exam_title : '').filter(Boolean).join(' | ');
                            }
                        } catch (e) {

                            examTitles = '';
                        }
                    }
                    // Determine status and action buttons
                    let statusHtml = '';
                    let rowClass = '';
                    let actionBtns = '';
                    console.log('hasExam', hasExam);
                    console.log('hasPassed', hasPassed);
                    if (hasExam && hasPassed) {
                        statusHtml = '<span class="badge badge-warning">For Endorsement</span>';
                        actionBtns += `
                            <button type="button" class="btn btn-danger btn-sm btnRemoveEmployee"><i class="fa fa-trash"></i></button>
                            <button type="button" class="btn btn-secondary btn-sm btnWillNotEndorse" title="Will Not Be Endorsed"><i class="fa fa-ban"></i></button>
                        `;
                        if ([3,4,5,6].includes(Number(trJobFunctions))) {
                            requiresHandsOn = true;
                            actionBtns += `
                                <button type="button" class="btn btn-info btn-sm btnAddHandsOn" title="Add Hands-On"><i class="fa fa-plus"></i></button>
                            `;
                        }
                        rowClass = '';
                    } else if (hasExam && !hasPassed) {
                        statusHtml = '<span class="badge badge-danger">Failed Exam</span>';
                        actionBtns = `
                            <button type="button" class="btn btn-danger btn-sm btnRemoveEmployee"><i class="fa fa-trash"></i></button>
                            <button type="button" class="btn btn-secondary btn-sm btnWillNotEndorse" title="Will Not Be Endorsed"><i class="fa fa-ban"></i></button>
                        `;
                        rowClass = 'custom-failed-row';
                    } else {
                        statusHtml = '<span class="badge badge-danger">No Exam</span>';
                        actionBtns = `
                            <button type="button" class="btn btn-danger btn-sm btnRemoveEmployee"><i class="fa fa-trash"></i></button>
                            <button type="button" class="btn btn-secondary btn-sm btnWillNotEndorse" title="Will Not Be Endorsed"><i class="fa fa-ban"></i></button>
                        `;
                        rowClass = 'custom-failed-row';
                    }
                    return Object.assign({
                        action: actionBtns,
                        status: statusHtml,
                        date_hired: emp.date_hired || '',
                        emp_no: emp.emp_no || '',
                        name: emp.name || '',
                        id: emp.id || '',
                        will_not_endorse: false,
                        remarks: '',
                        rating: ratings,
                        questionnaire: examTitles,
                        exam_remarks: examRemarks,
                        rowClass: rowClass,
                        hasExam: hasExam,
                        hasPassed: hasPassed,
                        requiresHandsOn: requiresHandsOn || false,
                        hands_on_attachment: ''
                    });
                });
                endorsementEmployeeTable.clear().rows.add(rows).draw();
                // Set row background for failed/no exam on draw
                
                applyCustomFailedRowBg();

                
            },
            error: function(xhr, status, error){
                console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
            }
        });
    }
});

endorsementEmployeeTable.on('draw', function () {
    applyCustomFailedRowBg();
});

$('#btnSubmitEndorsement').on('click', function () {
    Swal.fire({
        title: 'Submit Endorsement?',
        text: 'Are you sure you want to submit this endorsement? Please ensure all details are correct before submitting.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, submit it!'
    }).then(function (result) {
        if (result.isConfirmed) {
            formAddEndorsement.trigger('submit');
        }
    });
});



$(document).on('click', '.btnAddEmployeeForNotEndorsed', function () {
    var empNo = $(this).data('emp-no');
    var teId = $(this).data('te-id');
    var trId = $(this).data('tr-id');

    document.activeElement.blur();
    Swal.fire({
        title: 'Will Not Be Endorsed',
        html: `Employee <strong>${empNo}</strong> will be marked as "Will Not Be Endorsed".<br><br><em style="color: red;">Please provide remarks for not endorsing this employee.</em>`,
        input: 'textarea',
        inputPlaceholder: 'Enter remarks here...',
        showCancelButton: true,
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
        allowEscapeKey: true,
        target: document.body,
        didOpen: function () {
            const textarea = document.querySelector('.swal2-textarea');
            if (textarea) textarea.focus();
        },
        inputValidator: (value) => {
            if (!value) {
                return 'Remarks are required!';
            }
        }
    }).then(function (result) {
        if (result.isConfirmed) {
            addNotEndorsedEmployee(empNo, teId, trId, result.value);
        }
    });
});

// ==================== Hands-On Upload ====================
$(document).on('click', '.btnAddHandsOn', function () {
    var row = $(this).closest('tr');
    var rowIdx = endorsementEmployeeTable.row(row).index();
    $('#handsOnRowIndex').val(rowIdx);
    $('#handsOnImage').val('');
    $('#handsOnPreview').hide();
    $('#handsOnPreviewImg').attr('src', '');
    $('#modalHandsOnUpload').modal('show');
});

$('#handsOnImage').on('change', function () {
    var file = this.files[0];
    if (!file) {
        $('#handsOnPreview').hide();
        return;
    }
    var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
        toastr.error('Only JPG, JPEG, and PNG files are allowed.');
        $(this).val('');
        $('#handsOnPreview').hide();
        return;
    }
    var reader = new FileReader(); 
    reader.onload = function (e) {
        $('#handsOnPreviewImg').attr('src', e.target.result);
        $('#handsOnPreview').show();
    };
    reader.readAsDataURL(file);
});

$('#btnSaveHandsOn').on('click', function () {
    var file = $('#handsOnImage')[0].files[0];
    if (!file) {
        toastr.error('Please select an image file.');
        return;
    }
    var rowIdx = parseInt($('#handsOnRowIndex').val());
    var rowData = endorsementEmployeeTable.row(rowIdx).data();
    var reader = new FileReader();
    reader.onload = function (e) {
        rowData.hands_on_image = e.target.result;
        rowData.hands_on_file_name = file.name;
        rowData.hands_on_attachment = `<button type="button" class="btn btn-primary btn-sm btnViewHandsOnAttachment" title="View Hands-On"><i class="fa fa-eye"></i></button>`;
        endorsementEmployeeTable.row(rowIdx).data(rowData).invalidate().draw();
        toastr.success('Hands-on image attached for ' + (rowData.name || 'employee') + '.');
        $('#modalHandsOnUpload').modal('hide');
    };
    reader.readAsDataURL(file);
});

// ==================== View Hands-On Attachment ====================
$(document).on('click', '.btnViewHandsOnAttachment', function () {
    var row = $(this).closest('tr');
    var rowIdx = endorsementEmployeeTable.row(row).index();
    var rowData = endorsementEmployeeTable.row(row).data();

    // Prefer inline/base64 image if present
    if (!rowData) {
        toastr.error('No employee data available.');
        return;
    }

    if (rowData.hands_on_image) {
        var win = window.open('', '_blank');
        if (!win) {
            toastr.error('Please allow popups to view the image.');
            return;
        }
        win.document.write('<title>Hands-On Attachment</title><body style="margin:0;background:#000;"><img src="' + rowData.hands_on_image + '" style="max-width:100%;max-height:100vh;display:block;margin:auto;" /></body>');
        win.document.close();
        return;
    }

    // Try to build URL from storage: saved under storage/hands_on/{empId}/{filename}
    var filename = rowData.hands_on_filename;
    var empId = rowData.training_endorsement_employee_id;
     console.log('empId', empId, 'filename', filename, 'rowData', rowData);
    // return false;
    if (filename && empId) {
        var fileExtension = filename.split('.').pop();
        var url = './public/storage/hands_on_attachments/' + encodeURIComponent(empId + '.' + fileExtension);
        var win2 = window.open(url, '_blank');
        if (!win2) {
            toastr.error('Please allow popups to view the image.');
        }
        return;
    }
    toastr.error('No hands-on image attached.');
});

$('#btnExportEndorsement').on('click', function(){
     const id = $('#endorsementId').val();
    const trCtrlNo = $('#trainingReqCtrl').val();
    if (!id || !trCtrlNo) {
        toastr.error('Something went wrong. Please try again.');
        return;
    }
    window.open(`export_endorsement_pdf?id=${encodeURIComponent(id)}&tr_ctrl_no=${encodeURIComponent(trCtrlNo)}`, '_blank');

});


// ==================== Dropdown Loaders ====================
function getCheckedByUsers(selectedVal) {
    $.ajax({
        url: 'get_endorsement_users',
        method: 'GET',
        beforeSend: function(){
            $('#selectCheckedBy').empty();
        },
        success: function (response) {
            var select = $('#selectCheckedBy');
            // select.empty().append('<option value="" disabled selected>Select One</option>');
            $.each(response, function (i, user) {
                select.append('<option value="' + user.rapidx_id + '">' + user.name + ' (' + user.email + ')</option>');
            });
        }
    });
}

function getApprovedByUsers(selectedVal) {
    $.ajax({
        url: 'get_endorsement_users',
        method: 'GET',
        beforeSend: function(){
            $('#selectApprovedBy').empty();
        },
        success: function (response) {
            var select = $('#selectApprovedBy');
            // select.empty().append('<option value="" disabled selected>Select One</option>');
            $.each(response, function (i, user) {
                select.append('<option value="' + user.rapidx_id + '">' + user.name + ' (' + user.email + ')</option>');
            });
        }
    });
}

function applyCustomFailedRowBg() {
    endorsementEmployeeTable.rows().every(function () {
        const data = this.data();
        const node = this.node();
        if (data.rowClass === 'custom-failed-row') {
            $(node).css({'background-color': '#e35764', 'color': '#fff'});
        } else {
            $(node).css({'background-color': '', 'color': ''});
        }
    });
}

function getPreparedBy() {
    $.ajax({
        url: 'get_current_user',
        method: 'GET',
        success: function (response) {
            $('#preparedBy').val(response.name);
        }
    });
}

function getAllEmail(){
    $.ajax({
        type: "get",
        url: "get_all_email",
        // data: "data",
        dataType: "json",
        beforeSend: function(){
            $('#attn').empty();
        },
        success: function (response) {
            let options = "";
            response.forEach(user => {
                if (user.email) {
                    options += `<option value="${user.email}">${user.name} (${user.email})</option>`;
                } else {
                    options += `<option disabled>${user.name} (No Email)</option>`;
                }
            });
            $('#attn').html(options);
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

function drawEmployeeForNotEndorsed(training_endorsement_id, trId){
    dtForNotEndorsedEmployee = $('#tableForNotEndorsedEmployee').DataTable({
        processing: true,
        serverSide: true,
        bDestroy : true,
        ajax: {
            url: 'get_employees_for_not_endorsed',
            type: 'GET',
            data: {
                training_endorsement_id: training_endorsement_id,
                trId: trId
            }
        },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'date_hired' },
            { data: 'emp_no'},
            { data: 'name'},
        ]
    });
}

function addNotEndorsedEmployee(empNo, teId, trId, remarks){
    
    $.ajax({
        type: "POST",
        url: "add_not_endorsed_emp",
        data: {
            emp_no: empNo,
            training_endorsement_id: teId,
            training_request_id: trId,
            remarks: remarks,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        beforeSend: function(){
            showSwalLoading();
        },
        success: function (response) {
            if(!response.result){
                toastr.error(response.message);
                Swal.close();
                return;
            }
            toastr.success(response.message);
            drawEmployeeForNotEndorsed(teId, trId);
            Swal.close();
        },
        error: function(xhr, status, error){
            Swal.close();
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });

    
}