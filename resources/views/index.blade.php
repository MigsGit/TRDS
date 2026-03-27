<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>TRDSv2 Training Attendance</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @include('shared.css_links.css_links')
</head>
<body class="">
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
                    <div class="card-header">
                    </div>
                    <div class="card-body bg-light">
                        <div class="row align-items-center mb-4">
                            <div class="col-sm-5 text-center">
                                <div class="p-3 bg-white shadow-sm rounded">
                                    <h5 class="text-muted mb-0">Current Date</h5>
                                    <h2 id="date" class="font-weight-bold text-primary"></h2>
                                </div>
                            </div>
                            <div class="col-sm-7 text-center">
                                <div class="p-3">
                                    <h2 class="display-4 font-weight-bold">
                                        <span class="modelabel badge badge-secondary px-4 py-2" name="modelabel" id="modelabel">---</span>
                                    </h2>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5 justify-content-center">
                            <div class="col-lg-10">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 text-center border-right">
                                                <div class="position-relative d-inline-block">
                                                    {{-- <img alt="User Pic" src=".\storage\pmi_pictures\not_found.jpg"
                                                        id="employee_image" class="rounded-circle img-thumbnail shadow"
                                                        style="width:200px; height:200px; object-fit: cover;"> --}}

                                                    <div id="text" class="position-absolute w-100" style="bottom: 10px; left: 0;">
                                                        <span class="badge badge-danger p-2 shadow d-none">DUPLICATE RECORD</span>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="emplono" id="emplono">
                                            </div>

                                            <div class="col-md-8 pl-md-5">
                                                <div class="mb-3">
                                                    <small class="text-uppercase text-muted font-weight-bold">Employee Name</small>
                                                    <h2 class="mb-0">
                                                        <i class="fas fa-user text-primary mr-2"></i>
                                                        <span style="font-family: 'Arial', sans-serif; font-weight: 700;" name="emploname" id="emploname">---</span>
                                                    </h2>
                                                </div>

                                                <div class="row mt-4">
                                                    <div class="col-6">
                                                        <small class="text-uppercase text-muted font-weight-bold">Date Logged</small>
                                                        <h4>
                                                            <i class="fas fa-calendar-alt text-success mr-2"></i>
                                                            <span name="tapdate" id="tapdate">---</span>
                                                        </h4>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-uppercase text-muted font-weight-bold">Time Logged</small>
                                                        <h4>
                                                            <i class="fas fa-clock text-warning mr-2"></i>
                                                            <span name="taptime" id="taptime">---</span>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="fas fa-list mr-2"></i> Recent Attendance Logs</h5>
                            </div>
                            <div class="table-responsive">
                                <table id="tblTrainingAttendance" class="table table-hover table-striped mb-0" style="width: 100%;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">Employee Number</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Position</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Time In</th>
                                            <th class="text-center">Time Out</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <form method="post" id="trainingAttendanceTimeInOut" class="form-horizontal w-50">
                                @csrf
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span style="opacity:0;" class="input-group-text bg-primary text-white"><i class="fas fa-barcode"></i></span>
                                    </div>
                                    <input type="text" id="txtScanner"  style="opacity:0;" name="txtScanner"
                                        class="form-control form-control-lg border-primary"
                                        onkeyup="this.value = this.value.toUpperCase();"
                                        placeholder="Scan ID or Type ID Number..." autofocus>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->

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
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label>From</label>
                                <input type="date" class="form-control" name="fromDate" id="fromDate">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label>To</label>
                                <input type="date" class="form-control" name="toDate" id="toDate">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label>Present Count</label>
                                <input type="number" class="form-control" id="presentCount">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Absent Count</label>
                                <input type="number" class="form-control" id="absentCount">
                              </div>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered" id="tblTrainingAttendanceRequest" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Employee No</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Training Hours</th>
                                <th>Reason/Remarks of Absence</th>
                                <th>Action</th>
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
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label>From</label>
                                <input type="date" class="form-control" name="fromDate" id="fromDate">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label>To</label>
                                <input type="date" class="form-control" name="toDate" id="toDate">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label>Present Count</label>
                                <input type="number" class="form-control" id="presentCount">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Absent Count</label>
                                <input type="number" class="form-control" id="absentCount">
                              </div>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered" id="tblTrainingAttendanceRequest" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Employee No</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Training Hours</th>
                                <th>Reason/Remarks of Absence</th>
                                <th>Action</th>
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

@include('shared.js_links.js_links')
<script type="text/javascript">
    tbl = {
        TrainingAttendance : '#tblTrainingAttendance',
        TrainingAttendanceSummary : '#tblTrainingAttendanceSummary',
        TrainingAttendanceRequest : '#tblTrainingAttendanceRequest'
    }

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
            url: "view_training_attendance_request_details",
            // data: function (param){
            //     param.trainingRequestsId = '';
            // },
        },
        "columns":[
            { "data" : "status"},
            { "data" : "rapidx_emp_no" },
            { "data" : "fullname" },
            { "data" : "date","defaultContent": "NO RECORD!"},//date to
            { "data" : "training_hours","defaultContent": "" }, //hours time in out
            { "data" : "remarks" ,"defaultContent": ""}, //remarks
            { "data" : "action"}, //remarks
        ],
        "drawCallback": function(settings) {
            var api = this.api();

            var totalPresent = api.rows().data().filter(function(row) {
                return row.status === "PRESENT";
            }).length;

            $('#presentCount').val(totalPresent);
        },
        "order": [[ 1, "asc" ]],
    });
    $(tbl.TrainingAttendanceSummary).on('click','.aViewTrainingAttendance','tr', function () {
        trainingAttendanceRequest = $(this).attr('training-requests-id');
    });
    $('#fromDate').change(function (e) {
        e.preventDefault();
        fromDate = $(this).val();
        dtViewTrainingAttendanceRequest.ajax.url(`view_training_attendance_request_details?trainingAttendanceRequest=${trainingAttendanceRequest} && fromDate=${fromDate??''} && toDate=${toDate??''}`).draw();
    });
    $('#toDate').change(function (e) {
        e.preventDefault();
        toDate = $(this).val();
        dtViewTrainingAttendanceRequest.ajax.url(`view_training_attendance_request_details?trainingAttendanceRequest=${trainingAttendanceRequest} && fromDate=${fromDate??''} && toDate=${toDate??''}`).draw();
    });


    //========= Attendance
    dtTrainingAttendance = $(tbl.TrainingAttendance).DataTable({
    "processing" : false,
        "serverSide" : true,
        "ajax" : {
            url: "view_training_attendance",
        },
        "columns":[
            { "data" : "rapidx_emp_no" },
            { "data" : "fullname" },
            { "data" : "position" },
            { "data" : "date" },
            { "data" : "time_in" },
            { "data" : "time_out" },
        ],
        "order": [[ 1, "asc" ]],
    });
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

            let data = { employeeNo };
            let apiData = $.param(data);
            $.ajax({
                type: "POST",
                url: "api/save_attendance",
                data: apiData,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    let userCollection = response.userCollection
                    let fullName = response.fullName;
                    $('#emploname').html(fullName);
                    if(response.trainingAttendanceIsExists === 'true'){
                        $('#modelabel').html(response.msg).removeClass('badge-success');
                        $('#modelabel').html(response.msg).addClass('badge-danger');
                    }else{
                        $('#modelabel').html(response.timeOrTimeOut).removeClass('badge-danger');
                        $('#modelabel').html(response.timeOrTimeOut).addClass('badge-success');
                    }

                    dtTrainingAttendance.ajax.url('view_training_attendance?employeeNo='+employeeNo).draw();
                    console.log('Attendance saved:', response);
                    $scanner.val('').focus();

                },error: function (result) {
                    let errorResponse = result.responseJSON;
                    let status = result.status;
                    dtTrainingAttendance.ajax.url('view_training_attendance?employeeNo=').draw();
                    $('#emploname').html('---');
                    $scanner.val('').focus();
                    $('#modelabel').html(errorResponse.msg).addClass('badge-danger');
                    if( status === 500){
                        toastr.error(errorResponse.msg);
                    }
                }
            });
        }
    });

    // Start the clock immediately
    runAttendanceClock();
    // Refresh every 1000ms (1 second)
    const clockInterval = setInterval(runAttendanceClock, 1000);
    function runAttendanceClock() {
        const now = new Date();

        // 1. Full Date for the top card (e.g., Thursday, March 26, 2026)
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const fullDate = now.toLocaleDateString('en-US', dateOptions);

        // 2. Short Date for the "Date Logged" section (YYYY-MM-DD)
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const shortDate = `${year}-${month}-${day}`;

        // 3. Digital Time (12-hour format with AM/PM)
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const timeString = now.toLocaleTimeString('en-US', timeOptions);

        // Update the DOM elements using your original IDs
        if (document.getElementById('date')) {
            // document.getElementById('date').innerText = fullDate;
            $('#date').html( fullDate);
        }
        if (document.getElementById('tapdate')) {
            $('#tapdate').html( fullDate);
            // document.getElementById('tapdate').innerText = shortDate;
        }
        if (document.getElementById('taptime')) {
            $('#taptime').html(timeString);
            // document.getElementById('taptime').innerText = timeString;
        }
    }
    setInterval(function() {
        location.reload(); // Reload the page to refresh the session
        // $.ajax({
        //     url: '/refresh-session',
        //     type: 'GET',
        //     success: function(data) {
        //         // Update the CSRF token in the meta tag and all AJAX headers
        //         $('meta[name="csrf-token"]').attr('content', data.token);
        //         $.ajaxSetup({
        //             headers: {
        //                 'X-CSRF-TOKEN': data.token
        //             }
        //         });
        //         console.log('Session kept alive and token refreshed.');
        //     }
        // });
    // }, 1000 * 60 * 15); // 15 minutes
    }, 1000 * 60 * 15); // 15 minutes
</script>
</body>
</html>
