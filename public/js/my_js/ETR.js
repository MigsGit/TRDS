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
