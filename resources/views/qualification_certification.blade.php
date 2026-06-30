
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
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateCQForm"><i class="fa fa-plus fa-md mr-2"></i>Certify Employee</button>
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
                                                        <table id="tbl_mh" class="table table-striped table-hover table-bordered nowrap">
                                                            <thead class="table-primary">
                                                                <tr>
                                                                <th>Action</th>
                                                                <th>Ctrl No. / Doc No.</th>
                                                                <th>Date Filed</th>
                                                                <th>Trained by</th>
                                                                <!-- <th>Qualified by</th> -->
                                                                <th>Certified by</th>
                                                                <th>Approved / Conformed by</th>
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
                                <x-section-select name="select_section" id="select_section" label="Select Section" />
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
                        <form  id="formSubmit_Oper" >
                            @csrf
                                <h3 class="mt-5 mb-3 text-center">OPERATOR'S TRAINING / QUALIFICATION / CERTIFICATION SLIP</h3>

                                <div class="row mb-5">
                                    <div class="col-md-3">
                                        <label for="">Control No.:</label>
                                        <input class="form-control" type="hidden" class="form-control d-none" id="textconno_new_operator" name="textconno_new_operator" placeholder="Select section to generate Control No." readonly>
                                        <input class="form-control" type="text" class="form-control" id="" name="textconno_new_operator" placeholder="Auto Generated" readonly>
                                        <input class="form-control" type="hidden" class="form-control" id="textconno_operator" name="textconno_operator" readonly>
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
                                             <option value="1">3.1 Transfer to another station (E.g: final visual, insertion,IQC, IPQC, OQC, etc)</option>
                                            <option value="2">3.2 Transfer to other production section (E.g: TS,PPS,CN, YF)</option>
                                            <option value="3">3.3 Transfer to other product line (E.g: TS: BGA-FP, QFP; CN: FMS, PJS; YF: EOL, FOL; PPS: Molding CN, Molding TS, Grinding, Stamping; MH-WHS, MH-Prodn)
                                        </select>
                                    </div>
                                </div>

                                <!-- **************************************************************      APRODTO          ************************************************************************************************* -->

                                <div class="accordion" id="accordionExampleOper">
                                    {{-- <div class="card APRODTO">
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
                                                            <option value="" selected disabled>Select Result</option>
                                                            <option value="1" >Rule when to escalate </option>
                                                            <option value="2" >Filling-up of forms
                                                            </option>
                                                        </select>
                                                 </div>
                                                <div class="col-md-3">
                                                        <label class="" for="">Production Abnormlity Control (IMS-PMI-025):</label>
                                                        <select class="form-control select2bs4" style="width: 100%;" id="production_abnormality" name="production_abnormality" multiple>
                                                            <option value="" selected disabled>Select Result</option>
                                                            <option value="1" >Rule when to escalate </option>
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
                                                            <label class="" for="">Trained by:</label>
                                                            <select class="form-control select2bs4" style="width: 100%;" id="text_first_trainedby_oper" name="text_first_trainedby_oper" multiple></select>

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
                                                            <select class="form-control select2bs4" style="width: 100%;" id="text_second_trainedby_oper" name="text_second_trainedby_oper" multiple></select>

                                                            <input type="hidden" id="text_second_trainedby_oper_username" name="text_second_trainedby_oper_username">
                                                            <input type="hidden" id="text_second_trainedby_oper_email" name="text_second_trainedby_oper_email" multiple>
                                                        </div>
                                                        <div class="col-md-6">
                                                             <label class="" for="">Mentored by:</label>
                                                            <select class="form-control select2bs4" style="width: 100%;" id="text_second_mentoredby_oper" name="text_second_mentoredby_oper"></select>
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

                                            <div class="row mb-2">
                                                <div class="col-md-6"></div>
                                                <div class="col-md-3">
                                                    <label class="" for="">Send Email Alert to:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" id="text_alert_prod_sec" name="text_alert_prod_sec"></select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="" for="">Add cc:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" id="text_alert_prod_cc_sec" name="text_alert_prod_cc_sec" multiple></select>

                                                </div>
                                            </div>
                                            <!-- ------------------------------------------------ -->

                                        </div>
                                        </div>
                                    </div> 
                                    <div class="card BENGGTQ">
                                        <h2 class="card-header">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwoOper" aria-expanded="false" aria-controls="collapseTwoOper">
                                            <h5>ENGINEERING SECTION (Training and Qualification)</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseTwoOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="card-body">
                                            <p class="mb-3">TRAINING ITEMS:</p>
                                            <div class="col-md-3">
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
                                                        <option value="" selected disabled>Select Result</option>
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
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_oa_1st_result_es_oper" id="text_oa_1st_result_es_oper" multiple>
                                                        <option value="" selected disabled>Select Result</option>
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
                                    </div>--}}
                                    <div class="card">
                                        <h2 class="accordion-header">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThreeOper" aria-expanded="false" aria-controls="collapseThreeOper">
                                            <h5>QUALITY CONTROL SECTION (CERTIFICATTION)</h5>
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
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">1.1 Observation / Interview Result</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_obs_second_result_qcs_oper" id="text_obs_second_result_qcs_oper">
                                                        <option value="" selected disabled>Select Result</option>
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
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">3. Overall Assessment:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_oa_2nd_result_qcs_oper" id="text_oa_2nd_result_qcs_oper">
                                                        <option value="" selected disabled>Select Result</option>
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
                                                    <input class="form-control" type="text" id="text_1st_certifiedby_qcs_oper" name="text_1st_certifiedby_qcs_oper" list="list_display_empno" placeholder="Select Certified by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_1st_certifiedby_qcs_oper_username" name="text_1st_certifiedby_qcs_oper_username">
                                                    <input type="hidden" id="text_1st_certifiedby_qcs_oper_email" name="text_1st_certifiedby_qcs_oper_email">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Certified by:</label>
                                                    <input class="form-control" type="text" id="text_2nd_certifiedby_qcs_oper" name="text_2nd_certifiedby_qcs_oper" list="list_display_empno" placeholder="Select Certified by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_2nd_certifiedby_qcs_oper_username" name="text_2nd_certifiedby_qcs_oper_username">
                                                    <input type="hidden" id="text_2nd_certifiedby_qcs_oper_email" name="text_2nd_certifiedby_qcs_oper_email">
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
                                    {{--  <div class="card">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseFourOper" aria-expanded="false" aria-controls="collapseFourOper">
                                            <h5>PRODUCTION, ENGINEERING & QUALITY CONTROL SECTION (Certification-Completion)</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseFourOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="accordion-body">

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
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">2. Overall Assessment:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_oa_2nd_result_peqcs_oper" id="text_oa_2nd_result_peqcs_oper">
                                                        <option value="" selected disabled>Select Result</option>
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
                                                    <input class="form-select mb-2" type="text" id="text_1st_certified_prod_peqcs_oper" name="text_1st_certified_prod_peqcs_oper" list="list_display_empno" placeholder="Select Prod'n Certified by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_1st_certified_prod_peqcs_oper_username" name="text_1st_certified_prod_peqcs_oper_username">
                                                    <input type="hidden" id="text_1st_certified_prod_peqcs_oper_email" name="text_1st_certified_prod_peqcs_oper_email">
                                                </div>

                                                <div class="col-md-1">
                                                    <p for="">Production:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <input class="form-select mb-2" type="text" id="text_2nd_certified_prod_peqcs_oper" name="text_2nd_certified_prod_peqcs_oper" list="list_display_empno" placeholder="Select Prod'n Certified by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_2nd_certified_prod_peqcs_oper_username" name="text_2nd_certified_prod_peqcs_oper_username">
                                                    <input type="hidden" id="text_2nd_certified_prod_peqcs_oper_email" name="text_2nd_certified_prod_peqcs_oper_email">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-1">
                                                    <p for="">Engineering:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <input class="form-select mb-2" type="text" id="text_1st_certified_eng_peqcs_oper" name="text_1st_certified_eng_peqcs_oper" list="list_display_empno" placeholder="Select Eng'g Certified by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_1st_certified_eng_peqcs_oper_username" name="text_1st_certified_eng_peqcs_oper_username">
                                                    <input type="hidden" id="text_1st_certified_eng_peqcs_oper_email" name="text_1st_certified_eng_peqcs_oper_email">
                                                </div>

                                                <div class="col-md-1">
                                                    <p for="">Engineering:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <input class="form-select mb-2" type="text" id="text_2nd_certified_eng_peqcs_oper" name="text_2nd_certified_eng_peqcs_oper" list="list_display_empno" placeholder="Select Eng'g Certified by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_2nd_certified_eng_peqcs_oper_username" name="text_2nd_certified_eng_peqcs_oper_username">
                                                    <input type="hidden" id="text_2nd_certified_eng_peqcs_oper_email" name="text_2nd_certified_eng_peqcs_oper_email">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-1">
                                                    <p for="">Quality Control:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <input class="form-select mb-2" type="text" id="text_1st_certified_qc_peqcs_oper" name="text_1st_certified_qc_peqcs_oper" list="list_display_empno" placeholder="Select QC Certified by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_1st_certified_qc_peqcs_oper_username" name="text_1st_certified_qc_peqcs_oper_username">
                                                    <input type="hidden" id="text_1st_certified_qc_peqcs_oper_email" name="text_1st_certified_qc_peqcs_oper_email">
                                                </div>

                                                <div class="col-md-1">
                                                    <p for="">Quality Control:</p>
                                                </div>

                                                <div class="col-md-5">
                                                    <input class="form-select mb-2" type="text" id="text_2nd_certified_qc_peqcs_oper" name="text_2nd_certified_qc_peqcs_oper" list="list_display_empno" placeholder="Select QC Certified by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_2nd_certified_qc_peqcs_oper_username" name="text_2nd_certified_qc_peqcs_oper_username">
                                                    <input type="hidden" id="text_2nd_certified_qc_peqcs_oper_email" name="text_2nd_certified_qc_peqcs_oper_email">
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
                                    </div>
                                    <div class="card">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseFiveOper" aria-expanded="false" aria-controls="collapseFiveOper">
                                            <h5>VALIDATION PROCESS: ENGINEERING SECTION</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseFiveOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="accordion-body">

                                            <!-- ------------------------------------------------ -->

                                            <div class="row mb-1">
                                                <div class="col-md-3">
                                                    <label class="ms-5" for="">Engineering Validation Result:</label>
                                                </div>

                                                <div class="col-md-5"></div>

                                                <div class="col-md-4">
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_application_vpes_oper" id="text_application_vpes_oper">
                                                        <option value="" selected disabled>Select</option>
                                                        <option value="Applicable">Applicable</option>
                                                        <option value="Not Applicable">Not Applicable</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-1"></div>

                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="text_vpes_oper_1" name="text_vpes_oper" value="Pre-production samples and check sheet checking" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_vpes_oper_1" style="font-weight: normal;">Pre-production samples and check sheet checking</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <h5 class="mt-3 mb-3">RESULT</h5>

                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label class="" for="">First Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_first_result_vpes_oper" id="text_first_result_vpes_oper">
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Second Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_second_result_vpes_oper" id="text_second_result_vpes_oper">
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Validated by (after 2nd day):</label>
                                                    <input class="form-control" type="text" id="text_1st_validatedby_vpes_oper" name="text_1st_validatedby_vpes_oper" list="list_display_empno" placeholder="Select Validated by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_1st_validatedby_vpes_oper_username" name="text_1st_validatedby_vpes_oper_username">
                                                    <input type="hidden" id="text_1st_validatedby_vpes_oper_email" name="text_1st_validatedby_vpes_oper_email">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Validated by (after re-orientation):</label>
                                                    <input class="form-control" type="text" id="text_2nd_validatedby_vpes_oper" name="text_2nd_validatedby_vpes_oper" list="list_display_empno" placeholder="Select Validated by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_2nd_validatedby_vpes_oper_username" name="text_2nd_validatedby_vpes_oper_username">
                                                    <input type="hidden" id="text_2nd_validatedby_vpes_oper_email" name="text_2nd_validatedby_vpes_oper_email">
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

                                            <!-- ------------------------------------------------ -->

                                        </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseSixOper" aria-expanded="false" aria-controls="collapseSixOper">
                                            <h5>VALIDATION PROCESS: QUALITY CONTROL SECTION</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseSixOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="accordion-body">

                                            <!-- ------------------------------------------------ -->

                                            <div class="row mb-1">
                                                <div class="col-md-3">
                                                    <label class="ms-5" for="">QC Validation for other section</label>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-1"></div>

                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="text_vpqcs_oper_1" name="text_vpqcs_oper" value="Production Abnormality Control" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_vpqcs_oper_1" style="font-weight: normal;">Production Abnormality Control</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="text_vpqcs_oper_2" name="text_vpqcs_oper" value="Defect Escalation Procedure" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_vpqcs_oper_2" style="font-weight: normal;">Defect Escalation Procedure</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <h5 class="mt-3 mb-3">RESULT</h5>

                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label class="" for="">First Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_first_result_vpqcs_oper" id="text_first_result_vpqcs_oper">
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Second Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_second_result_vpqcs_oper" id="text_second_result_vpqcs_oper">
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Validated by (after 2nd day):</label>
                                                    <input class="form-control" type="text" id="text_1st_validatedby_vpqcs_oper" name="text_1st_validatedby_vpqcs_oper" list="list_display_empno" placeholder="Select Validated by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_1st_validatedby_vpqcs_oper_username" name="text_1st_validatedby_vpqcs_oper_username">
                                                    <input type="hidden" id="text_1st_validatedby_vpqcs_oper_email" name="text_1st_validatedby_vpqcs_oper_email">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Validated by (after re-orientation):</label>
                                                    <input class="form-control" type="text" id="text_2nd_validatedby_vpqcs_oper" name="text_2nd_validatedby_vpqcs_oper" list="list_display_empno" placeholder="Select Validated by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_2nd_validatedby_vpqcs_oper_username" name="text_2nd_validatedby_vpqcs_oper_username">
                                                    <input type="hidden" id="text_2nd_validatedby_vpqcs_oper_email" name="text_2nd_validatedby_vpqcs_oper_email">
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

                                            <div class="row mt-3 mb-1">
                                                <div class="col-md-4">
                                                    <div class="form-check ms-5">
                                                        <input class="form-check-input" type="checkbox" id="text_vpqcs_oper_1_1" name="text_vpqcs_oper_1" value="Pre-production samples and check sheet checking" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_vpqcs_oper_1_1" style="font-weight: normal;">Pre-production samples and check sheet checking</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-4"></div>

                                                <div class="col-md-4">
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_application_vpqcs_oper" id="text_application_vpqcs_oper">
                                                        <option value="" selected disabled>Select</option>
                                                        <option value="Applicable">Applicable</option>
                                                        <option value="Not Applicable">Not Applicable</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <h5 class="mt-3 mb-3">RESULT</h5>

                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label class="" for="">First Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_first_result_vpes_oper_2" id="text_first_result_vpes_oper_2">
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Second Take</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_second_result_vpes_oper_2" id="text_second_result_vpes_oper_2">
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="" for="">Validated by (after 3rd day):</label>
                                                    <input class="form-control" type="text" id="text_1st_validatedby_vpes_oper_2" name="text_1st_validatedby_vpes_oper_2" list="list_display_empno" placeholder="Select Validated by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_1st_validatedby_vpes_oper_2_username" name="text_1st_validatedby_vpes_oper_2_username">
                                                    <input type="hidden" id="text_1st_validatedby_vpes_oper_2_email" name="text_1st_validatedby_vpes_oper_2_email">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="" for="">Validated by (after re-orientation):</label>
                                                    <input class="form-control" type="text" id="text_2nd_validatedby_vpes_oper_2" name="text_2nd_validatedby_vpes_oper_2" list="list_display_empno" placeholder="Select Validated by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_2nd_validatedby_vpes_oper_2_username" name="text_2nd_validatedby_vpes_oper_2_username">
                                                    <input type="hidden" id="text_2nd_validatedby_vpes_oper_2_email" name="text_2nd_validatedby_vpes_oper_2_email">
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
                                    <div class="card">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseSevenOper" aria-expanded="false" aria-controls="collapseSevenOper">
                                            <h5>QC Validation for Visual Operator</h5>
                                        </button>
                                        </h2>
                                        <div id="collapseSevenOper" class="accordion-collapse collapse" data-parent="#accordionExampleOper">
                                        <div class="accordion-body">

                                            <!-- ------------------------------------------------ -->

                                            <p><> Reference Document</p>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="ms-4" for="">Reference Document</label>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="">Discuss the inspection sequence in detail.</label>
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

                                                <div class="col-md-1">
                                                    <div class="form-check ms-5">
                                                        <input class="form-check-input" type="checkbox" id="text_ins_seq_qcvvo_oper_yes" name="text_ins_seq_qcvvo_oper" value="Yes" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_ins_seq_qcvvo_oper_yes" style="font-weight: normal;">Yes</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-check ms-5">
                                                        <input class="form-check-input" type="checkbox" id="text_ins_seq_qcvvo_oper_no" name="text_ins_seq_qcvvo_oper" value="No" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_ins_seq_qcvvo_oper_no" style="font-weight: normal;">No</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="mt-3 mb-3">RESULT:</h6>

                                            <div class="row mb-5">
                                                <div class="col-md-4">
                                                    <label class="ms-3" for="">First Take:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_result1__qcvvo_oper" id="text_result1__qcvvo_oper">
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="ms-3" for="">Validated by:</label>
                                                    <input class="form-control" type="text" id="text_validated1_qcvvo_oper" name="text_validated1_qcvvo_oper" list="list_display_empno" placeholder="Select Validated by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_validated1_qcvvo_oper_username" name="text_validated1_qcvvo_oper_username">
                                                    <input type="hidden" id="text_validated1_qcvvo_oper_email" name="text_validated1_qcvvo_oper_email">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="ms-3" for="">Date:</label>
                                                    <input class="form-control" type="date" id="text_date1_qcvvo_oper" name="text_date1_qcvvo_oper">
                                                </div>
                                            </div>

                                            <p><> Reference Document</p>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="ms-4" for="">Reference Document</label>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="">Discuss the inspection sequence in detail.</label>
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

                                                <div class="col-md-1">
                                                    <div class="form-check ms-5">
                                                        <input class="form-check-input" type="checkbox" id="text_ins_seq_qcvvo_oper_yes_2" name="text_ins_seq_qcvvo_oper_2" value="Yes" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_ins_seq_qcvvo_oper_yes_2" style="font-weight: normal;">Yes</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-check ms-5">
                                                        <input class="form-check-input" type="checkbox" id="text_ins_seq_qcvvo_oper_no_2" name="text_ins_seq_qcvvo_oper_2" value="No" style="width: 1.2rem; height: 1.2rem; border: 2px solid black; accent-color: #007bff;">
                                                        <label class="fs-5  " for="text_ins_seq_qcvvo_oper_no_2" style="font-weight: normal;">No</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="mt-3 mb-3">RESULT:</h6>

                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <label class="ms-3" for="">Second Take:</label>
                                                    <select class="form-control select2bs4" style="width: 100%;" name="text_result2__qcvvo_oper" id="text_result2__qcvvo_oper">
                                                        <option value="" selected disabled>Select Result</option>
                                                        <option value="PASSED">PASSED</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="ms-3" for="">Validated by:</label>
                                                    <input class="form-control" type="text" id="text_validated2_qcvvo_oper" name="text_validated2_qcvvo_oper" list="list_display_empno" placeholder="Select Validated by">
                                                    <datalist id="list_display_empno"></datalist>

                                                    <input type="hidden" id="text_validated2_qcvvo_oper_username" name="text_validated2_qcvvo_oper_username">
                                                    <input type="hidden" id="text_validated2_qcvvo_oper_email" name="text_validated2_qcvvo_oper_email">
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
                                    </div> --}}
                                </div>

                                <hr style="height: 5px; background-color: black; border: none;">

                                <!-- <h4 class="mb-3">PRODUCTION SECTION (Training and Orientation)</h4> -->

                                <!-- **************************************************************      2ND SECTION          ************************************************************************************************* -->

                                <!-- <hr style="height: 5px; background-color: black; border: none;"> -->

                                <!-- ************************************************************ 3RD SECTION ******************************************************************* -->

                                <!-- <hr style="height: 5px; background-color: black; border: none;"> -->

                                <!-- ************************************************************ 4TH SECTION - BACK PAGE - ******************************************************************* -->

                                <!-- <hr style="height: 5px; background-color: black; border: none;"> -->

                                <!-- ************************************************************ 5TH SECTION - BACK PAGE - ******************************************************************* -->

                                <!-- <hr style="height: 5px; background-color: black; border: none;"> -->

                                <!-- ************************************************************ 6TH SECTION - BACK PAGE - ******************************************************************* -->

                                <!-- <hr style="height: 5px; background-color: black; border: none;"> -->

                                <!-- ************************************************************ 6TH SECTION - BACK PAGE - ******************************************************************* -->

                                <!-- <hr style="height: 5px; background-color: black; border: none;"> -->

                                <div class="col-md-6">
                                    <label for="">Approved / Confirmed by:</label>
                                    <input class="form-control" type="text" id="text_oper_approved_confirmed_by" name="text_oper_approved_confirmed_by" list="list_display_empno" placeholder="Select Approved / Confirmed by">
                                    <datalist id="list_display_empno"></datalist>

                                    <label for="" class="mt-1">QC Supervisor</label>

                                    <input type="hidden" id="text_oper_approved_confirmed_by_username" name="text_oper_approved_confirmed_by_username">
                                    <input type="hidden" id="text_oper_approved_confirmed_by_email" name="text_oper_approved_confirmed_by_email">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa-solid fa-xmark me-2" style="color: white"></i>CLOSE</button>
                                    <button type="submit" class="btn btn-success" id="addNew"><i class="fa-solid fa-file-import me-2" style="color: white"></i>SUBMIT</button>
                                </div>

                        </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Select Employee for Operator -->
        <div class="modal" id="select_Employee_operator" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Qualification / Certification Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">



                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="">Station (From):</label>

                                 <select class="form-control select2bs4" style="width: 100%;" name="text_oper_station_from" id="text_oper_station_from" placeholder="Enter Station (From)">
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="">Station (To):</label>
                                  <select class="form-control select2bs4" style="width: 100%;" name="text_oper_station_to" id="text_oper_station_to" placeholder="Enter Station (From)">
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="">Operator Name:</label>
                                <select class="form-control select2bs4" style="width: 100%;" id="text_oper_emp_number" name="text_oper_emp_number" placeholder="Enter Employee Number">
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">Remarks:</label>
                                <input type="text" class="form-control" id="text_oper_remarks" name="text_oper_remarks" placeholder="Enter Remarks">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btnAddOPEREmp" id="btnAddOPEREmp"><i class="fa-solid fa-plus"></i> Add to Table</button>

                        <hr style="border: 1px solid black;">

                        <h5 class="mb-3">Selected Employees:</h5>

                        <table class="table table-bordered tbl_oper_add_emp" id="tbl_oper_add_emp">
                            <thead class="table-info">
                                <tr>
                                    <th>Action</th>
                                    <th>Employee No.</th>
                                    <th>Employee Name</th>
                                    <th>Station (From)</th>
                                    <th>Station (To)</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <tr>
                                    <td>Paul Bariring</td>
                                    <td>PB0328</td>
                                    <td>Gas Station</td>
                                    <td>Police Station</td>
                                </tr>
                                <tr>
                                    <td>Jowee Maramag</td>
                                    <td>JM1213</td>
                                    <td>Radio Station</td>
                                    <td>Train Station</td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btnAddSelectedOPEREmp" id="btnAddSelectedOPEREmp"><i class="fa-solid fa-user-plus" aria-hidden="true"></i> Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW MODAL FOR OPERATOR -->
        <div class="modal" id="modalViewOper" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl" style="width: 95% !important; min-width: 95% !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Qualification / Certification Form</h1>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                            {{-- <form id="formSubmit_oper"> --}}
                                <h3 class="mb-3 text-center">OPERATOR'S TRAINING / QUALIFICATION / CERTIFICATION SLIP</h3>

                                <div class="row mb-5">
                                    <div class="col-md-3">
                                        <label for="">Control No.:</label>
                                        <input class="form-control" type="text" class="form-control" id="text_view_oper_conno" name="text_view_oper_conno" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Production Section:</label>
                                        <input class="form-control" type="text" name="text_view_oper_section" id="text_view_oper_section" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Series Name:</label>
                                        <input class="form-control" type="text" id="text_view_oper_series" name="text_view_oper_series" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Product Line:</label>
                                        <input class="form-control" type="text" id="text_view_oper_product_line" name="text_view_oper_product_line" readonly>
                                    </div>
                                </div>

                                <div class="table-responsive mt-3 mb-3">
                                    <table id="tbl_view_certified_list_Oper" class="table table-bordered table-hover nowrap">
                                        <thead class="table-primary">
                                            <tr>
                                                <!-- <th>Action</th> -->
                                                <th>Employee No.</th>
                                                <th>Operator's Name</th>
                                                <th>Station (From)</th>
                                                <th>Station (To)</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="">Reason for Certification:</label>
                                        <input type="text" class="form-control" name="text_view_oper_certification" id="text_view_oper_certification" readonly>
                                    </div>
                                </div>

                                <hr style="height: 5px; background-color: black; border: none;">

                                <h4 class="mt-3 mb-3">PRODUCTION SECTION (Training and Orientation)</h4>

                                <div class="table-responsive mt-3 mb-5">
                                    <table id="tbl_view_oper_training_orientation" class="table table-bordered table-hover nowrap">
                                        <thead class="table-primary">
                                            <tr>
                                                <!-- <th>Action</th> -->
                                                <th>Training</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <h5 class="mt-3 mb-3">RESULT</h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">First Take:</label>
                                        <input class="form-control" name="text_view_first_result_oper" id="text_view_first_result_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Second Take:</label>
                                        <input class="form-control" name="text_view_second_result_oper" id="text_view_second_result_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Trained by:</label>
                                        <input class="form-control" type="text" id="text_view_first_trainedby_oper" name="text_view_first_trainedby_oper" multiple readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Trained by:</label>
                                        <input class="form-control" type="text" id="text_view_second_trainedby_oper" name="text_view_second_trainedby_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="" for="">Date & Time:</label>
                                        <input class="form-control" type="text" id="text_view_first_date_time_oper" name="text_view_first_date_time_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Date & Time:</label>
                                        <input class="form-control" type="text" id="text_view_second_date_time_oper" name="text_view_second_date_time_oper" readonly>
                                    </div>
                                </div>

                                <!-- **************************************************************      2ND SECTION          ************************************************************************************************* -->

                                <hr style="height: 5px; background-color: black; border: none;">

                                <h4 class="mb-3">ENGINEERING SECTION (Training and Qualification)</h4>

                                <div class="table-responsive mt-3 mb-5">
                                    <table id="tbl_view_oper_es_training_orientation" class="table table-bordered table-hover nowrap">
                                        <thead class="table-primary">
                                            <tr>
                                                <!-- <th>Action</th> -->
                                                <th>Training</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
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
                                        <label class="" for="">1.1 Observation / Interview Result</label>
                                        <input class="form-control" name="text_view_obs_first_result_es_oper" id="text_view_obs_first_result_es_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">1.1 Observation / Interview Result</label>
                                        <input class="form-control" name="text_view_obs_second_result_es_oper" id="text_view_obs_second_result_es_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="" for="">2. Sample Checking:</label>
                                        <input class="form-control" type="text" name="text_view_first_sample_es_oper" id="text_view_first_sample_es_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">OK:</label>
                                        <input class="form-control" type="text" name="text_view_first_ok_es_oper" id="text_view_first_ok_es_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">NG:</label>
                                        <input class="form-control" type="text" name="text_view_first_ng_es_oper" id="text_view_first_ng_es_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">2. Sample Checking:</label>
                                        <input class="form-control" type="text" name="text_view_second_sample_es_oper" id="text_view_second_sample_es_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">OK:</label>
                                        <input class="form-control" type="text" name="text_view_second_ok_es_oper" id="text_view_second_ok_es_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">NG:</label>
                                        <input class="form-control" type="text" name="text_view_second_ng_es_oper" id="text_view_second_ng_es_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">3. Overall Assessment:</label>
                                        <input class="form-control" name="text_view_oa_1st_result_es_oper" id="text_view_oa_1st_result_es_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">3. Overall Assessment:</label>
                                        <input class="form-control" name="text_view_oa_2nd_result_es_oper" id="text_view_oa_2nd_result_es_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Reason for Disqualification:</label>
                                        <input class="form-control" type="text" id="text_view_1st_disqualification_es_oper" name="text_view_1st_disqualification_es_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Reason for Disqualification:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_disqualification_es_oper" name="text_view_2nd_disqualification_es_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Qualified by:</label>
                                        <input class="form-control" type="text" id="text_view_1st_qualifiedby_es_oper" name="text_view_1st_qualifiedby_es_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Qualified by:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_qualifiedby_es_oper" name="text_view_2nd_qualifiedby_es_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="" for="">Date & Time:</label>
                                        <input class="form-control" type="text" id="text_view_qc_1st_date_es_oper" name="text_view_qc_1st_date_es_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Date & Time:</label>
                                        <input class="form-control" type="text" id="text_view_qc_2nd_date_es_oper" name="text_view_qc_2nd_date_es_oper" readonly>
                                    </div>
                                </div>

                                <!-- ************************************************************ 3RD SECTION ******************************************************************* -->

                                <hr style="height: 5px; background-color: black; border: none;">

                                <h4 class="mb-3">QUALITY CONTROL SECTION (CERTIFICATTION)</h4>

                                <p class="mb-3">Let the operator discuss the details of training/orientation conducted by concerned Supervisor nad Eng'r as per check items specified.</p>

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
                                        <input class="form-control" name="text_view_obs_first_result_qcs_oper" id="text_view_obs_first_result_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">1.1 Observation / Interview Result</label>
                                        <input class="form-control" name="text_view_obs_second_result_qcs_oper" id="text_view_obs_second_result_qcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="" for="">2. Sample Checking:</label>
                                        <input class="form-control" type="text" name="text_view_first_sample_qcs_oper" id="text_view_first_sample_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">OK:</label>
                                        <input class="form-control" type="text" name="text_view_first_ok_qcs_oper" id="text_view_first_ok_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">NG:</label>
                                        <input class="form-control" type="text" name="text_view_first_ng_qcs_oper" id="text_view_first_ng_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">2. Sample Checking:</label>
                                        <input class="form-control" type="text" name="text_view_second_sample_qcs_oper" id="text_view_second_sample_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">OK:</label>
                                        <input class="form-control" type="text" name="text_view_second_ok_qcs_oper" id="text_view_second_ok_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">NG:</label>
                                        <input class="form-control" type="text" name="text_view_second_ng_qcs_oper" id="text_view_second_ng_qcs_oper" readonly>
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
                                                        <td><input type="checkbox" id="text_view_qcs_station_1st_oper_1" name="text_view_qcs_station_1st_oper" value="Visual"></td>
                                                        <td>Visual</td>
                                                        <td>Judgement Confirmation</td>
                                                        <td>Using GRR sample (50pcs.)</td>
                                                    </tr>

                                                    <tr>
                                                        <td><input type="checkbox" id="text_view_qcs_station_1st_oper_2" name="text_view_qcs_station_1st_oper" value="Assembly"></td>
                                                        <td>Assembly</td>
                                                        <td>Judgement Confirmation</td>
                                                        <td>50 samples drawn from their actual output</td>
                                                    </tr>

                                                    <tr>
                                                        <td><input type="checkbox" id="text_view_qcs_station_1st_oper_3" name="text_view_qcs_station_1st_oper" value="Others"></td>
                                                        <td>Others (Parts Prep/Prov. Insertion/Packing/ etc)</td>
                                                        <td>Work Confirmation</td>
                                                        <td>50 samples drawn from their actual output</td>
                                                    </tr>

                                                    <tr>
                                                        <td><input type="checkbox" id="text_view_qcs_station_1st_oper_4" name="text_view_qcs_station_1st_oper" value="Rework Station"></td>
                                                        <td>Rework Station (PPS only)</td>
                                                        <td>Work Confirmation</td>
                                                        <td>50 samples drawn from their actual output</td>
                                                    </tr>

                                                    <tr>
                                                        <td><input type="checkbox" id="text_view_qcs_station_1st_oper_5" name="text_view_qcs_station_1st_oper" value="Segregation Station"></td>
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
                                                        <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_1" name="text_view_qcs_station_2nd_oper" value="Visual"></td>
                                                        <td>Visual</td>
                                                        <td>Judgement Confirmation</td>
                                                        <td>Using GRR sample (50pcs.)</td>
                                                    </tr>

                                                    <tr>
                                                        <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_2" name="text_view_qcs_station_2nd_oper" value="Assembly"></td>
                                                        <td>Assembly</td>
                                                        <td>Judgement Confirmation</td>
                                                        <td>50 samples drawn from their actual output</td>
                                                    </tr>

                                                    <tr>
                                                        <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_3" name="text_view_qcs_station_2nd_oper" value="Others"></td>
                                                        <td>Others (Parts Prep/Prov. Insertion/Packing/ etc)</td>
                                                        <td>Work Confirmation</td>
                                                        <td>50 samples drawn from their actual output</td>
                                                    </tr>

                                                    <tr>
                                                        <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_4" name="text_view_qcs_station_2nd_oper" value="Rework Station"></td>
                                                        <td>Rework Station (PPS only)</td>
                                                        <td>Work Confirmation</td>
                                                        <td>50 samples drawn from their actual output</td>
                                                    </tr>

                                                    <tr>
                                                        <td><input type="checkbox" id="text_view_qcs_station_2nd_oper_5" name="text_view_qcs_station_2nd_oper" value="Segregation Station"></td>
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
                                        <input class="form-control" name="text_view_oa_1st_result_qcs_oper" id="text_view_oa_1st_result_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">3. Overall Assessment:</label>
                                        <input class="form-control" name="text_view_oa_2nd_result_qcs_oper" id="text_view_oa_2nd_result_qcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Reason for Disapproval:</label>
                                        <input class="form-control" type="text" id="text_view_1st_disapproval_qcs_oper" name="text_view_1st_disapproval_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Reason for Disapproval:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_disapproval_qcs_oper" name="text_view_2nd_disapproval_qcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Certified by:</label>
                                        <input class="form-control" type="text" id="text_view_1st_certifiedby_qcs_oper" name="text_view_1st_certifiedby_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Certified by:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_certifiedby_qcs_oper" name="text_view_2nd_certifiedby_qcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="" for="">Date & Time:</label>
                                        <input class="form-control" type="text" id="text_view_1st_date_qcs_oper" name="text_view_1st_date_qcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Date & Time:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_date_qcs_oper" name="text_view_2nd_date_qcs_oper" readonly>
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

                                <!-- ************************************************************ 4TH SECTION - BACK PAGE - ******************************************************************* -->

                                <hr style="height: 5px; background-color: black; border: none;">

                                <h4 class="mb-3">PRODUCTION, ENGINEERING & QUALITY CONTROL SECTION (Certification-Completion)</h4>

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
                                    <div class="col-md-2">
                                        <label class="" for="">1. Lot Qty (1st lot):</label>
                                        <input class="form-control" type="text" name="lot_view_1st_sample_peqcs_oper" id="lot_view_1st_sample_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">Injected NG Qty:</label>
                                        <input class="form-control" type="text" name="text_view_1st_injected_ng_peqcs_oper" id="text_view_1st_injected_ng_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">Detected NG:</label>
                                        <input class="form-control" type="text" name="text_view_1st_detected_ng_peqcs_oper" id="text_view_1st_detected_ng_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">1. Lot Qty (1st lot):</label>
                                        <input class="form-control" type="text" name="text_view_2nd_sample_peqcs_oper" id="text_view_2nd_sample_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">Injected NG Qty:</label>
                                        <input class="form-control" type="text" name="text_view_2nd_injected_ng_peqcs_oper" id="text_view_2nd_injected_ng_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="" for="">Detected NG:</label>
                                        <input class="form-control" type="text" name="text_view_2nd_detected_ng_peqcs_oper" id="text_view_2nd_detected_ng_peqcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">2. Overall Assessment:</label>
                                        <input class="form-control" name="text_view_oa_1st_result_peqcs_oper" id="text_view_oa_1st_result_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">2. Overall Assessment:</label>
                                        <input class="form-control" name="text_view_oa_2nd_result_peqcs_oper" id="text_view_oa_2nd_result_peqcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Reason for Disapproval:</label>
                                        <input class="form-control" type="text" id="text_view_1st_disapproval_peqcs_oper" name="text_view_1st_disapproval_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Reason for Disapproval:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_disapproval_peqcs_oper" name="text_view_2nd_disapproval_peqcs_oper" readonly>
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
                                        <input class="form-control mb-2" type="text" id="text_view_1st_certified_prod_peqcs_oper" name="text_view_1st_certified_prod_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-1">
                                        <p for="">Production:</p>
                                    </div>

                                    <div class="col-md-5">
                                        <input class="form-control mb-2" type="text" id="text_view_2nd_certified_prod_peqcs_oper" name="text_view_2nd_certified_prod_peqcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-1">
                                        <p for="">Engineering:</p>
                                    </div>

                                    <div class="col-md-5">
                                        <input class="form-control mb-2" type="text" id="text_view_1st_certified_eng_peqcs_oper" name="text_view_1st_certified_eng_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-1">
                                        <p for="">Engineering:</p>
                                    </div>

                                    <div class="col-md-5">
                                        <input class="form-control mb-2" type="text" id="text_view_2nd_certified_eng_peqcs_oper" name="text_view_2nd_certified_eng_peqcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-1">
                                        <p for="">Quality Control:</p>
                                    </div>

                                    <div class="col-md-5">
                                        <input class="form-control mb-2" type="text" id="text_view_1st_certified_qc_peqcs_oper" name="text_view_1st_certified_qc_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-1">
                                        <p for="">Quality Control:</p>
                                    </div>

                                    <div class="col-md-5">
                                        <input class="form-control mb-2" type="text" id="text_view_2nd_certified_qc_peqcs_oper" name="text_view_2nd_certified_qc_peqcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="" for="">Date & Time:</label>
                                        <input class="form-control" type="text" id="text_view_1st_date_peqcs_oper" name="text_view_1st_date_peqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Date & Time:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_date_peqcs_oper" name="text_view_2nd_date_peqcs_oper" readonly>
                                    </div>
                                </div>

                                <label for="">Note: NG Injection process shall be taken from first lot output</label>

                                <!-- ************************************************************ 5TH SECTION - BACK PAGE - ******************************************************************* -->

                                <hr style="height: 5px; background-color: black; border: none;">

                                <h4 class="mb-3">VALIDATION PROCESS: ENGINEERING SECTION</h4>

                                <div class="row mb-1">
                                    <div class="col-md-6">
                                        <label class="" for="">Engineering Validation Result:</label>
                                    </div>

                                    <div class="col-md-6">
                                        <input class="form-control" name="text_view_application_vpes_oper" id="text_view_application_vpes_oper" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <input class="form-control" name="text_view_vpes_oper_1" id="text_view_vpes_oper_1" readonly>
                                    </div>
                                </div>

                                <h5 class="mt-3 mb-3">RESULT</h5>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="" for="">First Take</label>
                                        <input class="form-control" name="text_view_first_result_vpes_oper" id="text_view_first_result_vpes_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Second Take</label>
                                        <input class="form-control" name="text_view_second_result_vpes_oper" id="text_view_second_result_vpes_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Validated by (after 2nd day):</label>
                                        <input class="form-control" type="text" id="text_view_1st_validatedby_vpes_oper" name="text_view_1st_validatedby_vpes_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Validated by (after re-orientation):</label>
                                        <input class="form-control" type="text" id="text_view_2nd_validatedby_vpes_oper" name="text_view_2nd_validatedby_vpes_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Date:</label>
                                        <input class="form-control" type="text" id="text_view_1st_date_vpes_oper" name="text_view_1st_date_vpes_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Date:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_date_vpes_oper" name="text_view_2nd_date_vpes_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="" for="">Remarks:</label>
                                        <input class="form-control" type="text" name="text_view_remarks_vpes_oper" id="text_view_remarks_vpes_oper" readonly>
                                    </div>
                                </div>

                                <!-- ************************************************************ 6TH SECTION - BACK PAGE - ******************************************************************* -->

                                <hr style="height: 5px; background-color: black; border: none;">

                                <h4 class="mb-3">VALIDATION PROCESS: QUALITY CONTROL SECTION</h4>

                                <div class="row mb-1">
                                    <div class="col-md-3">
                                        <label class="" for="">QC Validation for other section</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control" name="text_view_vpqcs_oper" id="text_view_vpqcs_oper" readonly>
                                    </div>
                                </div>

                                <h5 class="mt-3 mb-3">RESULT</h5>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="" for="">First Take</label>
                                        <input class="form-control" name="text_view_first_result_vpqcs_oper" id="text_view_first_result_vpqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Second Take</label>
                                        <input class="form-control" name="text_view_second_result_vpqcs_oper" id="text_view_second_result_vpqcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Validated by (after 2nd day):</label>
                                        <input class="form-control" type="text" id="text_view_1st_validatedby_vpqcs_oper" name="text_view_1st_validatedby_vpqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Validated by (after re-orientation):</label>
                                        <input class="form-control" type="text" id="text_view_2nd_validatedby_vpqcs_oper" name="text_view_2nd_validatedby_vpqcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Date:</label>
                                        <input class="form-control" type="text" id="text_view_1st_date_vpqcs_oper" name="text_view_1st_date_vpqcs_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Date:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_date_vpqcs_oper" name="text_view_2nd_date_vpqcs_oper" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="" for="">Remarks:</label>
                                        <input class="form-control" type="text" name="text_view_remarks_vpqcs_oper" id="text_view_remarks_vpqcs_oper" readonly>
                                    </div>
                                </div>

                                <!-- ************************************************************ 2ND SECTION VPQCS ******************************************************************* -->

                                <hr style="height: 5px; background-color: black; border: none;">

                                <div class="row mt-3 mb-1">
                                    <div class="col-md-6">
                                        <input class="form-control" name="text_view_vpqcs_oper_1_1" id="text_view_vpqcs_oper_1_1" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <input class="form-control" name="text_view_application_vpqcs_oper" id="text_view_application_vpqcs_oper" readonly>
                                    </div>
                                </div>

                                <h5 class="mt-3 mb-3">RESULT</h5>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="" for="">First Take</label>
                                        <input class="form-control" name="text_view_first_result_vpes_oper_2" id="text_view_first_result_vpes_oper_2" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Second Take</label>
                                        <input class="form-control" name="text_view_second_result_vpes_oper_2" id="text_view_second_result_vpes_oper_2" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Validated by (after 3rd day):</label>
                                        <input class="form-control" type="text" id="text_view_1st_validatedby_vpes_oper_2" name="text_view_1st_validatedby_vpes_oper_2" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Validated by (after re-orientation):</label>
                                        <input class="form-control" type="text" id="text_view_2nd_validatedby_vpes_oper_2" name="text_view_2nd_validatedby_vpes_oper_2" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="" for="">Date:</label>
                                        <input class="form-control" type="text" id="text_view_1st_date_vpes_oper_2" name="text_view_1st_date_vpes_oper_2" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" for="">Date:</label>
                                        <input class="form-control" type="text" id="text_view_2nd_date_vpes_oper_2" name="text_view_2nd_date_vpes_oper_2" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="" for="">Remarks:</label>
                                        <input class="form-control" type="text" name="text_view_remarks_vpes_oper_2" id="text_view_remarks_vpes_oper_2" readonly>
                                    </div>
                                </div>

                                <!-- ************************************************************ 6TH SECTION - BACK PAGE - ******************************************************************* -->

                                <hr style="height: 5px; background-color: black; border: none;">

                                <h4 class="mb-3">QC Validation for Visual Operator</h4>

                                <p><> Reference Document</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="" for="">Work Instruction Document</label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="">Discuss the inspection sequence in detail.</label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" id="text_view_refdocno_input_qcvvo_oper" name="text_view_refdocno_input_qcvvo_oper" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <input class="form-control" type="text" id="text_view_ins_seq_qcvvo_oper" name="text_view_ins_seq_qcvvo_oper" readonly>
                                    </div>
                                </div>

                                <h6 class="mt-3 mb-3">RESULT:</h6>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="" for="">First Take:</label>
                                        <input class="form-control" name="text_view_result1_qcvvo_oper" id="text_view_result1_qcvvo_oper" readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="" for="">Validated by:</label>
                                        <input class="form-control" type="text" id="text_view_validated1_qcvvo_oper" name="text_view_validated1_qcvvo_oper" readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="" for="">Date:</label>
                                        <input class="form-control" type="text" id="text_view_date1_qcvvo_oper" name="text_view_date1_qcvvo_oper" readonly>
                                    </div>
                                </div>

                                <p><> Reference Document</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="" for="">Work Instruction Document</label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="">Discuss the inspection sequence in detail.</label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" id="text_view_refdocno_input_qcvvo_oper_2" name="text_view_refdocno_input_qcvvo_oper_2" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <input class="form-control" type="text" id="text_view_ins_seq_qcvvo_oper_2" name="text_view_ins_seq_qcvvo_oper_2" readonly>
                                    </div>
                                </div>

                                <h6 class="mt-3 mb-3">RESULT:</h6>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="ms-3" for="">Second Take:</label>
                                        <input class="form-control" name="text_view_result2__qcvvo_oper" id="text_view_result2__qcvvo_oper" readonly>

                                    </div>

                                    <div class="col-md-4">
                                        <label class="ms-3" for="">Validated by:</label>
                                        <input class="form-control" type="text" id="text_view_validated2_qcvvo_oper" name="text_view_validated2_qcvvo_oper" readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="ms-3" for="">Date:</label>
                                        <input class="form-control" type="text" id="text_view_date2_qcvvo_oper" name="text_view_date2_qcvvo_oper" readonly>
                                    </div>
                                </div>

                                <hr style="height: 5px; background-color: black; border: none;">

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

                                    <div class="col-md-6">
                                        <label for="">Approved / Confirmed by:</label>
                                        <input class="form-control" type="text" id="text_view_oper_approved_confirmed_by" name="text_view_oper_approved_confirmed_by" readonly>
                                        <label for="" class="mt-1">QC Supervisor</label>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa-solid fa-xmark me-2" style="color: white"></i>CLOSE</button>
                                </div>

                            {{-- </form> --}}

                    </div>
                </div>
            </div>
        </div>

        <!-- GENERATE REPORT FOR OPERATOR -->
        <div class="modal" id="" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">GENERATE PDF</h1>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <form method="POST" action="exportOperFormPDF.php" id="formGeneratePDF" target="_blank">

                            <input type="text" name="text_report_oper_conno" id="text_report_oper_conno">
                            <input type="text" name="text_report_fk_cq_oper_conno" id="text_report_fk_cq_oper_conno">
                            <input type="text" name="text_report_oper_cq_section" id="text_report_oper_cq_section">

                            <input type="text" name="text_report_oper_cq_series" id="text_report_oper_cq_series">
                            <input type="text" name="text_report_oper_cq_prodLine" id="text_report_oper_cq_prodLine">
                            <input type="text" name="text_report_oper_cq_cert_reason" id="text_report_oper_cq_cert_reason">
                            <input type="text" name="text_report_oper_cq_training" id="text_report_oper_cq_training">
                            <input type="text" name="text_report_oper_cq_pt_result1" id="text_report_oper_cq_pt_result1">
                            <input type="text" name="text_report_oper_cq_pt_trainedby1" id="text_report_oper_cq_pt_trainedby1">
                            <input type="text" name="text_report_oper_cq_pt_date_time1" id="text_report_oper_cq_pt_date_time1">
                            <input type="text" name="text_report_oper_cq_pt_result2" id="text_report_oper_cq_pt_result2">
                            <input type="text" name="text_report_oper_cq_pt_trainedby2" id="text_report_oper_cq_pt_trainedby2">
                            <input type="text" name="text_report_oper_cq_pt_date_time2" id="text_report_oper_cq_pt_date_time2">
                            <input type="text" name="text_report_oper_cq_et_training" id="text_report_oper_cq_et_training">
                            <input type="text" name="text_report_oper_cq_es_obs_result1" id="text_report_oper_cq_es_obs_result1">
                            <input type="text" name="text_report_oper_cq_es_sample1" id="text_report_oper_cq_es_sample1">
                            <input type="text" name="text_report_oper_cq_es_ok1" id="text_report_oper_cq_es_ok1">
                            <input type="text" name="text_report_oper_cq_es_ng1" id="text_report_oper_cq_es_ng1">
                            <input type="text" name="text_report_oper_cq_es_oa1" id="text_report_oper_cq_es_oa1">
                            <input type="text" name="text_report_oper_cq_es_disqualification_reason1" id="text_report_oper_cq_es_disqualification_reason1">
                            <input type="text" name="text_report_oper_cq_es_qualifiedby1" id="text_report_oper_cq_es_qualifiedby1">
                            <input type="text" name="text_report_oper_cq_es_date_time1" id="text_report_oper_cq_es_date_time1">
                            <input type="text" name="text_report_oper_cq_es_obs_result2" id="text_report_oper_cq_es_obs_result2">
                            <input type="text" name="text_report_oper_cq_es_sample2" id="text_report_oper_cq_es_sample2">
                            <input type="text" name="text_report_oper_cq_es_ok2" id="text_report_oper_cq_es_ok2">
                            <input type="text" name="text_report_oper_cq_es_ng2" id="text_report_oper_cq_es_ng2">
                            <input type="text" name="text_report_oper_cq_es_oa2" id="text_report_oper_cq_es_oa2">
                            <input type="text" name="text_report_oper_cq_es_disqualification_reason2" id="text_report_oper_cq_es_disqualification_reason2">
                            <input type="text" name="text_report_oper_cq_es_qualifiedby2" id="text_report_oper_cq_es_qualifiedby2">
                            <input type="text" name="text_report_oper_cq_es_date_time2" id="text_report_oper_cq_es_date_time2">
                            <input type="text" name="text_report_oper_cq_qcs_obs_result1" id="text_report_oper_cq_qcs_obs_result1">
                            <input type="text" name="text_report_oper_cq_qcs_sample1" id="text_report_oper_cq_qcs_sample1">
                            <input type="text" name="text_report_oper_cq_qcs_ok1" id="text_report_oper_cq_qcs_ok1">
                            <input type="text" name="text_report_oper_cq_qcs_ng1" id="text_report_oper_cq_qcs_ng1">
                            <input type="text" name="text_report_oper_cq_qcs_training1" id="text_report_oper_cq_qcs_training1">
                            <input type="text" name="text_report_oper_cq_qcs_oa_result1" id="text_report_oper_cq_qcs_oa_result1">
                            <input type="text" name="text_report_oper_cq_qcs_disapproval_reason1" id="text_report_oper_cq_qcs_disapproval_reason1">
                            <input type="text" name="text_report_oper_cq_qcs_certifiedby1" id="text_report_oper_cq_qcs_certifiedby1">
                            <input type="text" name="text_report_oper_cq_qcs_date_time1" id="text_report_oper_cq_qcs_date_time1">
                            <input type="text" name="text_report_oper_cq_qcs_obs_result2" id="text_report_oper_cq_qcs_obs_result2">
                            <input type="text" name="text_report_oper_cq_qcs_sample2" id="text_report_oper_cq_qcs_sample2">
                            <input type="text" name="text_report_oper_cq_qcs_ok2" id="text_report_oper_cq_qcs_ok2">
                            <input type="text" name="text_report_oper_cq_qcs_ng2" id="text_report_oper_cq_qcs_ng2">
                            <input type="text" name="text_report_oper_cq_qcs_training2" id="text_report_oper_cq_qcs_training2">
                            <input type="text" name="text_report_oper_cq_qcs_oa_result2" id="text_report_oper_cq_qcs_oa_result2">
                            <input type="text" name="text_report_oper_cq_qcs_disapproval_reason2" id="text_report_oper_cq_qcs_disapproval_reason2">
                            <input type="text" name="text_report_oper_cq_qcs_certifiedby2" id="text_report_oper_cq_qcs_certifiedby2">
                            <input type="text" name="text_report_oper_cq_qcs_date_time2" id="text_report_oper_cq_qcs_date_time2">

                            <!-- FOR BACK PAGE -->

                            <input type="text" name="text_report_oper_cq_peqc_lot1" id="text_report_oper_cq_peqc_lot1">
                            <input type="text" name="text_report_oper_cq_peqc_inj1" id="text_report_oper_cq_peqc_inj1">
                            <input type="text" name="text_report_oper_cq_peqc_dej1" id="text_report_oper_cq_peqc_dej1">
                            <input type="text" name="text_report_oper_cq_peqc_oa_result1" id="text_report_oper_cq_peqc_oa_result1">
                            <input type="text" name="text_report_oper_cq_peqc_disapproval_reason1" id="text_report_oper_cq_peqc_disapproval_reason1">
                            <input type="text" name="text_report_oper_cq_peqc_prod_certified1" id="text_report_oper_cq_peqc_prod_certified1">
                            <input type="text" name="text_report_oper_cq_peqc_eng_certified1" id="text_report_oper_cq_peqc_eng_certified1">
                            <input type="text" name="text_report_oper_cq_peqc_qc_certified1" id="text_report_oper_cq_peqc_qc_certified1">
                            <input type="text" name="text_report_oper_cq_peqc_date_time1" id="text_report_oper_cq_peqc_date_time1">
                            <input type="text" name="text_report_oper_cq_peqc_lot2" id="text_report_oper_cq_peqc_lot2">
                            <input type="text" name="text_report_oper_cq_peqc_inj2" id="text_report_oper_cq_peqc_inj2">
                            <input type="text" name="text_report_oper_cq_peqc_dej2" id="text_report_oper_cq_peqc_dej2">
                            <input type="text" name="text_report_oper_cq_peqc_oa_result2" id="text_report_oper_cq_peqc_oa_result2">
                            <input type="text" name="text_report_oper_cq_peqc_disapproval_reason2" id="text_report_oper_cq_peqc_disapproval_reason2">
                            <input type="text" name="text_report_oper_cq_peqc_prod_certified2" id="text_report_oper_cq_peqc_prod_certified2">
                            <input type="text" name="text_report_oper_cq_peqc_eng_certified2" id="text_report_oper_cq_peqc_eng_certified2">
                            <input type="text" name="text_report_oper_cq_peqc_qc_certified2" id="text_report_oper_cq_peqc_qc_certified2">
                            <input type="text" name="text_report_oper_cq_peqc_date_time2" id="text_report_oper_cq_peqc_date_time2">
                            <input type="text" name="text_report_oper_cq_vpes_applicable" id="text_report_oper_cq_vpes_applicable">
                            <input type="text" name="text_report_oper_cq_vpes_validation_result" id="text_report_oper_cq_vpes_validation_result">
                            <input type="text" name="text_report_oper_cq_vpes_result1" id="text_report_oper_cq_vpes_result1">
                            <input type="text" name="text_report_oper_cq_vpes_validatedby1" id="text_report_oper_cq_vpes_validatedby1">
                            <input type="text" name="text_report_oper_cq_vpes_date1" id="text_report_oper_cq_vpes_date1">
                            <input type="text" name="text_report_oper_cq_vpes_result2" id="text_report_oper_cq_vpes_result2">
                            <input type="text" name="text_report_oper_cq_vpes_validatedby2" id="text_report_oper_cq_vpes_validatedby2">
                            <input type="text" name="text_report_oper_cq_vpes_date2" id="text_report_oper_cq_vpes_date2">
                            <input type="text" name="text_report_oper_cq_vpes_remarks" id="text_report_oper_cq_vpes_remarks">
                            <input type="text" name="text_report_oper_cq_vpqcs_validation_station" id="text_report_oper_cq_vpqcs_validation_station">
                            <input type="text" name="text_report_oper_cq_vpqcs_result1" id="text_report_oper_cq_vpqcs_result1">
                            <input type="text" name="text_report_oper_cq_vpqcs_validatedby1" id="text_report_oper_cq_vpqcs_validatedby1">
                            <input type="text" name="text_report_oper_cq_vpqcs_date1" id="text_report_oper_cq_vpqcs_date1">
                            <input type="text" name="text_report_oper_cq_vpqcs_result2" id="text_report_oper_cq_vpqcs_result2">
                            <input type="text" name="text_report_oper_cq_vpqcs_validatedby2" id="text_report_oper_cq_vpqcs_validatedby2">
                            <input type="text" name="text_report_oper_cq_vpqcs_date2" id="text_report_oper_cq_vpqcs_date2">
                            <input type="text" name="text_report_oper_cq_vpqcs_remarks" id="text_report_oper_cq_vpqcs_remarks">
                            <input type="text" name="text_report_oper_cq_vpqcs_sheet_checking" id="text_report_oper_cq_vpqcs_sheet_checking">
                            <input type="text" name="text_report_oper_cq_vpqcs_applicable" id="text_report_oper_cq_vpqcs_applicable">
                            <input type="text" name="text_report_oper_cq_vpqcs_sheet_result1" id="text_report_oper_cq_vpqcs_sheet_result1">
                            <input type="text" name="text_report_oper_cq_vpqcs_sheet_validatedby1" id="text_report_oper_cq_vpqcs_sheet_validatedby1">
                            <input type="text" name="text_report_oper_cq_vpqcs_sheet_date1" id="text_report_oper_cq_vpqcs_sheet_date1">
                            <input type="text" name="text_report_oper_cq_vpqcs_sheet_result2" id="text_report_oper_cq_vpqcs_sheet_result2">
                            <input type="text" name="text_report_oper_cq_vpqcs_sheet_validatedby2" id="text_report_oper_cq_vpqcs_sheet_validatedby2">
                            <input type="text" name="text_report_oper_cq_vpqcs_sheet_date2" id="text_report_oper_cq_vpqcs_sheet_date2">
                            <input type="text" name="text_report_oper_cq_vpqcs_sheet_remarks" id="text_report_oper_cq_vpqcs_sheet_remarks">
                            <input type="text" name="text_report_oper_cq_vvo_docno1" id="text_report_oper_cq_vvo_docno1">
                            <input type="text" name="text_report_oper_cq_vvo_sequence_details1" id="text_report_oper_cq_vvo_sequence_details1">
                            <input type="text" name="text_report_oper_cq_vvo_result1" id="text_report_oper_cq_vvo_result1">
                            <input type="text" name="text_report_oper_cq_vvo_validatedby1" id="text_report_oper_cq_vvo_validatedby1">
                            <input type="text" name="text_report_oper_cq_vvo_date1" id="text_report_oper_cq_vvo_date1">
                            <input type="text" name="text_report_oper_cq_vvo_docno2" id="text_report_oper_cq_vvo_docno2">
                            <input type="text" name="text_report_oper_cq_vvo_sequence_details2" id="text_report_oper_cq_vvo_sequence_details2">
                            <input type="text" name="text_report_oper_cq_vvo_result2" id="text_report_oper_cq_vvo_result2">
                            <input type="text" name="text_report_oper_cq_vvo_validatedby2" id="text_report_oper_cq_vvo_validatedby2">
                            <input type="text" name="text_report_oper_cq_vvo_date2" id="text_report_oper_cq_vvo_date2">
                            <input type="text" name="text_report_oper_cq_approved_confirmedby" id="text_report_oper_cq_approved_confirmedby">

                            <input type="submit" class="btn btn-warning float-start pdf" id="pdf" name="pdf" value="Generate PDF">
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js_content')
    <script type="text/javascript">
    $(function () {
         // In-memory array that holds employees staged in the modal
        operEmpArray = [];
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
        $(document).on('submit', '#formSubmit_oper, #formSubmit_Oper', function (e) {
            e.preventDefault();

            var $form = $(this);
            $form.append('text_select_position', $('#text_select_position').val());
            $form.append('text_select_section', $('#text_select_section').val());
            // Serialize form into an object (handles multiple inputs with same name)
            var formArray = $form.serializeArray();
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

            // Add the Operator Employees table data (empId, empName, stFrom, stTo)
            data.operator_employees = (typeof getOperEmpTableData === 'function')
                ? getOperEmpTableData()
                : [];

          
            // Send to server
            let serialized_data = {

            }
            call_ajax_serialize(data,serialized_data,'save_qualification_certification_oper', function(response){
                if (response && response.success) {
                    Swal.fire({ icon: 'success', title: 'Saved', text: response.message || 'Operator form saved.' });
                    $('#modalCreateCQForm').modal('hide');
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: (response && response.message) ? response.message : 'Failed to save.' });
                }
            });
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
    });

        function initDivDeptSecCombos(comboSelectors) {
            comboSelectors.forEach(function(selector) {
                    getDivDeptSec({ comboId: $(selector) });
            });
            // comboSelectors.forEach(function(selector) {
            //         getDivDeptSec({ comboId: $(selector) });
            // });
        }

        initDivDeptSecCombos([
                '#text_section_operator',
        ]);



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
        const initGetSystemOneEmployeeDetailsCombos = (comboSelectors) => {

            comboSelectors.forEach(function(selector) {
                    getSystemOneEmployeeDetails($(selector));
            });
        }
        const initSelectPassFail = (comboSelectors) => {

            comboSelectors.forEach(function(selector) {
                    selectPassFail($(selector));
            });
        }

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
                '#text_obs_first_result_es_oper',

        ]);
        initSelectPassFail([
            '#text_oa_1st_result_es_oper',
        ])
        // call_ajax_serialize = (data = null, serialized_data, handler, fn,elFormId =null);

    </script>
@endsection
