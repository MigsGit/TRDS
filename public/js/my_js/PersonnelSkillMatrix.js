

$(document).ready(function() {
    const directEmployee = $('#tblDirectEmployees');
    const subconEmployee = $('#tblSubconEmployees');
    const updateEmpInfoModalId = $('#updateEmpInfoModalId');
    const viewEmpInfoModalId = $('#viewEmpInfoModalId');
    const btnChooseFileToExport = $('#btnChooseFileToExport');
    const chooseExportReportModal = $('#chooseExportReportId');
    const exportSkillMatrix = $('#btnGenerateVisualMatrix');



    const directEmployeeTable = directEmployee.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get_direct_employees',
            type: 'GET',
        },
        columns: [
            { data: 'action', name: 'action', orderable:false, searchable:false },
            { data: 'EmpNo', name: 'EmpNo' },
            { data: 'EmpName', name: 'EmpName' },
            { data: 'DateHired', name: 'DateHired' },
            { data: 'Position', name: 'Position' },
            { data: 'Section', name: 'Section' }
        ]
    });

    const subconEmployeeTable = subconEmployee  .DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get_subcon_employees',
            type: 'GET',
        },
        columns: [
            { data: 'action', name: 'action', orderable:false, searchable:false },
            { data: 'EmpNo', name: 'EmpNo' },
            { data: 'EmpName', name: 'EmpName' },
            { data: 'DateHired', name: 'DateHired' },
            { data: 'Position', name: 'Position' },
            { data: 'Section', name: 'Section' }
        ]
    });

    let currentEmpNo = '';

    const employeeTrainingsTable = $('#tblEmployeeTrainings').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: true,
        responsive: true,
        ajax: {
            url: 'get_employee_trainings',
            type: 'GET',
            data: function (d) {
                d.id = currentEmpNo;
            },
            dataSrc: function (json) {

                // Update the summary labels
                $('#lblPassed').text(json.passed);
                $('#lblComplied').text(json.complied);
                $('#lblFailed').text(json.failed);
                $('#lblTotal').text(json.total);

                return json.data;
            }
        },
        columns: [
            { data: 'trainingDate'},
            { data: 'title'},
            { data: 'seriesName'},
            { data: 'station'},
            { data: 'detailedStation'},
            { data: 'objective' },
            { data: 'trainor' },
            { data: 'result' },
            { data: 'trainingVenue' },
            // { data: 'mechanics' },
            { data: 'typeOfTraining' },
        ]
    });

    $('#tblDirectEmployees').on('click', '.btnUpdateDirectEmpInfo', function () {
        const empNo = $(this).data('empno');
        updateEmpInfoModalId.modal('show');
        getDirectEmployeeInfo(empNo);

    });

    $('#tblDirectEmployees').on('click', '.btnViewDirectEmpInfo', function () {
        const empNo = $(this).data('empno');
        viewEmpInfoModalId.modal('show');
        viewDirectEmployeeInfo(empNo);
        currentEmpNo = empNo;
        employeeTrainingsTable.ajax.reload();
    });

      $('#tblSubconEmployees').on('click', '.btnViewSubconEmpInfo', function () {
        const empNo = $(this).data('empno');
        viewEmpInfoModalId.modal('show');
        viewSubconEmpInfo(empNo);
        currentEmpNo = empNo;
        employeeTrainingsTable.ajax.reload();
    });

    $('#tblSubconEmployees').on('click', '.btnUpdateSubconEmpInfo', function () {
        const empNo = $(this).data('empno');
        updateEmpInfoModalId.modal('show');
        getSubconEmployeeInfo(empNo);

    });

    function getDirectEmployeeInfo(empNo) {
        $.ajax({
            url: 'get_direct_employee_info',
            method: 'GET',
            data: { id: empNo },
            success: function (response) {
                // console.log(response);
                let middleName = response.MiddleName
                    ? response.MiddleName.charAt(0).toUpperCase() + '.'
                    : '';
                let empFullName = response.FirstName + ' ' + middleName + ' ' + response.LastName;
                $('#empNo').val(response.EmpNo);
                $('#empName').val(empFullName);
                $('#position').val(response.Position);
                $('#section').val(response.Section);
                $('#dateHired').val(response.DateHired);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching employee info:', error);
            }
        });
    }

    function viewDirectEmployeeInfo(empNo) {
        $.ajax({
            url: 'view_direct_employee_info',
            method: 'GET',
            data: { id: empNo },
            success: function (response) {
                // console.log(response);
                let middleName = response.MiddleName
                    ? response.MiddleName.charAt(0).toUpperCase() + '.'
                    : '';
                let empFullName = response.FirstName + ' ' + middleName + ' ' + response.LastName;
                $('#viewEmpNo').val(response.EmpNo);
                $('#viewEmpName').val(empFullName);
                $('#viewPosition').val(response.Position);
                $('#viewSection').val(response.Section);
                $('#viewDepartment').val(response.Department);
                $('#viewDivision').val(response.Division);
                $('#viewDateHired').val(response.DateHired);
                $('#viewEmploymentStatus').val(response.EmpStatus);

            },
            error: function (xhr, status, error) {
                console.error('Error fetching employee info:', error);
            }
        });
    }

    function getSubconEmployeeInfo(empNo) {
        $.ajax({
            url: 'get_subcon_employee_info',
            method: 'GET',
            data: { id: empNo },
            success: function (response) {
                // console.log(response);
                let middleName = response.MiddleName
                    ? response.MiddleName.charAt(0).toUpperCase() + '.'
                    : '';
                let empFullName = response.FirstName + ' ' + middleName + ' ' + response.LastName;
                $('#empNo').val(response.EmpNo);
                $('#empName').val(empFullName);
                $('#position').val(response.Position);
                $('#section').val(response.Section);
                $('#dateHired').val(response.DateHired);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching employee info:', error);
            }
        });
    }

    function viewSubconEmpInfo(empNo) {
        $.ajax({
            url: 'view_subcon_employee_info',
            method: 'GET',
            data: { id: empNo },
            success: function (response) {
                console.log(response);
                let middleName = response.MiddleName
                    ? response.MiddleName.charAt(0).toUpperCase() + '.'
                    : '';
                let empFullName = response.FirstName + ' ' + middleName + ' ' + response.LastName;
                $('#viewEmpNo').val(response.EmpNo);
                $('#viewEmpName').val(empFullName);
                $('#viewPosition').val(response.Position);
                $('#viewSection').val(response.Section);
                $('#viewDepartment').val(response.Department);
                $('#viewDivision').val(response.Division);
                $('#viewDateHired').val(response.DateHired);
                $('#viewEmploymentStatus').val(response.EmpStatus);

            },
            error: function (xhr, status, error) {
                console.error('Error fetching employee info:', error);
            }
        });
    }


    $('#employeeAccordion .collapse').on('show.bs.collapse', function () {

        // Reset all headers
        $('#employeeAccordion .accordion-header')
            .removeClass('bg-primary text-white')
            .addClass('bg-light');

        // Reset all button colors
        $('#employeeAccordion .accordion-header .btn')
            .removeClass('text-white')
            .addClass('text-dark');

        // Highlight current header
        $(this).prev('.card-header')
            .removeClass('bg-light')
            .addClass('bg-primary text-white');

        // Highlight current button text
        $(this).prev('.card-header')
            .find('.btn')
            .removeClass('text-dark')
            .addClass('text-white');
    });

    btnChooseFileToExport.on('click', function () {
        console.log('clicked');
        chooseExportReportModal.modal('show');
    });

    exportSkillMatrix.on('click', function(){
        console.log('exportClicked');
         window.location.href = 'export_skill_map_pdf';
    });





});




