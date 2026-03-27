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
                                <input type="number" class="form-control" id="presentCount" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Absent Count</label>
                                <input type="number" class="form-control" id="absentCount" readonly>
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
        columns: [
             { 
                data: "status",
                render: function(data) {
                    let badge = data === 'PRESENT' ? 'badge-success' : 'badge-danger';
                    return `<span class="badge badge-pill ${badge}">${data}</span>`;
                }
            },
            { data: "emp_no" },
            { data: "name" },
            { data: "date" },
            { data: "training_hours",
                render: function(data) {
                    let badge = data === 'NO RECORD' ? 'badge-danger' : 'badge-success';
                    return `<span class="badge badge-pill ${badge}">${data}</span>`;
                }
            },
            { data: "remarks" },
            { data: "action" },
           
        ],
        // Update your input fields whenever the table is drawn
        "drawCallback": function(settings) {
            var json = settings.json; // This contains the 'with' data from Laravel
            if (json) {
                $('#presentCount').val(json.totalPresent);
                $('#absentCount').val(json.totalAbsent);
            }
        },
        "order": [[ 1, "asc" ]],
    });
    
    $(tbl.TrainingAttendanceSummary).on('click','.aViewTrainingAttendance','tr', function () {
        trainingAttendanceRequest = $(this).attr('training-requests-id');
        let ctrlNo = $(this).attr('ctrl-no');
        $('#trainingAttendanceCtrlNo').val(ctrlNo);
    });

    $('#fromDate, #toDate').on('change', function() {
        // Only draw if all three fields have values to avoid empty loops
        if($('#fromDate').val() && $('#toDate').val()) {
            let fromDate = $('#fromDate').val(); 
            let toDate = $('#toDate').val()
            dtViewTrainingAttendanceRequest.ajax.url(`view_training_attendance_request_details?trainingAttendanceRequest=${trainingAttendanceRequest} && fromDate=${fromDate??''} && toDate=${toDate??''}`).draw();
        }
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
                url: "save_attendance",
                data: apiData,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    let userCollection = response.userCollection
                    let fullName = response.fullName;
                    // let now = new Date();
                    // // Format Date: e.g., Thursday, March 26, 2026
                    // const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    // // Format Time: e.g., 02:30:05 PM
                    // const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
                    // const dateString = now.toLocaleDateString('en-US',  response.date ?? dateOptions)
                    // const timeString = now.toLocaleTimeString('en-US', response.time_out ?? response.time_out ?? timeOptions);

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
                    $scanner.val('').focus();
                    $('#modelabel').html(errorResponse.msg).addClass('badge-danger');
                    // console.log(errorResponse);
                    // console.log(errorResponse.msg);
                    // console.log(errorResponse.trainingAttendanceIsExists);
                    // console.log(errorResponse.isSuccess);
                    // console.log(result.status);
                    // console.log(result.statusText);
                    // $('#modal-loading').modal('hide');
                    if( status === 500){
                        toastr.error(errorResponse.msg);
                    }
                }
            });
            // call_ajax_serialize(data, {}, 'save_attendance', function (response) {
            //     console.log('Attendance saved:', response);
            //     $scanner.val('').focus();
            //     dtTrainingAttendance.ajax.url('view_training_attendance?employeeNo='+employeeNo).draw(); // uncomment if you need to redraw datatable
            // });
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
    </script>
@endsection

