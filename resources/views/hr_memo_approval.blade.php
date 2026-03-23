@php $layout = 'layouts.super_user_layout'; @endphp
@extends($layout)
@section('title', 'HR Memo & Approval')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>HR Memo & Approval</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">HR Memo & Approval</li>
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
                                <h3 class="card-title">HR Memo & Approval Module</h3>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                <div class="float-sm-right">
                                    <button class="btn btn-primary" id="btnShowAddHrMemoApproval">
                                        <i class="fa fa-initial-icon"></i> New Memo
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
                                    <table id="tblHrMemoApproval" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Action</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 20%;" class="text-center">Document No.</th>
                                                <th style="width: 20%;" class="text-center">Date Filed</th>
                                                <th style="width: 20%;"  class="text-center">Reason</th>
                                                <th style="width: 20%;" class="text-center">Subject</th>
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
    <div class="modal fade" id="modalHrMemoApproval" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit HR Memo Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formHrMemoApproval" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <input type="hidden" id="txtHrMemoId" name="hr_memo_id">

                                <div class="form-group">
                                    <label>Document No</label>
                                    {{-- AUTO-GENERATE --}}
                                    <input type="text" class="form-control" name="document_no" id="documentNo" placeholder="Auto Generate" readonly>
                                </div>

                                <div class="form-group">
                                    <label>Classification</label>
                                    <select class="form-control" name="classification" id="classification" required>
                                        <option value="" disabled selected>Select One</option>
                                        <option value="1">Direct</option>
                                        <option value="2">Subcon</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Reason</label>
                                    <select class="form-control" name="reason" id="reason" required>
                                        <option value="" disabled selected>Select One</option>
                                        <option value="1">Newly Hired</option>
                                        <option value="2">Maternity Leave</option>
                                        <option value="3">Sick Leave</option>
                                        <option value="4">Vacation Leave</option>
                                        <option value="5">Promoted</option>
                                        <option value="6">Transferred</option>
                                        <option value="7">Regularization</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Date Filed</label>
                                    <input type="date" class="form-control" name="date_filed" id="dateFiled" required>
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label>Subject</label>
                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Enter Subject" required>
                                </div>

                                <div class="form-group">
                                    <label>From</label>
                                    <input type="text" class="form-control" name="from" id="from" value="HRS Training and Recruitment" readonly>
                                </div>

                                <div class="form-group">
                                    <label>To</label>
                                    <select class="form-control select2bs5 selectToRecipients" name="to[]" id="selectTo" multiple required>
                                        {{-- <option value="" disabled selected> Select To/s </option> --}}
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Cc</label>
                                    <select class="form-control select2bs5 selectCcRecipients" name="cc[]" id="selectCc" multiple required>
                                        {{-- <option value="" disabled selected> Select Cc/s </option> --}}
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Multiple Data for Trainee Details --}}
                        <div class="row mt-5">
                            <div class="col">
                                <div class="table-responsive">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" id="btnAddTrainee" data-counter="" class="btn btn-primary"><i class="fa fa-plus"></i> Add Trainee</button>
                                    </div>
                                    <br>
                                    <table class="table table-sm table-bordered" id="tblTraineeDetails" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">Action</th>
                                                {{-- <th style="width: 10%;">Number</th> --}}
                                                <th style="width: 10%;">Employee No</th>
                                                <th style="width: 10%;">Name</th>
                                                <th style="width: 10%;">Training Venue</th>
                                                <th style="width: 10%;">Endorsement Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <div class="row"> --}}
                            <div class="col-md-6 justify-content-start">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>

                            <div class="col-md-6 justify-content-end">
                                <button type="button" id="btnApprove" class="btn btn-success float-right d-none">
                                    <i class="fa fa-thumbs-up"></i> Approve
                                </button>

                                <button type="button" id="btnDisapprove" class="btn btn-danger float-right mr-2 d-none">
                                    <i class="fa fa-thumbs-down"></i> Disapprove
                                </button>

                                <button type="submit" id="btnSubmitHrMemoApproval" class="btn btn-success float-right"><i class="fa fa-check"></i> Save</button>
                            </div>
                        {{-- </div> --}}
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal fade" id="modalTraineeDetails" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-object-group text-info"></i>Add Trainee Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="formTraineeDetails" autocomplete="off">
                        @csrf
                        <div class="input-group input-group-sm mb-3" hidden>
                            <div class="input-group-prepend w-50">
                                <span class="input-group-text w-100">Trainee Details ID</span>
                            </div>
                                <input type="text" class="form-control form-control-sm" id="txtFrmTraineeDetailsId" name="trainee_details_id" readonly>
                        </div>

                        <div class="row">
                            {{-- LEFT SIDE --}}
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100">Employee Number</span>
                                            </div>
                                                {{-- <input type="text" class="form-control form-control-sm" name="employeen_umber" id="employeeNumber" required> --}}
                                                <select class="form-control form-control-sm select2bs5 selectEmpNo" type="text" name="employeen_umber" id="employeeNumber" required></select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100">Date Hired</span>
                                            </div>
                                                <input type="date" class="form-control form-control-sm" name="date_hired" id="dateHired" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100">Training Venue</span>
                                            </div>
                                                <input type="text" class="form-control form-control-sm" name="training_venue" id="trainingVenue" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100">Department</span>
                                            </div>
                                                <input type="text" class="form-control form-control-sm" name="department" id="department" readonly>
                                                {{-- <select class="form-control select2bs5" type="text" name="dept_section" id="deptSection"  required></select> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT SIDE --}}
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100">Employee Name</span>
                                            </div>
                                                <input type="text" class="form-control form-control-sm" name="employee_name" id="employeeName" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100">Position</span>
                                            </div>
                                                <input type="text" class="form-control form-control-sm" name="position" id="position" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100">Endorsement Date</span>
                                            </div>
                                                <input type="date" class="form-control form-control-sm" name="endorsement_date" id="endorsementDate" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100">Product Allocation (Section)</span>
                                            </div>
                                                <input type="text" class="form-control form-control-sm" name="prod_allocation" id="prodAllocation" readonly>
                                                {{-- <select class="form-control select2bs5" type="text" name="prod_allocation" id="prodAllocation" required></select> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="table-responsive">
                                    <div class="d-flex justify-content-between mb-3">
                                        <button type="button" id="btnAddExamination" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Examination/s</button>
                                    </div>

                                    <table class="table table-sm" id="tblExamination" style="width: 100%;">
                                        <thead>
                                            <tr class="bg-light">
                                                <th style="width: 30%;">Title</th>
                                                <th style="width: 30%;">Result</th>
                                                <th style="width: 30%;">Training Remarks</th>
                                                <th style="width: 10%;">Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="btnAddTraineeDetailsToList">Add To List</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_content')
    {{-- <script type="text/javascript"> --}}
        <script src="{{ asset('public/js/my_js/HrMemoApproval.js') }}?<?=time()?>"></script>
    {{-- </script> --}}
@endsection

