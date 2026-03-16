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

                                {{-- <div class="float-sm-left mb-4 col-2">
                                    <select class="form-control selectFilter" name="select_filter" id="selectFilterId">
                                        <option value="4" selected>All Training Request</option>
                                        <option value="1">Conformance</option>
                                        <option value="2">Receiving</option>
                                        <option value="3">TU Head Approval</option>
                                    </select>
                                </div> --}}

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
    <div class="modal fade" id="modalAddTrainingRequest" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Training Request Form</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formAddTrainingRequest" autocomplete="off">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group  mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100" id="basic-addon1">Control Number</span>
                                    </div>
                                    <input type="text" class="form-control" name="document_no"  placeholder="Auto Generated" id="documentNo" readonly>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100" id="basic-addon1">Date Filed</span>
                                    </div>
                                    <input type="text" class="form-control" name="date_filed" value="<?= date('Y-m-d') ?>" id="dateFiled" required readonly>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="header">
                                <h5 class="text-info"><i class="fa fa-info-circle"></i> Request Details</h5>
                            </div>

                        </div>

                        <div class="row">
                                <div class="col-sm-6">
                                    <div class="input-group  mb-3">
                                        <div class="input-group-prepend w-50">
                                            <span class="input-group-text w-100" id="basic-addon1">Department</span>
                                        </div>
                                        <select name="department" id="selectDepartment" class="form-control select2bs5" required></select>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="input-group  mb-3">
                                        <div class="input-group-prepend w-50">
                                            <span class="input-group-text w-100" id="basic-addon1">Section</span>
                                        </div>
                                        <select name="section" id="selectSection" class="form-control select2bs5" required></select>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="input-group  mb-3">
                                        <div class="input-group-prepend w-50">
                                            <span class="input-group-text w-100" id="basic-addon1">Job Function</span>
                                        </div>
                                        <select name="job_function" id="selectJobFunction" class="form-control select2bs5" required>
                                            <option value="0" selected disabled>Select One</option>
                                            <option value="1">Operator</option>
                                            <option value="2">Material Handler</option>
                                            <option value="3">Inspector</option>
                                            <option value="4">Technician</option>
                                            <option value="5">Engineer</option>
                                            <option value="6">Supervisor</option>
                                            <option value="7">Clerk</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="input-group  mb-3">
                                        <div class="input-group-prepend w-50">
                                            <span class="input-group-text w-100" id="basic-addon1">Area/Line Allocation</span>
                                        </div>
                                        <select name="area_line" id="selectAreaLine" class="form-control select2bs5" required>
                                            <option value="0" selected disabled>Select One</option>
                                            <option value="1">Automotive Line</option>
                                            <option value="2">Non-Automotive Line</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="input-group  mb-3">
                                        <div class="input-group-prepend w-20">
                                            <span class="input-group-text w-100" id="basic-addon1">Reason</span>
                                        </div>
                                        <select name="reason" id="selectReason" class="form-control select2bs5" required>
                                            <option value="0" selected disabled>Select Reason</option>
                                            <option value="1">Newly Hired</option>
                                            <option value="2">Regularization</option>
                                            <option value="3">Transferred from other assembly line</option>
                                            <option value="4">Transferred from other section/department/division</option>
                                            <option value="5">ML/SL/VL (whose leave reached at least 1 month)</option>
                                            <option value="6">New Product/Line</option>
                                            <option value="7">Flexibility Certification</option>
                                            <option value="8">Re-certification</option>
                                        </select>

                                    </div>
                                </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col">
                                <div class="table-responsive tbl">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" id="btnAddTrainee" class="btn btn-primary"><i class="fa fa-plus"></i> Request Employee</button>
                                    </div>
                                    <br>
                                    <table class="table table-sm table-bordered" id="tblRequestedEmployeeDetails" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Actionss</th>
                                                <th>Date Hired</th>
                                                <th>Employee No</th>
                                                <th>Name</th>
                                                <th>Position/Dept./Section</th>
                                                <th>Title</th>
                                                <th>Result</th>
                                                <th>Remarks</th>
                                                <th>Venue</th>
                                                <th>Endorsement Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="table-responsive" hidden>
                                    <table id="tblRequestedTrainingDetails" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Date Hired</th>
                                                <th>Employee No</th>
                                                <th>Name</th>
                                                <th>Position/Dept./Section</th>
                                                {{-- <th>From Station/Production</th> --}}
                                                {{-- <th>To Station/Production</th> --}}
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div class="input-group  mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100" id="basic-addon1">Requestor</span>
                                    </div>
                                    <input type="text" class="form-control" name="requestor" id="txtRequestor" readonly>
                                    {{-- <input type="hidden" class="form-control" name="requestor_id" id="txtRequestorId" readonly> --}}
                                </div>
                            </div>

                            <div class="col-sm-6">
                                 <div class="input-group  mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100" id="basic-addon1">Section Head</span>
                                    </div>
                                    <select name="section_head" id="selectSectionHead" class="form-control select2bs5" required></select></select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="btnSubmitTrainingRequest" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->


    <div class="modal fade" id="modalAddEmployee" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add Employee</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formAddEmployee" autocomplete="off">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="input-group  mb-3">
                                    <div class="input-group-prepend w-30">
                                        <span class="input-group-text w-100" id="basic-addon1">Memo Doc No.</span>
                                    </div>
                                    <select name="memo_doc_no" id="selectMemoDocNo" class="form-control select2bs5" required></select>
                                </div>
                            </div>

                        </div>

                        <div class="row mt-2">
                            <div class="col">
                                <div class="table-responsive">
                                    <br>
                                    <table class="table table-sm table-bordered" id="tblRequestedEmployeeByMemoDoc" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Date Hired</th>
                                                <th>Employee No</th>
                                                <th>Name</th>
                                                <th>Position/Dept./Section</th>
                                                <th>Title</th>
                                                <th>Result</th>
                                                <th>Remarks</th>
                                                <th>Venue</th>
                                                <th>Endorsement Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">

                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="btnSubmitHrMemoApproval" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


@endsection

@section('js_content')
    <script type="text/javascript">
    tbl = {
        TrainingAttendance : '#tblTrainingAttendance'
    }
    dtTrainingAttendance = $(tbl.TrainingAttendance).DataTable({
        "processing" : false,
            "serverSide" : true,
            "ajax" : {
            url: "view_training_attendance",
            // data: function (param){
            //     param.status = $("#selEmpStat").val();
            // }
            },

            "columns":[
                { "data" : "rawBulkCheckBox", orderable:false, searchable:false },
                { "data" : "module_name" },
                { "data" : "updated_by" },
                { "data" : "action", orderable:false, searchable:false },
            ],

        //   "columnDefs": [
        //     {
        //       "targets": [3, 5],
        //       "data": null,
        //       "defaultContent": "N/A"
        //     },
        //     // { "visible": false, "targets": 1 }
        //   ],
            "order": [[ 1, "asc" ]],
        });//end of dataTableUsers

    </script>
@endsection

