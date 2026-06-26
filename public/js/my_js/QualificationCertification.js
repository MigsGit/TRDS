const getDivDeptSec = (params) => {
    let data = {
    };

    call_ajax(data,'get_div_dept_sec',function(response){
        console.log(response);

        let paramsGetSelect2Value = {
            comboId : params.comboId,
            dataValue : response['section']
        }
        fnGetSelect2Value(paramsGetSelect2Value)
    });

}
const getDropdownMasterDetailsByFkid = (params) => {
    let data = {
        dropdown_masters_id : params.dropdownMastersId
    };

    call_ajax(data,'get_dropdown_master_details_by_fkid',function(response){
console.log('dsds',response['dropdown_masters_details']);

        let paramsGetSelect2Value = {
            comboId : params.comboId,
            dataValue : response['data']
        }
        fnGetSelect2Value(paramsGetSelect2Value)
    });

}


function fnGetSelect2Value(params){
    // $('#formEditSa select[name="select_checked_by_qc[]"]'.select2({

    params.comboId.select2({
        data : params.dataValue,
        theme: 'bootstrap-5',
    });
    var arrValue = [];
    $.each(params.dataValue, function(key, value){
        arrValue.push(value)
    });

    params.comboId.val(arrValue).trigger('change');
}

/* =========================================================
   Operator Employee Modal — Add Multiple Employees to Table
   ========================================================= */

// In-memory array that holds employees staged in the modal
operEmpArray = [];

/**
 * Initialise the modal:
 *  - (re-)attach select2 to the employee combo
 *  - clear the staging table & array
 */
function initOperEmpModal() {
    getSystemOneEmployeeDetails($('#text_oper_emp_number'));
    operEmpArray = [];
    $('#tbl_oper_add_emp tbody').empty();
    $('#text_oper_emp_number').val(null).trigger('change');
    $('#text_oper_station_from').val(null).trigger('change');
    $('#text_oper_station_to').val(null).trigger('change');
}

/**
 * Add the currently selected employee + stations as one row
 * in the staging table.
 */
function addOperEmpToTable() {
    const $empSelect    = $('#text_oper_emp_number');
    const $stationFrom  = $('#text_oper_station_from');
    const $stationTo    = $('#text_oper_station_to');

    const empId   = $empSelect.val();
    const empName = $empSelect.find('option:selected').text().trim();
    const stFrom  = $stationFrom.val();
    const stFromText = $stationFrom.find('option:selected').text().trim();
    const stTo    = $stationTo.val();
    const stToText = $stationTo.find('option:selected').text().trim();

    // --- validation ---
    if (!empId) {
        Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please select an Employee.' });
        return;
    }
    if (!stFrom) {
        Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please select Station (From).' });
        return;
    }
    if (!stTo) {
        Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please select Station (To).' });
        return;
    }

    // Prevent duplicates inside the staging table
    const alreadyStaged = operEmpArray.some(e => e.empId === empId);
    if (alreadyStaged) {
        Swal.fire({ icon: 'info', title: 'Duplicate', text: 'This employee is already in the list.' });
        return;
    }

    // Push to in-memory array
    const entry = { empId, empName, stFrom, stFromText, stTo, stToText };
    operEmpArray.push(entry);
    console.log('operEmpArray',operEmpArray);

    // Append row to staging table
    const idx = operEmpArray.length - 1;
    const row = `<tr data-index="${idx}">
        <td>
            <button type="button" class="btn btn-danger btn-sm btnRemoveOperEmpRow"
                    data-index="${idx}">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
        <td>${empId}</td>
        <td>${empName}</td>
        <td>${stFromText}</td>
        <td>${stToText}</td>
    </tr>`;

    $('#tbl_oper_add_emp tbody').append(row);

    // Reset combos for the next entry
    $empSelect.val(null).trigger('change');
    $stationFrom.val(null).trigger('change');
    $stationTo.val(null).trigger('change');
}

/**
 * Move all staged employees into the main certification table
 * (tbl_certified_list_operator) and close the modal.
 */
function addSelectedOperEmpToMain() {
    if (operEmpArray.length === 0) {
        Swal.fire({ icon: 'warning', title: 'No Employees', text: 'Please add at least one employee first.' });
        return;
    }

    operEmpArray.forEach(function(entry) {
        // Prevent the same employee appearing twice in the main table
        let alreadyInMain = false;
        $('#tbl_certified_list_operator tbody tr').each(function() {
            if ($(this).data('empid') === entry.empId) {
                alreadyInMain = true;
            }
        });
        if (alreadyInMain) return;

        const row = `<tr data-empid="${entry.empId}">
            <td>
                <button type="button" class="btn btn-danger btn-sm btnRemoveOperEmpMain">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
            <td>${entry.empId}</td>
            <td>${entry.empName}</td>
            <td data-value="${entry.stFrom}">${entry.stFromText}</td>
            <td data-value="${entry.stTo}">${entry.stToText}</td>
        </tr>`;

        $('#tbl_certified_list_operator tbody').append(row);
    });

    // Close modal and clean up
    $('#select_Employee_operator').modal('hide');
    operEmpArray = [];
    $('#tbl_oper_add_emp tbody').empty();
}

/**
 * Collect the main certification table rows into a plain array
 * (useful when building the payload for form submission).
 */
function getOperEmpTableData() {
    const employees = [];
    $('#tbl_certified_list_operator tbody tr').each(function() {
        employees.push({
            empId    : $(this).find('td:eq(1)').text().trim(),
            empName  : $(this).find('td:eq(2)').text().trim(),
            stFrom   : $(this).find('td:eq(3)').data('value'),
            stTo     : $(this).find('td:eq(4)').data('value'),
        });
    });
    return employees;
}

/* ---- Event bindings ---- */

// Open modal → reinitialise
$(document).on('show.bs.modal', '#select_Employee_operator', function () {
    initOperEmpModal();
});

// "Add to Table" button inside the modal
$(document).on('click', '#btnAddOPEREmp', function () {
    addOperEmpToTable();
});

// Remove row from staging table
$(document).on('click', '.btnRemoveOperEmpRow', function () {
    const idx = $(this).data('index');
    operEmpArray.splice(idx, 1);
    $(this).closest('tr').remove();
    // Re-index remaining rows
    $('#tbl_oper_add_emp tbody tr').each(function(i) {
        $(this).attr('data-index', i);
        $(this).find('.btnRemoveOperEmpRow').attr('data-index', i);
    });
});

// "Add" button in modal footer → move to main table
$(document).on('click', '#btnAddSelectedOPEREmp', function () {
    addSelectedOperEmpToMain();
});

// Remove row from the main certification table
$(document).on('click', '.btnRemoveOperEmpMain', function () {
    $(this).closest('tr').remove();
});

