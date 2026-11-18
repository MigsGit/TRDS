@php $layout = 'layouts.super_user_layout'; @endphp

@extends($layout)
@section('title', 'ETR')

@section('content_page')
    <style type="text/css">
        table.table thead th{
            padding-top: 5px;
            padding-bottom: 5px;
            padding-right: 5px;
            padding-left: 5px;
            font-size: 16px;
            text-align: center;
            vertical-align: middle;
            /* white-space:nowrap; */
            padding: 5px 5px;
            margin: 3px 3px;
        }
        table.table tbody td{
            padding: 4px 4px;
            margin: 1px 1px;
            font-size: 16px;
            vertical-align: middle;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.12) !important;
        }
        .info-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 18px;
            display: flex;
            align-items: center;
            background: #fff;
            transition: all .3s ease;
            height: 100%;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,.08);
        }

        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 15px;
            color: #fff;
            flex-shrink: 0;
        }

        .bg-department { background: #eca93d; }
        .bg-section { background: #20c997; }
        .bg-division { background: #17a2b8; }
        .bg-date { background: #e66f0f; }
        .bg-employment { background: #28a745; }
        .bg-hiring-status { background: #0c60b9; }
    </style>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Training Request Database System (TRDS)</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('blank') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Training Request Database System (TRDS)</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <!-- Header -->
                    {{-- <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                                <h4 class="mb-0 font-weight-bold">
                                    <i class="fa fa-users text-dark mr-2"></i>
                                    Training Record of Employee
                                </h4>
                            </div>

                            <div class="col-lg-4 offset-lg-2 col-md-6 ml-auto">
                                <label for="selectEmployee" class="small text-muted mb-1">
                                    Select Employee
                                </label>

                                <select class="form-control get-employee-info select2bs5" id="selectEmployee">
                                </select>
                            </div>
                        </div>
                    </div> --}}

                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-lg-8 col-md-12 mb-2 mb-lg-0">
                                <div class="d-flex align-items-center flex-wrap">
                                    <h4 class="mb-0 font-weight-bold mr-3">
                                        <i class="fa fa-users text-dark mr-2"></i>
                                        Training Record of Employee
                                    </h4>

                                    <button type="button"
                                            class="btn btn-outline-dark"
                                            id="btnChooseFileToExport">
                                        <i class="fa fa-user mr-1"></i>
                                        Personnel Skill Matrix
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <label for="selectEmployee"
                                    class="small text-muted mb-1">
                                    Select Employee
                                </label>

                                <select class="form-control get-employee-info select2bs5"
                                        id="selectEmployee">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Employee Profile -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <!-- Header -->
                            <div class="card-header border-0 text-white rounded-top-4 bg-secondary">
                                <div class="d-flex align-items-center">
                                    <!-- Avatar -->
                                    <div class="rounded-circle bg-white text-primary d-flex justify-content-center align-items-center shadow"
                                        style="width:85px;height:85px;">
                                        <i class="fa fa-user fa-3x"></i>
                                    </div>

                                    <!-- Employee Name -->
                                    <div class="ml-4">
                                        <h3 class="mb-1 font-weight-bold text-white" id="displayEmployeeName">Employee Name</h3>

                                        <span class="badge badge-light px-3 py-2">
                                            <i class="fa fa-briefcase mr-1"></i>
                                            <span id="displayPosition">Position</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4 mb-3">
                                        <div class="info-card">
                                            <div class="info-icon bg-department">
                                                <i class="fa fa-building"></i>
                                            </div>
                                            <div>
                                                <div class="info-label"> <strong>Department</strong></div>
                                                <div class="info-value" id="department">-</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4 mb-3">
                                        <div class="info-card">
                                            <div class="info-icon bg-section">
                                                <i class="fa fa-sitemap"></i>
                                            </div>
                                            <div>
                                                <div class="info-label"> <strong>Section</strong></div>
                                                <div class="info-value" id="section">-</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4 mb-3">
                                        <div class="info-card">
                                            <div class="info-icon bg-division">
                                                <i class="fa fa-object-group"></i>
                                            </div>
                                            <div>
                                                <div class="info-label"> <strong>Division</strong></div>
                                                <div class="info-value" id="division">-</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4 mb-3">
                                        <div class="info-card">
                                            <div class="info-icon bg-date">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <div>
                                                <div class="info-label"> <strong>Date Hired</strong></div>
                                                <div class="info-value" id="dateHired">-</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4 mb-3">
                                        <div class="info-card">
                                            <div class="info-icon bg-employment">
                                                <i class="fa fa-id-badge"></i>
                                            </div>
                                            <div>
                                                <div class="info-label"> <strong>Employment Status</strong></div>
                                                <div class="info-value" id="employmentStatus">-</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4 mb-3">
                                        <div class="info-card">
                                            <div class="info-icon bg-hiring-status">
                                                <i class="fa fa-check-circle"></i>
                                            </div>
                                            <div>
                                                <div class="info-label"> <strong>Hiring Status</strong></div>
                                                <div class="info-value" id="hiringStatus">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <!-- Training History -->
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="fa fa-graduation-cap text-dark mr-2"></i>
                                    Training History
                                </h5>
                            </div>

                            <div class="card-body">
                                <div class="d-flex align-items-end justify-content-between">
                                    <!-- Tabs -->
                                    <ul class="nav nav-tabs" id="trainingTabs" role="tablist">
                                        <li class="nav-item">
                                            <button class="nav-link active"
                                                id="trdsSummary-tab"
                                                data-toggle="tab"
                                                data-target="#trdsSummary"
                                                type="button">
                                                TRDS Summary
                                            </button>
                                        </li>

                                        <li class="nav-item">
                                            <button class="nav-link"
                                                id="employeeTrainingRecord-tab"
                                                data-toggle="tab"
                                                data-target="#employeeTrainingRecord"
                                                type="button">
                                                Employee Training Record ( OLD FILES )
                                            </button>
                                        </li>
                                    </ul>

                                    <!-- Export Button -->
                                    <a
                                        href="#"
                                        id="btnExportTraining"
                                        class="btn btn-dark d-none"
                                        target="_blank">
                                    </a>
                                </div>

                                <!-- Tab Content -->
                                <div class="tab-content mt-3">
                                    <!-- Tab 1 -->
                                    <div class="tab-pane fade show active" id="trdsSummary">
                                        <div class="table-responsive">
                                            <table id="tableTrdsSummary"
                                                class="table table-hover table-striped w-100 mb-0">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Date</th>
                                                        <th>Training Title /<br> Reason for Certification</th>
                                                        <th>Series Name / Family</th>
                                                        <th>Station</th>
                                                        <th>Detailed <br>Station</th>
                                                        <th>Objective</th>
                                                        <th>Trainor</th>
                                                        <th>Result</th>
                                                        <th>Venue</th>
                                                        <th>Type of <br>Training</th>
                                                        <th>Remarks</th>
                                                        <th>Attachment</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Tab 2 -->
                                    <div class="tab-pane fade" id="employeeTrainingRecord">
                                        <div class="table-responsive">
                                            <table id="tableEmployeeTrainingRecord"
                                                class="table table-hover table-striped w-100 mb-0">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Date</th>
                                                        <th>Title</th>
                                                        <th>Objective</th>
                                                        <th>Trainor</th>
                                                        <th>Results</th>
                                                        <th>Venue</th>
                                                        <th>Mechanics</th>
                                                        <th>Type of Training</th>
                                                        <th>Remark</th>
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

            <!-- Export Report Modal -->
            {{-- <div class="modal fade" id="chooseExportReportId" data-backdrop="static" tabindex="-1" role="dialog">
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
                                <button class="btn btn-secondary text-light w-100 py-3 fs-5 d-flex align-items-center justify-content-center gap-2" data-toggle="modal" title="Click this to generate excel file." data-target="#modalGenerateSkillMatrixPerProductLine">
                                    <i class="fas fa-eye fa-lg mr-2" aria-hidden="true"></i> <span>Employee Skill Matrix</span>
                                </button>
                            </div>

                            <div class="col-md-12 mb-3">
                                <button class="btn btn-warning text-light w-100 py-3 fs-5 d-flex align-items-center justify-content-center gap-2" data-toggle="modal" title="Click this to generate excel file." data-target="#modalGenerateSkillMatrixDetails">
                                    <i class="fas fa-eye fa-lg mr-2" aria-hidden="true"></i> <span>Employee Skill Map</span>
                                </button>
                            </div>

                            <div class="col-md-12 mb-3">
                                <button class="btn btn-warning text-light w-100 py-3 fs-5 d-flex align-items-center justify-content-center gap-2" data-toggle="modal" title="Click this to generate excel file." data-target="#modalGenerateSkillCardDetails">
                                    <i class="fas fa-eye fa-lg mr-2" aria-hidden="true"></i> <span>Employee Skill Card</span>
                                </button>
                            </div>

                            <div class="col-md-12 mb-3">
                                <button class="btn btn-warning text-light w-100 py-3 fs-5 d-flex align-items-center justify-content-center gap-2" data-toggle="modal" title="Click this to generate excel file." data-target="#modalGenerateInspectorSkillChartDetails">
                                    <i class="fas fa-eye fa-lg mr-2" aria-hidden="true"></i> <span>Inspector Skill Chart</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div> --}}

            <div class="modal fade" id="chooseExportReportId" data-backdrop="static" tabindex="-1" role="dialog"
                aria-labelledby="chooseExportReportTitle" aria-hidden="true">

                <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">

                        <!-- Header -->
                        <div class="modal-header text-white border-0"
                            style="background: linear-gradient(135deg, #173b67, #2878b5); padding: 22px 24px;">

                            <div class="d-flex align-items-center">
                                <div class="mr-3 d-flex align-items-center justify-content-center"
                                    style="width: 46px; height: 46px; background: rgba(255,255,255,.15); border-radius: 12px;">
                                    <i class="fas fa-file-export fa-lg"></i>
                                </div>

                                <div>
                                    <h5 class="modal-title font-weight-bold mb-1" id="chooseExportReportTitle">
                                        Export Reports
                                    </h5>
                                    <small style="opacity: .85;">
                                        Choose the report you want to generate
                                    </small>
                                </div>
                            </div>

                            <button type="button" class="close text-white ml-3" data-dismiss="modal" aria-label="Close"
                                style="text-shadow: none; opacity: .9;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body p-4" style="background: #f7f9fc;">

                            <!-- 3-Column Section -->
                            <div class="row">
                                <!-- Column 1 -->
                                <div class="col-md-4 mb-3">
                                    <a href="{{ asset('public/manual/List of Certified Personnel_CN201P_October 2026.xlsx') }}" download
                                    class="btn btn-light border w-100 text-left p-3 shadow-sm h-100"
                                    title="List of Certified Personnel for CN201P"
                                    style="border-radius: 10px; white-space: normal;">

                                        <div class="d-flex align-items-center h-100">
                                            <div class="d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                                                style="width: 48px; height: 48px; background: #e4efff; border-radius: 12px;">
                                                <i class="fas fa-id-card fa-lg text-primary"></i>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="font-weight-bold text-dark mb-1" style="font-size: 0.9rem;">
                                                    List of Certified Personnel for CN201P
                                                </div>
                                                <small class="text-muted d-block" style="font-size: 0.78rem;">
                                                    Generate Certified List.
                                                </small>
                                            </div>

                                            <i class="fas fa-download text-muted ml-2"></i>
                                        </div>
                                    </a>
                                </div>

                                <!-- Column 2 (CN201S) -->
                                <div class="col-md-4 mb-3">
                                    <a href="{{ asset('public/manual/List of Certified Personnel_CN201S_October 2026.xlsx') }}" download
                                        class="btn btn-light border w-100 text-left p-3 shadow-sm h-100"
                                        title="List of Certified Personnel for CN201S"
                                        style="border-radius: 10px; white-space: normal; text-decoration: none;">

                                        <div class="d-flex align-items-center h-100">
                                            <div class="d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                                                style="width: 48px; height: 48px; background: #e4efff; border-radius: 12px;">
                                                <i class="fas fa-id-card fa-lg text-primary"></i>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="font-weight-bold text-dark mb-1" style="font-size: 0.9rem;">
                                                    List of Certified Personnel for CN201S
                                                </div>
                                                <small class="text-muted d-block" style="font-size: 0.78rem;">
                                                    Generate Certified List.
                                                </small>
                                            </div>

                                            <i class="fas fa-download text-muted ml-2"></i>
                                        </div>
                                    </a>
                                </div>

                                <!-- Column 3 (CN109S) -->
                                <div class="col-md-4 mb-3">
                                    <a href="{{ asset('public/manual/CN List of Certified Personnel for CN109S.xls') }}" download
                                        class="btn btn-light border w-100 text-left p-3 shadow-sm h-100"
                                        title="List of Certified Personnel for CN109S"
                                        style="border-radius: 10px; white-space: normal; text-decoration: none;">

                                        <div class="d-flex align-items-center h-100">
                                            <div class="d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                                                style="width: 48px; height: 48px; background: #e4efff; border-radius: 12px;">
                                                <i class="fas fa-id-card fa-lg text-primary"></i>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="font-weight-bold text-dark mb-1" style="font-size: 0.9rem;">
                                                    List of Certified Personnel for CN109S
                                                </div>
                                                <small class="text-muted d-block" style="font-size: 0.78rem;">
                                                    Generate Certified List.
                                                </small>
                                            </div>

                                            <i class="fas fa-download text-muted ml-2"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Employee Skill Matrix (Full Width) -->
                            <button type="button" class="btn btn-light border w-100 text-left p-3 mb-3 shadow-sm"
                                data-toggle="modal" data-target="#modalGenerateSkillMatrixPerProductLine"
                                title="Generate the Employee Skill Matrix"
                                style="border-radius: 10px; white-space: normal;">

                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                                        style="width: 54px; height: 54px; background: #fff0d9; border-radius: 12px;">
                                        <i class="fas fa-chart-line fa-lg" style="color: #19643b;"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold text-dark mb-1">
                                            Employee Skill Matrix
                                        </div>
                                        <small class="text-muted">
                                            Generate an employee skill matrix with optional product-line filtering.
                                        </small>
                                    </div>

                                    <i class="fas fa-chevron-right text-muted ml-2"></i>
                                </div>
                            </button>

                            <!-- Employee Skill Map (Full Width) -->
                            <button type="button" class="btn btn-light border w-100 text-left p-3 mb-3 shadow-sm"
                                data-toggle="modal" data-target="#modalGenerateSkillMatrixDetails"
                                title="Generate the Employee Skill Map"
                                style="border-radius: 10px; white-space: normal;">

                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                                        style="width: 54px; height: 54px; background: #fff0d9; border-radius: 12px;">
                                        <i class="fas fa-chart-pie fa-lg" style="color: #7e1e33;"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold text-dark mb-1">
                                            Employee Skill Map
                                        </div>
                                        <small class="text-muted">
                                            Generate a process-based skill map by product line, position, and employee.
                                        </small>
                                    </div>

                                    <i class="fas fa-chevron-right text-muted ml-2"></i>
                                </div>
                            </button>

                            <!-- Employee Skill Card (Full Width) -->
                            <button type="button" class="btn btn-light border w-100 text-left p-3 shadow-sm"
                                data-toggle="modal" data-target="#modalGenerateSkillCardDetails"
                                title="Generate the Employee Skill Card"
                                style="border-radius: 10px; white-space: normal;">

                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                                        style="width: 54px; height: 54px; background: #fff0d9; border-radius: 12px;">
                                        <i class="fas fa-project-diagram fa-lg" style="color: #d98200;"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold text-dark mb-1">
                                            Employee Skill Card
                                        </div>
                                        <small class="text-muted">
                                            Generate a process-based skill card by employee.
                                        </small>
                                    </div>

                                    <i class="fas fa-chevron-right text-muted ml-2"></i>
                                </div>
                            </button>

                            <!-- Employee Skill Chart (Full Width) -->
                            <button type="button" class="btn btn-light border w-100 text-left p-3 shadow-sm"
                                data-toggle="modal" data-target="#modalGenerateInspectorSkillChartDetails"
                                title="Generate the Employee Skill Chart"
                                style="border-radius: 10px; white-space: normal;">

                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                                        style="width: 54px; height: 54px; background: #fff0d9; border-radius: 12px;">
                                        <i class="fas fa-chart-bar fa-lg" style="color: #d98200;"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold text-dark mb-1">
                                            Employee Skill Chart
                                        </div>
                                        <small class="text-muted">
                                            Generate a skill chart by Section.
                                        </small>
                                    </div>

                                    <i class="fas fa-chevron-right text-muted ml-2"></i>
                                </div>
                            </button>

                            <!-- Footer Note -->
                            <div class="mt-4 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Select a report to proceed to its export settings.
                                </small>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- Export Report Modal -->
            {{-- <div class="modal fade" id="modalGenerateSkillMatrixDetails" tabindex="-1" role="dialog" data-backdrop="static">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <!-- Header -->
                        <div class="modal-header">
                            <h4 class="modal-title font-weight-bold">
                                Skill Map (Per Process)
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
                                        <label for="selectPosition" class="form-label me-2">Position</label>
                                        <div id="selectedPosition" class="fs-5 mb-1"></div>
                                        <select name="position" id="selectPosition" class="form-control select2bs5" required>
                                        </select>
                                    </div>

                                    <div class="mb-4 col-sm-12">
                                        <label for="selectEmployee" class="form-label me-2">Employee</label>
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
            </div> --}}
<div class="modal fade" id="modalGenerateSkillMatrixDetails"
     tabindex="-1" role="dialog"
     aria-labelledby="skillMatrixDetailsTitle"
     aria-hidden="true" data-backdrop="static">

    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg"
             style="border-radius: 14px; overflow: hidden;">

            <!-- Header -->
            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg, #173b67, #2878b5); padding: 22px 26px;">

                <div class="d-flex align-items-center">
                    <div class="mr-3 d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px; background: rgba(255,255,255,.15); border-radius: 12px;">
                        <i class="fas fa-project-diagram fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="modal-title font-weight-bold mb-1"
                            id="skillMatrixDetailsTitle">
                            Skill Map (Per Process)
                        </h5>

                        <small style="opacity: .85;">
                            Generate a skill matrix based on process assignments
                        </small>
                    </div>
                </div>

                <button type="button"
                        class="close text-white ml-3"
                        data-dismiss="modal"
                        aria-label="Close"
                        style="text-shadow: none; opacity: .9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4" style="background: #f7f9fc;">

                <!-- Section Heading -->
                <div class="mb-4">
                    <h6 class="font-weight-bold text-dark mb-1">
                        <i class="fas fa-sliders-h text-primary mr-2"></i>
                        Matrix Configuration
                    </h6>

                    <small class="text-muted">
                        Select a product line, position, and employees to include.
                    </small>
                </div>

                <!-- Selection Card -->
                <div class="card border-0 shadow-sm"
                     style="border-radius: 10px;">

                    <div class="card-body p-4">

                        <!-- Product Line -->
                        <div class="form-group mb-4">

                            <label for="selectedProductLine"
                                   class="font-weight-bold text-dark">
                                <i class="fas fa-industry text-muted mr-1"></i>
                                Product Line
                                <span class="text-danger">*</span>
                            </label>

                            <div id="selectedProdLine"
                                 class="fs-5 mb-1"></div>

                            <select name="product_line"
                                    id="selectedProductLine"
                                    class="form-control select2bs5"
                                    required>
                            </select>

                            <small class="form-text text-muted">
                                Choose the product line for the skill matrix.
                            </small>
                        </div>

                        <hr class="my-4">

                        <!-- Position -->
                        <div class="form-group mb-4">

                            <label for="selectPosition"
                                   class="font-weight-bold text-dark">
                                <i class="fas fa-user-tie text-muted mr-1"></i>
                                Position
                                <span class="text-danger">*</span>
                            </label>

                            <div id="selectedPosition"
                                 class="fs-5 mb-1"></div>

                            <select name="position"
                                    id="selectPosition"
                                    class="form-control select2bs5"
                                    required>
                            </select>

                            <small class="form-text text-muted">
                                Select the employee position or category.
                            </small>
                        </div>

                        <hr class="my-4">

                        <!-- Employee -->
                        <div class="form-group mb-0">

                            <label for="selectedEmployees"
                                   class="font-weight-bold text-dark">
                                <i class="fas fa-users text-muted mr-1"></i>
                                Employees
                                <span class="text-danger">*</span>
                            </label>

                            <div id="selectedEmployee"
                                 class="fs-5 mb-1"></div>

                            <select name="employee[]"
                                    id="selectedEmployees"
                                    class="form-control select2bs5"
                                    multiple required>
                            </select>

                            <small class="form-text text-muted">
                                Select one or more employees to include in the export.
                            </small>
                        </div>

                    </div>
                </div>

                <!-- Helpful Note -->
                <div class="mt-3 px-3 py-2"
                     style="background: #eaf3ff; border-left: 4px solid #2878b5; border-radius: 5px;">

                    <small class="text-dark">
                        <i class="fas fa-info-circle text-primary mr-1"></i>
                        <strong>Note:</strong>
                        The employee list can be filtered by the selected product line
                        and position.
                    </small>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer bg-white border-top px-4 py-3">

                <button type="button"
                        class="btn btn-primary font-weight-bold px-4 btnGenerateVisualMatrix"
                        id="btnGenerateVisualMatrix"
                        style="border-radius: 7px;">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Skill Map
                </button>

                <button type="button"
                        class="btn btn-light border px-4"
                        data-dismiss="modal"
                        style="border-radius: 7px;">
                    Cancel
                </button>

            </div>

        </div>
    </div>
</div>


            <!-- Export Report Modal -->
            {{-- <div class="modal fade" id="modalGenerateSkillMatrixPerProductLine" tabindex="-1" role="dialog" data-backdrop="static">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <h4 class="modal-title font-weight-bold">
                                Skill Map Matrix
                            </h4>

                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                                <div class="row">
                                     <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="toggleSkillMatrixPerProductLine" name="export_per_product_line" value="1">
                                        <label class="custom-control-label" for="toggleSkillMatrixPerProductLine" id="lblSkillMatrixExportMode">
                                            Per Employee Only
                                        </label>
                                    </div>

                                    <div class="mb-4 col-sm-12 productlineClass" style="display: none;">
                                        <label for="selectedPerProductLine" class="form-label me-2">Product Line</label>
                                        <div id="selectedPerProdLine" class="fs-5 mb-1"></div>
                                        <select name="per_product_line" id="selectedPerProductLine" class="form-control select2bs5" multiple required></select>
                                    </div>

                                    <div class="mb-4 col-sm-12">
                                        <label for="selectedEmployeePerProductLine" class="form-label me-2">Employee</label>
                                        <div id="selectEmployeePerProductLine" class="fs-5 mb-1"></div>
                                        <select name="employee[]" id="selectedEmployeePerProductLine" class="form-control select2bs5" multiple required></select>
                                    </div>


                                </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btnGenerateSkillMatrixPerProductLine" id="btnGenerateSkillMatrixPerProductLine">Export</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div> --}}
            <div class="modal fade" id="modalGenerateSkillMatrixPerProductLine"
     tabindex="-1" role="dialog" aria-labelledby="skillMatrixModalTitle"
     aria-hidden="true" data-backdrop="static">

    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">

            <!-- Header -->
            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg, #173b67, #2878b5); padding: 22px 26px;">

                <div class="d-flex align-items-center">
                    <div class="mr-3 d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px; background: rgba(255,255,255,.15); border-radius: 12px;">
                        <i class="fas fa-download"></i>
                    </div>

                    <div>
                        <h5 class="modal-title font-weight-bold mb-1"
                            id="skillMatrixModalTitle">
                            Skill Matrix
                        </h5>
                    </div>
                </div>

                <button type="button" class="close text-white ml-3"
                        data-dismiss="modal" aria-label="Close"
                        style="text-shadow: none; opacity: .9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4" style="background: #f7f9fc;">

                <!-- Export Mode -->
                <div class="card border-0 shadow-sm mb-4"
                     style="border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">

                            <div class="d-flex align-items-center mb-2 mb-sm-0">
                                <div class="mr-3 d-flex align-items-center justify-content-center"
                                     style="width: 42px; height: 42px; background: #e8f1fb; border-radius: 10px;">
                                    <i class="fas fa-sliders-h text-primary"></i>
                                </div>

                                <div>
                                    <div class="font-weight-bold text-dark">
                                        Export Mode
                                    </div>
                                    <small class="text-muted">
                                        Choose how export is handled
                                    </small>
                                </div>
                            </div>

                            <div class="custom-control custom-switch ml-2">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="toggleSkillMatrixPerProductLine"
                                       name="export_per_product_line"
                                       value="1">

                                <label class="custom-control-label font-weight-bold"
                                       for="toggleSkillMatrixPerProductLine"
                                       id="lblSkillMatrixExportMode">
                                    Per Employee Only
                                </label>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Selection Section -->
                <div class="mb-3">
                    <h6 class="font-weight-bold text-dark mb-1">
                        <i class="fas fa-users text-primary mr-2"></i>
                        Employee Selection
                    </h6>
                    {{-- <small class="text-muted">
                        Select the applicable product line and employees.
                    </small> --}}
                </div>

                <div class="card border-0 shadow-sm"
                     style="border-radius: 10px;">
                    <div class="card-body p-4">

                        <!-- Product Line -->
                        <div class="form-group productlineClass"
                             style="display: none;">

                            <label for="selectedPerProductLine"
                                   class="font-weight-bold text-dark">
                                <i class="fas fa-industry text-muted mr-1"></i>
                                Product Line
                            </label>

                            <select name="per_product_line"
                                    id="selectedPerProductLine"
                                    class="form-control select2bs5"
                                    multiple required>
                            </select>

                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Select one or more product lines to filter employees.
                            </small>
                        </div>

                        <!-- Employee -->
                        <div class="form-group mb-0">

                            <label for="selectedEmployeePerProductLine"
                                   class="font-weight-bold text-dark">
                                <i class="fas fa-user-check text-muted mr-1"></i>
                                Employee
                            </label>

                            <select name="employee[]"
                                    id="selectedEmployeePerProductLine"
                                    class="form-control select2bs5"
                                    multiple required>
                            </select>

                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Select the employees to include in the export.
                            </small>
                        </div>

                    </div>
                </div>

                <!-- Info -->
                <div class="mt-3 px-3 py-2"
                     style="background: #eaf3ff; border-left: 4px solid #2878b5; border-radius: 5px;">
                    <small class="text-dark">
                        <i class="fas fa-lightbulb text-primary mr-1"></i>
                        <strong>Tip:</strong>
                        Turn on <em>Per Product Line + Employee</em> to filter
                        the employee list by product line.
                    </small>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer bg-white border-top px-4 py-3">

                <button type="button"
                        class="btn btn-primary font-weight-bold px-4 btnGenerateSkillMatrixPerProductLine"
                        id="btnGenerateSkillMatrixPerProductLine"
                        style="border-radius: 7px;">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Skill Matrix
                </button>

                <button type="button"
                        class="btn btn-light border px-4"
                        data-dismiss="modal"
                        style="border-radius: 7px;">
                    Cancel
                </button>

            </div>

        </div>
    </div>
</div>

    <div class="modal fade" id="modalGenerateSkillCardDetails"
        tabindex="-1" role="dialog"
        aria-labelledby="skillCardDetailsTitle"
        aria-hidden="true" data-backdrop="static">

        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius: 14px; overflow: hidden;">

                <!-- Header -->
                <div class="modal-header text-white border-0"
                    style="background: linear-gradient(135deg, #173b67, #2878b5); padding: 22px 26px;">

                    <div class="d-flex align-items-center">
                        <div class="mr-3 d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background: rgba(255,255,255,.15); border-radius: 12px;">
                            <i class="fas fa-project-diagram fa-lg"></i>
                        </div>

                        <div>
                            <h5 class="modal-title font-weight-bold mb-1"
                                id="skillCardDetailsTitle">
                                Skill Card
                            </h5>

                            <small style="opacity: .85;">
                                Generate a skill card based on process assignments
                            </small>
                        </div>
                    </div>

                    <button type="button"
                            class="close text-white ml-3"
                            data-dismiss="modal"
                            aria-label="Close"
                            style="text-shadow: none; opacity: .9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4" style="background: #f7f9fc;">

                    <!-- Section Heading -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-sliders-h text-primary mr-2"></i>
                            Matrix Configuration
                        </h6>

                        <small class="text-muted">
                            Select a product line, position, and employees to include.
                        </small>
                    </div>

                    <!-- Selection Card -->
                    <div class="card border-0 shadow-sm"
                        style="border-radius: 10px;">

                        <div class="card-body p-4">

                            <!-- Employee -->
                            <div class="form-group mb-0">

                                <label for="selectedEmployees"
                                    class="font-weight-bold text-dark">
                                    <i class="fas fa-users text-muted mr-1"></i>
                                    Employees
                                    <span class="text-danger">*</span>
                                </label>

                                <div id="selectedEmployee"
                                    class="fs-5 mb-1"></div>

                                <select name="skill_card_employee" id="skillCardEmployee" class="form-control select2bs5 selectEmpNoSkillCard" required></select>

                                <small class="form-text text-muted">
                                    Select employee to include in the export.
                                </small>
                            </div>

                        </div>
                    </div>

                    <!-- Helpful Note -->
                    <div class="mt-3 px-3 py-2"
                        style="background: #eaf3ff; border-left: 4px solid #2878b5; border-radius: 5px;">

                        <small class="text-dark">
                            <i class="fas fa-info-circle text-primary mr-1"></i>
                            <strong>Note:</strong>
                            The employee list can be filtered by the selected product line
                            and position.
                        </small>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer bg-white border-top px-4 py-3">

                    <button type="button"
                            class="btn btn-primary font-weight-bold px-4 btnGenerateSkillCard"
                            id="btnGenerateSkillCard"
                            style="border-radius: 7px;">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Skill Card
                    </button>

                    <button type="button"
                            class="btn btn-light border px-4"
                            data-dismiss="modal"
                            style="border-radius: 7px;">
                        Cancel
                    </button>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalGenerateInspectorSkillChartDetails"
        tabindex="-1" role="dialog"
        aria-labelledby="skillChartDetailsTitle"
        aria-hidden="true" data-backdrop="static">

        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius: 14px; overflow: hidden;">

                <!-- Header -->
                <div class="modal-header text-white border-0"
                    style="background: linear-gradient(135deg, #173b67, #2878b5); padding: 22px 26px;">

                    <div class="d-flex align-items-center">
                        <div class="mr-3 d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background: rgba(255,255,255,.15); border-radius: 12px;">
                            <i class="fas fa-project-diagram fa-lg"></i>
                        </div>

                        <div>
                            <h5 class="modal-title font-weight-bold mb-1"
                                id="skillChartDetailsTitle">
                                Skill Chart
                            </h5>

                            <small style="opacity: .85;">
                                Generate a skill chart based on process assignments
                            </small>
                        </div>
                    </div>

                    <button type="button"
                            class="close text-white ml-3"
                            data-dismiss="modal"
                            aria-label="Close"
                            style="text-shadow: none; opacity: .9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4" style="background: #f7f9fc;">

                    <!-- Section Heading -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-sliders-h text-primary mr-2"></i>
                            Matrix Configuration
                        </h6>

                        <small class="text-muted">
                            Select one or more sections to include.
                        </small>
                    </div>

                    <!-- Selection Card -->
                    <div class="card border-0 shadow-sm"
                        style="border-radius: 10px;">

                        <div class="card-body p-4">

                            <div class="form-group mb-0">
                                <label>Section/s</label>
                                <select class="form-control select2bs5" name="section_export[]" id="selectSectionToExport" multiple required>
                                    <option value="" disabled selected>Select Section/s</option>
                                    <option value="TSF1">TS-F1</option>
                                    <option value="TSF3">TS-F3</option>
                                    <option value="CN">CN</option>
                                    <option value="CNF3">CN-F3</option>
                                    <option value="PPDCN">PPD-CN</option>
                                    <option value="PPDTS">PPD-TS</option>
                                    <option value="PPDF3">PPD-F3</option>
                                    <option value="YF">YF</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- Helpful Note -->
                    <div class="mt-3 px-3 py-2"
                        style="background: #eaf3ff; border-left: 4px solid #2878b5; border-radius: 5px;">

                        <small class="text-dark">
                            <i class="fas fa-info-circle text-primary mr-1"></i>
                            <strong>Note:</strong>
                            Section can be one or multiple selections.
                        </small>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer bg-white border-top px-4 py-3">

                    <button type="button"
                            class="btn btn-primary font-weight-bold px-4 btnGenerateSkillChart"
                            id="btnGenerateSkillChart"
                            style="border-radius: 7px;">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Skill Chart
                    </button>

                    <button type="button"
                            class="btn btn-light border px-4"
                            data-dismiss="modal"
                            style="border-radius: 7px;">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
    </div>

    </section>
</div>
@endsection

@section('js_content')
    <script type="text/javascript">
        let dataEmployeeTrainingRecord
        let dataTRDSSummary
        let getEmployeeTrainingRecordId = '';
        let getEmployeeNoForTrdsSummary = '';

        $(document).ready(function () {

            UpdateExportButton();

            $('.get-employee-info').select2({
                theme: 'bootstrap-5',
                placeholder: 'Search Employee',
                minimumInputLength: 3,
                ajax: {
                    transport: function (params, success, failure) {
                        GetEmployeeDetails(
                            $('.get-employee-info'),
                            params.data.term,
                            success,
                            failure
                        );
                    },

                    processResults: function (response) {
                        return {
                            results: response
                        };
                    }
                }
            });

            $('.get-employee-info').on('select2:select', function (e) {
                const data = e.params.data;
                const hiringStatus = data.hiringStatus;
                console.log('data:',data);
                console.log('hiringStatus:',hiringStatus);
                console.log('employeeNo:',data.employeeNo);

                $('#displayEmployeeName').text(data.text);
                $('#displayPosition').text(data.position);
                $('#department').text(data.department);
                $('#section').text(data.section);
                $('#division').text(data.division);
                $('#dateHired').text(data.dateHired);
                $('#employmentStatus').text(data.employmentStatus);

                $('#btnExportTraining').attr('data-employee-id', data.id).attr('data-employee-no', data.employeeNo).removeClass('d-none');
                UpdateExportButton();

                if(hiringStatus === 'Contractual'){
                    $('#hiringStatus').text('Contractual');
                } else if(hiringStatus === 'Probationary'){
                    $('#hiringStatus').text('Probationary');
                }else{
                    switch(hiringStatus){
                        case '1':
                            $('#hiringStatus').text('Contractual');
                            break;
                        case '2':
                            $('#hiringStatus').text('Probationary');
                            break;
                        default:
                            $('#hiringStatus').text('Regular');
                    }
                }

                getEmployeeTrainingRecordId = data.id
                getEmployeeNoForTrdsSummary = data.employeeNo;
                dataEmployeeTrainingRecord.draw();
                dataTRDSSummary.draw();
            });

            dataEmployeeTrainingRecord = $("#tableEmployeeTrainingRecord").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[0, "desc"]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ Employee Training Record",
                    "lengthMenu": "Show _MENU_ Employee Training Record",
                },
                "ajax" : {
                    url: "view_employee_training_record",
                    data: function (d) {
                        d.getEmployeeTrainingRecordId = getEmployeeTrainingRecordId;
                    }
                },
                "columns": [
                    { data: "date", defaultContent: "-" },
                    { data: "employee_training_record_info.Title", defaultContent: "-" },
                    { data: "employee_training_record_info.Objective", defaultContent: "-" },
                    { data: "employee_training_record_info.Trainor", defaultContent: "-" },
                    { data: "Result" },
                    { data: "employee_training_record_info.Venue", defaultContent: "-" },
                    { data: "employee_training_record_info.Mechanics", defaultContent: "-" },
                    { data: "employee_training_record_info.TypeTraining", defaultContent: "-" },
                    { data: "Remarks" }
                ]
            });

            dataTRDSSummary = $("#tableTrdsSummary").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[0, "desc"]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ TRDS Summary",
                    "lengthMenu": "Show _MENU_ TRDS Summary",
                },
                "ajax" : {
                    url: "view_trds_summary",
                    data: function (d) {
                        d.getEmployeeNoForTrdsSummary = getEmployeeNoForTrdsSummary;
                    }
                },
                "columns": [
                    { data: 'trainingDate'},
                    { data: 'title'},
                    { data: 'seriesName'},
                    { data: 'station'},
                    { data: 'detailedStation'},
                    { data: 'objective' },
                    { data: 'trainor' },
                    { data: 'result' },
                    { data: 'trainingVenue' },
                    { data: 'typeOfTraining' },
                    { data: 'training_remarks' },
                    { data: 'attachment' },
                ]
            });

            $('#trainingTabs button[data-toggle="tab"]').on('shown.bs.tab', function () {
                UpdateExportButton();
            });

            // $('#btnExportTraining').on('click', function () {
            //     const activeTab = $('#trainingTabs .nav-link.active').attr('id');
            //     const employeeNo = $('#btnExportTraining').attr('data-employee-no');
            //     if (activeTab === 'trdsSummary-tab') {
            //         console.log('Export TRDS Summary');

            //     }
            //     else if (activeTab === 'employeeTrainingRecord-tab') {
            //         console.log('Export ETR');

            //     }
            // });

            $('#modalGenerateSkillCardDetails').on('shown.bs.modal', (event) => {
                console.log('Modal is open');
                // event.relatedTarget is the button that triggered it
                selectEmpDetailsFromCertification($('.selectEmpNoSkillCard'));
            });

            //Employee Skill Card
            $('#btnGenerateSkillCard').on('click', function (){
                const params = new URLSearchParams({
                    employee_id: $('#skillCardEmployee').val()
                });

                window.open(`export_employee_skill_card?${params.toString()}`, '_blank');
            });

            //Employee Skill Chart
            $('#btnGenerateSkillChart').on('click', function (){
                const sections = $('#selectSectionToExport').val();

                if (!sections || sections.length === 0) {
                    alert('Please select at least one section.');
                    return;
                }

                const params = new URLSearchParams();

                sections.forEach(function (section) {
                    params.append('section_export[]', section);
                });

                window.open(`export_inspector_skill_chart?${params.toString()}`, '_blank');
            });

        });
    </script>
@endsection
