@php $layout = 'layouts.super_user_layout'; @endphp
@extends($layout)
@section('title', 'Training Request')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Training Attedance Details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Training Attedance</li>
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
                                <h3 class="card-title">Training Attendance Details</h3>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                <div class="float-sm-right  mb-3">
                                    <button class="btn btn-primary" id="btnShowModalTrainingAttendance">
                                        <i class="fa fa-plus fa-md me-2"></i> Add Training Attendance
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tblTrainingAttendance" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Action</th>
                                                <th style="width: 15%;" class="text-center">Ctrl No. / Doc No.</th>
                                                <th style="width: 10%;" class="text-center">Date From</th>
                                                <th style="width: 15%;"  class="text-center">Date To</th>
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
    <div class="modal fade" id="modalViewTrainingAttendanceRequest" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Training Attendance Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                          <label>Ctrl No</label>
                            <input type="text" class="form-control" name="trainingAttendanceCtrlNo" id="trainingAttendanceCtrlNo" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                          <label>From</label>
                            <input type="date" class="form-control" name="trainingAttendanceCtrlNo" id="trainingAttendanceCtrlNo">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                          <label>To</label>
                            <input type="date" class="form-control" name="trainingAttendanceCtrlNo" id="trainingAttendanceCtrlNo">
                        </div>
                    </div>
                    <table class="table table-sm table-bordered" id="tblTrainingAttendanceRequest" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Employee No</th>
                                <th>Name</th>
                                <th>Date From</th>
                                <th>Date To</th>
                                <th>Training Hours</th>
                                <th>Reason/Remarks of Absence</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="btnSubmitTrainingAttendance" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('js_content')
    <script type="text/javascript">
    tbl = {
        TrainingAttendance : '#tblTrainingAttendance',
        TrainingAttendanceRequest : '#tblTrainingAttendanceRequest'
    }
    dtTrainingAttendance = $(tbl.TrainingAttendance).DataTable({
    "processing" : false,
        "serverSide" : true,
        "ajax" : {
            url: "view_training_attendance",
        },
        "columns":[
            { "data" : "action", orderable:false, searchable:false },
            { "data" : "ctrl_number" },
            { "data" : "date_filed" },
            { "data" : "received_date" },
        ],
        "order": [[ 1, "asc" ]],
    });

    dtViewTrainingAttendanceRequest = $(tbl.TrainingAttendanceRequest).DataTable({
    "processing" : false,
        "serverSide" : true,
        "ajax" : {
            url: "view_training_attendance_request",
            // data: function (param){
            //     param.trainingRequestsId = '';
            // },
        },
        "columns":[
            { "data" : "action", orderable:false, searchable:false },
            { "data" : "emp_no" },
            { "data" : "name" },
            { "data" : "training_endorsement_date" },//date from
            { "data" : "training_endorsement_date" },//date to
            { "data" : "training_endorsement_date" }, //hours time in out
            { "data" : "training_endorsement_date" }, //remarks
        ],
        "order": [[ 1, "asc" ]],
    });
    $(tbl.TrainingAttendance).on('click','#checkBulkUserModule','tr', function () {

    });

    </script>
@endsection

