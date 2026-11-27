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
    <!-- {{-- </script> --}} -->
@endsection