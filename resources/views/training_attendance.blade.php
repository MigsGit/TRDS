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
            </div><!-- /.container-fluid  L:\01 - Regular Reports\31 - Month-end Report\39 - Azure Cloud\2026-->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active .menuTab" id="Pending-tab" data-toggle="tab" href="#menu1" role="tab" aria-controls="menu1" aria-selected="true">Attendance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link .menuTab" id="Completed-tab" data-toggle="tab" href="#menu2" role="tab" aria-controls="menu2" aria-selected="false">Summary</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-4" id="myTabContent">
                            <div class="tab-pane fade show active" id="menu1" role="tabpanel" aria-labelledby="menu1-tab">
                                <div class="card-header">
                                    <h3 class="card-title">Training Attendance Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <center>
                                                <br>
                                                <br>
                                                <br>
                                                <p id="date"></p>
                                            </center>
                                        </div>
                                        <div class="col-sm-7">
                                            <center>
                                                <p>
                                                    <h3><label><span class="modelabel" name="modelabel" id="modelabel">Time In</span></label></h3>
                                                </p>
                                            </center>
                                            <br>
                                            <div class="row">
                                                <div class="panel panel-default"
                                                    style="width: 900px; height: 265px; margin: auto ; border: none; right">
                                                    <div class="panel-body">
                                                        <div class="col-sm-4" style="text-align: center;">
                                                            <div class="box">
                                                                <img alt="User Pic" src=".\storage\pmi_pictures\not_found.jpg"
                                                                    id="employee_image" class="img-circle img-responsive"
                                                                    style="width:275px;height:235px;">
                                                                <div class="text" id="text">
                                                                    <label>
                                                                        DUPLICATE RECORD
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="emplono" id="emplono">
                                                        </div>

                                                        <div class="col-sm-8">
                                                            <div class="container">
                                                            </div>
                                                            <ul class="container details">
                                                                <p>
                                                                    <h3><span class="glyphicon glyphicon-user one"
                                                                            style="width:550px;">&nbsp;<b><span
                                                                                    style="font-family: Arial; font-size: 33px"
                                                                                    name="emploname"
                                                                                    id="emploname"></span></b></span></h3>
                                                                </p>
                                                                <br>
                                                                <!-- <p><h3><label>Department&nbsp;&nbsp;<span class="glyphicon glyphicon-briefcase one" style="width:50px;"></span></label></h3><h2><span name="dept" id="dept"></span></h2></p>  -->

                                                                <p>
                                                                    <h3><span class="glyphicon glyphicon-calendar one"
                                                                            style="width:550px;">&nbsp;<b><span
                                                                                    style="font-family: Arial; font-size: 33px"
                                                                                    name="tapdate" id="tapdate"></span></b></span>
                                                                    </h3>
                                                                </p>
                                                                <br>
                                                                <p>
                                                                    <h3><span class="glyphicon glyphicon-time one"
                                                                            style="width:550px;">&nbsp;<b><span
                                                                                    style="font-family: Arial; font-size: 33px"
                                                                                    name="taptime" id="taptime"></span></b></span>
                                                                    </h3>
                                                                </p>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="tblTrainingAttendance" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 15%;" class="text-center">Employee Number</th>
                                                    <th style="width: 10%;" class="text-center">Name</th>
                                                    <th style="width: 10%;" class="text-center">Position</th>
                                                    <th style="width: 15%;"  class="text-center">Date</th>
                                                    <th style="width: 15%;"  class="text-center">Time In</th>
                                                    <th style="width: 15%;"  class="text-center">Time Out</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="row">
                                        @csrf
                                        <form method="post" id="trainingAttendanceTimeInOut" class="form-horizontal">
                                            {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
                                            {{-- <input type="" name="walkin_id" id="walkin_id" placeholder="walkin_id"> --}}
                                            <input type="text" id="txtScanner" name="txtScanner"
                                                onkeyup="this.value = this.value.toUpperCase();" placeholder="txtScanner">
                                            {{-- <input type="text" class="form-control" id="date" name="date" maxlength="50" required=""
                                             readonly>
                                            <input type="text" class="form-control" id="time" name="time" maxlength="50" required=""
                                             readonly placeholder="time">
                                            <input type="text" class="form-control" id="status" name="status" maxlength="50" required=""
                                                value="1" readonly placeholder="status"> --}}
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="menu2" role="tabpanel" aria-labelledby="menu2-tab">
                                <div class="card card-dark">
                                    <div class="card-header">
                                        <h3 class="card-title">Training Attendance Summary</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="float-sm-right  mb-3">
                                        </div>
                                        <div class="table-responsive">
                                            <table id="tblTrainingAttendanceSummary" class="table table-bordered table-striped table-hover" style="width: 100%;">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        TrainingAttendanceSummary : '#tblTrainingAttendanceSummary',
        TrainingAttendanceRequest : '#tblTrainingAttendanceRequest'
    }
    // dtTrainingAttendance = $(tbl.TrainingAttendance).DataTable({
    // "processing" : false,
    //     "serverSide" : true,
    //     "ajax" : {
    //         url: "view_training_attendance",
    //     },
    //     "columns":[
    //         { "data" : "action", orderable:false, searchable:false },
    //         { "data" : "ctrl_number" },
    //         { "data" : "date_filed" },
    //         { "data" : "received_date" },
    //     ],
    //     "order": [[ 1, "asc" ]],
    // });
    dtTrainingAttendanceSummary = $(tbl.TrainingAttendanceSummary).DataTable({
    "processing" : false,
        "serverSide" : true,
        "ajax" : {
            url: "view_training_attendance_summary",
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
    $(tbl.TrainingAttendanceSummary).on('click','#checkBulkUserModule','tr', function () {

    });



    //Attendance
    // $("#txtScanner").keyup(function (e) {
    //     let employeeNo = $('#txtScanner').val();
    //     if (e.keyCode == 13) {
    //         let data = {
    //             employeeNo : employeeNo
    //         };
    //         let serialized_data = {};
    //         console.log(data);
           
          
    //     }
    // });
    // $(document).keypress(function (e) {
    //     $("#txtScanner").focus();
    // });


    // Replace the existing block with this
    // Attendance
    // $(function () {
    //     // Always handle form submit via AJAX and prevent full page reload
    //     $('#trainingAttendanceTimeInOut').on('submit', function (e) {
    //         e.preventDefault();
    //         let employeeNo = $('#txtScanner').val();
    //         if (!employeeNo) return;
    //         let data = { employeeNo: employeeNo };
    //         let serialized_data = {};
    //         console.log('Submitting attendance', data);
    //         call_ajax_serialize(data, serialized_data, 'save_attendance', function (response) {
    //             console.log(response);
    //             // optional: clear input and refocus
    //             $('#txtScanner').val('').focus();
    //             // dt.draw(); // uncomment if you need to redraw a datatable
    //         });
    //     });

    //     // When Enter is pressed in the scanner input, prevent default and trigger the AJAX submit
    //     $('#txtScanner').on('keydown', function (e) {
    //         if (e.key === 'Enter' || e.keyCode === 13) {
    //             e.preventDefault();
    //             $('#trainingAttendanceTimeInOut').submit();
    //         }
    //     });

    //     // keep scanner focused
    //     $(document).keypress(function () {
    //         $('#txtScanner').focus();
    //     });
    // });

    $(function () {
        const $form = $('#trainingAttendanceTimeInOut');
        const $scanner = $('#txtScanner');

        // Handle form submission via AJAX
        $form.on('submit', function (e) {
            e.preventDefault();
            submitAttendance();
        });

        // Trigger submit on Enter key
        $scanner.on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $form.submit();
            }
        });

        // Keep scanner focused
        $(document).on('keypress', function () {
            $scanner.focus();
        });

        // Submit attendance via AJAX
        function submitAttendance() {
            const employeeNo = $scanner.val().trim();
            
            if (!employeeNo) {
                console.warn('Employee number is empty');
                return;
            }

            const data = { employeeNo };
            
            call_ajax_serialize(data, {}, 'save_attendance', function (response) {
                console.log('Attendance saved:', response);
                $scanner.val('').focus();
                // dt.draw(); // uncomment if you need to redraw datatable
            });
        }
    });
    </script>
@endsection

