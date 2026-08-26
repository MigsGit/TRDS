

$(document).ready(function() {
    const directEmployee = $('#tblDirectEmployees');
    const subconEmployee = $('#tblSubconEmployees');
    const updateEmpInfoModalId = $('#updateEmpInfoModalId');
    const viewEmpInfoModalId = $('#viewEmpInfoModalId');
    const btnChooseFileToExport = $('#btnChooseFileToExport');
    const chooseExportReportModal = $('#chooseExportReportId');
    const exportSkillMatrix = $('#btnGenerateVisualMatrix');
    const modalGenerateSkillMatrixDetails = $('#modalGenerateSkillMatrixDetails');
    const modalGenerateSkillMatrixPerProductLine = $('#modalGenerateSkillMatrixPerProductLine');
    const exportSkillMatrixPerProductLine = $('#btnGenerateSkillMatrixPerProductLine');


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
        chooseExportReportModal.modal('show');
    });

   exportSkillMatrix.on('click', function () {
        let employees = [];

        $('#selectEmployee option:selected').each(function () {
            employees.push({
                empNo: $(this).val(),
                empName: $(this).data('name'),
                dateHired: $(this).data('date-hired')
            });
        });

        // console.log(employees);

        // Pass to export
        const params = new URLSearchParams({
             product_line: $('#selectedProductLine').val(),
             position: $('#selectPosition').val(),
            employees: JSON.stringify(employees)
        });

        // window.location.href = 'export_skill_map_pdf?' + params.toString();
        window.location.href = 'export_skill_map_excel?' + params.toString();
    });

    modalGenerateSkillMatrixDetails.on('show.bs.modal', function (e) {
        getProductLine();
        getEmployeePosition();

        $('#selectEmployee').html(
            '<option value="" selected disabled>Please select Product Line and Position first</option>'
        );

    });

     modalGenerateSkillMatrixPerProductLine.on('show.bs.modal', function (e) {
        getProductLine();
        getEmployeePosition();

        $('#selectedEmployeePerProductLine').html(
            '<option value="" selected disabled>Please select Product Line and Position first</option>'
        );

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

    function getProductLine(){
        $.ajax({
            url: 'get_product_line',
            method: 'GET',
            success: function (response) {
                // const $select = $('#selectedProductLine');
                const $select = $('#selectedProductLine, #selectedPerProductLine');
                $select.empty();
                $select.append('<option value="" disabled selected>Select Product Line</option>');
                $.each(response, function(index, response) {
                    $select.append('<option value="' + response.id + '">' + response.product_line + '</option>');
                });

            },
            error: function (xhr, status, error) {
                console.error('Error fetching product line', error);
            }
        });
    }

    // function getEmployeePosition(){
    //      $.ajax({
    //         url: 'get_employee_position',
    //         method: 'GET',
    //         success: function (response) {
    //             const $select = $('#selectPosition , #selectedPerPosition');
    //             $select.empty();
    //             $select.append('<option value="" disabled selected>Select Position</option>');
    //             $.each(response, function(index, pos) {
    //                 $select.append('<option value="' + pos.position_category + '">' + pos.position_category + '</option>');
    //             });

    //         },
    //         error: function (xhr, status, error) {
    //             console.error('Error fetching employee position:', error);
    //         }
    //     });
    // }
    function getEmployeePosition() {
        $.ajax({
            url: 'get_employee_position',
            method: 'GET',
            success: function (response) {
                console.log('response', response)

                const $select = $('#selectPosition, #selectPositionPerProductLine');

                $select.empty();

                // Add default option to both selects
                $select.append(
                    '<option value="" selected disabled>Select Position</option>'
                );

                $.each(response, function(index, pos) {

                    $select.append(
                        '<option value="' + pos.position_category + '">' +
                            pos.position_category +
                        '</option>'
                    );

                });

            },
            error: function (xhr, status, error) {
                console.error('Error fetching employee position:', error);
            }
        });
    }

    function getEmployee() {

    const productLine = $('#selectedProductLine').val();
    const position = $('#selectPosition').val();

    const perProductLine = $('#selectedPerProductLine').val(); // ARRAY
    const perPosition = $('#selectPositionPerProductLine').val();

    let productLines = [];
    let selectedPosition = null;

    // Normal Product Line
    if (productLine && position) {

        productLines = [productLine];
        selectedPosition = position;

    // Multiple Product Lines
    } else if (perProductLine && perProductLine.length > 0 && perPosition) {

        productLines = perProductLine;
        selectedPosition = perPosition;

    } else {

        $('#selectEmployee')
            .html(`
                <option value="" selected disabled>
                    Please select Product Line and Position first
                </option>
            `)
            .trigger('change');

        return;
    }

    $.ajax({
        url: 'get_employees',
        method: 'GET',
        data: {
            product_line: productLines,
            position: selectedPosition
        },

        success: function (response) {

            let options = '';

            if (response.length === 0) {

                options = `
                    <option value="" selected disabled>
                        No employees found
                    </option>
                `;

            } else {

                options += `
                    <option value="all">
                        Select All Employees
                    </option>
                `;

                $.each(response, function (index, employee) {

                    options += `
                        <option value="${employee.EmpNo}|${employee.EmpName}|${employee.dateHired}">
                            ${employee.EmpNo} - ${employee.EmpName}
                        </option>
                    `;

                });
            }

            $('#selectEmployee, #selectedEmployeePerProductLine')
                .html(options)
                .trigger('change');
        },

        error: function (xhr, status, error) {
            console.error('Error fetching employees:', error);
        }
    });
}

    $('#selectedProductLine, #selectPosition, #selectedPerProductLine, #selectPositionPerProductLine').on('change', function () {
        const productLine = $('#selectedProductLine').val();
        const perProductLine = $('#selectedPerProductLine').val();

        const position = $('#selectPosition').val();
        const perPosition = $('#selectPositionPerProductLine').val();

        if (productLine && position) {

            getEmployee(productLine, position);

        } else if (perProductLine && perPosition) {

            getEmployee(perProductLine, perPosition);

        } else {

            $('#selectEmployee').html(
                '<option value="" selected disabled>' +
                'Please select Product Line and Position first' +
                '</option>'
            ).trigger('change');

        }

    });

    const selected = $('#selectEmployee option:selected');

    const empNo = selected.val();
    const empName = selected.data('name');
    const dateHired = selected.data('date-hired');

    $('#selectEmployee').on('change', function () {

        let selected = $(this).val() || [];

        // Select All + employee selected
        if (selected.includes('all') && selected.length > 1) {

            // Get the last selected option
            const lastSelected = $(this)
                .find('option:selected')
                .last()
                .val();

            if (lastSelected === 'all') {

                // Select All was clicked last
                $(this).val(['all']);

            } else {

                // Employee was clicked
                // Remove Select All
                selected = selected.filter(value => value !== 'all');

                $(this).val(selected);
            }

            // Update Select2 if you're using Select2
            $(this).trigger('change.select2');
        }
    });

    //pdf
    // exportSkillMatrix.on('click', function () {

    //     let employees = [];

    //     const selectedOptions = $('#selectEmployee option:selected');

    //     // Check if "Select All Employees" is selected
    //     const selectAll = selectedOptions.filter(function () {
    //         return $(this).val() === 'all';
    //     }).length > 0;

    //     if (selectAll) {

    //         // Get ALL employees currently loaded in the dropdown
    //         $('#selectEmployee option').each(function () {

    //             if ($(this).val() === 'all') {
    //                 return;
    //             }

    //             const details = $(this).val().split('|');

    //             employees.push({
    //                 empNo: details[0] || '',
    //                 empName: details[1] || '',
    //                 dateHired: details[2] || ''
    //             });
    //         });

    //     } else {

    //         // Get only selected employees
    //         selectedOptions.each(function () {

    //             const details = $(this).val().split('|');

    //             employees.push({
    //                 empNo: details[0] || '',
    //                 empName: details[1] || '',
    //                 dateHired: details[2] || ''
    //             });
    //         });
    //     }

    //     // console.log('Employees for export:', employees);

    //     if (employees.length === 0) {
    //         alert('Please select at least one employee.');
    //         return;
    //     }

    //     const params = new URLSearchParams({
    //         product_line: $('#selectedProductLine').val(),
    //         position: $('#selectPosition').val(),
    //         employees: JSON.stringify(employees)
    //     });

    //     window.location.href = 'export_skill_map_excel?' + params.toString();
    //     modalGenerateSkillMatrixDetails.modal('hide');
    // });

    exportSkillMatrix.on('click', function () {

        let employees = [];

        const selectedOptions = $('#selectEmployee option:selected');

        const selectAll = selectedOptions.filter(function () {
            return $(this).val() === 'all';
        }).length > 0;

        if (selectAll) {

            $('#selectEmployee option').each(function () {

                if ($(this).val() === 'all') {
                    return;
                }

                const details = $(this).val().split('|');

                employees.push({
                    empNo: details[0] || '',
                    empName: details[1] || '',
                    dateHired: details[2] || ''
                });
            });

        } else {

            selectedOptions.each(function () {

                const details = $(this).val().split('|');

                employees.push({
                    empNo: details[0] || '',
                    empName: details[1] || '',
                    dateHired: details[2] || ''
                });
            });
        }

        if (employees.length === 0) {
            alert('Please select at least one employee.');
            return;
        }

        // Create POST form
        const form = $('<form>', {
            method: 'POST',
            action: 'export_skill_map_excel'
        });

        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'product_line',
            value: $('#selectedProductLine').val()
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'position',
            value: $('#selectPosition').val()
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'employees',
            value: JSON.stringify(employees)
        }));

        $('body').append(form);

        form.submit();
    });

    exportSkillMatrixPerProductLine.on('click', function () {

        let employees = [];

        const selectedOptions = $('#selectedEmployeePerProductLine option:selected');

        const selectAll = selectedOptions.filter(function () {
            return $(this).val() === 'all';
        }).length > 0;

        if (selectAll) {

            $('#selectedEmployeePerProductLine option').each(function () {

                if ($(this).val() === 'all') {
                    return;
                }

                const details = $(this).val().split('|');

                employees.push({
                    empNo: details[0] || '',
                    empName: details[1] || '',
                    dateHired: details[2] || ''
                });
            });

        } else {

            selectedOptions.each(function () {

                const details = $(this).val().split('|');

                employees.push({
                    empNo: details[0] || '',
                    empName: details[1] || '',
                    dateHired: details[2] || ''
                });
            });
        }

        if (employees.length === 0) {
            alert('Please select at least one employee.');
            return;
        }

        // Create POST form
        const form = $('<form>', {
            method: 'POST',
            action: 'export_skill_map_excel_per_product_line'
        });

        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'product_line',
            value: $('#selectedPerProductLine').val()
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'position',
            value: $('#selectPositionPerProductLine').val()
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'employees',
            value: JSON.stringify(employees)
        }));

        $('body').append(form);

        form.submit();
    });

});




