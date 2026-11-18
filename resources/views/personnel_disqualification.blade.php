@php $layout = 'layouts.super_user_layout'; @endphp
@extends($layout)
@section('title', 'Personnel Disqualification')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Personnel Disqualification</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Personnel Disqualification</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-sm-12"> 
                        <!-- general form elements -->
                        <div class="card card-dark">
                            <div class="card-header">
                                <h3 class="card-title">Personnel Disqualification Module</h3>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                
                                <div class="float-sm-right">
                                    <button class="btn btn-primary" id="btnAddDisqualification">
                                        <i class="fa fa-initial-icon"></i> Add Disqualification
                                    </button>
                                </div>

                                <div class="float-sm-left col-2">
                                    <label><strong>Filter Year : &nbsp;</strong></label>
                                    <input type="text" id="SearchYear" class="form-control" name="year" title="<?php echo date('Y'); ?>" value="<?php echo date('Y'); ?>">
                                </div>

                                <div class="float-sm-left mb-4 col-2">
                                    <label><strong>Month :</strong></label>
                                    <select class="form-control selectMonth" name="month_value" id="SelectMonth">
                                        <option value="<?php echo date('m'); ?>" readonly><?php echo date('F'); ?></option><!-- selected -->
                                        <option value="" selected>All</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>

                                <div class="table-responsive">
                                    <table id="tblDisqualification" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Action</th>
                                                <!-- <th hidden>Disqualified Personnels</th> -->
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 10%;" class="text-center">Control No.</th>
                                                <th style="width: 20%;" class="text-center">Disqualified Personnel</th>
                                                <th style="width: 10%;" class="text-center">Station</th>
                                                <th style="width: 10%;" class="text-center">Disqualification Date</th>
                                                <th style="width: 10%;" class="text-center">Recertification Date</th>
                                                <th style="width: 20%;" class="text-center">Reason for Disqualification</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <!-- !-- End Page Content -->

                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- MODALS -->
    <div class="modal fade" id="modalDisqualification" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit Disqualification Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- ===================================================== -->
                    <!-- PART 1:PERSONNEL DISQUALIFICATION -->
                    <!-- ===================================================== -->

                    <div class="card mb-3">
                        <div class="card-header p-2">
                            <button type="button" class="btn btn-link btn-block text-left" data-toggle="collapse" data-target="#collapsePart1" aria-expanded="true">
                            <!-- <button type="button" class="btn btn-link btn-block text-left collapsed" data-toggle="collapse" data-target="#collapsePart1" aria-expanded="false"> -->
                                <i class="fas fa-user-alt-slash mr-2"></i>
                                <strong>Part 1: Personnel Disqualification</strong>
                                <i class="fa fa-chevron-down float-right"></i>
                            </button>
                        </div>

                        <!-- <div id="collapsePart1" class="collapse show"> -->
                        <div id="collapsePart1" class="collapse">
                            <div class="card-body">
                                <form method="post" id="formDisqualiPart1" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="part1_id" id="part1Id">
                                    <!-- <input type="hidden" name="part1_evidence_id" id="txtCSREvidenceIdReviewStaff"> -->
                                    <!-- <input type="hidden" name="process" value="personnel_disqualification"> -->

                                    <div class="row">
                                        <!-- ATTENTION TO -->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Attention To (Select Name/s)</label>
                                                <select class="form-control select2bs5 selectAttentionToRecipients"
                                                    name="attention_to[]" 
                                                    id="selectAttentionTo"
                                                    multiple required>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Control No</label>
                                                    {{-- AUTO-GENERATE --}}
                                                <input type="text" class="form-control" id="controlNo" placeholder="Auto Generate" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- EMPLOYEE NAME -->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Disqualified Employee Name</label>
                                                <select class="form-control select2bs5 selectEmployeeName" name="employee_name" id="employeeName" required></select>
                                            </div>
                                        </div>

                                        <!-- DISQUALIFICATION DATE -->
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Disqualification Date</label>
                                                <input type="date" class="form-control" name="disqualification_date" id="disqualificationDate">
                                            </div>
                                        </div>

                                        <!-- RECERTIFICATION DATE -->
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Recertification Date</label>
                                                <input type="date" class="form-control" name="recertification_date" id="recertificationDate">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Personnel Designation</label>
                                                <input type="text" class="form-control" id="personnelDesignation" placeholder="Auto Generated" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Section</label>
                                                <input type="text" class="form-control" id="section" placeholder="Auto Generated" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Station</label>
                                                <!-- <input type="text" class="form-control" id="station"> -->
                                                <select class="form-control select2bs5 selectStation" name="station" id="station" data-placeholder="Select Station" required></select>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Device/Model Name</label>
                                                <input type="text" class="form-control" name="device_name" id="deviceName" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Reason for Disqualification</label>
                                                <input type="text" class="form-control" name="reason_for_disqualification" id="reasonForDisqualification" placeholder="Auto Generated" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Mode of Defect</label>
                                                <select class="form-control select2bs5 selectModeofDefect" name="mode_of_defect" id="modeOfDefect" data-placeholder="Select Mode of Defect" required></select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Series/Model Name</label>
                                                <select class="form-control select2bs5 selectSeriesModelName" name="series_model_name" id="seriesModelName" data-placeholder="Select Series/Model Name" required></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Classification</label>
                                                <select class="form-control select2bs5 selectClassification" name="classification" id="classification" data-placeholder="Select Classification" required></select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Sub-Classification</label>
                                                <select class="form-control select2bs5 selectSubClassification" name="sub_classification" id="subClassification" data-placeholder="Select Sub-Classification" disabled></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SAVE BUTTON -->
                                    <div class="text-right" id="formDisqualificationPart1Footer">
                                        <button type="submit" id="btnSubmitDisqualificationPart1" class="btn btn-primary">
                                            <i class="fa fa-check"></i> Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ===================================================== -->
                    <!-- PART 2: Counselling & Reorientation -->
                    <!-- ===================================================== -->

                    <div class="card mb-3">
                        <div class="card-header p-2">
                            <button type="button" class="btn btn-link btn-block text-left" data-toggle="collapse" data-target="#collapsePart2" aria-expanded="false">
                                <i class="fas fa-book-reader mr-2"></i>
                                <strong>Part 2: Counselling & Reorientation</strong>
                                <i class="fa fa-chevron-down float-right"></i>
                            </button>
                        </div>

                        <div id="collapsePart2" class="collapse show">
                            <div class="card-body">
                                <form method="post" id="formDisqualiPart2" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="part1_id" id="part1Id">
                                    <input type="hidden" name="part2_id" id="part2Id">
                                    <!-- <input type="hidden" name="csr_evidence_id" id="txtCSREvidenceIdReviewStaff"> -->
                                    <!-- <input type="hidden" name="part2_process_status" id="part2ProcessStatus" value=""> -->

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <!-- <div class="form-group">
                                                <label>Conducted By</label>
                                                <input type="hidden" name="trained_by" id="trainedById">
                                                <input type="text" class="form-control" id="trainedByName" placeholder="Auto Generate" readonly>
                                            </div> -->
                                            <div class="form-group">
                                                <label>Conducted By</label>
                                                <select class="form-control select2bs5 selectConductedBy" name="conducted_by" id="conductedByName" required></select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Counselling Date</label>
                                                <input type="date" class="form-control" name="counselling_date" id="counsellingDate">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SAVE BUTTON -->
                                    <div class="text-right mb-2" id="formDisqualificationPart2Footer">
                                        <button type="submit" id="btnSubmitDisqualificationPart2" class="btn btn-primary mt-2">
                                            <i class="fa fa-check"></i> Save
                                        </button>
                                    </div>

                                    <div class="col">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" id="btnAddTraining" class="btn btn-primary" data-id="" data-status="" data-part1_id><i class="fa fa-plus"></i> Add Training</button>
                                        </div>
                                        <br>
                                        <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                                            <table class="table table-sm table-bordered" id="tblTrainings" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 10%;">Action</th>
                                                        <th style="width: 20%;">Category</th>
                                                        <th style="width: 30%;">Scope</th>
                                                        <th style="width: 30%;">Trained By</th>
                                                        <th style="width: 10%;">Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ===================================================== -->
                    <!-- PART 3: Results Monitoring -->
                    <!-- ===================================================== -->

                    <div class="card mb-3">
                        <div class="card-header p-2">
                            <button type="button" class="btn btn-link btn-block text-left" data-toggle="collapse" data-target="#collapsePart3" aria-expanded="false">
                                <i class="fas fa-book-reader mr-2"></i>
                                <strong>Part 3: Examination Result</strong>
                                <i class="fa fa-chevron-down float-right"></i>
                            </button>
                        </div>

                        <div id="collapsePart3" class="collapse show">
                            <div class="card-body mx-5">
                                <form method="post" id="formDisqualiPart3" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="part1_id" id="part1Id">
                                    <input type="hidden" name="part2_id" id="part2Id">
                                    <input type="hidden" name="part3_id" id="part3Id">

                                    <div class="row mx-5 justify-content-between">
                                        <!-- Disqualification Exam -->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Disqualification Exam</label>
                                                <select class="form-control select2bs5 selectDisqualiExam" name="disquali_exam" id="disqualiExam" required></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mx-5 justify-content-between">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>First Take</label>
                                                <select class="form-control select2bs5" name="first_take_result" id="firstTakeResult" disabled>
                                                    <option value="">Select First Take</option>
                                                    <option value="1">Passed</option>
                                                    <option value="0">Failed</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Second Take</label>
                                                <select class="form-control select2bs5" name="second_take_result" id="secondTakeResult" disabled>
                                                    <option value="">Select Second Take</option>
                                                    <option value="1">Passed</option>
                                                    <option value="0">Failed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mx-5 justify-content-between">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>In-Charge (LQC Supv'r)</label>
                                                <select class="form-control select2bs5 selectIncharged" name="first_take_incharged" id="firstTakeIncharged" disabled></select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>In-Charge (LQC Supv'r)</label>
                                                <select class="form-control select2bs5 selectIncharged" name="second_take_incharged" id="secondTakeIncharged" disabled></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mx-5 justify-content-between">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Date/Time</label>
                                                <input type="datetime-local" class="form-control" name="first_take_date" id="firstTakeDate" disabled>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Date/Time</label>
                                                <input type="datetime-local" class="form-control" name="second_take_date" id="secondTakeDate" disabled>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ===================================================== -->
                    <!-- PART 4: QC Sample Check Result (Operator & QC Inspector only) -->
                    <!-- ===================================================== -->

                    <div class="card mb-3 d-none" id="cardPart4">
                        <div class="card-header p-2">
                            <button type="button" class="btn btn-link btn-block text-left" data-toggle="collapse" data-target="#collapsePart4" aria-expanded="true">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                <strong>Part 4: QC Sample Check Result (n = 10pcs)</strong>
                                <i class="fa fa-chevron-down float-right"></i>
                            </button>
                        </div>

                        <div id="collapsePart4" class="collapse show">
                            <div class="card-body">
                                <form method="post" id="formDisqualiPart4" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="part1_id" class="js-part1-id">

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h6><strong>First Take</strong></h6>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>OK (pcs)</label>
                                                        <input type="number" class="form-control" name="qc_first_ok" min="0" max="10">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>NG (pcs)</label>
                                                        <input type="number" class="form-control" name="qc_first_ng" min="0" max="10">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Certified by (QC)</label>
                                                <select class="form-control select2bs5 js-people" name="qc_first_certified_by"></select>
                                            </div>
                                            <div class="form-group">
                                                <label>Date/Time</label>
                                                <input type="datetime-local" class="form-control" name="qc_first_date">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <h6><strong>Second Take</strong></h6>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>OK (pcs)</label>
                                                        <input type="number" class="form-control" name="qc_second_ok" min="0" max="10">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>NG (pcs)</label>
                                                        <input type="number" class="form-control" name="qc_second_ng" min="0" max="10">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Certified by (QC)</label>
                                                <select class="form-control select2bs5 js-people" name="qc_second_certified_by"></select>
                                            </div>
                                            <div class="form-group">
                                                <label>Date/Time</label>
                                                <input type="datetime-local" class="form-control" name="qc_second_date">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Overall Result</label>
                                                <select class="form-control select2bs5" name="qc_overall_result" required>
                                                    <option value="">Select Overall Result</option>
                                                    <option value="1">Passed</option>
                                                    <option value="0">Failed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ===================================================== -->
                    <!-- PART 5: Monitoring Result (6 days, depends on designation) -->
                    <!-- ===================================================== -->

                    <div class="card mb-3 d-none" id="cardPart5">
                        <div class="card-header p-2">
                            <button type="button" class="btn btn-link btn-block text-left" data-toggle="collapse" data-target="#collapsePart5" aria-expanded="true">
                                <i class="fas fa-calendar-check mr-2"></i>
                                <strong>Part 5: Monitoring Result (6 days, n = min 1pc, max 10pcs)</strong>
                                <i class="fa fa-chevron-down float-right"></i>
                            </button>
                        </div>

                        <div id="collapsePart5" class="collapse show">
                            <div class="card-body">
                                <form method="post" id="formDisqualiPart5" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="part1_id" class="js-part1-id">
                                    <input type="hidden" name="designation" id="monitoringDesignation">

                                    {{-- Operator: 6-day table. Day rows are added by JS. --}}
                                    <div id="monitoringOperator" class="d-none">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered" id="tblMonitoring" style="min-width: 1100px;">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Time</th>
                                                        <th>P.O No./Series name</th>
                                                        <th>Sample</th>
                                                        <th>Accept</th>
                                                        <th>Reject</th>
                                                        <th>Mode of defect/qty</th>
                                                        <th>QC Inspector</th>
                                                        <th style="width: 50px;"></th>
                                                    </tr>
                                                </thead>
                                                @for ($day = 1; $day <= 6; $day++)
                                                    <tbody class="monitoring-day" data-day="{{ $day }}">
                                                        <tr class="table-secondary">
                                                            <td colspan="9">
                                                                <strong>Day {{ $day }}</strong>
                                                                <span class="badge bg-danger ml-2 day-count">0 / 10 samples</span>
                                                                <button type="button" class="btn btn-primary btn-sm float-right btnAddRow">
                                                                    <i class="fa fa-plus"></i> Add Row
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                @endfor
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Material Handler & Technician: upload only --}}
                                    <div id="monitoringMhTech" class="d-none">
                                        <div class="form-group">
                                            <label>Employee Quality Card <small class="text-muted">(updated by the immediate superior)</small></label>
                                            <input type="file" class="form-control" name="employee_quality_card">
                                            <small class="text-muted js-current-file" id="employeeQualityCardCurrent"></small>
                                        </div>
                                    </div>

                                    {{-- QC Inspector: upload only --}}
                                    <div id="monitoringInspector" class="d-none">
                                        <div class="form-group">
                                            <label>Line QC Supervisor Spot Audit Report <small class="text-muted">(updated by the QC supervisor)</small></label>
                                            <input type="file" class="form-control" name="spot_audit_report">
                                            <small class="text-muted js-current-file" id="spotAuditReportCurrent"></small>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ===================================================== -->
                    <!-- PART 6: Inspection Result (Operator & QC Inspector only) -->
                    <!-- ===================================================== -->

                    <div class="card mb-3 d-none" id="cardPart6">
                        <div class="card-header p-2">
                            <button type="button" class="btn btn-link btn-block text-left" data-toggle="collapse" data-target="#collapsePart6" aria-expanded="true">
                                <i class="fas fa-search mr-2"></i>
                                <strong>Part 6: Inspection Result (5 lots / 5 frequency, severity-tightened inspection)</strong>
                                <i class="fa fa-chevron-down float-right"></i>
                            </button>
                        </div>

                        <div id="collapsePart6" class="collapse show">
                            <div class="card-body">
                                <form method="post" id="formDisqualiPart6" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="part1_id" class="js-part1-id">

                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="tblInspection" style="min-width: 1200px;">
                                            <thead>
                                                <tr>
                                                    <th>Part/Product name</th>
                                                    <th>Lot No.</th>
                                                    <th>Lot Qty/Sample (for IPQC)</th>
                                                    <th>Sample (for IQC/OQC)</th>
                                                    <th>Accept (no. of OK)</th>
                                                    <th>Reject (no. of NG)</th>
                                                    <th>Mode of defect/qty</th>
                                                    <th>QC Inspector/Supervisor/Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < 5; $i++)
                                                    <tr>
                                                        <td><input type="text" class="form-control form-control-sm" name="inspection[{{ $i }}][part_name]" data-field="part_name"></td>
                                                        <td><input type="text" class="form-control form-control-sm" name="inspection[{{ $i }}][lot_no]" data-field="lot_no"></td>
                                                        <td><input type="number" min="0" class="form-control form-control-sm" name="inspection[{{ $i }}][lot_qty_ipqc]" data-field="lot_qty_ipqc"></td>
                                                        <td><input type="number" min="0" class="form-control form-control-sm" name="inspection[{{ $i }}][sample_iqc_oqc]" data-field="sample_iqc_oqc"></td>
                                                        <td><input type="number" min="0" class="form-control form-control-sm" name="inspection[{{ $i }}][accept]" data-field="accept"></td>
                                                        <td><input type="number" min="0" class="form-control form-control-sm" name="inspection[{{ $i }}][reject]" data-field="reject"></td>
                                                        <td><input type="text" class="form-control form-control-sm" name="inspection[{{ $i }}][mode_of_defect]" data-field="mode_of_defect"></td>
                                                        <td>
                                                            <select class="form-control form-control-sm js-people mb-1" name="inspection[{{ $i }}][qc_inspector]" data-field="qc_inspector"></select>
                                                            <input type="date" class="form-control form-control-sm" name="inspection[{{ $i }}][qc_date]" data-field="qc_date">
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ===================================================== -->
                    <!-- PART 7: Overall Disposition & Sign-off -->
                    <!-- ===================================================== -->

                    <div class="card mb-3" id="cardPart7">
                        <div class="card-header p-2">
                            <button type="button" class="btn btn-link btn-block text-left" data-toggle="collapse" data-target="#collapsePart7" aria-expanded="true">
                                <i class="fas fa-gavel mr-2"></i>
                                <strong>Part 7: Overall Disposition</strong>
                                <i class="fa fa-chevron-down float-right"></i>
                            </button>
                        </div>

                        <div id="collapsePart7" class="collapse show">
                            <div class="card-body">
                                <form method="post" id="formDisqualiPart7" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="part1_id" class="js-part1-id">

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Overall Disposition</label>
                                                <select class="form-control select2bs5" name="disposition" id="dispositionResult" required>
                                                    <option value="">Select Overall Disposition</option>
                                                    <option value="1">Employee may go back to normal operation</option>
                                                    <option value="2">Employee may transfer to less critical station</option>
                                                    <option value="3">Failed qualification/certification</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 d-none" id="recertifiedDateGroup">
                                            <div class="form-group">
                                                <label>Re-certified Date</label>
                                                <input type="date" class="form-control" name="recertified_date" id="recertifiedDate">
                                            </div>
                                        </div>

                                        <div class="col-sm-6 d-none" id="transferDateGroup">
                                            <div class="form-group">
                                                <label>Date of Transfer</label>
                                                <input type="date" class="form-control" name="transfer_date" id="transferDate">
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $signOffs = [
                                            'lqc_supervisor' => 'LQC Supervisor',
                                            'conformed_by'   => 'Conformed by (Immediate Superior of Disqualified Personnel)',
                                            'section_head'   => 'Approved by (Section Head)',
                                            'lqc_head'       => 'Approved by (LQC Head)',
                                        ];
                                    @endphp

                                    @foreach ($signOffs as $key => $label)
                                        <h6 class="mt-3"><strong>{{ $label }}</strong></h6>
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <select class="form-control select2bs5 js-people" name="{{ $key }}_name"></select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Sign</label>
                                                    <input type="text" class="form-control" name="{{ $key }}_sign" placeholder="Type full name as signature">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input type="date" class="form-control" name="{{ $key }}_date">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL FOOTER -->
                <div class="modal-footer">
                    <div class="row col-md-12">
                        <div class="col-md-6 justify-content-start">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>

                        <div class="col-md-6 justify-content-end">
                            <button type="button" id="btnApproved" class="btn btn-success float-right d-none">
                                <i class="fa fa-thumbs-up"></i> Approved
                            </button>

                            <button type="button" id="btnDisapproved" class="btn btn-danger float-right d-none mr-2">
                                <i class="fa fa-thumbs-down"></i> Disapproved
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal fade" id="modalTrainings" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-object-group text-info"></i>Add Training</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="formTrainings" autocomplete="off">
                        @csrf
                        <div class="input-group mb-3" hidden>
                            <div class="input-group-prepend w-50">
                                <span class="input-group-text w-100">Training ID</span>
                            </div>
                            <input type="hidden" name="part1_id" id="part1Id">
                            <input type="hidden" name="part2_id" id="part2Id">
                            <input type="hidden" name="part2_training_status" id="part2TrainingStatus">
                            <input type="hidden" name="part2_training_id" id="part2TrainingId">
                        </div>

                        <!-- <div id="part2Div"> -->
                            <div class="row" id="part2ScopeDiv">
                                <!-- Training & Orientation Scope -->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Scope</label>
                                        <select class="form-control select2bs5 selectTrainingScope" name="training_scope[]" id="trainingScope" multiple required></select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Trained By -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Trained By</label>
                                        <!-- <input type="hidden" name="trained_by" id="trainedById">
                                        <input type="text" class="form-control" id="trainedByName" required> -->
                                        <select class="form-control select2bs5 selectTrainedBy" name="trained_by" id="trainedBy" required></select>
                                    </div>
                                </div>

                                <!-- Training Date-->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Training Date</label>
                                        <input type="date" class="form-control" name="training_date" id="trainingDate">
                                    </div>
                                </div>
                            </div>
                        <!-- </div> -->

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success" id="btnSaveTraining">Save Training</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_content')
    <!-- {{-- <script type="text/javascript"> --}} -->
    <script src="{{ asset('public/js/my_js/PersonnelDisqualification.js') }}?<?=time()?>"></script>
    <script src="{{ asset('public/js/my_js/PersonnelDisqualificationParts.js') }}?<?=time()?>"></script>
    <!-- {{-- </script> --}} -->
@endsection