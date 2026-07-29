@php $layout = 'layouts.super_user_layout'; @endphp
@extends($layout)
@section('title', 'Start Exam')

@section('content_page')

    <style>
        table.table thead th{
            text-align: center;
            vertical-align: middle;
        }
        table.table tbody td{
            vertical-align: middle;
        }
        .exam-scroll-container {
            max-height: 80vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        .exam-scroll-container::-webkit-scrollbar {
            width: 8px;
        }

        .exam-scroll-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }

        .exam-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Theoretical Exam</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('blank') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Theoretical Exam</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" style="margin-top: 8px;"><strong>Exam Result</strong></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height: 80vh; overflow-y: auto;">
                                    <table id="tableExamResult" class="table table-bordered table-hover w-100">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Category</th>
                                                <th>Exam Title</th>
                                                <th>Description</th>
                                                <th>Exam Instruction</th>
                                                <th>Purpose</th>
                                                <th>Department</th>
                                                <th>Position</th>
                                                <th>Product Line</th>
                                                <th>Passing Score</th>
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
    </div>

    <!-- Exam Result Modal Start -->
    <div class="modal fade" id="modalExamResult" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl-custom modal-dialog-centered" role="document">
            <div class="modal-content shadow">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-question-circle"></i> Exam Result
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <ul class="nav nav-tabs" id="examTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active tab-filter" data-status="0" data-questionnaire_id="" data-questionnaire_revision="" href="#">
                                For Review
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tab-filter" data-status="1" data-questionnaire_id="" data-questionnaire_revision="" href="#">
                                Completed
                            </a>
                        </li>
                    </ul>

                    <div class="mt-3">
                        <div class="table-responsive" style="max-height: 80vh; overflow-y: auto;">
                            <table class="table table-bordered table-striped w-100" id="tableExamResultDetails">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Exam Take #</th>
                                        <th>Date of Exam Take</th>
                                        <th>Employee Number</th>
                                        <th>Name</th>
                                        <th>Score</th>
                                        <th>Total Points</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Exam Result Modal End -->

    <!-- Exam Result Modal Start -->
    <div class="modal fade" id="modalEmployeeExamResult" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl-custom modal-dialog-centered" role="document">
            <div class="modal-content shadow">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-question-circle"></i> Exam Result
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        <div id="stepOne">
                            <div class="container-fluid">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Training Request Ctrl No.: </label>
                                                    <input type="text" class="form-control" name="exam_training_request_ctrl_no" id="txtExamTrainingRequestCtrlNo" required>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Employee No.: </label>
                                                    <input type="text" class="form-control" name="exam_training_request_employee_no" id="txtExamTrainingRequestEmployeeNo" required disabled>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Name: </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="exam_training_request_name" id="txtExamTrainingRequestName" required readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right Column -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Date Hired: </label>
                                                    <input type="text" class="form-control" name="exam_training_request_date_hired" id="txtExamTrainingRequestDateHired" required readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Date Examination: </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="exam_training_request_date_examination" id="txtExamTrainingRequestDateExamination" required readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Examination Take: </label>
                                                    <input type="text" class="form-control" name="exam_training_request_examination_take" id="txtExamTrainingRequestExaminationTake" required readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-dark w-100" id="btnNext">
                                            <i class="fas fa-arrow-right"></i> Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-none" id="stepTwo">
                            <div class="exam-scroll-container">
                                <input type="text" class="w-100" name="exam_result_details_id" id="examResultDetailsId" readonly>
                            </div>

                            <div class="mt-3 d-flex justify-content-between">
                                <button type="button" id="btnPrev" class="btn btn-secondary">Previous</button>
                                {{-- <button type="submit" class="btn btn-success">Submit Exam</button> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Exam Result Modal End -->


    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="" id="modalImage" style="width: 100%; max-height: 80vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    <!-- Change Examination Date Modal -->
    <div class="modal fade"id="modalChangeExaminationDate" tabindex="-1" role="dialog" aria-labelledby="changeExaminationDateLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content shadow">

                <!-- Modal Header -->
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="changeExaminationDateLabel">
                        <i class="fa fa-calendar-alt mr-2"></i>
                        Change Examination Date
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form method="post" id="formChangeExaminationDate" enctype="multipart/form-data">
                    @csrf
                    <!-- Modal Body -->
                    <div class="modal-body">
                        <input type="hidden" name="examination_result_detail_id" id="txtExaminationResultDetailId">

                        <div class="form-group">
                            <label for="lblExaminationDate" class="font-weight-bold">
                                Examination Date
                            </label>

                            <input type="date"
                                class="form-control"
                                id="examinationDate"
                                name="examination_date"
                                placeholder="Enter Examination Date"
                                autocomplete="off"
                                required>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" id="btnExaminationDate" class="btn btn-dark px-4">
                            <i id="iBtnExaminationDateIcon" class="fa fa-check mr-1"></i>
                            Save Date
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Exam Result Status Modal End -->
    <div class="modal fade" id="modalChangeExamResultStatus">
        <div class="modal-dialog">
            <div class="modal-content modal-md">
                <div class="modal-header">
                    <h4 class="modal-title" id="h4ChangeExamResultStatusTitle"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formChangeExamResultStatus">
                    @csrf
                    <div class="modal-body">
                    <label id="lblChangeExamResultStatusLabel"></label>
                    <input type="hidden" name="exam_result_details_id" id="txtChangeExamResultStatusId" placeholder="Exam Result Details Id">
                    <input type="hidden" name="status" id="txtChangeExamResultStatus" placeholder="Status">
                    </div>
                    <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    <button type="submit" id="btnChangeExamResultStatus" class="btn btn-dark"><i id="iBtnChangeExamResultStatusIcon" class="fa fa-check"></i> Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Change Exam Result Status Modal End -->
@endsection


@section('js_content')
    <script type="text/javascript">
        let examResultTable;
        let examResultTableDetails;
        let examResultId;
        let examResultDetailsId;
        let filters = {examStatus: 0, questionnaireId: '', questionnaireRevision: ''};

        $(document).ready(function () {
            $('.select2bs5').select2({ theme: 'bootstrap-5' });

            examResultTable = $("#tableExamResult").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[4, "asc"]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ Exam Result",
                    "lengthMenu": "Show _MENU_ Exam Result",
                },
                "ajax" : {
                    url: "view_exam_result",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "status"},
                    { "data" : "category",
                        "defaultContent": 'N/A',
                        "name": 'Category',
                        "orderable": true,
                        "searchable": true,
                        "render": function (data, type, row) {

                            switch (row.category) {
                                case 0:
                                    return "Newly Hired";
                                case 1:
                                    return "Certification";
                                case 2:
                                    return "Re-Certification";
                                default:
                                    return "Unknown";
                            }
                        },
                    },
                    { "data" : "exam_title"},
                    { "data" : "description"},
                    { "data" : "exam_instruction"},
                    { "data" : "purpose"},
                    { "data" : "department"},
                    { "data" : "position"},
                    { "data" : "product_line"},
                    { "data" : "passing_score"}
                ],
                columnDefs: [
                    {
                        targets: 0, // Action column
                        className: 'text-nowrap'
                    }
                ]
            });

            $(document).on('click', '.actionQuestionnaireDetailsForExamResult', function() {
                filters.questionnaireId = $(this).attr('questionnaire-id');
                filters.questionnaireRevision = $(this).attr('questionnaire-revision');
                filters.examStatus = 0;

                $('.tab-filter').removeClass('active');
                $('.tab-filter[data-status="0"]').addClass('active');

                $('#modalExamResult').modal('show');

                examResultTableDetails.ajax.reload();
            });

            examResultTableDetails = $('#tableExamResultDetails').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'view_exam_result_details',
                    data: function (param) {
                        param.examStatus = filters.examStatus;
                        param.questionnaireId = filters.questionnaireId;
                        param.questionnaireRevision = filters.questionnaireRevision;
                    }
                },
                columns: [
                    { data: 'action' },
                    { data: 'exam_taken' },
                    { data: 'date_examination' },
                    { data: 'employee_no'   },
                    { data: 'employee_name' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const score = Number(row.score) || 0;
                            const essayScore = Number(row.identification_essay_score) || 0;

                            return score + essayScore;
                        }
                    },
                    { data: 'remark' },
                    { data: 'rating' }
                ]
            });

            $('.tab-filter').on('click', function (e) {
                e.preventDefault();

                $('.tab-filter').removeClass('active');
                $(this).addClass('active');

                filters.examStatus = $(this).data('status');

                examResultTableDetails.ajax.reload(null, false);
            });

            $(document).on('click', '.actionEmployeeExamResult', function() {
                examResultDetailsId = $(this).attr('exam_result_details-id');
                $('#examResultDetailsId').val(examResultDetailsId);

                GetEmployeeExamResultById(examResultDetailsId);
            });

            $('#btnNext').click(function (e) {
                e.preventDefault();
                $('#stepOne').addClass('d-none');
                $('#stepTwo').removeClass('d-none');

            });

            $('#btnPrev').click(function (e) {
                e.preventDefault();
                $('#stepTwo').addClass('d-none');
                $('#stepOne').removeClass('d-none');
            });

            $(document).on('click', '.previewImage', function() {
                $('#modalImage').attr('src', $(this).attr('src'));
            });

            $(document).on('submit', '#formUpdateScoreForIdentificationEssay', function(event){
                event.preventDefault();
                UpdateExamScoreForEmployee();
            });

            $(document).on('click', '.actionChangeExaminationDate',function(e){
                e.preventDefault();

                examResultDetailsId = $(this).attr('examinationResultDetails-id');
                let examinationDate = $(this).attr('examinationResultDetails-examination_date');
                $("#txtExaminationResultDetailId").val(examResultDetailsId);
                $("#examinationDate").val(examinationDate);
            });

            $("#formChangeExaminationDate").submit(function(event){
                event.preventDefault();
                UpdateExaminationDate();
            });

            $(document).on('click', '.actionChangeExamResultStatus',function(e){
                e.preventDefault();

                let examResultDetailsStatus = $(this).attr('status');
                examResultDetailsId     = $(this).attr('examinationResultDetails-id');

                $("#txtChangeExamResultStatusId").val(examResultDetailsId);
                $("#txtChangeExamResultStatus").val(examResultDetailsStatus);

                if(examResultDetailsStatus == 0){
                    $("#lblChangeExamResultStatusLabel").text('Are you sure to activate?');
                    $("#h4ChangeExamResultStatusTitle").html('<i class="fa fa-question-circle"></i> Activate Exam Result Details');
                }else{
                    $("#lblChangeExamResultStatusLabel").text('Are you sure to delete?');
                    $("#h4ChangeExamResultStatusTitle").html('<i class="fa fa-question-circle"></i> Delete Exam Result Details');
                }
            });


            $("#formChangeExamResultStatus").submit(function(event){
                event.preventDefault();
                ChangeExaminationResultStatus();
            });
        });
    </script>
@endsection
