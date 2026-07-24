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
                        <h1>ETR</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('blank') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">ETR</li>
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
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                                <h4 class="mb-0 font-weight-bold">
                                    <i class="fa fa-users text-dark mr-2"></i>
                                    Employee Training Record (ETR)
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
                                <div class="table-responsive">
                                    <table id="EmployeeTrainingRecord"
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
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('js_content')
    <script type="text/javascript">
        let dataQuestionnaire
        let getEmployeeTrainingRecordId = '';

        $(document).ready(function () {
            // ===============================================================================================================================================
            // ================================================================ QUESTIONNAIRE ================================================================
            // ===============================================================================================================================================
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
                console.log(data);

                $('#displayEmployeeName').text(data.text);
                $('#displayPosition').text(data.position);
                $('#department').text(data.department);
                $('#section').text(data.section);
                $('#division').text(data.division);
                $('#dateHired').text(data.dateHired);
                $('#employmentStatus').text(data.employmentStatus);

                switch (data.hiringStatus) {
                    case '1':
                        $('#hiringStatus').text('Contractual');
                        break;
                    case '2':
                        $('#hiringStatus').text('Probationary');
                        break;
                    default:
                        $('#hiringStatus').text('Regular');
                }

                getEmployeeTrainingRecordId = data.id
                dataQuestionnaire.draw();
            });
            
            dataQuestionnaire = $("#EmployeeTrainingRecord").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[3, "asc"],[3, "asc"]],
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
                    { data: null,
                        render: function (data, type, row) {
                            return (row.employee_training_record_info?.PeriodFrom ?? "-") +
                                " - " +
                                (row.employee_training_record_info?.PeriodTo ?? "-");
                        }   },
                    { data: "employee_training_record_info.Title", defaultContent: "-" },
                    { data: "employee_training_record_info.Objective", defaultContent: "-" },
                    { data: "employee_training_record_info.Trainor", defaultContent: "-" },
                    { data: "Result" },
                    { data: "employee_training_record_info.Venue", defaultContent: "-" },
                    { data: "employee_training_record_info.Mechanics", defaultContent: "-" },
                    { data: "employee_training_record_info.TypeTraining", defaultContent: "-" },
                    { data: "Remarks"
                    }
                ]
            });
        });
    </script>
@endsection
