const getDivDeptSec = (params) => {
    let data = {
    };

    call_ajax(data,'get_div_dept_sec',function(response){

        let paramsGetSelect2Value = {
            comboId : params.comboId,
            dataValue : response['section']
        }
        fnGetSelect2Value(paramsGetSelect2Value)
    });
}
const selectPassFail = (comboId) => {
    let data = [
            { id : 'PASSED', text : 'PASSED' },
            { id : 'FAILED', text : 'FAILED' }  ,
    ]
    let paramsGetSelect2Value = {
        comboId : comboId,
        dataValue : data
    }

    fnGetSelect2Value(paramsGetSelect2Value)
}

const initDropdownMasterDetailsByFkidCombos = (comboSelectors,dropdownMastersId,specificValues ={}) => {
    comboSelectors.forEach(function(selector) {
            getDropdownMasterDetailsByFkid({
                comboId: $(selector),
                dropdownMastersId: dropdownMastersId,
                selectedValues : (specificValues && specificValues[selector]) ? specificValues[selector] : null
            });
    });
    
    console.log('11',specificValues);
    
}
const getDropdownMasterDetailsByFkid = (params) => {
    let data = {
        dropdown_masters_id : params.dropdownMastersId
    };

    call_ajax(data,'get_dropdown_master_details_by_fkid',function(response){
        // return;
        let paramsGetSelect2Value = {
            comboId : params.comboId,
            dataValue : response['data'],
            selectedValues : params.selectedValues ?? []
        }

        fnGetSelect2Value2(paramsGetSelect2Value)
    });

}

const fnGetSelect2Value2 = (params) => {
    if (params.comboId.hasClass("select2-hidden-accessible")) {
        params.comboId.select2('destroy').empty();
    } else {
        params.comboId.empty();
    }

    const mappedSelect2Data = $.map(params.dataValue || [], function(obj) {
        let displayId = obj.id !== undefined ? String(obj.id) : '';
        
        let displayText = '';
        if (typeof obj === 'object') {
            // Priority list of known column names
            displayText = obj.dropdown_masters_details || obj.dropdown_master_details || obj.text || obj.value;
            
            if (!displayText) {
                for (let key in obj) {
                    if (key !== 'id' && typeof obj[key] === 'string' && obj[key].trim().length > 0) {
                        displayText = obj[key];
                        break;
                    }
                }
            }
        } else if (typeof obj === 'string') {
            return { id: obj, text: obj };
        }

        return {
            id: displayId,
            text: displayText || 'Missing Label'
        };
    });


    // 4. Initialize Select2
    params.comboId.select2({
        data: mappedSelect2Data, 
        theme: 'bootstrap-5',
        multiple: true,
        placeholder: 'Select Reason...',
        allowClear: true
    });

    // 5. Force the selected pills into view
    if (params.selectedValues && Array.isArray(params.selectedValues)) {
        const cleanStringIds = params.selectedValues.map(id => String(id).trim());
        params.comboId.val(cleanStringIds).trigger('change');
    } else {
        params.comboId.val(null).trigger('change');
    }
}

const fnGetSelect2Value23 = (params) => {
    // 1. Reset dropdown completely to clear out old instances and cached data
    if (params.comboId.hasClass("select2-hidden-accessible")) {
        params.comboId.select2('destroy').empty();
    } else {
        params.comboId.empty();
    }

    // 2. Remap the database array columns explicitly to 'id' and 'text'
    // CRITICAL: We enforce String(obj.id) so the data types match the val() array exactly!
    const mappedSelect2Data = $.map(params.dataValue, function(obj) {
        return {
            id: String(obj.id), 
            text: obj.dropdown_masters_details
        };
    });

    // 3. Render dataset structures with a clean placeholder configuration
    params.comboId.select2({
        data: mappedSelect2Data, 
        theme: 'bootstrap-5',
        multiple: true,
        placeholder: 'Select Reason...',
        allowClear: true
    });

    // 4. Monitor active value updates
    params.comboId.off('change').on('change', function() {
        let selectedObjects = $(this).select2('data');
        let extractedData = selectedObjects.map(function(item) {
            return { id: item.id, text: item.text };
        });
    });

    // 5. Set pre-selected data layers cleanly
    if (params.selectedValues && Array.isArray(params.selectedValues)) {
        // Enforce all IDs to be clean strings to match our mappedSelect2Data IDs
        const cleanStringIds = params.selectedValues.map(id => String(id).trim());
        
        // Pass the string array and trigger the change event
        params.comboId.val(cleanStringIds).trigger('change');
    } else {
        params.comboId.val(null).trigger('change');
    }
}

const fnGetSelect2Value21 = (params) => {
    // 1. Reset dropdown components completely to avoid memory leak duplication bugs
    // if (params.comboId.hasClass("select2-hidden-accessible")) {
    //     params.comboId.select2('destroy').empty();
    // }

    // 2. Render dataset mapping structures
    params.comboId.select2({
        data: params.dataValue,
        theme: 'bootstrap-5',
        multiple: true
    });

    // 3. Monitor active value updates
    params.comboId.off('change').on('change', function() {
        let selectedObjects = $(this).select2('data');
        let extractedData = selectedObjects.map(function(item) {
            return { id: item.id, text: item.text };
        });
    });

    // 4. Set pre-selected data layers cleanly
    if (params.selectedValues && Array.isArray(params.selectedValues)) {
        const cleanStringIds = params.selectedValues.map(id => String(id));
        params.comboId.val(cleanStringIds).trigger('change');
    } else {
        params.comboId.val(null).trigger('change');
    }
}

const fnGetSelect2Value = (params) =>  {
    // 1. Initialize the Select2 dropdown with your AJAX data source
    params.comboId.select2({
        data : params.dataValue,
        theme: 'bootstrap-5',
    });

    // 2. Set up a listener for changes to cleanly pull the entire object data matrix
    params.comboId.on('change', function() {
        // .select2('data') returns an array of selected option objects [{id: "...", text: "...", ...}]
        let selectedObjects = $(this).select2('data');

        let extractedData = selectedObjects.map(function(item) {
            return {
                id: item.id,     // The underlying database primary key/value
                text: item.text  // The string display description/label
            };
        });

        // You can now store 'extractedData' to your global queue variables or use it directly!
    });

    // If your backend payload contains pre-selected values, map their keys to set them:
    var arrValue = [];
    $.each(params.dataValue, function(key, value){
        arrValue.push(value.id); // Push the ID to pre-select it
    });

    // Un-comment this line if you want the dropdown to auto-select items arriving from AJAX:
    // params.comboId.val(arrValue).trigger('change');
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
const initOperEmpModal = () => {
    getSystemOneEmployeeDetails($('#text_oper_emp_number'));
    operEmpArray = [];
    $('#tbl_oper_add_emp tbody').empty();
    $('#text_oper_emp_number').val(null).trigger('change');
    $('#text_oper_station_from').val(null).trigger('change');
    $('#text_oper_station_to').val(null).trigger('change');
}
const initDivDeptSecCombos = (comboSelectors) => {
    comboSelectors.forEach(function(selector) {
            getDivDeptSec({ comboId: $(selector) });
    });
}

/**
 * Add the currently selected employee + stations as one row
 * in the staging table.
 */
const addOperEmpToTable = () => {
    const $empSelect    = $('#text_oper_emp_number');
    const $stationFrom  = $('#text_oper_station_from');
    const $stationTo    = $('#text_oper_station_to');
    const $optRemarks    = $('#text_oper_remarks');

    const empId   = $empSelect.val();
    const empName = $empSelect.find('option:selected').text().trim();
    const stFrom  = $stationFrom.val();
    const stFromText = $stationFrom.find('option:selected').text().trim();
    const stTo    = $stationTo.val();
    const stToText = $stationTo.find('option:selected').text().trim();
    const optRemarks = $optRemarks.val();

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
    const entry = { empId, empName, stFrom, stFromText, stTo, stToText, optRemarks };
    operEmpArray.push(entry);

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
        <td>${optRemarks}</td>
    </tr>`;

    $('#tbl_oper_add_emp tbody').append(row);

    // Reset combos for the next entry
    $empSelect.val(null).trigger('change');
    // $stationFrom.val(null).trigger('change');
    // $stationFrom.val(null).trigger('change');
    // $stationTo.val(null).trigger('change');
}

/**
 * Move all staged employees into the main certification table
 * (tbl_certified_list_operator) and close the modal.
 */
const addSelectedOperEmpToMain = () => {
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
            <td>${entry.optRemarks}</td>
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
const getOperEmpTableData = () => {
    const employees = [];
    $('#tbl_certified_list_operator tbody tr').each(function() {
        employees.push({
            empId    : $(this).find('td:eq(1)').text().trim(),
            empName  : $(this).find('td:eq(2)').text().trim(),
            stFrom   : $(this).find('td:eq(3)').data('value'),
            stTo     : $(this).find('td:eq(4)').data('value'),
            optRemarks     : $(this).find('td:eq(5)').text().trim(),
        });
    });
    return employees;
}

/* ---- Edit ---- */

/**
 * Best Practice: Populates the main table and synchronizes state during an Edit AJAX request.
 * @param {Array} qcSlipEmployees - The response payload array containing employee details
 */
const populateEditOperEmpTable = (qcSlipEmployees) => {
    // 1. Clear the main table to prevent old leftovers
    const $mainTableBody = $('#tbl_certified_list_operator tbody');
    $mainTableBody.empty();

    // 2. Loop through your incoming collection using jQuery $.each
    $.each(qcSlipEmployees, function(index, emp) {
        
        // Match your application's data-attribute structure
        const empId = emp.employee_no;
        const empName = emp.employee_name || empId; // Use fallback if name is in relation
        const stFrom = emp.station_from;
        const stTo = emp.station_to;
        const remarks = emp.remarks || '';
        
        // Keep your text display dynamic (e.g., convert "1" to "Station 1" if needed, or use the database value)
        const stFromText = "Station " + stFrom; 
        const stToText = "Station " + stTo;

        // 3. Build your standard row HTML precisely matching your creation architecture
        // CRITICAL: We append data attributes so your "remove" and "get data" functions work perfectly
        const row = `
            <tr data-empid="${empId}">
                <td>
                    <button type="button" class="btn btn-danger btn-sm btnRemoveOperEmpMain">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
                <td>${empId}</td>
                <td>${empName}</td>
                <td data-value="${stFrom}">${stFromText}</td>
                <td data-value="${stTo}">${stToText}</td>
                <td>${remarks}</td>
            </tr>
        `;

        $mainTableBody.append(row);
    });
};


/* ---- Event bindings ---- */

// Open modal → reinitialise
$(document).on('show.bs.modal', '#select_Employee_operator', () => {
    initOperEmpModal();
});

// "Add to Table" button inside the modal
$(document).on('click', '#btnAddOPEREmp', () => {
    addOperEmpToTable();
});

// Remove row from staging table
$(document).on('click', '.btnRemoveOperEmpRow', () => {
    idx = $(this).data('index');
    operEmpArray.splice(idx, 1);
    $(this).closest('tr').remove();
    // Re-index remaining rows
    $('#tbl_oper_add_emp tbody tr').each(function(i) {
        $(this).attr('data-index', i);
        $(this).find('.btnRemoveOperEmpRow').attr('data-index', i);
    });
});

// "Add" button in modal footer → move to main table
$(document).on('click', '#btnAddSelectedOPEREmp', ()=> {
    addSelectedOperEmpToMain();
});

// Remove row from the main certification table
$(document).on('click', '.btnRemoveOperEmpMain', () => {
    $(this).closest('tr').remove();
});

