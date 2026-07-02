

$(document).ready(function() {
    const directEmployee = $('#tblDirectEmployees');
    const subconEmployee = $('#tblSubconEmployees');

    const directEmployeeTable = directEmployee.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get_direct_employees',
            type: 'GET',
        },
        columns: [
            { data: 'action', name: 'action', orderable:false, searchable:false },
            // { data: 'status', name: 'status' },
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
            // { data: 'status', name: 'status' },
            { data: 'EmpNo', name: 'EmpNo' },
            { data: 'EmpName', name: 'EmpName' },
            { data: 'DateHired', name: 'DateHired' },
            { data: 'Position', name: 'Position' },
            { data: 'Section', name: 'Section' }
        ]
    });

});




