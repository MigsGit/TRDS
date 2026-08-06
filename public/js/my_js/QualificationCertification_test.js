$(document).ready(function () {
    /**
     * Synchronizes a group of checkboxes with a piped string value from the database.
     * @param {string} nameAttribute - The HTML name attribute of the checkbox group.
     * @param {string} rawDbValue - The piped string from your database (e.g., "Visual | Assembly").
     */
    function syncCheckboxesWithDb(nameAttribute, rawDbValue) {
        // 1. Explode and clean your database values into an array of clean strings
        // If the database value is null/empty, fall back to an empty array
        const activeValues = rawDbValue
            ? rawDbValue.split('|').map(item => item.trim())
            : [];

        // 2. Query all checkboxes with that specific name attribute inside your form container
        form.formSubmitOper.find(`input[type="checkbox"][name="${nameAttribute}"]`).each(function() {
            const checkbox = $(this);
            const checkboxValue = checkbox.val(); // e.g., "Visual", "Assembly", "Others"

            // 3. Set prop to true if it matches, false if it does not.
            // This automatically handles BOTH checking and unchecking!
            checkbox.prop('checked', activeValues.includes(checkboxValue));
        });
    }
    // ==========================================
    // HOW TO EXECUTE IT FOR YOUR TWO TABLES:
    // ==========================================

    const getApprovalStatusToggle = (params) => {
        let approvalStatus = params.approval_status;
            $('.btn-link').removeClass('show');
            $('#collapseOneOper').removeClass('show');
            $('.operSave').removeClass('d-none');
            $('.operApproved').addClass('d-none');
            if(approvalStatus ==='APRODTO'){
                $('#collapseOneOper').addClass('show');
            }
            if(approvalStatus ==='BENGGTQ'){
                $('#collapseTwoOper').addClass('show');
            }
            if(approvalStatus ==='CQCC'){
                $('#collapseThreeOper').addClass('show');

            }
            if(approvalStatus ==='DPPDONLY'){
                $('#collapseFourOper').addClass('show');
            }
            if(approvalStatus ==='ENGVP'){
                $('#collapseFiveOper').addClass('show');
            }
            if(approvalStatus ==='EQCVP'){
                $('#collapseSixOper').addClass('show');
            }
            if(approvalStatus ==='FQCVVO'){
                $('#collapseSevenOper').addClass('show');

            }
            if(approvalStatus ==='QCAPP'){
                // $('#operDisapproved').removeClass('d-none');
                // $('#operApproved').removeClass('d-none');
                $('.operSave').addClass('d-none');
                $('.operApproved').removeClass('d-none');

            }
            if(approvalStatus ==='OK'){
                $('#operDisapproved').addClass('d-none');
                $('#operApproved').addClass('d-none');
                $('#operClosed').addClass('d-none');
                $('#operSave').addClass('d-none');
            }
    }
    const saveFirstTakeInsSequence = (params) =>{
        let data = {
            qcSlipsId : params.qcSlipsId,
            qcSlipEmployeesId : params.QcSlipEmployeesId,
            category : params.category,
            value : params.value,
        }
        call_ajax_serialize(data, {}, 'save_first_take_ins_sequence', function (response) {
            if (response && response.is_success === 'true') {
                // dataTable.fvi_operator.ajax.reload(null, false);
            } else {
                // alert((response && response.message) ? response.message : 'Failed to save. Please try again.');
            }
        });
    }

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
    }
    const getDropdownMasterDetailsByFkid = (params) => {
        let data = {
            dropdown_masters_id : params.dropdownMastersId
        };

        call_ajax(data,'get_dropdown_master_details_by_fkid',function(response){
            let paramsGetSelect2Value = {
                comboId : params.comboId,
                dataValue : response['data'],
                selectedValues : params.selectedValues ?? []
            }
            fnGetSelect2ValueMultiple(paramsGetSelect2Value)
        });

    }

    // const initGetSystemOneEmployeeDetailsCombos = (comboSelectors) => {
    //     comboSelectors.forEach(function(selector) {
    //             getSystemOneEmployeeDetails($(selector));
    //     });
    // }
    /**
     * Initialize multiple AJAX employee dropdowns with optional pre-selected user objects
    */
    const initGetSystemOneEmployeeDetailsCombos = (comboSelectors, selectedValuesMap = {}) => {
        console.log('selectedValuesMap', selectedValuesMap);

        comboSelectors.forEach(function(selector) {
            // Fetch the pre-selected employee objects specifically for this selector
            const preSelectedEmployees = selectedValuesMap[selector] || []; // Expected array: [{id: 'R152', text: 'John Doe'}, ...]

            getSystemOneEmployeeDetails($(selector), preSelectedEmployees);
        });
    }
      /**
     * Setup Select2 AJAX search AND append pre-selected options if they exist
     */
    const getSystemOneEmployeeDetails = (comboId, preSelectedEmployees = []) => {
        // Reset element completely to avoid initialization memory leaks
        if (comboId.hasClass("select2-hidden-accessible")) {
            comboId.select2('destroy').empty();
        } else {
            comboId.empty();
        }

        // Initialize Select2 with dynamic AJAX configuration
        comboId.select2({
            theme: 'bootstrap-5',
            placeholder: 'Search Employee Name or ID...',
            minimumInputLength: 2,
            multiple: true, // Crucial for handling array selections
            ajax: {
                url: "get_system_one_employee_details",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        // 💡 THE FIX: If we have pre-selected values, append their DOM options, select them, and trigger change
        if (preSelectedEmployees && preSelectedEmployees.length > 0) {
            const selectedIds = [];

            preSelectedEmployees.forEach(function(employee) {
                // Only proceed if employee object has both id and text properties
                if (employee.id && employee.text) {
                    const empId = String(employee.id).trim();
                    selectedIds.push(empId);

                    // Create the option element if it doesn't exist in the HTML structure yet
                    if (comboId.find("option[value='" + empId + "']").length === 0) {
                        const newOption = new Option(employee.text, empId, true, true);
                        comboId.append(newOption);
                    }
                }
            });

            // Set the selected array and trigger change so Select2 renders the tag pills immediately!
            comboId.val(selectedIds).trigger('change');
        } else {
            comboId.val(null).trigger('change');
        }
    }

    //EmpNo only
    const initGetSystemOneEmployeeDetailsCombosTest = (comboSelectors, selectedValuesMap = {}) => {
    console.log('selectedValuesMap', selectedValuesMap);

    comboSelectors.forEach(function(selector) {
        // Fetch raw input array (Can be simple string IDs like ["R152", "R131"] or objects [{id, text}])
        const rawInputData = selectedValuesMap[selector] || [];

        // Normalize the array to ensure it consists of clean Select2 structures {id, text}
        const preSelectedEmployees = rawInputData.map(function(item) {
            if (typeof item === 'object' && item !== null) {
                return {
                    id: String(item.id || item.value || '').trim(),
                    text: String(item.text || item.name || item.id || '').trim()
                };
            }

            // If it's a flat string ID (e.g., "R152"), fallback safely by using it as both ID and text
            // until the user triggers a remote server search query.
            let cleanId = String(item).trim();
            return {
                id: cleanId,
                text: cleanId
            };
        });

        getSystemOneEmployeeDetails($(selector), preSelectedEmployees);
    });
}

    const getSystemOneEmployeeDetailsTest  = (comboId, preSelectedEmployees = []) => {
        // Reset element completely to avoid initialization memory leaks
        if (comboId.hasClass("select2-hidden-accessible")) {
            comboId.select2('destroy').empty();
        } else {
            comboId.empty();
        }

        // Initialize Select2 with dynamic AJAX configuration
        comboId.select2({
            theme: 'bootstrap-5',
            placeholder: 'Search Employee Name or ID...',
            minimumInputLength: 2,
            multiple: true, // Crucial for handling array selections
            ajax: {
                url: "get_system_one_employee_details",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results, // Backend must format this to match: [{id: "R152", text: "Abdul"}]
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        // If we have pre-selected values, append their DOM options, select them, and trigger change
        if (preSelectedEmployees && preSelectedEmployees.length > 0) {
            const selectedIds = [];

            preSelectedEmployees.forEach(function(employee) {
                if (employee.id && employee.text) {
                    const empId = String(employee.id).trim();
                    selectedIds.push(empId);

                    // Create the option element if it doesn't exist in the HTML structure yet
                    if (comboId.find("option[value='" + empId + "']").length === 0) {
                        const newOption = new Option(employee.text, empId, true, true);
                        comboId.append(newOption);
                    }
                }
            });

            // Set the selected array and trigger change so Select2 renders the tag pills immediately!
            comboId.val(selectedIds).trigger('change');
        } else {
            comboId.val(null).trigger('change');
        }
    }

    const fnGetSelect2ValueMultiple = (params) => {
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
    const fnGetSelect2Value = (params) =>  {
        params.comboId.select2({
            data : params.dataValue,
            theme: 'bootstrap-5',
        });
        params.comboId.on('change', function() {
            let selectedObjects = $(this).select2('data');

            let extractedData = selectedObjects.map(function(item) {
                return {
                    id: item.id,
                    text: item.text
                };
            });

        });
        var arrValue = [];
        $.each(params.dataValue, function(key, value){
            arrValue.push(value.id); // Push the ID to pre-select it
        });
    }

    /* =========================================================
    Operator Employee Modal — Add Multiple Employees to Table
    ========================================================= */

    // In-memory array that holds employees staged in the modal

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
    const addOperEmpToTable = () => { //nmodify
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
        if (empId.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please select an Employee.' });
            return;
        }
        if (stFrom.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please select Station (From).' });
            return;
        }
        if (stTo.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please select Station (To).' });
            return;
        }

        const normalize = (val) => {
            if (val && typeof val === 'object') {
                // Most common cases: String wrapper object, or {value: ...} style object
                if ('value' in val) return String(val.value).trim();
                return String(val).trim(); // falls back to toString(), e.g. new String("R131") -> "R131"
            }
            return String(val ?? '').trim();
        };

        const targetId = normalize(empId);
        const alreadyStaged = operEmpArray.some(e => normalize(e.empId) === targetId);

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
                    <i class="fa-solid fa fa-trash"></i>
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
    }

    /**
     * Move all staged employees into the main certification table
     * (tbl_certified_list_operator) and close the modal.
     */
    const addSelectedOperEmpToMain = () => { //nmodify

        if (operEmpArray.length === 0) {
            Swal.fire({ icon: 'warning', title: 'No Employees', text: 'Please add at least one employee first.' });
            return;
        }

        operEmpArray.forEach(function(entry) {
            // Prevent the same employee appearing twice in the main table
            let alreadyInMain = false;
            const normalize = (val) => {
                if (val && typeof val === 'object') {
                    // Most common cases: String wrapper object, or {value: ...} style object
                    if ('value' in val) return String(val.value).trim();
                    return String(val).trim(); // falls back to toString(), e.g. new String("R131") -> "R131"
                }
                return String(val ?? '').trim();
            };

            const targetId = normalize(entry.empId);
            $('#tbl_certified_list_operator tbody tr').each(function() {
                if ($(this).data('empid') === targetId) {
                    alreadyInMain = true;
                }
            });
            if (alreadyInMain) return;

            const row = `<tr data-empid="${entry.empId}">
                <td>
                    <button type="button" class="btn btn-danger btn-sm btnRemoveOperEmpMain">
                        <i class="fa-solid fa fa-trash"></i>
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

    /**
     * Best Practice: Populates the main table and synchronizes state during an Edit AJAX request.
     * @param {Array} qcSlipEmployees - The response payload array containing employee details
     */
    const populateEditOperEmpTable = (qcSlipEmployees,approvalStatus) => { //nmodify
        // 1. Clear the main table to prevent old leftovers
        const $mainTableBody = $('#tbl_certified_list_operator tbody');
        $mainTableBody.empty();

        // 2. Loop through your incoming collection using jQuery $.each
        $.each(qcSlipEmployees, function(index, emp) {
            // Match your application's data-attribute structure
            const empId = emp.employee_no;
            const empName = emp.system_one_hris_subcon.empname ??''; // Use fallback if name is in relation
            const stFrom = emp.get_station_from?.dropdown_masters_details ??'';
            const stTo = emp.get_station_to?.dropdown_masters_details ?? '';
            const remarks = emp.remarks || '';

            let remove =``;
            if(approvalStatus === 'APRODTO'){
                remove += ` <button type="button" class="btn btn-danger btn-sm btnRemoveOperEmpMain">
                            <i class="fa-solid fa fa-trash"></i>
                        </button>`;
            }

            // 3. Build your standard row HTML precisely matching your creation architecture
            // CRITICAL: We append data attributes so your "remove" and "get data" functions work perfectly
            const row = `
                <tr data-empid="${empId}">
                    <td>
                       ${remove}
                    </td>
                    <td>${empId}</td>
                    <td>${empName}</td>
                    <td data-value="${stFrom}">${stFrom}</td>
                    <td data-value="${stTo}">${stTo}</td>
                    <td>${remarks}</td>
                </tr>
            `;

            $mainTableBody.append(row);
        });
    };

    const getEmployeeDetailsByEmpNoSelect2 = (params) => {
        let response = params.response;
        // Safe access: optional chaining prevents crash if approversCollection is missing from response
        const approversCollection = response?.approversCollection ?? null;
        const rawOperApprovedConfirmedBy = response?.rawOperApprovedConfirmedBy ?? null;


        // Guard: exit early if the entire approvers collection is absent
        if (!approversCollection || typeof approversCollection !== 'object') {
            return;
        }

        const aProd = approversCollection?.APRODTO?.[0] ?? null;
        const eQcvp = approversCollection?.EQCVP?.[0]  ?? null;
        const dPpdOnly = approversCollection?.DPPDONLY?.[0]  ?? null;


        const operApprovedConfirmedBy = rawOperApprovedConfirmedBy   ?? [];
        const aProdToFirst            = aProd?.first_approver_exploded   ?? [];
        const aProdToFirstMentoredBy  = aProd?.first_approver2_exploded  ?? [];
        const aProdToSecond           = aProd?.second_approver_exploded  ?? [];
        const aProdToSecondMentoredBy = aProd?.second_approver2_exploded ?? [];

        const bEnggTqFirst  = approversCollection?.BENGGTQ?.[0]?.first_approver_exploded  ?? [];
        const bEnggTqSecond = approversCollection?.BENGGTQ?.[0]?.second_approver_exploded ?? [];

        const cQcFirst  = approversCollection?.CQCC?.[0]?.first_approver_exploded  ?? [];
        const cQcSecond = approversCollection?.CQCC?.[0]?.second_approver_exploded ?? [];

        const eQcvpToFirst   = eQcvp?.first_approver_exploded   ?? [];
        const eQcvpToFirst2  = eQcvp?.first_approver2_exploded  ?? [];
        const eQcvpToSecond  = eQcvp?.second_approver_exploded  ?? [];
        const eQcvpToSecond2 = eQcvp?.second_approver2_exploded ?? [];

        const dPpdOnlyToFirst   = dPpdOnly?.first_approver_exploded   ?? [];
        const dPpdOnlyToFirst2  = dPpdOnly?.first_approver2_exploded  ?? [];
        const dPpdOnlyToFirst3  = dPpdOnly?.first_approver3_exploded  ?? [];
        const dPpdOnlyToSecond  = dPpdOnly?.second_approver_exploded  ?? [];
        const dPpdOnlyToSecond2 = dPpdOnly?.second_approver2_exploded ?? [];
        const dPpdOnlyToSecond3 = dPpdOnly?.second_approver3_exploded ?? [];

        const eEngvpToFirst   = approversCollection?.EENGVP?.[0]?.first_approver_exploded   ?? [];
        const eEngvpToSecond  = approversCollection?.EENGVP?.[0]?.second_approver_exploded  ?? [];

        const fQcvvoFirst  = approversCollection?.FQCVVO?.[0]?.first_approver_exploded  ?? [];
        const fQcvvoSecond = approversCollection?.FQCVVO?.[0]?.second_approver_exploded ?? [];

        const qCappApprover = approversCollection?.QCAPP?.[0]?.oper_approved_confirmed_by ?? [];

        // 1. Map them to a standard format Select2 expects: {id, text}
        const mappedOperApprovedConfirmedBy = operApprovedConfirmedBy.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedaProdToFirst = aProdToFirst.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedaProdToFirstMentoredBy = aProdToFirstMentoredBy.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedaProdToSecond = aProdToSecond.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedaProdToSecondMentoredBy = aProdToSecondMentoredBy.map(emp => ({ id: emp.id, text: emp.name }));
        console.log('mappedaProdToFirstMentoredBy222',mappedaProdToFirstMentoredBy);

        const mappedbEnggTqFirst= bEnggTqFirst.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedbEnggTqSecond = bEnggTqSecond.map(emp => ({ id: emp.id, text: emp.name }));

        const mappedcQcFirst = cQcFirst.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedcQcSecond = cQcSecond.map(emp => ({ id: emp.id, text: emp.name }));

        const mappeddPpdOnlyToFirst = dPpdOnlyToFirst.map(emp => ({ id: emp.id, text: emp.name }));
        const mappeddPpdOnlyToFirst2 = dPpdOnlyToFirst2.map(emp => ({ id: emp.id, text: emp.name }));
        const mappeddPpdOnlyToSecond = dPpdOnlyToFirst3.map(emp => ({ id: emp.id, text: emp.name }));
        const mappeddPpdOnlyToSecond2 = dPpdOnlyToSecond.map(emp => ({ id: emp.id, text: emp.name }));
        const mappeddPpdOnlyToSecond3 = dPpdOnlyToSecond2.map(emp => ({ id: emp.id, text: emp.name }));
        const mappeddPpdOnlyToSecond4 = dPpdOnlyToSecond3.map(emp => ({ id: emp.id, text: emp.name }));

        const mappedeQcvpToFirst = eQcvpToFirst.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedeQcvpToFirst2 = eQcvpToFirst2.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedeQcvpToSecond = eQcvpToSecond.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedeQcvpToSecond2 = eQcvpToSecond2.map(emp => ({ id: emp.id, text: emp.name }));

        const mappedeEngvpToFirst = eEngvpToFirst.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedeEngvpToSecond = eEngvpToSecond.map(emp => ({ id: emp.id, text: emp.name }));


        const mappedfQcvvoFirst = fQcvvoFirst.map(emp => ({ id: emp.id, text: emp.name }));
        const mappedfQcvvoSecond = fQcvvoSecond.map(emp => ({ id: emp.id, text: emp.name }));

        const mappedqCappApprover = qCappApprover.map(emp => ({ id: emp.id, text: emp.name }));

        // 2. Assign those formatted arrays to their target selectors inside the map
        let editSelectionsMap = {};
        //A
        editSelectionsMap['#text_first_trainedby_oper'] = mappedaProdToFirst;
        editSelectionsMap['#text_first_mentoredby_oper'] = mappedaProdToFirstMentoredBy;
        editSelectionsMap['#text_second_trainedby_oper'] = mappedaProdToSecond;
        editSelectionsMap['#text_second_mentoredby_oper'] = mappedaProdToSecondMentoredBy;

        editSelectionsMap['#text_1st_qualifiedby_es_oper'] = mappedbEnggTqFirst;
        editSelectionsMap['#text_2nd_qualifiedby_es_oper'] = mappedbEnggTqSecond;

        editSelectionsMap['#text_1st_certifiedby_qcs_oper'] = mappedcQcFirst;
        editSelectionsMap['#text_2nd_certifiedby_qcs_oper'] = mappedcQcSecond;
        editSelectionsMap['#text_1st_certified_prod_peqcs_oper'] = mappeddPpdOnlyToFirst;
        editSelectionsMap['#text_1st_certified_eng_peqcs_oper'] = mappeddPpdOnlyToFirst2;
        editSelectionsMap['#text_1st_certified_qc_peqcs_oper'] = mappeddPpdOnlyToSecond;
        editSelectionsMap['#text_2nd_certified_prod_peqcs_oper'] = mappeddPpdOnlyToSecond2;
        editSelectionsMap['#text_2nd_certified_eng_peqcs_oper'] = mappeddPpdOnlyToSecond3;
        editSelectionsMap['#text_2nd_certified_qc_peqcs_oper'] = mappeddPpdOnlyToSecond4;

        editSelectionsMap['#text_1st_validatedby_vpes_oper'] = mappedeEngvpToFirst;
        editSelectionsMap['#text_2nd_validatedby_vpes_oper'] = mappedeEngvpToSecond;

        editSelectionsMap['#text_1st_validatedby_vpqcs_oper'] = mappedeQcvpToFirst;
        editSelectionsMap['#text_1st_validatedby_vpes_oper_2'] = mappedeQcvpToFirst2;
        editSelectionsMap['#text_2nd_validatedby_vpqcs_oper'] = mappedeQcvpToSecond;
        editSelectionsMap['#text_2nd_validatedby_vpes_oper_2'] = mappedeQcvpToSecond2;


        editSelectionsMap['#text_validated1_qcvvo_oper'] = mappedfQcvvoFirst;
        editSelectionsMap['#text_validated2_qcvvo_oper'] = mappedfQcvvoSecond;

        editSelectionsMap['#text_oper_approved_confirmed_by'] = mappedOperApprovedConfirmedBy;


        // 3. Initialize all employee selectors simultaneously
        initGetSystemOneEmployeeDetailsCombos(
            [
                //A
                '#text_first_trainedby_oper',
                '#text_first_mentoredby_oper',
                '#text_second_trainedby_oper',
                '#text_second_mentoredby_oper',
                //B
                '#text_1st_qualifiedby_es_oper',
                '#text_2nd_qualifiedby_es_oper',
                //C
                '#text_1st_certifiedby_qcs_oper',
                '#text_2nd_certifiedby_qcs_oper',
                //D
                '#text_1st_certified_prod_peqcs_oper',
                '#text_1st_certified_eng_peqcs_oper',
                '#text_1st_certified_qc_peqcs_oper',
                '#text_2nd_certified_prod_peqcs_oper',
                '#text_2nd_certified_eng_peqcs_oper',
                '#text_2nd_certified_qc_peqcs_oper',
                //E ENGG
                '#text_1st_validatedby_vpes_oper',
                '#text_2nd_validatedby_vpes_oper',
                //E QC
                '#text_1st_validatedby_vpqcs_oper',
                '#text_1st_validatedby_vpes_oper_2',
                '#text_2nd_validatedby_vpqcs_oper',
                '#text_2nd_validatedby_vpes_oper_2',
                //F
                '#text_validated1_qcvvo_oper',
                '#text_validated2_qcvvo_oper',

                '#text_oper_approved_confirmed_by',
            ],
            editSelectionsMap
        );
    }

    function checkCheckboxesFromColumn(rawString, prefix = 'chk') {
        if (!rawString || rawString.trim() === '') return;

        // Explode by '|' and trim whitespace
        const codes = rawString.split('|').map(item => item.trim());

        codes.forEach(function(code) {
            // Convert "PP-MDGEN-135" to lowercase and swap dashes with underscores -> "pp_mdgen_135"
            let formattedId = code.toLowerCase().replace(/-/g, '_');

            // Target your element (e.g., "#chk_pp_mdgen_135" or "#chk_other_pp_mdgen_135")
            let checkboxSelector = `#${prefix}_${formattedId}`;

            // Find and check the element
            form.formSubmitOper.find(checkboxSelector).prop('checked', true);
        });
    }
    function select2Value(params) {
        // value = "1 | 2" from DB
        const selected = (params.value ?? '')
            .split('|')
            .map(v => v.trim())
            .filter(v => v !== '');

        params.combo.val(selected).trigger('change');
    }

    // ---- Get Data for Edit Operator
    const getQcSlipsById  = (params) => {
        let data = {
            qcSlipsId :  params.qcSlipsId
        }
        call_ajax(data, 'get_qc_slips_by_id', function(response){
            let data = response.qcSlip;
            let qcSlipEmployeeData = response.qcSlipEmployee;
            let qcReasonCertification = data.qc_reason_certification;
            let aOperProdTrainingOrientation = data.a_oper_prod_training_orientation;
            let bOpEnggSectionTrainingOrientation = data.b_op_engg_section_training_orientation;
            let cQcCertification = data.c_qc_certification;
            let opApprovers = data.op_approvers;
            form.formSubmitOper.find('.form-control, .form-select').removeClass('is-invalid is-valid').attr('title', '');
            const approvalStatus = data.appproval_status ?? '';
            populateEditOperEmpTable(data.qc_slip_employees,approvalStatus);

            $('#btnEmployeeOperator').prop('disabled', approvalStatus === 'APRODTO' ? false : true);
            dataTable.fvi_operator.ajax.url(`load1st_qc_validation?qcSlipsId=${data.id} `).draw();
            dataTable.tbl_fvi_operator_2.ajax.url(`load2nd_qc_validation?qcSlipsId=${data.id} `).draw();
            let currentStatus = data.approval_status ??'';

            $('#operDisapproved').addClass('d-none');
            $('#operApproved').addClass('d-none');
            $('#operClosed').removeClass('d-none');
            $('#operSave').removeClass('d-none');
            $('#approval_status').val(currentStatus);

            // ==== Toggle Collapse based on approval status
            getApprovalStatusToggle({
                approval_status: currentStatus,
             });
            // ==== Get All Approvers / Validated by/ Mentored by
            let paramsGetEmpNo = {
                response : response,
            };
            getEmployeeDetailsByEmpNoSelect2(paramsGetEmpNo);

            // ==== QC Slip

            form.formSubmitOper.find('#qc_slips_id').val(data.id);
            form.formSubmitOper.find('#textconno_new_operator').val(data.control_no);
            $('#text_select_position').val(data.position_category);
            form.formSubmitOper.find('#text_section_operator').val(data.section)
            form.formSubmitOper.find('#text_series_operator').val(data.series_name);
            $('#text_select_position').val(data.position_category).trigger('change');
            $('#select_section').val(data.section_category).trigger('change');

            const arrProductLine = Array.isArray(data.product_line) ? data.product_line : [data.product_line];
            const productLine = '#text_operator_product_line';
            let editSelectionsMap2 = {};
            editSelectionsMap2[productLine] = arrProductLine;
            initDropdownMasterDetailsByFkidCombos(
                [productLine],
                2,
                editSelectionsMap2
            );
            // ==== QC Reason Certification
            const textCertificationOperator = '#text_certification_operator';
            let editSelectionsMap3 = {};
            editSelectionsMap3[textCertificationOperator] = response.rawReasonsStringCollection;
            initDropdownMasterDetailsByFkidCombos(
                [textCertificationOperator],
                3,
                editSelectionsMap3
            );
            // ==== Flexibility
            const transferFlexibility = '#transfer_flexibility';
            let editSelectionsMap6 = {};
            editSelectionsMap6[transferFlexibility] = response.rawReasonsStringCollection;
            initDropdownMasterDetailsByFkidCombos(
                [transferFlexibility],
                6,
                editSelectionsMap6
            );
            // ==== A PROD
            // Safe access: optional chaining prevents "Cannot read properties of undefined" if API shape changes
            const aProdData = response?.approversCollection?.APRODTO?.[0] ?? null;
            const arraProdTrainingItems = Array.isArray(response.rawAOperProdTrainingOrientationCollection) ? response.rawAOperProdTrainingOrientationCollection : [response.rawAOperProdTrainingOrientationCollection];
            const aProdTrainingItems = '#text_training_orientation_ps_oper';
            let editSelectionsMap4 = {};
            editSelectionsMap4[aProdTrainingItems] = arraProdTrainingItems;
            initDropdownMasterDetailsByFkidCombos(
                [aProdTrainingItems],
                4,
                editSelectionsMap4
            );
            let defectParams =
            select2Value({
                combo : form.formSubmitOper.find('#defect_escalation'),
                value : aOperProdTrainingOrientation?.defect_escalation,
            });
            select2Value({
                combo : form.formSubmitOper.find('#production_abnormality'),
                value : aOperProdTrainingOrientation?.production_abnormality,
            });
            // Guard: validate aProdData exists and has properties before reading from it
            if (aProdData && typeof aProdData === 'object') {
                const approverFirstStatus  = aProdData?.first_status  ?? '';
                const approverSecondStatus  = aProdData?.second_status  ?? '';
                const approverFirstDate  = aProdData?.first_date  ?? '';
                const approverFirstTime  = aProdData?.first_time  ?? '';
                const approverSecondDate = aProdData?.second_date ?? '';
                const approverSecondTime = aProdData?.second_time ?? '';

                form.formSubmitOper.find('#text_first_a_prod_result').val(approverFirstStatus).trigger('change');
                form.formSubmitOper.find('#text_second_a_prod_result').val(approverSecondStatus).trigger('change');
                form.formSubmitOper.find('#text_first_date_oper').val(approverFirstDate);
                form.formSubmitOper.find('#text_first_time_oper').val(approverFirstTime);
                form.formSubmitOper.find('#text_second_date_oper').val(approverSecondDate);
                form.formSubmitOper.find('#text_second_time_oper').val(approverSecondTime);
            }

            // 1. Let's say this is your raw string from the database response
            const orientationDocs = aOperProdTrainingOrientation?.orientation_docs;
            const enggTqOrientationDocs = aOperProdTrainingOrientation?.engg_tq_orientation_docs;
            // 2. Uncheck ALL checkboxes in the form first to have a clean state
            form.formSubmitOper.find('input[type="checkbox"]').prop('checked', false);
            // 3. Check ALL checkboxes in the form based on the orientation docs or
            checkCheckboxesFromColumn(orientationDocs,'chk');
            checkCheckboxesFromColumn(enggTqOrientationDocs,'chk');
            checkCheckboxesFromColumn(enggTqOrientationDocs,'chk');

            //=== B ENGG
            const aEnggData = response?.approversCollection?.BENGGTQ?.[0] ?? null;
            //  initDropdownMasterDetailsByFkidCombos([
            //         '#transfer_flexibility',
            //         '#text_training_orientation_es_oper',
            // ],5);

            const arraBEnggTrainingItems = Array.isArray( response.rawBEnggTrainingItemsCollection) ?  response.rawBEnggTrainingItemsCollection : [ response.rawBEnggTrainingItemsCollection];
            console.log('rawBEnggTrainingItemsCollection',arraBEnggTrainingItems  );

            const aBEnggTrainingItems = '#text_training_orientation_es_oper';
            let editSelectionsMap5 = {};
            editSelectionsMap5[aBEnggTrainingItems] =  response.rawBEnggTrainingItemsCollection;
            initDropdownMasterDetailsByFkidCombos(
                [aBEnggTrainingItems],
                5,
                editSelectionsMap5
            );
            checkCheckboxesFromColumn(bOpEnggSectionTrainingOrientation?.engg_orientation_docs,'chk');

            form.formSubmitOper.find('#text_engg_orientation_docs').val(bOpEnggSectionTrainingOrientation?.obs_first_result_es_oper);
            form.formSubmitOper.find('#text_obs_first_result_es_oper').val(bOpEnggSectionTrainingOrientation?.first_sample_es_oper);
            form.formSubmitOper.find('#text_first_sample_es_oper').val(bOpEnggSectionTrainingOrientation?.first_ok_es_oper);
            form.formSubmitOper.find('#text_first_ok_es_oper').val(bOpEnggSectionTrainingOrientation?.first_ng_es_oper);
            form.formSubmitOper.find('#text_oa_1st_result_es_oper').val(bOpEnggSectionTrainingOrientation?.oa_1st_result_es_oper);
            // form.formSubmitOper.find('#text_first_ng_es_oper').val(bOpEnggSectionTrainingOrientation?.first_ng_es_oper);


            form.formSubmitOper.find('#text_obs_second_result_es_oper').val(bOpEnggSectionTrainingOrientation?.obs_second_result_es_oper);
            form.formSubmitOper.find('#text_second_sample_es_oper').val(bOpEnggSectionTrainingOrientation?.second_sample_es_oper);
            form.formSubmitOper.find('#text_second_ok_es_oper').val(bOpEnggSectionTrainingOrientation?.second_ok_es_oper);
            form.formSubmitOper.find('#text_second_ng_es_oper').val(bOpEnggSectionTrainingOrientation?.second_ng_es_oper);
            if (aEnggData && typeof aEnggData === 'object') {
                form.formSubmitOper.find('#text_1st_disqualification_es_oper').val(aEnggData?.first_remarks ?? '');
                form.formSubmitOper.find('#text_2nd_disqualification_es_oper').val(aEnggData?.second_remarks ?? '');
                form.formSubmitOper.find('#text_qc_1st_date_es_oper').val(aEnggData?.first_date ?? '');
                form.formSubmitOper.find('#text_qc_1st_time_es_oper').val(aEnggData?.first_time ?? '');
                form.formSubmitOper.find('#text_qc_2nd_date_es_oper').val(aEnggData?.second_date ?? '');
                form.formSubmitOper.find('#text_qc_2nd_time_es_oper').val(aEnggData?.second_time ?? '');
            }
            // ==== C QC Certification
            const cQcData = response?.approversCollection?.CQCC?.[0] ?? null;
            form.formSubmitOper.find('#text_obs_first_result_qcs_oper').val(cQcCertification?.obs_first_result_qcs_oper ?? '').trigger('change');
            form.formSubmitOper.find('#text_first_sample_qcs_oper').val(cQcCertification?.first_sample_qcs_oper ?? '').trigger('change');
            form.formSubmitOper.find('#text_first_ok_qcs_oper').val(cQcCertification?.first_ok_qcs_oper ?? '');
            form.formSubmitOper.find('#text_first_ng_qcs_oper').val(cQcCertification?.first_ng_qcs_oper ?? '');
            form.formSubmitOper.find('#text_obs_second_result_qcs_oper').val(cQcCertification?.obs_second_result_qcs_oper ?? '');
            form.formSubmitOper.find('#text_second_ok_qcs_oper').val(cQcCertification?.second_ok_qcs_oper ?? '');
            form.formSubmitOper.find('#text_second_sample_qcs_oper').val(cQcCertification?.second_sample_qcs_oper ?? '');
            form.formSubmitOper.find('#text_second_ng_qcs_oper').val(cQcCertification?.second_ng_qcs_oper ?? '');


            // Execute for the first table (1st Oper)
            syncCheckboxesWithDb('text_qcs_station_1st_oper', cQcCertification?.qcs_station_1st_oper);
            // Execute for the second table (2nd Oper)
            syncCheckboxesWithDb('text_qcs_station_2nd_oper', cQcCertification?.qcs_station_2nd_oper);
            if (cQcData && typeof cQcData === 'object') {

                form.formSubmitOper.find('#text_oa_1st_result_qcs_oper').val(cQcData?.first_status ?? '').trigger('change');
                form.formSubmitOper.find('#text_oa_2nd_result_qcs_oper').val(cQcData?.second_status ?? '').trigger('change');
                form.formSubmitOper.find('#text_1st_disapproval_qcs_oper').val(cQcData?.first_remarks ?? '');
                form.formSubmitOper.find('#text_2nd_disapproval_qcs_oper').val(cQcData?.second_remarks ?? '');
                form.formSubmitOper.find('#text_1st_date_qcs_oper').val(cQcData?.first_date ?? '');
                form.formSubmitOper.find('#text_1st_time_qcs_oper').val(cQcData?.first_time ?? '');
                form.formSubmitOper.find('#text_2nd_date_qcs_oper').val(cQcData?.second_date ?? '');
                form.formSubmitOper.find('#text_2nd_time_qcs_oper').val(cQcData?.second_time ?? '');
            }

            // D PPD ONLY Process
            const dPpdOnly = response?.approversCollection?.DPPDONLY?.[0] ?? null;
            const dPpdCertificationCompletion = data.d_ppd_certification_completion;
            if (dPpdOnly && typeof dPpdOnly === 'object') {
                 form.formSubmitOper.find('#text_lot_1st_sample_peqcs_oper').val(dPpdCertificationCompletion?.lot_1st_sample_peqcs_oper ?? '');

                form.formSubmitOper.find('#text_1st_injected_ng_peqcs_oper').val(dPpdCertificationCompletion?.['1st_injected_ng_peqcs_oper'] ?? '');
                form.formSubmitOper.find('#1st_detected_ng_peqcs_oper').val(dPpdCertificationCompletion?.['1st_detected_ng_peqcs_oper'] ?? '');
                form.formSubmitOper.find('#text_1st_detected_ng_peqcs_oper').val(dPpdCertificationCompletion?.['1st_detected_ng_peqcs_oper'] ?? '');form.formSubmitOper.find('#text_2nd_sample_peqcs_oper').val(dPpdCertificationCompletion?.['2nd_sample_peqcs_oper'] ?? '');
                form.formSubmitOper.find('#2nd_sample_peqcs_oper').val(dPpdCertificationCompletion?.['2nd_sample_peqcs_oper'] ?? '');
                form.formSubmitOper.find('#text_2nd_injected_ng_peqcs_oper').val(dPpdCertificationCompletion?.['2nd_injected_ng_peqcs_oper'] ?? '');
                form.formSubmitOper.find('#text_2nd_detected_ng_peqcs_oper').val(dPpdCertificationCompletion?.['2nd_detected_ng_peqcs_oper'] ?? '');
                form.formSubmitOper.find('#text_oa_1st_result_peqcs_oper').val(dPpdOnly?.first_status ?? '').trigger('change');
                form.formSubmitOper.find('#text_oa_2nd_result_peqcs_oper').val(dPpdOnly?.second_status ?? '').trigger('change');
                form.formSubmitOper.find('#text_oa_1st_result_peqcs_oper').val(dPpdOnly?.first_remarks ?? '');
                form.formSubmitOper.find('#text_oa_2nd_result_peqcs_oper').val(dPpdOnly?.second_remarks ?? '');
                form.formSubmitOper.find('#text_1st_date_peqcs_oper').val(dPpdOnly?.first_date ?? '');
                form.formSubmitOper.find('#text_1st_time_peqcs_oper').val(dPpdOnly?.first_time ?? '');
                form.formSubmitOper.find('#text_2nd_date_peqcs_oper').val(dPpdOnly?.second_date ?? '');
                form.formSubmitOper.find('#text_2nd_time_peqcs_oper').val(dPpdOnly?.second_time ?? '');
            }
            // E ENGG Validation Process
            const eQcValidationProcess = data.e_qc_validation_process;
            const eEngvpData = response?.approversCollection?.EENGVP?.[0] ?? null;
            form.formSubmitOper.find('#text_application_vpes_oper').val(eQcValidationProcess?.engg_application_vpes_oper ?? '').trigger('change');
            let vpesOper = eQcValidationProcess?.engg_vpes_oper ?? '';
            if(vpesOper != ""){
                form.formSubmitOper.find('#text_vpes_oper_1').prop('checked', true);
            }
            form.formSubmitOper.find('#text_vpes_oper').val(eQcValidationProcess?.engg_vpes_oper ?? '').trigger('change');
            form.formSubmitOper.find('#text_first_result_vpes_oper').val(eEngvpData?.first_status ?? '').trigger('change');
            form.formSubmitOper.find('#text_second_result_vpes_oper').val(eEngvpData?.engg_vpes_oper ?? '').trigger('change');
            form.formSubmitOper.find('#text_1st_date_vpes_oper').val(eEngvpData?.first_date ?? '');
            form.formSubmitOper.find('#text_2nd_date_vpes_oper').val(eEngvpData?.second_date ?? '');
            form.formSubmitOper.find('#text_remarks_vpes_oper').val(eEngvpData?.first_remarks ?? '');

            //E Qc Validation Process
            const eQcvpData = response?.approversCollection?.EQCVP?.[0] ?? null;

            form.formSubmitOper.find('#text_vpqcs_oper').val(eQcValidationProcess?.vpqcs_oper ?? '').trigger('change');
            form.formSubmitOper.find('#text_application_vpqcs_oper').val(eQcValidationProcess?.application_vpqcs_oper ?? '').trigger('change');

            form.formSubmitOper.find('#text_first_result_vpqcs_oper').val(eQcvpData?.first_status ?? '').trigger('change');
            form.formSubmitOper.find('#text_first_result_vpes_oper_2').val(eQcvpData?.first_status_2 ?? '').trigger('change');
            form.formSubmitOper.find('#text_second_result_vpqcs_oper').val(eQcvpData?.second_status ?? '').trigger('change');
            form.formSubmitOper.find('#text_second_result_vpes_oper_2').val(eQcvpData?.second_status_2 ?? '').trigger('change');
            form.formSubmitOper.find('#text_remarks_vpqcs_oper').val(eQcvpData?.first_remarks ?? '');
            form.formSubmitOper.find('#text_remarks_vpes_oper_2').val(eQcvpData?.second_remarks ?? '');
            form.formSubmitOper.find('#text_1st_date_vpqcs_oper').val(eQcvpData?.first_date ?? '');
            form.formSubmitOper.find('#text_1st_date_vpes_oper_2').val(eQcvpData?.first_date_2 ?? '');
            form.formSubmitOper.find('#text_2nd_date_vpqcs_oper').val(eQcvpData?.second_date ?? '');
            form.formSubmitOper.find('#text_2nd_date_vpes_oper_2').val(eQcvpData?.second_date_2 ?? '');

            //F QC Validation
            const fQcVvo = response?.approversCollection?.FQCVVO?.[0] ?? {};
            const fQcValidation = data.f_qc_validation ?? {};

            form.formSubmitOper.find('#text_date1_qcvvo_oper').val(fQcVvo.first_date ?? '');
            form.formSubmitOper.find('#text_date2_qcvvo_oper').val(fQcVvo.second_date ?? '');

            let refdocnoInputQcvvoOper = fQcValidation?.refdocno_input_qcvvo_oper ?? '';
            let refdocnoInputQcvvoOper2 = fQcValidation?.refdocno_input_qcvvo_oper_2 ?? '';
            form.formSubmitOper.find('#text_refdocno_input_qcvvo_oper').val(refdocnoInputQcvvoOper);
            form.formSubmitOper.find('#text_refdocno_input_qcvvo_oper_2').val(refdocnoInputQcvvoOper2);
            if(refdocnoInputQcvvoOper != ""){
                form.formSubmitOper.find('#text_refdoc_qcvvo_oper').prop('checked', true);
            }
            if(refdocnoInputQcvvoOper2 != ""){
                form.formSubmitOper.find('#text_refdoc_qcvvo_oper_2').prop('checked', true);
            }

            $('#modalCreateCQForm').modal();

        })
    }
    

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
    // $(document).on('click', '.btnRemoveOperEmpRow', () => { //nmodifys
    //     idx = $(this).data('index');
    //     operEmpArray.splice(idx, 1);
    //     $(this).closest('tr').remove();
    //     // Re-index remaining rows
    //     $('#tbl_oper_add_emp tbody tr').each(function(i) {
    //         $(this).attr('data-index', i);
    //         $(this).find('.btnRemoveOperEmpRow').attr('data-index', i);
    //     });
    // });
    $(document).on('click', '.btnRemoveOperEmpRow', function () {
        const idx = $(this).data('index');
        operEmpArray.splice(idx, 1);
        $(this).closest('tr').remove();

        // Re-index remaining rows
        $('#tbl_oper_add_emp tbody tr').each(function (i) {
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
        //  nmodifys
    });
});
