@php $layout = 'layouts.super_user_layout'; @endphp
@extends($layout)
@section('title', 'Personnel Skill Matrix')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Personnel Skill Matrix</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Personnel Skill Matrix</li>
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
                                <h3 class="card-title">Personnel Skill Matrix Module</h3>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                <div class="float-sm-right">
                                    <button class="btn btn-success" id="btnChooseFileToExport">
                                        <i class="fas fa-file-export fa-md me-2"></i> Export Report
                                    </button>
                                </div>
                                <br><br>

                                <ul class="nav nav-tabs" id="employeeTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active"id="direct-tab"data-toggle="tab" href="#directEmployees" role="tab">
                                            Direct Employees
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" id="indirect-tab" data-toggle="tab" href="#subconEmployees" role="tab">
                                            Subcon Employees
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">

                                    <!-- Direct Employees -->
                                    <div class="tab-pane fade show active" id="directEmployees" role="tabpanel">
                                        <div class="table-responsive">
                                            <table id="tblDirectEmployees" class="table table-bordered table-striped table-hover" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th width="10%" class="text-center">Action</th>
                                                        <th width="5%" class="text-center">E.N.</th>
                                                        <th width="20%" class="text-center">Name</th>
                                                        <th width="7%" class="text-center">Date Hired</th>
                                                        <th width="20%" class="text-center">Position</th>
                                                        <th width="20%" class="text-center">Section</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>

                                    </div>

                                    <!-- Indirect Employees -->
                                    <div class="tab-pane fade" id="subconEmployees" role="tabpanel">
                                        <div class="table-responsive">
                                            <table id="tblSubconEmployees" class="table table-bordered table-striped table-hover" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th width="10%" class="text-center">Action</th>
                                                        <th width="5%" class="text-center">E.N.</th>
                                                        <th width="20%" class="text-center">Name</th>
                                                        <th width="7%" class="text-center">Date Hired</th>
                                                        <th width="20%" class="text-center">Position</th>
                                                        <th width="20%" class="text-center">Section</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>

                                    </div>
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

    <!-- MODAL -->

    <!-- Update Employee Modal -->
    <div class="modal fade" id="updateEmpInfoModalId" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">
                        Update Employee Details
                    </h4>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body p-0">

                    <div id="employeeAccordion">

                        <!-- Personal Information -->
                        <div class="card">

                               <div class="card-header accordion-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-white font-weight-bold" data-toggle="collapse" data-target="#collapseOne">
                                            Personal Information
                                        </button>
                                    </h5>
                                </div>

                            <div id="collapseOne" class="collapse show" data-parent="#employeeAccordion">

                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        {{ asset('images/default-user.png') }}
                                        <img src="{{ asset('images/default-user') }}"
                                        class="img-thumbnail rounded"
                                        style="width:150px;height:180px;object-fit:cover;">
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">E.N.</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" readonly id="empNo">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Name</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" readonly id="empName">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Position</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" readonly id="position">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Section</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" readonly id="section">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Date Hired</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" readonly id="dateHired">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Date Certified</label>
                                        <div class="col-md-7">
                                            <input type="date" class="form-control" id="dateCertified">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Validity Period</label>
                                        <div class="col-md-7">
                                            <input type="date" class="form-control" id="validity">
                                        </div>
                                    </div>

                                    {{-- <div class="form-group row">
                                        <div class="col-md-12 text-center">
                                            <div class="form-check">
                                                <input class="form-check-input"type="checkbox"id="visualExam">
                                                <label class="form-check-label">With Visual Exam
                                                </label>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <!-- Qualification -->
                        {{-- <div class="card">
                                <div class="card-header accordion-header bg-light">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-dark font-weight-bold" data-toggle="collapse" data-target="#collapseTwo">
                                            Qualification / Certification (Tech., Eng'g, Supv.)
                                        </button>
                                    </h5>
                                </div>


                            <div id="collapseTwo" class="collapse" data-parent="#employeeAccordion">
                                <div class="card-body">
                                    <!-- Your Qualification Table -->
                                </div>
                            </div>
                        </div> --}}

                        <!-- Skill Card -->
                        {{-- <div class="card">
                            <div class="card-header accordion-header bg-light">
                                <h5 class="mb-0">
                                    <button class="btn btn-link text-dark font-weight-bold" data-toggle="collapse" data-target="#collapseThree">
                                        Skill Card Rating
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseThree" class="collapse" data-parent="#employeeAccordion">
                                <div class="card-body">
                                    <!-- Skill Card Table -->
                                </div>
                            </div>
                        </div> --}}
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Save Changes</button>
                    <button class="btn btn-secondary"data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    <!-- View Employee Modal -->
    <div class="modal fade" id="viewEmpInfoModalId" tabindex="-1" role="dialog" data-backdrop="static">
        {{-- <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document"> --}}
        <div class="modal-dialog modal-dialog-scrollable modal-semi-full">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">
                        View Employee Details
                    </h4>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <div class="row">

                        <!-- Employee Information -->
                        <div class="col-md-5">

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label font-weight-bold text-right">E.N.</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="viewEmpNo" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label font-weight-bold text-right">Name</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="viewEmpName" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label font-weight-bold text-right">Position</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="viewPosition" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label font-weight-bold text-right">Section</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="viewSection" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label font-weight-bold text-right">Department</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="viewDepartment" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label font-weight-bold text-right">Division</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="viewDivision" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label font-weight-bold text-right">Date Hired</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="viewDateHired" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label font-weight-bold text-right">Employment Status</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="viewEmploymentStatus" readonly>
                                </div>
                            </div>

                            {{-- <div class="form-group row mb-0">
                                <label class="col-md-3 col-form-label font-weight-bold text-right">Hiring Status</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="viewHiringStatus" readonly>
                                </div>
                            </div> --}}

                        </div>

                        <!-- Summary -->
                        <div class="col-md-7">

                            <table class="table table-borderless mt-4">
                                <tr>
                                    <th class="text-success text-right">Passed:</th>
                                    <td id="lblPassed">0</td>
                                </tr>

                                <tr>
                                    <th class="text-primary text-right">Complied:</th>
                                    <td id="lblComplied">0</td>
                                </tr>

                                <tr>
                                    <th class="text-danger text-right">Failed:</th>
                                    <td id="lblFailed">0</td>
                                </tr>

                                <tr>
                                    <th class="text-right" style="font-size:35px;">
                                        Total:
                                    </th>

                                    <td style="font-size:35px;font-weight:bold;" id="lblTotal">
                                        0
                                    </td>
                                </tr>

                            </table>

                        </div>

                    </div>

                    <hr>

                    <!-- Accordion -->
                    <div id="viewAccordion">
                        <div class="card">
                            <div class="card-header p-0">
                                <button class="btn btn-block text-left font-weight-bold" data-toggle="collapse" data-target="#trainingCollapse">
                                    TRAININGS
                                    <i class="fas fa-chevron-down float-right mt-1"></i>
                                </button>
                            </div>

                            <div id="trainingCollapse" class="collapse" data-parent="#viewAccordion">

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tblEmployeeTrainings" class="table table-bordered table-striped table-hover" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Date</th>
                                                    <th class="text-center">Training Title / Reason for Certification</th>
                                                    <th class="text-center">Series Name / Family</th>
                                                    <th class="text-center">Station</th>
                                                    <th class="text-center">Detailed Stations</th>
                                                    <th class="text-center">Objective</th>
                                                    <th class="text-center">Trainor</th>
                                                    <th class="text-center">Results</th>
                                                    <th class="text-center">Venue</th>
                                                    {{-- <th class="text-center">Mechanics</th> --}}
                                                    <th class="text-center">Type of Training</th>
                                                    {{-- <th class="text-center">Remarks</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button class="btn btn-primary" id="btnGenerateSkillCard"><i class="fas fa-file-alt"></i>
                        Generate Skill Card
                    </button>
                    <button class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i>
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="chooseExportReportId" data-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">
                        Choose file to Export
                    </h4>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="col-md-12 mb-3">
                        <button class="btn btn-warning text-light w-100 py-3 fs-5 d-flex align-items-center justify-content-center gap-2" data-toggle="modal" title="Click this to generate pdf file." data-target="#modalGenerateSkillMatrixDetails">
                            <i class="fas fa-eye fa-lg mr-2" aria-hidden="true"></i> <span>Employee Skill Map</span>
                        </button>
                    </div>
                    <div class="col-md-12 mb-3">
                        <a role="button" class="btn btn-success w-100 py-3 fs-5 d-flex align-items-center justify-content-center gap-2" data-placement="bottom" title="Click this to generate excel file." >
                            <i class="fas fa-list fa-lg mr-2" aria-hidden="true"></i> <span>List of Certified Personnel</span>
                        </a>
                    </div>

                    <div class="col-md-12 mb-3">
                        <a role="button" class="btn btn-info w-100 py-3 fs-5 d-flex align-items-center justify-content-center gap-2" data-placement="bottom" title="Click this to generate excel file." data-original-title="" data-toggle="modal" data-target="#modalDateRangeCertificationMatrix">
                            <i class="fas fa-certificate fa-lg mr-2" aria-hidden="true"></i> <span>Inspectors Certification Matrix</span>
                        </a>
                    </div>

                     <div class="col-md-12 mb-3">
                        <a role="button" class="btn btn-primary w-100 py-3 fs-5 d-flex align-items-center justify-content-center gap-2" data-placement="bottom" title="Click this to generate excel file.">
                            <i class="fas fa-chart-bar fa-lg mr-2" aria-hidden="true"></i> <span>Inspectors Skill Chart</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalGenerateSkillMatrixDetails" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">
                        Skill Map
                    </h4>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                        <div class="row">
                            <div class="mb-4 col-sm-12">
                                <label for="selectedProductLine" class="form-label me-2">Product Line</label>
                                <div id="selectedProdLine" class="fs-5 mb-1"></div>
                                <select name="product_line" id="selectedProductLine" class="form-control select2bs5" required></select>
                            </div>

                            <div class="mb-4 col-sm-12">
                                <label for="selectedPosition" class="form-label me-2">Position</label>
                                <div id="selectedPosition" class="fs-5 mb-1"></div>
                                <select name="position" id="selectPosition" class="form-control select2bs5" required>
                                    {{-- <option value="" selected="" disabled="">-- Select Position --</option>
                                    <option value="1">Operator</option>
                                    <option value="2">Inspector</option> --}}
                                </select>
                            </div>

                            <div class="mb-4 col-sm-12">
                                <label for="text_generate_empno" class="form-label me-2">Employee</label>
                                <div id="selectedEmployee" class="fs-5 mb-1"></div>
                                <select name="employee[]" id="selectEmployee" class="form-control select2bs5" multiple required></select>
                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btnGenerateVisualMatrix" id="btnGenerateVisualMatrix">Export</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('js_content')
    <script type="text/javascript">

    </script>
@endsection

