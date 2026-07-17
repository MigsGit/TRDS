
@php $layout = 'layouts.super_user_layout'; @endphp
@extends($layout)
@section('title', 'HR Memo & Approval')
@section('content_page')
@php
    $classificationTabs = [
        ['key' => 'mh', 'label' => 'MH', 'active' => true],
    ];
@endphp

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
                                        <div class="tab-pane fade show active" id="mh" role="tabpanel" aria-labelledby="for-checking-tab">
                                            <div class="card shadow-sm border-0">
                                                <div class="card-body">

                                                    <!-- Edited 4-24-25 -->
                                                    <div class="row mt-2 mb-2">
                                                        <div class="col-md-3">
                                                            <x-section-select name="select_mh_sort_by_section" id="select_mh_sort_by_section" />
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="text-secondary" id="exam_label_mh"></h6>
                                                        <!-- <button class="btn btn-primary"><i class="fa fa-plus me-2"></i> Add New</button> -->
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table id="tbl_operator" class="table table-striped table-hover table-bordered nowrap">
                                                            <thead class="table-primary">
                                                                <tr>
                                                                <th>Action</th>
                                                                <th>Status</th>
                                                                <th>Ctrl No. / Doc No.</th>
                                                                <th>Series Name</th>
                                                                <th>Approvers</th>
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

                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </section>

        <!-- CREATE MODAL -->
        <div class="modal fade" id="modalCreateCQForm" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="createCQFormLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl" style="width: 95% !important; min-width: 95% !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mb-0" id="createCQFormLabel">Qualification / Certification Form OPT</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                            </button>
                    </div>

                    <div class="modal-body">
                        <label for="">Select position and section you want to certify/qualify:</label>

                        <!-- Edited 4-7-25 -->
                        <input type="hidden" id="hidden_created_by_name" name="hidden_created_by_name">
                        <input type="hidden" id="hidden_created_by_username" name="hidden_created_by_username" value="">
                        <input type="hidden" id="hidden_created_by_email" name="hidden_created_by_email">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="text_select_position">Select Section</label>
                                {{-- <x-section-select name="select_section" id="select_section" label="Select Section" /> --}}
                                    <select class="form-control select2bs4" style="width: 100%;" style="width: 100%" name="select_section" id="select_section">
                                        <option value="" selected disabled>Select Position</option>
                                        <option value="TSF1" selected>TS-F1</option>
                                        <option value="TSF3">TS-F3</option>
                                        <option value="CN">CN</option>
                                        <option value="CNF3">CN-F3</option>
                                        <option value="PPDCN">PPD-CN</option>
                                        <option value="PPDTS">PPD-TS</option>
                                        <option value="PPDF3">PPD-F3</option>
                                        <option value="YF">YF</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="text_select_position">Select Position</label>
                                    <select class="form-control select2bs4" style="width: 100%;" style="width: 100%" name="text_select_position" id="text_select_position">
                                        <option value="" selected disabled>Select Position</option>
                                        <option value="Operator" selected>Operator</option>
                                        <option value="MH">MH</option>
                                        <option value="Technician">Technician</option>
                                        <option value="Supervisor">Supervisor</option>
                                        <option value="Engineer">Engineer</option>
                                        <option value="Planner">Planner</option>
                                        <option value="Inspector">Inspector</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr style="height: 5px; background-color: black; border: none;">

                        <!-- FORMAT 5 Operator -->
                        <div class="d-none" id="div_Oper">
                            <form  id="formSubmitOper" >
                            @csrf
                                <h3 class="mt-5 mb-3 text-center">OPERATOR'S TRAINING / QUALIFICATION / CERTIFICATION SLIP</h3>

                                <div class="col-md-3">
                                    <label for="">QC Slip Id:</label>
                                    <input class="form-control" type="text" class="form-control" id="qc_slips_id" name="qc_slips_id" placeholder="Auto Generated" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Approval Status:</label>
                                    <input class="form-control" type="text" class="form-control" id="approval_status" name="approval_status" placeholder="Auto Generated" readonly>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-md-3">
                                        <label for="">Control No.:</label>
                                        {{-- <input class="form-control" type="hidden" class="form-control d-none" id="textconno_new_operator" name="textconno_new_operator" placeholder="Select section to generate Control No." readonly> --}}
                                        <input class="form-control" type="text" class="form-control" id="textconno_new_operator" name="textconno_new_operator" placeholder="Auto Generated" readonly>
                                       {{--  <input class="form-control" type="hidden" class="form-control" id="textconno_operator" name="textconno_operator" readonly> --}}
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Production Section:</label>
                                        <select class="form-control select2bs4" style="width: 100%;" name="text_section_operator" id="text_section_operator">
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Series Name:</label>
                                        <input class="form-control" type="text" id="text_series_operator" name="text_series_operator" placeholder="Enter series name here">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Product Line:</label>
                                         <select class="form-control select2bs4" style="width: 100%;" name="text_operator_product_line" id="text_operator_product_line">
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2 mb-5">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" id="" data-target="#select_Employee_operator" data-toggle="modal" ><i class="fa-solid fa-user-plus me-3"></i>Add Employee</button>
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

                                <!-- **************************************************************      APRODTO          ************************************************************************************************* -->

                             <div class="accordion" id="accordionExampleOper">
                                     <div class="card APRODTO">
                                        <h2 class="card-header">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOneOper" aria-expanded="true" aria-controls="collapseOneOper">
                                            <h5>A. PRODUCTION SECTION (Training and Orientation)</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseOneOper" class="accordion-collapse collapse show" data-parent="#accordionExampleOper">
                                        <div class="card-body">

                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                        <label class="" for="">TRAINING ITEMS:</label>
                                                        <select class="form-control select2bs4" style="width: 100%;" id="text_training_orientation_ps_oper" name="text_training_orientation_ps_oper" multiple></select>

                                                 </div>
                                                <div class="col-md-3">
                                                        <label class="" for="">Defect Escalation:</label>
                                                        <select class="form-control select2bs4" style="width: 100%;" id="defect_escalation" name="defect_escalation" multiple>
                                                             <option value="" disabled selected>Select Result</option>                                <option value="1" >Rule when to escalate </option>
                                                            <option value="2" >Filling-up of forms
                                                            </option>
                                                        </select>
                                                 </div>
                                                <div class="col-md-3">
                                                        <label class="" for="">Production Abnormlity Control (IMS-PMI-025):</label>
                                                        <select class="form-control select2bs4" style="width: 100%;" id="production_abnormality" name="production_abnormality" multiple>
                                                             <option value="" disabled selected>Select Result</option>                                <option value="1" >Rule when to escalate </option>
                                                            <option value="2" >Filling-up of forms
                                                            </option>
                                                        </select>
                                                 </div>
                                            </div>
                                            <div class="container my-4">
                                                <div class="row g-4">

                                                <div class="col-lg-6">
                                                    <div class="card shadow-sm h-100">
                                                        <div class="card-header bg-light border-bottom-0 pt-3">

                                                        </div>
                                                        <div class="table-responsive p-3">
                                                            <table class="table table-bordered table-hover align-middle mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th scope="col" style="width: 25%;">Category</th>
                                                                        <th scope="col">Document / Code Reference</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">CN</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="engg_tq_orientation_docs[]" value="PP-CN-010" id="chk_pp_cn_010">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_pp_cn_010">PP-CN-010</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">PPS</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="orientation_docs[]" value="PP-MDGEN-135" id="chk_pp_mdgen_135">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_pp_mdgen_135">PP-MDGEN-135</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">YF</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="orientation_docs[]" value="PP-YFLEX-296" id="chk_pp_yflex_296">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_pp_yflex_296">PP-YFLEX-296</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">TS</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="orientation_docs[]" value="WI-TSDGEN-044" id="chk_wi_tsdgen_044">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_wi_tsdgen_044">WI-TSDGEN-044</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-lg-6">
                                                    <div class="card shadow-sm h-100">
                                                        <div class="card-header bg-light border-bottom-0 pt-3">
                                                        </div>
                                                        <div class="table-responsive p-3">
                                                            <table class="table table-bordered table-hover align-middle mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th scope="col" style="width: 25%;">Category</th>
                                                                        <th scope="col">Document / Code Reference</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">CN</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="orientation_docs[]" value="WI-CN-216" id="chk_wi_cn_216">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_wi_cn_216">WI-CN-216</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">CN</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="orientation_docs[]" value="PP-CN-407" id="chk_pp_cn_407">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_pp_cn_407">PP-CN-407</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">CN</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="orientation_docs[]" value="PP-CN-066" id="chk_pp_cn_066">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_pp_cn_066">PP-CN-066</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">PPS</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="orientation_docs[]" value="PP-MDGEN-136" id="chk_pp_mdgen_136">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_pp_mdgen_136">PP-MDGEN-136</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">YF</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="orientation_docs[]" value="PP-YFLEX-448" id="chk_pp_yflex_448">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_pp_yflex_448">PP-YFLEX-448</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">TS</span></td>
                                                                        <td>
                                                                            <div class="form-check m-0">
                                                                                <input class="form-check-input" type="checkbox" name="orientation_docs[]" value="WI-IC-4743" id="chk_wi_ic_4743">
                                                                                <label class="form-check-label fw-medium ms-1" for="chk_wi_ic_4743">WI-IC-4743</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                </div>
                                            </div>

                                            <h5 class="mt-3 mb-3">RESULT</h5>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                                <select class="form-control select2bs4" style="width: 100%;" name="text_first_a_prod_result" id="text_first_a_prod_result">
                                                              <option value="" disabled selected>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                            </select>

                                                        </div>
                                                        <div class="col-md-6">
                                                                <select class="form-control select2bs4" style="width: 100%;" name="text_second_a_prod_result" id="text_second_a_prod_result">
                                                             <option value="" disabled selected>Select Result</option>                                    <option value="PASSED" selected >PASSED</option>
                                                                <option value="FAILED">FAILED</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="" for="">Trained by:</label>
                                                            <select class="form-control select2bs4" style="width: 100%;" id="text_first_trainedby_oper" name="text_first_trainedby_oper[]" multiple></select>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="" for="">Mentored by:</label>
                                                            <select class="form-control select2bs4" style="width: 100%;" id="text_first_mentoredby_oper" name="text_first_mentoredby_oper" multiple></select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="" for="">Trained by:</label>
                                                            <select class="form-control select2bs4" style="width: 100%;" id="text_second_trainedby_oper" name="text_second_trainedby_oper[]" multiple></select>
                                                        </div>
                                                        <div class="col-md-6">
                                                             <label class="" for="">Mentored by:</label>
                                                            <select class="form-control select2bs4" style="width: 100%;" id="text_second_mentoredby_oper" name="text_second_mentoredby_oper" multiple></select>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="row mb-5">
                                                <div class="col-md-3">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_first_date_oper" name="text_first_date_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Time:</label>
                                                    <input class="form-control" type="time" id="text_first_time_oper" name="text_first_time_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_second_date_oper" name="text_second_date_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Time:</label>
                                                    <input class="form-control" type="time" id="text_second_time_oper" name="text_second_time_oper">
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="card BENGGTQ">
                                        <h2 class="card-header">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwoOper" aria-expanded="false" aria-controls="collapseTwoOper">
                                            <h5>B. ENGINEERING SECTION (Training and Qualification)</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseTwoOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="card-body">
                                            <p class="mb-3">TRAINING ITEMS:</p>
                                            <div class="col-md-6">
                                                <label class="" for="">TRAINING ITEMS:</label>
                                                <select class="form-control select2bs4" style="width: 100%;" id="text_training_orientation_es_oper" name="text_training_orientation_es_oper" multiple></select>
                                            </div>
                                             <div class="container my-4 BENGGTQ">
                                                <div class="row justify-content-center">
                                                    <div class="col-xl-6 col-lg-8 col-md-10">
                                                        <div class="card shadow-sm border-0">
                                                            <div class="card-header bg-light py-3 border-bottom-0">
                                                                <h6 class="card-title mb-0 text-muted text-uppercase fw-bold small tracking-wider">Orientation Checklist - Set C</h6>
                                                            </div>
                                                            <div class="table-responsive p-3">
                                                                <table class="table table-bordered table-hover align-middle mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th scope="col" style="width: 25%;">Category</th>
                                                                            <th scope="col">Document / Code Reference</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">CN</span></td>
                                                                            <td>
                                                                                <div class="form-check m-0">
                                                                                    <input class="form-check-input" type="checkbox" name="engg_orientation_docs[]" value="PP-CN-367" id="chk_pp_cn_367">
                                                                                    <label class="form-check-label fw-medium ms-1" for="chk_pp_cn_367">PP-CN-367</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">CN</span></td>
                                                                            <td>
                                                                                <div class="form-check m-0">
                                                                                    <input class="form-check-input" type="checkbox" name="engg_orientation_docs[]" value="PP-CN-336" id="chk_pp_cn_336">
                                                                                    <label class="form-check-label fw-medium ms-1" for="chk_pp_cn_336">PP-CN-336</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">CN</span></td>
                                                                            <td>
                                                                                <div class="form-check m-0">
                                                                                    <input class="form-check-input" type="checkbox" name="engg_orientation_docs[]" value="PP-CN-086" id="chk_pp_cn_086">
                                                                                    <label class="form-check-label fw-medium ms-1" for="chk_pp_cn_086">PP-CN-086</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">CN</span></td>
                                                                            <td>
                                                                                <div class="form-check m-0">
                                                                                    <input class="form-check-input" type="checkbox" name="engg_orientation_docs[]" value="PP-CN-063" id="chk_pp_cn_063">
                                                                                    <label class="form-check-label fw-medium ms-1" for="chk_pp_cn_063">PP-CN-063</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">PPS</span></td>
                                                                            <td>
                                                                                <div class="form-check m-0">
                                                                                    <input class="form-check-input" type="checkbox" name="engg_orientation_docs[]" value="PP-MDGEN-138" id="chk_pp_mdgen_138">
                                                                                    <label class="form-check-label fw-medium ms-1" for="chk_pp_mdgen_138">PP-MDGEN-138</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><span class="badge bg-secondary text-uppercase px-2 py-1.5">YF</span></td>
                                                                            <td>
                                                                                <div class="form-check m-0">
                                                                                    <input class="form-check-input" type="checkbox" name="engg_orientation_docs[]" value="PP-OPNGEN-017" id="chk_pp_opngen_017">
                                                                                    <label class="form-check-label fw-medium ms-1" for="chk_pp_opngen_017">PP-OPNGEN-017</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="mt-3 mb-3">RESULT</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="" for="">First Take:</label>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Second Take:</label>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label class="" for="">1. Observation / Interview Result</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_obs_first_result_es_oper" id="text_obs_first_result_es_oper">

                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">1. Observation / Interview Result</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_obs_second_result_es_oper" id="text_obs_second_result_es_oper">
                                                            <option value="" disabled selected>Select Result</option>
                                                            <option value="PASSED">PASSED</option>
                                                            <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">2. Sample Checking:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_first_sample_es_oper" id="text_first_sample_es_oper">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">2. Sample Checking:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_second_sample_es_oper" id="text_second_sample_es_oper">
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-3">
                                                    <label class="" for="">OK:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_first_ok_es_oper" id="text_first_ok_es_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">NG:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_first_ng_es_oper" id="text_first_ng_es_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">OK:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_second_ok_es_oper" id="text_second_ok_es_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">NG:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_second_ng_es_oper" id="text_second_ng_es_oper">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">3. Overall Assessment:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_oa_1st_result_es_oper" id="text_oa_1st_result_es_oper">
                                                            <option value="" disabled selected>Select Result</option>
                                                            <option value="PASSED">PASSED</option>
                                                            <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">3. Overall Assessment:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_oa_2nd_result_es_oper" id="text_oa_2nd_result_es_oper">

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Reason for Disqualification:</label>
                                                    <input class="form-control" type="text" id="text_1st_disqualification_es_oper" name="text_1st_disqualification_es_oper">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Reason for Disqualification:</label>
                                                    <input class="form-control" type="text" id="text_2nd_disqualification_es_oper" name="text_2nd_disqualification_es_oper">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Qualified by NMODI:</label>
                                                        <select class="form-control select2bs4" style="width: 100%;" id="text_1st_qualifiedby_es_oper" name="text_1st_qualifiedby_es_oper[]" placeholder="Enter Employee Number" multiple>
                                                        </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Qualified by:</label>

                                                    <select class="form-control select2bs4" style="width: 100%;" id="text_2nd_qualifiedby_es_oper" name="text_2nd_qualifiedby_es_oper[]" placeholder="Enter Employee Number" multiple>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-5">
                                                <div class="col-md-3">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_qc_1st_date_es_oper" name="text_qc_1st_date_es_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Time:</label>
                                                    <input class="form-control" type="time" id="text_qc_1st_time_es_oper" name="text_qc_1st_time_es_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_qc_2nd_date_es_oper" name="text_qc_2nd_date_es_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Time:</label>
                                                    <input class="form-control" type="time" id="text_qc_2nd_time_es_oper" name="text_qc_2nd_time_es_oper">
                                                </div>
                                            </div>

                                            <!-- ------------------------------------------------ -->

                                        </div>
                                        </div>
                                    </div>
                                    <div class="card CQCC">
                                        <h2 class="card-header">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThreeOper" aria-expanded="false" aria-controls="collapseThreeOper">
                                            <h5>C. QUALITY CONTROL SECTION (CERTIFICATTION)</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseThreeOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="card-body">
                                            <p class="mb-3">1. Let the operator discuss the details of training/orientation conducted by concerned Supervisor nad Eng'r as per check items specified.</p>

                                            <h5 class="mt-3 mb-3">RESULT</h5>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="" for="">First Take:</label>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Second Take:</label>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label class="" for="">1.1 Observation / Interview Result</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_obs_first_result_qcs_oper" id="text_obs_first_result_qcs_oper">
                                                        <option value="" disabled selected>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">1.1 Observation / Interview Result</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_obs_second_result_qcs_oper" id="text_obs_second_result_qcs_oper">
                                                        <option value="" disabled selected>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">2. Sample Checking:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_first_sample_qcs_oper" id="text_first_sample_qcs_oper">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">2. Sample Checking:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_second_sample_qcs_oper" id="text_second_sample_qcs_oper">
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-3">
                                                    <label class="" for="">OK:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_first_ok_qcs_oper" id="text_first_ok_qcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">NG:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_first_ng_qcs_oper" id="text_first_ng_qcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">OK:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_second_ok_qcs_oper" id="text_second_ok_qcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">NG:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_second_ng_qcs_oper" id="text_second_ng_qcs_oper">
                                                </div>
                                            </div>

                                            <div class="row mb-1">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table id="" class="table table-bordered table-hover nowrap">
                                                            <thead class="table table-warning">
                                                                <tr class="text-center">
                                                                    <th></th>
                                                                    <th>Station</th>
                                                                    <th>Type of Exam</th>
                                                                    <th>Method</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_1st_oper_1" name="text_qcs_station_1st_oper" value="Visual"></td>
                                                                    <td>Visual</td>
                                                                    <td>Judgement Confirmation</td>
                                                                    <td>Using GRR sample (50pcs.)</td>
                                                                </tr>

                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_1st_oper_2" name="text_qcs_station_1st_oper" value="Assembly"></td>
                                                                    <td>Assembly</td>
                                                                    <td>Judgement Confirmation</td>
                                                                    <td>50 samples drawn from their actual output</td>
                                                                </tr>

                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_1st_oper_3" name="text_qcs_station_1st_oper" value="Others"></td>
                                                                    <td>Others (Parts Prep/Prov. Insertion/Packing/ etc)</td>
                                                                    <td>Work Confirmation</td>
                                                                    <td>50 samples drawn from their actual output</td>
                                                                </tr>

                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_1st_oper_4" name="text_qcs_station_1st_oper" value="Rework Station"></td>
                                                                    <td>Rework Station (PPS only)</td>
                                                                    <td>Work Confirmation</td>
                                                                    <td>50 samples drawn from their actual output</td>
                                                                </tr>

                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_1st_oper_5" name="text_qcs_station_1st_oper" value="Segregation Station"></td>
                                                                    <td>Segregation Station</td>
                                                                    <td>Work Confirmation</td>
                                                                    <td>50 samples drawn from their actual output</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table id="" class="table table-bordered table-hover nowrap">
                                                            <thead class="table table-warning">
                                                                <tr class="text-center">
                                                                    <th></th>
                                                                    <th>Station</th>
                                                                    <th>Type of Exam</th>
                                                                    <th>Method</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_2nd_oper_1" name="text_qcs_station_2nd_oper" value="Visual"></td>
                                                                    <td>Visual</td>
                                                                    <td>Judgement Confirmation</td>
                                                                    <td>Using GRR sample (50pcs.)</td>
                                                                </tr>

                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_2nd_oper_2" name="text_qcs_station_2nd_oper" value="Assembly"></td>
                                                                    <td>Assembly</td>
                                                                    <td>Judgement Confirmation</td>
                                                                    <td>50 samples drawn from their actual output</td>
                                                                </tr>

                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_2nd_oper_3" name="text_qcs_station_2nd_oper" value="Others"></td>
                                                                    <td>Others (Parts Prep/Prov. Insertion/Packing/ etc)</td>
                                                                    <td>Work Confirmation</td>
                                                                    <td>50 samples drawn from their actual output</td>
                                                                </tr>

                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_2nd_oper_4" name="text_qcs_station_2nd_oper" value="Rework Station"></td>
                                                                    <td>Rework Station (PPS only)</td>
                                                                    <td>Work Confirmation</td>
                                                                    <td>50 samples drawn from their actual output</td>
                                                                </tr>

                                                                <tr>
                                                                    <td><input type="checkbox" id="text_qcs_station_2nd_oper_5" name="text_qcs_station_2nd_oper" value="Segregation Station"></td>
                                                                    <td>Segregation Station</td>
                                                                    <td>Work Confirmation</td>
                                                                    <td>50 samples drawn from their actual output</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p for="">** Assembly operators are those who install parts</p>
                                                    <p for="">** if sample size is equals or exceeds on the lot size, conduct 100% inspection</p>
                                                </div>

                                                <div class="col-md-6">
                                                    <p for="">** Assembly operators are those who install parts</p>
                                                    <p for="">** if sample size is equals or exceeds on the lot size, conduct 100% inspection</p>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">3. Overall Assessment:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_oa_1st_result_qcs_oper" id="text_oa_1st_result_qcs_oper">
                                                        <option value="" disabled selected>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">3. Overall Assessment:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_oa_2nd_result_qcs_oper" id="text_oa_2nd_result_qcs_oper">
                                                        <option value="" disabled selected>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Reason for Disapproval:</label>
                                                    <input class="form-control" type="text" id="text_1st_disapproval_qcs_oper" name="text_1st_disapproval_qcs_oper">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Reason for Disapproval:</label>
                                                    <input class="form-control" type="text" id="text_2nd_disapproval_qcs_oper" name="text_2nd_disapproval_qcs_oper">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Certified by:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_1st_certifiedby_qcs_oper" id="text_1st_certifiedby_qcs_oper" multiple>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Certified by:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_2nd_certifiedby_qcs_oper" id="text_2nd_certifiedby_qcs_oper" multiple>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-5">
                                                <div class="col-md-3">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_1st_date_qcs_oper" name="text_1st_date_qcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Time:</label>
                                                    <input class="form-control" type="time" id="text_1st_time_qcs_oper" name="text_1st_time_qcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_2nd_date_qcs_oper" name="text_2nd_date_qcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Time:</label>
                                                    <input class="form-control" type="time" id="text_2nd_time_qcs_oper" name="text_2nd_time_qcs_oper">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-1">
                                                    <label for="">Note:</label>
                                                </div>

                                                <div class="col-md-10">
                                                    <p for="">#1 For PPS Finishing and Visual, complete the certification/qualification at the back page</p>
                                                    <p for="">#2 For Engineering and QC Validation result, please update the back page</p>
                                                </div>
                                            </div>

                                            <!-- ------------------------------------------------ -->

                                        </div>
                                        </div>
                                    </div>
                                    <div class="card PPD">
                                        <h2 class="card-header">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFourOper" aria-expanded="false" aria-controls="collapseFourOper">
                                            <h5>D PRODUCTION, ENGINEERING & QUALITY CONTROL SECTION (Certification-Completion)</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseFourOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="card-body">

                                            <!-- ------------------------------------------------ -->

                                            <p class="mb-3">FOR PPS Visual Operators only</p>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="" for="">First Take:</label>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Second Take:</label>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">1. Lot Qty (1st lot):</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_lot_1st_sample_peqcs_oper" id="text_lot_1st_sample_peqcs_oper">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">1. Lot Qty (1st lot):</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_2nd_sample_peqcs_oper" id="text_2nd_sample_peqcs_oper">
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-3">
                                                    <label class="" for="">Injected NG Qty:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_1st_injected_ng_peqcs_oper" id="text_1st_injected_ng_peqcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Detected NG:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_1st_detected_ng_peqcs_oper" id="text_1st_detected_ng_peqcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Injected NG Qty:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_2nd_injected_ng_peqcs_oper" id="text_2nd_injected_ng_peqcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Detected NG:</label>
                                                    <input class="form-control" type="number" placeholder="Enter sample count" min="0" name="text_2nd_detected_ng_peqcs_oper" id="text_2nd_detected_ng_peqcs_oper">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">2. Overall Assessment:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_oa_1st_result_peqcs_oper" id="text_oa_1st_result_peqcs_oper">
                                                        <option value="" disabled selected>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">2. Overall Assessment:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_oa_2nd_result_peqcs_oper" id="text_oa_2nd_result_peqcs_oper">
                                                        <option value="" disabled selected>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Reason for Disapproval:</label>
                                                    <input class="form-control" type="text" id="text_1st_disapproval_peqcs_oper" name="text_1st_disapproval_peqcs_oper">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Reason for Disapproval:</label>
                                                    <input class="form-control" type="text" id="text_2nd_disapproval_peqcs_oper" name="text_2nd_disapproval_peqcs_oper">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="">Certified by:</label>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="">Certified by:</label>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-1">
                                                    <p for="">Production:</p>
                                                </div>
                                                <div class="col-md-5">

                                                  <select class="form-control select2bs4" style="width: 100%;" name="text_1st_certified_prod_peqcs_oper" id="text_1st_certified_prod_peqcs_oper" multiple>
                                                  </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <p for="">Production:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_2nd_certified_prod_peqcs_oper" id="text_2nd_certified_prod_peqcs_oper" multiple>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-1">
                                                    <p for="">Engineering:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_1st_certified_eng_peqcs_oper" id="text_1st_certified_eng_peqcs_oper" multiple>
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <p for="">Engineering:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_2nd_certified_eng_peqcs_oper" id="text_2nd_certified_eng_peqcs_oper" multiple>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-1">
                                                    <p for="">Quality Control:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_1st_certified_qc_peqcs_oper" id="text_1st_certified_qc_peqcs_oper" multiple>
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <p for="">Quality Control:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_2nd_certified_qc_peqcs_oper" id="text_2nd_certified_qc_peqcs_oper" multiple>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-5">
                                                <div class="col-md-3">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_1st_date_peqcs_oper" name="text_1st_date_peqcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Time:</label>
                                                    <input class="form-control" type="time" id="text_1st_time_peqcs_oper" name="text_1st_time_peqcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_2nd_date_peqcs_oper" name="text_2nd_date_peqcs_oper">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Time:</label>
                                                    <input class="form-control" type="time" id="text_2nd_time_peqcs_oper" name="text_2nd_time_peqcs_oper">
                                                </div>
                                            </div>

                                            <label for="">Note: NG Injection process shall be taken from first lot output</label>

                                            <!-- ------------------------------------------------ -->

                                        </div>
                                        </div>
                                    </div>-
                                    <div class="card EENGGVALIDATION">
                                        <h2 class="card-header">
                                            
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFiveOper" aria-expanded="false" aria-controls="collapseFiveOper">
                                            <h5>VALIDATION PROCESS: ENGINEERING SECTION</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseFiveOper" class="accordion-collapse collapse">
                                            <div class="card-body">
                                                <div class="row mb-1">
                                                    <div class="col-md-3">
                                                        <label class="ms-5" for="">Engineering Validation Result:</label>
                                                    </div>

                                                    <div class="col-md-5"></div>

                                                    <div class="col-md-4">
                                                        <select class="form-control select2bs4" style="width: 100%;" name="text_application_vpes_oper" id="text_application_vpes_oper">
                                                            <option value="" selected disabled>Select</option>
                                                            <option value="1">Applicable</option>
                                                            <option value="2">Not Applicable</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-1"></div>

                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="text_vpes_oper_1" name="text_vpes_oper" value="1" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                            <label class="fs-5  " for="text_vpes_oper_1" style="font-weight: normal;">Pre-production samples and check sheet checking</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <h5 class="mt-3 mb-3">RESULT</h5>

                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <label class="" for="">First Take</label>
                                                        <select class="form-control select2bs4" style="width: 100%;" name="text_first_result_vpes_oper" id="text_first_result_vpes_oper">
                                                            <option value="" disabled selected>Select Result</option>
                                                            <option value="PASSED">PASSED</option>
                                                            <option value="FAILED">FAILED</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="" for="">Second Take</label>
                                                        <select class="form-control select2bs4" style="width: 100%;" name="text_second_result_vpes_oper" id="text_second_result_vpes_oper">
                                                            <option value="" disabled selected>Select Result</option>
                                                            <option value="PASSED">PASSED</option>
                                                            <option value="FAILED">FAILED</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="" for="">Validated by (after 2nd day):</label>
                                                            <select class="form-control select2bs4" style="width: 100%;" name="text_1st_validatedby_vpes_oper" id="text_1st_validatedby_vpes_oper" multiple>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="" for="">Validated by (after re-orientation):</label>
                                                        <select class="form-control select2bs4" style="width: 100%;" name="text_2nd_validatedby_vpes_oper" id="text_2nd_validatedby_vpes_oper" multiple>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="" for="">Date:</label>
                                                        <input class="form-control" type="date" id="text_1st_date_vpes_oper" name="text_1st_date_vpes_oper">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="" for="">Date:</label>
                                                        <input class="form-control" type="date" id="text_2nd_date_vpes_oper" name="text_2nd_date_vpes_oper">
                                                    </div>
                                                </div>

                                                <div class="row mb-5">
                                                    <div class="col-md-12">
                                                        <label class="" for="">Remarks:</label>
                                                        <input class="form-control" type="text" name="text_remarks_vpes_oper" id="text_remarks_vpes_oper" placeholder="Enter remarks">
                                                    </div>
                                                </div>

                                                </div>
                                        </div>
                                    </div> 
                                   {{-- <div class="card FQCVP">
                                        <h2 class="card-header">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFiveOper" aria-expanded="false" aria-controls="collapseSixOper">
                                            <h5>E. VALIDATION PROCESS: QUALITY CONTROL SECTION</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseSixOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="card-body">
                                            <div class="row mb-1">
                                                <div class="col-md-3">
                                                    <label class="ms-5" for="">QC Validation for other section</label>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-4">
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_vpqcs_oper" id="text_vpqcs_oper">
                                                        <option value="" selected disabled>Select</option>
                                                        <option value="1">Production Abnormality Control</option>
                                                        <option value="2">Defect Escalation Procedure</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <h5 class="mt-3 mb-3">RESULT</h5>

                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label class="" for="">First Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_first_result_vpqcs_oper" id="text_first_result_vpqcs_oper">
                                                            <option value="" disabled selected>Select Result</option>
                                                            <option value="PASSED">PASSED</option>
                                                            <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Second Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_second_result_vpqcs_oper" id="text_second_result_vpqcs_oper">
                                                            <option value="" disabled selected>Select Result</option>
                                                            <option value="PASSED">PASSED</option>
                                                            <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Validated by (after 2nd day):</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_1st_validatedby_vpqcs_oper[]" id="text_1st_validatedby_vpqcs_oper" multiple>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 nmodify2">
                                                    <label class="" for="">Validated by (after re-orientation):</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_2nd_validatedby_vpqcs_oper[]" id="text_2nd_validatedby_vpqcs_oper" multiple>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_1st_date_vpqcs_oper" name="text_1st_date_vpqcs_oper">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_2nd_date_vpqcs_oper" name="text_2nd_date_vpqcs_oper">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="" for="">Remarks:</label>
                                                    <input class="form-control" type="text" name="text_remarks_vpqcs_oper" id="text_remarks_vpqcs_oper" placeholder="Enter remarks">
                                                </div>
                                            </div>

                                            <!-- ************************************************************ 2ND SECTION VPQCS ******************************************************************* -->

                                            <hr style="height: 5px; background-color: black; border: none;">
                                                <div class="col-md-4">
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_application_vpqcs_oper" id="text_application_vpqcs_oper">
                                                        <option value="" selected disabled>Select</option>
                                                        <option value="1">Pre-production samples and check sheet checking</option>
                                                        <option value="2">Applicable</option>
                                                        <option value="3">Not Applicable</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <h5 class="mt-3 mb-3">RESULT</h5>

                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label class="" for="">First Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_first_result_vpes_oper_2" id="text_first_result_vpes_oper_2">
                                                            <option value="" disabled selected>Select Result</option>
                                                            <option value="PASSED">PASSED</option>
                                                            <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Second Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_second_result_vpes_oper_2" id="text_second_result_vpes_oper_2">
                                                            <option value="" disabled selected>Select Result</option>
                                                            <option value="PASSED">PASSED</option>
                                                            <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3 nmodify">
                                                <div class="col-md-6">
                                                    <label class="" for="">Validated by (after 3rd day):</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_1st_validatedby_vpes_oper_2[]" id="text_1st_validatedby_vpes_oper_2" multiple>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Validated by (after re-orientation):</label>
                                                     <select class="form-control select2bs4" style="width: 100%;" name="text_2nd_validatedby_vpes_oper_2[]" id="text_2nd_validatedby_vpes_oper_2" multiple>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_1st_date_vpes_oper_2" name="text_1st_date_vpes_oper_2">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_2nd_date_vpes_oper_2" name="text_2nd_date_vpes_oper_2">
                                                </div>
                                            </div>

                                            <div class="row mb-5">
                                                <div class="col-md-12">
                                                    <label class="" for="">Remarks:</label>
                                                    <input class="form-control" type="text" name="text_remarks_vpes_oper_2" id="text_remarks_vpes_oper_2" placeholder="Enter remarks">
                                                </div>
                                            </div>

                                            <!-- ------------------------------------------------ -->

                                        </div>
                                        </div>
                                    </div>
                                    <div class="card GVVO">
                                        <h2 class="card-header">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseSevenOper" aria-expanded="false" aria-controls="collapseSevenOper">
                                            <h5>F. QC Validation for Visual Operator</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseSevenOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="card-body">

                                            <h6 class="mt-3 mb-3">RESULT:</h6>
                                            <div class="col-md-4">
                                                <label class="ms-3" for="">First Take:</label>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="ms-4" for="">Reference Document</label>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <div class="form-check ms-5">
                                                        <input class="form-check-input" type="checkbox" id="text_refdoc_qcvvo_oper" name="text_refdoc_qcvvo_oper[]" value="Work Instruction Document" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_refdoc_qcvvo_oper" style="font-weight: normal;">1. Work Instruction Document</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <input class="form-control" type="text" id="text_refdocno_input_qcvvo_oper" name="text_refdocno_input_qcvvo_oper" placeholder="Enter the reference document number">
                                                </div>
                                            </div>
                                            <div class="row mb-5">
                                                <div class="table-responsive mt-3 mb-5">
                                                    <table id="tbl_fvi_operator" class="table table-bordered table-hover nowrap">
                                                        <thead class="table-primary">
                                                            <tr>
                                                                <th>Employee No.</th>
                                                                <th>Employee Name</th>
                                                                <th>Discuss the Inspection Sequence in Details</th>
                                                                <th>Assessment Result</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row mb-5">
                                                <div class="col-md-4" nmodify6>
                                                    <label class="ms-3" for="">Validated by:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_validated1_qcvvo_oper[]" id="text_validated1_qcvvo_oper" multiple>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="ms-3" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_date1_qcvvo_oper" name="text_date1_qcvvo_oper">
                                                </div>
                                            </div>



                                            <hr style="height: 5px; background-color: black; border: none;">
                                            <h6 class="mt-3 mb-3">RESULT:</h6>
                                                <div class="row mb-3">
                                                   <div class="col-md-4">
                                                    <label class="ms-3" for="">Second Take:</label>
                                                 </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="ms-4" for="">Reference Document</label>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <div class="form-check ms-5">
                                                        <input class="form-check-input" type="checkbox" id="text_refdoc_qcvvo_oper_2" name="text_refdoc_qcvvo_oper_2[]" value="Work Instruction Document" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_refdoc_qcvvo_oper_2" style="font-weight: normal;">1. Work Instruction Document</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <input class="form-control" type="text" id="text_refdocno_input_qcvvo_oper_2" name="text_refdocno_input_qcvvo_oper_2" placeholder="Enter the reference document number">
                                                </div>
                                            </div>
                                            <div class="row mb-5">
                                                <div class="table-responsive mt-3 mb-5">
                                                    <table id="tbl_fvi_operator_2" class="table table-bordered table-hover nowrap">
                                                        <thead class="table-primary">
                                                            <tr>
                                                                <th>Employee No.</th>
                                                                <th>Employee Name</th>
                                                                <th>Discuss the Inspection Sequence in Details</th>
                                                                <th>Assessment Result</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-md-4 nmodify6">
                                                    <label class="ms-3" for="">Validated by:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_validated2_qcvvo_oper[]" id="text_validated2_qcvvo_oper" multiple>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="ms-3" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_date2_qcvvo_oper" name="text_date2_qcvvo_oper">
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-12">
                                                    <label for="">
                                                    Note #3: Do not combine machine operator's orientation to other station or process in (one) 1 slip
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table id="" class="table table-bordered table-hover nowrap">
                                                            <thead class="table table-warning">
                                                                <tr class="text-center">
                                                                    <th>Designation</th>
                                                                    <th>Training / Orientation</th>
                                                                    <th>Qualifier</th>
                                                                    <th>Certifier</th>
                                                                    <th>Approver / Confirmation</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <tr>
                                                                    <td>Operator</td>
                                                                    <td>Supervisor and/or Material Handler</td>
                                                                    <td>Process Engineer</td>
                                                                    <td>QC Inspector</td>
                                                                    <td>QC Supervisor</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ------------------------------------------------ -->

                                        </div>
                                        </div>
                                    </div>  --}}
                                </div>

                                <hr style="height: 5px; background-color: black; border: none;">

                                <div class="col-md-6 nmodify3">
                                    <label for="">Approved / Confirmed by:</label>
                                     <select class="form-control select2bs4" style="width: 100%;" name="text_oper_approved_confirmed_by[]" id="text_oper_approved_confirmed_by" multiple>
                                    </select>
                                    <label for="" class="mt-1">QC Supervisor</label>
                                </div>
                                <div class="modal-footer justify-content-between">

                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="operClosed"><i class="fa-solid fa fa-xmark me-2" style="color: white"></i>Close</button>
                                    <button type="submit" class="btn btn-success" id="operSave"><i class="fa-solid fa fa-save me-2" style="color: white"></i> Save</button>

                                    <button type="button" class="btn btn-danger d-none" id="operDisapproved"><i class="fa-solid fa fa-thumbs-down me-2" style="color: white d-none"></i>Disapproved</button>
                                    <button type="button" class="btn btn-success" id="operApproved"><i class="fa-solid fa fa-thumbs-up me-2" style="color: white"></i> Approved</button>
                                </div>
                        </form>

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
            formSubmitOper: $('#formSubmitOper'),
        };
        dataTable = {
            operator: '',
            fvi_operator: '',
            tbl_fvi_operator_2: '',
        };
        table = {
           operator: '#tbl_operator',
           fvi_operator: '#tbl_fvi_operator',
           tbl_fvi_operator_2: '#tbl_fvi_operator_2',
        };


        const updateApproval = (params) => {
            let data = {
                decision : params.decision,
                qcSlipsId : params.qcSlipsId
            }
            call_ajax_serialize(data, {}, 'update_approval', function (response) {
                if (response && response.is_success === 'true') {
                    // Swal.fire({ icon: 'success', title: 'Success', text: response.message || 'Approval status updated.' });
                    // dataTable.operator.ajax.reload(null, false);
                    // $('#modalCreateCQForm').modal('hide');
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: (response && response.message) ? response.message : 'Failed to update approval status.' });
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
                swalConfirmAction('Are you sure you want to DISAPPROVED this request?', function () {
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
            swalConfirmAction('Are you sure you want to APPROVED this request?', function () {
                updateApproval(params);
            });
        });

         $(document).on('click', '#btnCreateCQForm',function (e) {
            form.formSubmitOper[0].reset();
             initDropdownMasterDetailsByFkidCombos([
                '#text_oper_station_to',
                '#text_oper_station_from',
            ],1);
            initDropdownMasterDetailsByFkidCombos([
                '#text_operator_product_line',
            ],2);
            initDropdownMasterDetailsByFkidCombos([
                    '#text_certification_operator',
            ],3);
            initDropdownMasterDetailsByFkidCombos([
                    '#text_training_orientation_ps_oper',
            ],4);
            initDropdownMasterDetailsByFkidCombos([
                    '#transfer_flexibility',
                    '#text_training_orientation_es_oper',
            ],5);
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
            fixedHeader: true,
            "columns":[
                // { "data" : "rawBulkCheckBox", orderable:false, searchable:false },
                { "data" : "rawAction", orderable:false, searchable:false },
                { "data" : "rawStatus", orderable:false, searchable:false },
                { "data" : "control_no" },
                { "data" : "series_name" },
                { "data" : "approvers" },
                // { "data" : "certified_by" },
                // { "data" : "approved_conformed_by" },
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
        $('#operDisapproved').addClass('d-none');
        $('#operApproved').addClass('d-none');
        $('#operClosed').removeClass('d-none');
        $('#operSave').removeClass('d-none');

        //  Best Practice: Event Delegation with correct object scoping
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
            console.log('btnGetQcSlipsId clicked');

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

            const targetIDs = ['213', '216'];

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

            const targetIDs = ['213', '216'];

            const hasValue = valuesArray.some(val => targetIDs.includes(val.trim()));

            $('.div-transfer-flexibility').toggleClass('d-none', !hasValue);
         });

         const saveFormOper = ($form) => {

            // 1. Serialize standard form inputs into an array
            var formArray = $form.serializeArray();

            // 2. Push extra custom field values manually
            formArray.push({ name: 'text_alert_prod_sec', value: $('#text_alert_prod_sec').val() });
            formArray.push({ name: 'text_alert_prod_cc_sec', value: $('#text_alert_prod_cc_sec').val() });
            formArray.push({ name: 'text_select_position', value: $('#text_select_position').val() });
            formArray.push({ name: 'text_select_section', value: $('#select_section').val() });

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
            });

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
                    form.formSubmitOper[0].reset();
                } else {
                    // Swal.fire({ icon: 'error', title: 'Error', text: (response && response.message) ? response.message : 'Failed to save.' });
                }
            });
         }
        // $(selector).click(function (e) {
        //     e.preventDefault();

        // });
         $('#formSendEmail').click(function (e) {
            e.preventDefault();
            var $form = $('#formSubmitOper,#formSubmitOper');
            saveFormOper($form);
        });
        $(document).on('submit', '#formSubmitOper, #formSubmitOper', function (e) {
            e.preventDefault();
            var $form = $(this);
            if($('#approval_status').val() === "FQCVVO"){
                //TODO: Swal fire
                saveFormOper($form);
            }else{
                $('#modalSendEmail').modal();
            }
        });

        var $positionSelect = $('#text_select_position');
        var $positionSections = $('#divMH, #divTechnian, #divSEP, #divInspector, #div_Oper');

        function togglePositionSection(position) {
            $positionSections.addClass('d-none');

            switch (position) {
                case 'MH':
                    $('#divMH').removeClass('d-none');
                    break;
                case 'Technician':
                    $('#divTechnian').removeClass('d-none');
                    break;
                case 'Supervisor':
                case 'Engineer':
                case 'Planner':
                    $('#divSEP').removeClass('d-none');
                    break;
                case 'Inspector':
                    $('#divInspector').removeClass('d-none');
                    break;
                case 'Operator':
                    $('#div_Oper').removeClass('d-none');
                    break;
            }
        }

        $positionSelect.on('change', function () {
            togglePositionSection($(this).val());
        });

        togglePositionSection($positionSelect.val());

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

        ]);
        initSelectPassFail([
            '#text_oa_1st_result_es_oper',
            '#text_obs_first_result_es_oper',
            '#text_oa_2nd_result_es_oper',
        ]);

        // Delete a row from the FVI table
        $(document).on('click', '#tbl_fvi_operator .btn-delete-fvi-row', function () {
            $(this).closest('tr').remove();
        });
    });

    </script>
@endsection
