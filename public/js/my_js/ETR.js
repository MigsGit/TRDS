const GetEmployeeDetails = (element, searchTerm, successCallback, errorCallback) => {

    ajaxRequest({
        url: 'get_systemone_employee_training_details',
        method: 'GET',
        dataType: 'json',
        data: {
            search: searchTerm
        },

        successCallback: function (response) {

            successCallback(
                response.map(item => ({
                    id: item.pkid,
                    employeeNo: item.EmpNo,
                    position: item.Position,
                    department: item.Department,
                    division: item.Division,
                    section: item.Section,
                    employmentStatus: item.EmpStatus,
                    hiringStatus: item.HiringStatus,
                    dateHired: item.DateHired,
                    text: `${item.EmpNo} - ${decodeURIComponent(escape(item.EmpName))}`,
                }))
            );

        },

        errorCallback: errorCallback
    });

}

const UpdateExportButton = () => {
    const activeTab = $('#trainingTabs .nav-link.active').attr('id');
    const employeeId = $('#btnExportTraining').attr('data-employee-id');
    const employeeNo = $('#btnExportTraining').attr('data-employee-no');

    if (!employeeNo) {
        $('#btnExportTraining').addClass('d-none');
        return;
    }

    if (activeTab === 'trdsSummary-tab') {
        $('#btnExportTraining')
            .attr('href', 'get_trds_summary/' + encodeURIComponent(employeeId) + '/' + encodeURIComponent(employeeNo))
            .attr('title', 'Export TRDS Summary')
            .attr('target', '_blank')
            .html('<i class="fa fa-file-excel-o mr-1"></i> Export TRDS Summary')
            .removeClass('d-none');

    } else if (activeTab === 'employeeTrainingRecord-tab') {
        $('#btnExportTraining')
            .attr('href', 'get_employee_training_record/' + encodeURIComponent(employeeId) + '/' + encodeURIComponent(employeeNo))
            .attr('title', 'Export Employee Training Record')
            .attr('target', '_blank')
            .html('<i class="fa fa-file-excel-o mr-1"></i> Export Employee Training Record')
            .removeClass('d-none');
    }
};

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
                    result += '<option value="' + response[di]['EmpNo'] + '" >' + response[di]['EmpName'] + '</option>';
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
                    result += '<option value="' + response[i]['employee_no'] + '" ';
                    result += '>' + response[i]['employee_name'] + '</option> ';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }
            cboElement.html(result);
            // if(empId != null){
            //     cboElement.val(empId).trigger('change');
            // }

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