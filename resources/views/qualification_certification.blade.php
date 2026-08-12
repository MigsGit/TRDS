

@php $layout = 'layouts.super_user_layout'; @endphp
@extends($layout)
@section('title', 'HR Memo & Approval')
@section('content_page')
@php
    $classificationTabs = [
        ['key' => 'mh', 'label' => 'MH', 'active' => true],
    ];
@endphp

<style>
    .card-body {
       max-height: 80vh; overflow-y: auto;
    }
</style>
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-12">
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-header bg-white py-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div class="mb-2 mb-md-0">
                                            <p class="text-uppercase text-muted small mb-1">Certification workspace</p>
                                            <h5 class="card-title mb-0 text-secondary">Qualification / Certification</h5>
                                        </div>
                                        <button type="button" id="btnCreateCQForm" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateCQForm"><i class="fa fa-plus fa-md mr-2"></i>Certify Employee</button>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        @foreach ($classificationTabs as $tab)
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link {{ !empty($tab['active']) ? 'active' : '' }}"
                                                   id="{{ $tab['key'] }}_tab"
                                                   data-toggle="tab"
                                                   href="#{{ $tab['key'] }}"
                                                   role="tab"
                                                   aria-controls="{{ $tab['key'] }}"
                                                   aria-selected="{{ !empty($tab['active']) ? 'true' : 'false' }}">
                                                    {{ $tab['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="myTabContent">
                                        <!-- For MH Tab -->
                                        <div class="tab-pane fade show active" id="operator" role="tabpanel" aria-labelledby="for-checking-tab">
                                            <div class="card shadow-sm border-0">
                                                <div class="card-body overflow-auto">
                                                    <div class="row mt-2 mb-2">
                                                        <div class="col-md-3">
                                                            <x-position-select name="select_position" id="select_position" />
                                                        </div>
                                                        <div class="col-md-3">
                                                            <x-section-select name="select_mh_sort_by_section" id="select_mh_sort_by_section" />
                                                        </div>
                                                        <div class="col-md-3">
                                                            <x-status-select name="select_access" id="select_access" />
                                                        </div>
                                                    </div>
                                                    {{-- <div class="row mt-2 mb-2">
                                                        <div class="col-md-3">
                                                            <select class="form-control select2bs4" style="width: 100%;" style="width: 100%" name="select_access" id="select_access">
                                                                <option value="" selected disabled>Select Status</option>
                                                                <option value="ALL">SELECT ALL</option>
                                                                <option value="FORAPP">PENDING</option>
                                                                <option value="OK">CLOSED</option>
                                                                <option value="MYAPPROVAL">FOR MY APPROVAL</option>
                                                            </select>
                                                        </div>
                                                    </div> --}}

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="text-secondary" id="exam_label_mh"></h6>
                                                        <!-- <button class="btn btn-primary"><i class="fa fa-plus me-2"></i> Add New</button> -->
                                                    </div>
                                                    <div class="table-responsive">
                                                        {{-- <table id="tbl_operator" class="table table-striped table-hover table-bordered nowrap"> --}}
                                                             <table id="tbl_operator" class="table table-striped table-hover table-bordered nowrap">
                                                            <thead class="table-primary">
                                                                <tr>
                                                                <th>Action</th>
                                                                <th>Status</th>
                                                                <th>Ctrl No. / Doc No.</th>
                                                                <th>Series Name</th>
                                                                <th>Created by</th>
                                                                <th>Section</th>
                                                                <th>Position</th>
                                                                <th>Date Filed</th>
                                                                <!-- <th>Qualified by</th> -->
                                                                {{-- <th>Certified by</th> --}}
                                                                {{-- <th>Approved / Conformed by</th> --}}
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- CREATE MODAL -->
    <div class="modal fade" id="modalCreateCQForm" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="createCQFormLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl" style="width: 95% !important; min-width: 95% !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mb-0" id="createCQFormLabel">Qualification / Certification Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                            </button>
                    </div>

                    <div class="modal-body">
                        <label for="">Select position and section you want to certify/qualify:</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-section-select name="select_section" id="select_section" label="Select Section" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="text_select_position">Select Position</label>
                                    <select class="form-control select2bs4" style="width: 100%;" name="text_select_position" id="text_select_position">
                                        <option value="" disabled>Select Position</option>
                                        <option value="Operator">Operator</option>
                                        <option value="Inspector">Inspector</option>
                                        <option value="Technician" selected>Technician</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- GLOBAL INPUTS --}}
                        <hr style="height: 5px; background-color: black; border: none;">
                          <div class="col-md-3 d-none">
                                <label for="">QC Slip Id:</label>
                                    <input class="form-control" type="text" class="form-control" id="qc_slips_id" name="qc_slips_id" placeholder="Auto Generated" readonly>
                                </div>
                                <div class="col-md-3  d-none">
                                    <label for="">Approval Status:</label>
                                    <input class="form-control" type="text" class="form-control" id="approval_status" name="approval_status" placeholder="Auto Generated" readonly>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-md-3">
                                        <label for="">Control No.:</label>
                                        <input class="form-control" type="text" class="form-control" id="textconno_new_operator" name="textconno_new_operator" placeholder="Auto Generated" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Production Section:</label>
                                        <select class="form-control select2bs4" style="width: 100%;" name="text_section_operator" id="text_section_operator">
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="" id="seriesDesignation">Series Name:</label>
                                        <input class="form-control" type="text" id="text_series_operator" name="text_series_operator" placeholder="Enter series name here">
                                    </div>

                                    <div class="col-md-3" id="productLine">
                                        <label for="">Product Line:</label>
                                         <select class="form-control select2bs4" style="width: 100%;" name="text_operator_product_line" id="text_operator_product_line">
                                        </select>
                                    </div>

                                    <div class="col-md-3" id="dateOfTransfer">
                                        <label for="">Date of Transfer:</label>
                                         <input type="date" class="form-control" style="width: 100%;" name="text_date_of_transfer" id="text_date_of_transfer">
                                    </div>
                                </div>
                                <div class="row mt-2 mb-5">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" id="btnEmployeeOperator" data-target="#select_Employee_operator" data-toggle="modal" ><i class="fa-solid fa fa-user-plus me-3"></i>Add Employee</button>
                                    </div>
                                </div>
                                <div class="table-responsive mt-3 mb-5">
                                    <table id="tbl_certified_list_operator" class="table table-bordered table-hover nowrap">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Action</th>
                                                <th>Employee No.</th>
                                                <th>Employee Name</th>
                                                <th>Station From</th>
                                                <th>Station To</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-md-12">
                                        <label for="">Reason for Certification:</label>
                                        <select class="form-control select2bs4" style="width: 100%;" name="text_certification_operator[]" id="text_certification_operator" multiple>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-5 div-transfer-flexibility d-none">
                                    <div class="col-md-12">
                                        <label for="">Lateral Transfer Flexibility:</label>
                                        <select class="form-control select2bs4" style="width: 100%;" name="transfer_flexibility[]" id="transfer_flexibility" multiple>

                                        </select>
                                    </div>
                            </div>

                        <hr style="height: 5px; background-color: black; border: none;">
                            <!-- FORMAT 5 Operator -->

                        <div class="d-none" id="div_Oper">
                              @include('qualification_certification.modal_qualification_certification_operator')
                        </div>
                        <div class="d-none" id="divInspector">
                              @include('qualification_certification.modal_qualification_certification_inspector')
                        </div>
                        <div class="d-none" id="divTechnician">
                              @include('qualification_certification.modal_qualification_certification_technician')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @include('components.operator_prodn_training_orientation')
    </div>
</div>
@endsection

@section('js_content')
    <script type="text/javascript">
    $(document).ready(function () {
        operEmpArray = [];
        form = {
            formSubmitTech: $('#formSubmit_Tech'),
            formSubmitOper: $('#formSubmitOper'),
            formSubmitMh: $('#formSubmit_MH'),
            formSubmitInspector: $('#formSubmit_Ins'),
        };
        dataTable = {
            operator: '',
            fvi_operator: '',
            tbl_fvi_operator_2: '',
            training_items: '',
        };
        table = {
           operator: '#tbl_operator',
           fvi_operator: '#tbl_fvi_operator',
           tbl_fvi_operator_2: '#tbl_fvi_operator_2',
        };

        $('#modalCreateCQForm').on('hidden.bs.modal', function () {
            // resetFormValues({'frmId'  :   form.formSubmitOper})
            // resetFormValues({'frmId'  :   form.formSubmitMh})
            // resetFormValues({'frmId'  :   form.formSubmitInspector})
        });

        const updateApproval = (params) => {
            let data = {
                decision : params.decision,
                qcSlipsId : params.qcSlipsId
            }
            call_ajax_serialize(data, {}, 'update_approval', function (response) {
                if (response && response.is_success === 'true') {
                    Swal.fire({ icon: 'success', title: 'Success', text: response.message || 'Approval status updated.' });
                    dataTable.operator.draw();
                    $('#modalCreateCQForm').modal('hide');
                }
            });
        }

        $('#operDisapproved').click(function (e) {
                let qcSlipsId = $('#qc_slips_id').val();
                let decision = 'DIS';
                let params = {
                    decision : decision,
                    qcSlipsId : qcSlipsId
                }
                swalConfirmation('Are you sure you want to DISAPPROVED this request?', function () {
                    updateApproval(params);
                });
        });

        $('#operApproved').click(function (e) {
            let qcSlipsId = $('#qc_slips_id').val();
            let decision = 'OK';
            let params = {
                decision : decision,
                qcSlipsId : qcSlipsId
            }
            swalConfirmation('Are you sure you want to APPROVED this request?', function () {
                updateApproval(params);
            });
        });

        dataTable.operator = $(table.operator).DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                url: "load_qc_slip", //Rapid Ts Warehouse Packaging
                data: function (param){
                    // param.qcSlipsId = $('#qc_slips_id').val();;
                },
            },
                'columnDefs': [
                    { responsivePriority: 1, targets: 0 },  // Action always visible
                    { responsivePriority: 2, targets: 1 },  // Status next priority
                    { responsivePriority: 1, targets: -1 } // Date Filed hides first on small screens
                ],
            fixedHeader: true,
            "columns":[
                // { "data" : "rawBulkCheckBox", orderable:false, searchable:false },
                { "data" : "rawAction", orderable:false, searchable:false },
                { "data" : "rawStatus", orderable:false, searchable:false },
                { "data" : "control_no" },
                { "data" : "series_name" },
                { "data" : "created_by" },
                { "data" : "section_category" },
                { "data" : "position_category" },
                { "data" : "created_at" },
            ],

        });
        dataTable.fvi_operator = $(table.fvi_operator).DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                url: "load1st_qc_validation", //Rapid Ts Warehouse Packaging
                // data: function (param){
                //     param.qcSlipsId = $('#qc_slips_id').val();
                // },
            },
            fixedHeader: true,
            "columns":[
                { "data" : "employee_no" },
                { "data" : "employee_name" },
                { "data" : "first_take_ins_sequence","name":"first_take_ins_sequence",orderable: false, searchable: false  },
                { "data" : "first_take_ins_assessment_result","name":"first_take_ins_assessment_result", orderable: false, searchable: false  },
            ],
        });
        dataTable.tbl_fvi_operator_2 = $(table.tbl_fvi_operator_2).DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                url: "load2nd_qc_validation", //Rapid Ts Warehouse Packaging
                // data: function (param){
                //     param.qcSlipsId = $('#qc_slips_id').val();
                // },
            },
            fixedHeader: true,
            "columns":[
                { "data" : "employee_no" },
                { "data" : "employee_name" },
                { "data" : "second_take_ins_sequence","name":"second_take_ins_sequence",orderable: false, searchable: false  },
                { "data" : "second_take_ins_assessment_result","name":"second_take_ins_assessment_result", orderable: false, searchable: false  },
            ],
        });
        dataTable.training_items = $('#tblTrainingItems').DataTable({
            processing: true,
            serverSide: true,
            paging: false,         // Display all matrix items in one view
            searching: false,      // Matrix layout does not require search bar
            info: false,
            ordering: false,
            ajax: {
                url: "load_qc_lqc_training_items_by_qc_slip_id",
                type: "GET",
                data: function (params) {
                    params.qc_slips_id = $('#qc_slips_id').val()??'';
                }
            },
            columns: [
                { data: 'item_name', name: 'item_name' },
                { data: 'day_1', name: 'day_1', className: 'text-center' },
                { data: 'day_2', name: 'day_2', className: 'text-center' },
                { data: 'day_3', name: 'day_3', className: 'text-center' },
                { data: 'day_4', name: 'day_4', className: 'text-center' },
                { data: 'day_5', name: 'day_5', className: 'text-center' },
                { data: 'remarks', name: 'remarks' }
            ]
        });
        // Pre-fill Day 1–5 date inputs in the #tblTrainingItems header from server response
        dataTable.training_items.on('xhr', function () {
            var json = dataTable.training_items.ajax.json();
            if (json && json.headerDates) {
                $.each(json.headerDates, function (dayNumber, dateValue) {
                    $('#tblTrainingItems').closest('.table-responsive')
                        .find('.header-date-input[data-day="' + dayNumber + '"]')
                        .val(dateValue || '');
                });
            }
        });

        $('#select_position').change(function (e) {
            e.preventDefault();
            $('#select_access').val('').trigger('change');
            $('#select_mh_sort_by_section').val('').trigger('change');
            let selectSortBySection = $('#select_mh_sort_by_section').val();
            let selectAccess = $('#select_access').val();
            let selectPosition = $(this).val();
            dataTable.operator.ajax.url("load_qc_slip?selectMhSortBySection="+selectSortBySection+"&selectAccess="+selectAccess+"&selectPosition="+selectPosition).draw();
        });

        $('#select_mh_sort_by_section, #select_access').on('change', function () {
            let selectSortBySection = $('#select_mh_sort_by_section').val();
            let selectAccess = $('#select_access').val();
            let selectPosition = $('#select_position').val();
            dataTable.operator.ajax.url("load_qc_slip?selectMhSortBySection="+selectSortBySection+"&selectAccess="+selectAccess+"&selectPosition="+selectPosition).draw();

        });
        // Best Practice: Event Delegation with correct object scoping
        $(document).on('click', '.btnRemoveOperEmpMain', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('change', '.first_take_ins_sequence',function (e) {
            let qcSlipsIdData = $(this).attr('qc-slips-id');
            let QcSlipEmployeesIdData = $(this).attr('qc-slip-employees-id');
            let valueData = $(this).val();
            let categoryData = 'firstTakeInsSequence';
            let params = {
                qcSlipsId : qcSlipsIdData,
                value : valueData,
                QcSlipEmployeesId : QcSlipEmployeesIdData,
                category : categoryData,
            }
            saveFirstTakeInsSequence(params);
        })
        $(document).on('change', '.first_take_ins_assessment_result',function (e) {
            let qcSlipsIdData = $(this).attr('qc-slips-id');
            let QcSlipEmployeesIdData = $(this).attr('qc-slip-employees-id');
            let valueData = $(this).val();
            let categoryData = 'firstTakeInsAssessmentResult';
            let params = {
                qcSlipsId : qcSlipsIdData,
                value : valueData,
                QcSlipEmployeesId : QcSlipEmployeesIdData,
                category : categoryData,
            }
            saveFirstTakeInsSequence(params);

        })
        $(document).on('change', '.second_take_ins_sequence',function (e) {
            let qcSlipsIdData = $(this).attr('qc-slips-id');
            let QcSlipEmployeesIdData = $(this).attr('qc-slip-employees-id');
            let valueData = $(this).val();
            let categoryData = 'secondTakeInsSequence';
            let params = {
                qcSlipsId : qcSlipsIdData,
                value : valueData,
                QcSlipEmployeesId : QcSlipEmployeesIdData,
                category : categoryData,
            }
            saveFirstTakeInsSequence(params);
        })
        $(document).on('change', '.second_take_ins_assessment_result',function (e) {
            let qcSlipsIdData = $(this).attr('qc-slips-id');
            let QcSlipEmployeesIdData = $(this).attr('qc-slip-employees-id');
            let valueData = $(this).val();
            let categoryData = 'secondTakeInsAssessmentResult';
            let params = {
                qcSlipsId : qcSlipsIdData,
                value : valueData,
                QcSlipEmployeesId : QcSlipEmployeesIdData,
                category : categoryData,
            }
            saveFirstTakeInsSequence(params);
        })

        $(table.operator).on('click', '#btnGetQcSlipsId','tr',function (e) {
            e.preventDefault();
            let qcSlipsId = $(this).attr('qc-slips-id');
            let params = {
                qcSlipsId: qcSlipsId,
            };
            getQcSlipsById(params);

        });
        $(table.operator).on('click', '#btnViewQcSlipsId','tr',function (e) {
            e.preventDefault();
            let qcSlipsId = $(this).attr('qc-slips-id');
            let params = {
                qcSlipsId: qcSlipsId,
            };
            getQcSlipsById(params);
        });
        $(document).on('change', '#text_certification_operator',function (e) {
            e.preventDefault();
            // 1. Grab the value (fallback to empty string if null/undefined)
            let selectedValue = $(this).val() || '';

            let valuesArray = Array.isArray(selectedValue)
                ? selectedValue
                : selectedValue.toString().split(',');

            const targetIDs = ['214', '216'];

            const hasValue = valuesArray.some(val => targetIDs.includes(val.trim()));

            $('.div-transfer-flexibility').toggleClass('d-none', !hasValue);
        });
        $(document).on('change', '#text_training_orientation_ps_oper',function (e) {
            e.preventDefault();
            // 1. Grab the value (fallback to empty string if null/undefined)
            let selectedValue = $(this).val() || '';

            let valuesArray = Array.isArray(selectedValue)
                ? selectedValue
                : selectedValue.toString().split(',');

            const targetIDs = ['214', '216'];

            const hasValue = valuesArray.some(val => targetIDs.includes(val.trim()));

            $('.div-transfer-flexibility').toggleClass('d-none', !hasValue);
         });

        const saveFormOper = ($forms = null) => {
            // 1. Serialize standard form inputs into an array
            // console.log('saveFormOper called',$form[0]);
            console.log($forms);
            var formArray = $forms.serializeArray();
            // 2. Push extra custom field values manually
            formArray.push({ name: 'text_alert_prod_sec', value: $('#text_alert_prod_sec').val() });
            formArray.push({ name: 'text_alert_prod_cc_sec', value: $('#text_alert_prod_cc_sec').val() });
            formArray.push({ name: 'text_select_position', value: $('#text_select_position').val() });

            formArray.push({ name: 'select_section', value: $('#select_section').val() });
            formArray.push({ name: 'text_select_section', value: $('#text_select_section').val() });//Insert to Operator
            formArray.push({ name: 'qc_slips_id', value: $('#qc_slips_id').val() });
            formArray.push({ name: 'text_section_operator', value: $('#text_section_operator').val() });
            formArray.push({ name: 'text_series_operator', value: $('#text_series_operator').val() });
            formArray.push({ name: 'text_operator_product_line', value: $('#text_operator_product_line').val() });
            // formArray.push({ name: 'text_date_of_transfer', value: $('#text_date_of_transfer').val() });
            formArray.push({ name: 'text_certification_operator', value: $('#text_certification_operator').val() });
            formArray.push({ name: 'transfer_flexibility', value: $('#transfer_flexibility').val() });


            // 3. Process the array into a clean key-value object map
            var data = {};
            $.each(formArray, function(i, field) {
                if (data[field.name] !== undefined) {
                    if (!Array.isArray(data[field.name])) {
                        data[field.name] = [data[field.name]];
                    }
                    data[field.name].push(field.value);
                } else {
                    data[field.name] = field.value;
                }
            },$forms[0]);

            // 4. Safely pull your dynamic table data array
            data.operator_employees = (typeof getOperEmpTableData === 'function')
                ? getOperEmpTableData()
                : [];

            call_ajax_serialize(data,{},'save_qualification_certification_oper', function(response){
                if (response.is_success === 'true') {
                    Swal.fire({ icon: 'success', title: 'Saved', text: response.message || 'Operator form saved.' });
                    dataTable.operator.draw();
                    $('#modalCreateCQForm').modal('hide');
                    $('#modalSendEmail').modal('hide');
                    $forms[0].reset();
                }
            },$forms);
        }

        $('#formSendEmail').click(function (e) {
            e.preventDefault();
            let position = $('#text_select_position').val();
               switch (position) {
                case 'MH':
                    $('#divMH').removeClass('d-none');
                    break;
                case 'Technician':
                    saveFormOper(form.formSubmitTech);
                    // $('#divTechnician').removeClass('d-none');
                    break;
                case 'Supervisor':
                case 'Engineer':
                case 'Planner':
                    $('#divSEP').removeClass('d-none');
                    break;
                case 'Inspector':
                    saveInspectorDetails();
                    break;
                case 'Operator':
                    saveFormOper();
                    break;
                default:
                    alert('Unknown position selected. Please select a valid position.');
                    break;
            }
        });
        $(document).on('submit', '#formSubmit_Tech', function (e) {
            e.preventDefault();
            var $form = $(this);
            // saveFormOper($form);
            $('#modalSendEmail').modal();

        });
        $(document).on('submit', '#formSubmitOper', function (e) {
            e.preventDefault();
            var $form = $(this);
            Swal.fire({
            title: 'Are you sure you want to save this request?',
            // html: 'This will allow you to add employees who will not be endorsed for this training endorsement.<br> <em style="font-size: 1rem;">You can specify the reason for not endorsing each employee.</em>',
            html: '',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
                if (result.isConfirmed) {
                    if($('#approval_status').val() === "FQCVVO"){
                        saveFormOper($form);
                    }else{
                        $('#modalSendEmail').modal();
                    }
                }
            });
        });
        const saveInspectorDetails = ($forms = null) => {
            // 1. Serialize standard form inputs into an array
            var $form = form.formSubmitInspector ?? $forms;
            var formArray = $form.serializeArray();

            // 2. Push extra custom field values manually
            formArray.push({ name: 'text_alert_prod_sec', value: $('#text_alert_prod_sec').val() });
            formArray.push({ name: 'text_alert_prod_cc_sec', value: $('#text_alert_prod_cc_sec').val() });
            formArray.push({ name: 'text_select_position', value: $('#text_select_position').val() });

            formArray.push({ name: 'select_section', value: $('#select_section').val() });
            formArray.push({ name: 'text_select_section', value: $('#text_select_section').val() });//Insert to Operator
            formArray.push({ name: 'qc_slips_id', value: $('#qc_slips_id').val() });
            formArray.push({ name: 'text_section_operator', value: $('#text_section_operator').val() });
            formArray.push({ name: 'text_series_operator', value: $('#text_series_operator').val() });
            formArray.push({ name: 'text_operator_product_line', value: $('#text_operator_product_line').val() });
            formArray.push({ name: 'text_date_of_transfer', value: $('#text_date_of_transfer').val() });
            formArray.push({ name: 'text_certification_operator', value: $('#text_certification_operator').val() });
            formArray.push({ name: 'transfer_flexibility', value: $('#transfer_flexibility').val() });

            // 3. Process the array into a clean key-value object map
            var data = {};
            $.each(formArray, function(i, field) {
                if (data[field.name] !== undefined) {
                    if (!Array.isArray(data[field.name])) {
                        data[field.name] = [data[field.name]];
                    }
                    data[field.name].push(field.value);
                } else {
                    data[field.name] = field.value;
                }
            },$form[0]);

            // 4. Safely pull your dynamic table data array
            data.operator_employees = (typeof getOperEmpTableData === 'function')
                ? getOperEmpTableData()
                : [];
            call_ajax_serialize(data,{},'save_qualification_certification_oper', function(response){
                // if (response.is_success === 'true') {
                    Swal.fire({ icon: 'success', title: 'Saved'});
                    dataTable.operator.draw();
                    $('#modalCreateCQForm').modal('hide');
                    $('#modalSendEmail').modal('hide');
                    $form[0].reset();
                // }
            },$form);
        }
        // #formSubmit_MH, btnCreateCQForm
        $(document).on('submit', '#formSubmit_Ins',  function (e) {
            e.preventDefault();
            var $form = $(this);
            $('#modalSendEmail').modal();
            // Swal.fire({
            //     title: 'Are you sure you want to save this request?',
            //     // html: 'This will allow you to add employees who will not be endorsed for this training endorsement.<br> <em style="font-size: 1rem;">You can specify the reason for not endorsing each employee.</em>',
            //     html: '',
            //     icon: 'question',
            //     showCancelButton: true,
            //     confirmButtonText: 'Yes, proceed',
            //     cancelButtonText: 'Cancel'
            // }).then(function (result) {
            //         if (result.isConfirmed) {
                        // if($('#approval_status').val() === "LQCHEADAPP"){
                        //     saveInspectorDetails($form);
                        // }else{
                        // }
            //         }
            //     });
            // });
        });

        const selectOperatorValidation = () => {
            let approvalStatus = $('#approval_status').val();
            let params = {
                approvalStatus: approvalStatus,
                positionCategory: $('#text_select_position').val(),
            }
            getApprovalStatusToggle(params)
        }
        const selectInspectorValidation = () => {
            let approvalStatus = $('#approval_status').val();
            let params = {
                approvalStatus: approvalStatus,
                positionCategory: $('#text_select_position').val(),
            }
            getApprovalStatusToggle(params)
           
           
        }
        var $positionSelect = $('#text_select_position');
        var $positionSections = $('#divMH, #divTechnician, #divSEP, #divInspector, #div_Oper , .operSave, .operApproved','.inspectorSave');

        // $positionSelect.click(function () {
        //     alert('click');
        // }).on('change', function () {
        //    togglePositionSection($(this).val());
        // });
        $positionSelect.on('change', function () {
            let params = {
                approvalStatus: $('#approval_status').val(),
                positionCategory: $(this).val(),
            }
            getApprovalStatusToggle(params)
        });
        const togglePositionSection = (position) => {
            initOperEmpModal();
            $('#tbl_certified_list_operator tbody').empty();
            $positionSections.addClass('d-none');
            // text_operator_product_line
            // text_series_operator
            // text_certification_operator
            // transfer_flexibility

            initDropdownMasterDetailsByFkidCombos([
                '#text_operator_product_line',
            ],2);
            initDropdownMasterDetailsByFkidCombos([
                    '#text_certification_operator',
            ],3);
            initDropdownMasterDetailsByFkidCombos([
                    '#transfer_flexibility',
            ],6);

            // $('.inspectorSave').addClass('d-none');
            // $('.operSave').addClass('d-none');
            // $('.operApproved').addClass('d-none');
            // $('.btnSaveInspector').addClass('d-none');
            switch (position) {
                case 'MH':
                    $('#divMH').removeClass('d-none');
                    break;
                case 'Technician':
                    $('#divTechnician').removeClass('d-none');
                    break;
                case 'Supervisor':
                case 'Engineer':
                case 'Planner':
                    $('#divSEP').removeClass('d-none');
                    break;
                case 'Inspector':
                    selectInspectorValidation();
                    break;
                case 'Operator':
                    selectOperatorValidation();
                    break;
            }

        }
        initDivDeptSecCombos([
                '#text_section_operator',
        ]);
        const initSelectPassFail = (comboSelectors) => {

            comboSelectors.forEach(function(selector) {
                    selectPassFail($(selector));
            });
        }
        // const initGetSystemOneEmployeeDetailsCombos = (comboSelectors) => {
        //     comboSelectors.forEach(function(selector) {
        //             getSystemOneEmployeeDetails($(selector));
        //     });
        // }
        initGetSystemOneEmployeeDetailsCombos([
            '#text_oper_emp_number',
            '#text_first_trainedby_oper',
            '#text_first_mentoredby_oper',
            '#text_second_trainedby_oper',
            '#text_second_mentoredby_oper',
            '#text_alert_prod_sec',
            '#text_alert_prod_cc_sec',
            '#text_1st_qualifiedby_es_oper',
            '#text_2nd_qualifiedby_es_oper',
            //C
            '#text_1st_certifiedby_qcs_oper',
            '#text_2nd_certifiedby_qcs_oper',
            //D PPD
            '#text_1st_certified_prod_peqcs_oper',
            '#text_1st_certified_eng_peqcs_oper',
            '#text_1st_certified_qc_peqcs_oper',
            '#text_2nd_certified_prod_peqcs_oper',
            '#text_2nd_certified_eng_peqcs_oper',
            '#text_2nd_certified_qc_peqcs_oper',

            '#text_1st_validatedby_vpes_oper',
            '#text_2nd_validatedby_vpes_oper',
            //EQcValidationProcess
            '#text_1st_validatedby_vpqcs_oper',
            '#text_2nd_validatedby_vpqcs_oper',
            '#text_1st_validatedby_vpes_oper_2',
            '#text_2nd_validatedby_vpes_oper_2',
            //F
            '#text_validated1_qcvvo_oper',
            '#text_validated2_qcvvo_oper',
            //APPROVED BY
            '#text_oper_approved_confirmed_by',
            //Inspector
            '#text_certified_inspector',
            '#text_mentored',
            '#text_sec2_certified_inspector',
            '#text_vpqcs_validated1_inspector',
            '#text_vpqcs_validated2_inspector',
            '#text_sec3_approved_inspector',
            '#text_alert_qctq_sec_insp',
            '#text_alert_qctq_cc_sec_insp',
            //Tech
            '#text_tech_trained_qualified_by',
            '#text_tech_mentored_by',
            '#text_tech_es_1st_certified_by',
            '#text_tech_es_2nd_certified_by',
            '#text_tech_qcs_1st_certified_by',
            '#text_tech_qcs_2nd_take_result',
            '#text_tech_approved_by',

        ]);
        // initSelectPassFail([
        //     '#text_oa_1st_result_es_oper',
        //     '#text_obs_first_result_es_oper',
        //     '#text_oa_2nd_result_es_oper',
        // ]);

        // Delete a row from the FVI table
        $(document).on('click', '#tbl_fvi_operator .btn-delete-fvi-row', function () {
            $(this).closest('tr').remove();
        });
        $('#btnSaveMatrix').on('click', function () {
            let matrixData = [];
            $('#tblTrainingItems tbody tr').each(function () {
                let row = $(this);
                let itemId = row.find('.input-remark').attr('data-item-id');
                if (itemId) {
                    let dayResults = {};
                    row.find('.input-result').each(function () {
                        let dayNum = $(this).data('day');
                        dayResults['day_' + dayNum] = $(this).val();
                    });

                    matrixData.push({
                        training_item_id: itemId,
                        day_results:      dayResults,
                        remark:           row.find('.input-remark').val(),
                        sub_description:  row.find('.input-sub-desc').val() ?? null,
                    });
                }
            });
            // Collect header dates keyed as day_dates[day_N]
            let dayDates = {};
            $('#tblTrainingItems').closest('.table-responsive')
                .find('.header-date-input').each(function () {
                    dayDates['day_' + $(this).data('day')] = $(this).val();
                });
            //     console.log('dayDates',dayDates);
            // return;

            $.ajax({
                url:  "save_qc_lqc_training_items_by_qc_slip_id",
                type: "POST",
                data: {
                    _token:     "{{ csrf_token() }}",
                    qc_slips_id: $('#qc_slips_id').val(),
                    matrix:     matrixData,
                    day_dates:  dayDates,
                },
                success: function (response) {
                    if (response.is_success === 'true') {
                        Swal.fire({ icon: 'success', title: 'Saved', text: response.message || 'Training items saved.' });
                    }
                }
            });
        });
        $('#btnCreateCQForm').click(function (e) {
            e.preventDefault();
            let categoryPosition = $('#text_select_position').val();
            dataTable.training_items.draw();
            togglePositionSection(categoryPosition);
        });
    });

    </script>
@endsection
