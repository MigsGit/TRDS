@php $layout = 'layouts.super_user_layout'; @endphp

@extends($layout)
@section('title', 'Theoretical Exam')

@section('content_page')
    <style type="text/css">
        table.table thead th{
            text-align: center;
            vertical-align: middle;
        }

        table.table tbody td{
            vertical-align: middle;
        }

        #tableQuestionnaire thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa; /* Light header color */
            z-index: 5;
        }

        .removeQuestion {
            position: absolute;
            top: 2px;
            left: 2px;
            padding: 0 4px;
            font-size: 0.75rem;
        }

        th.position-relative {
            position: relative;
        }

        .removeOption {
            position: absolute;
            top: 2px;
            right: 2px;
            padding: 0 4px;
            font-size: 0.75rem;
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
                                <h3 class="card-title" style="margin-top: 8px;"><strong>Questionnaire</strong></h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" class="btn btn-dark" id="buttonCreateQuestionnaire" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaire">
                                        <i class="fa fa-plus fa-md"></i> Create New Record
                                    </button>
                                </div>
                                <div class="table-responsive" style="max-height: 80vh; overflow-y: auto;">
                                    <table id="tableQuestionnaire" class="table table-bordered table-hover w-100">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Category</th>
                                                <th>Exam Title</th>
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

    <!-- Create / Update Questionnaire Modal Start -->
    <div class="modal fade" id="modalCreateUpdateQuestionnaire" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-question-circle"></i> Questionnaire
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form method="post" id="formCreateUpdateQuestionnaire">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="questionnaire_id" id="txtCreateUpdateQuestionnaireId">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Category: </label>
                                        <select class="form-control" name="questionnaire_category" id="slctQuestionnaireCategory" required>
                                            <option value="" selected disabled>-- Select Category --</option>
                                            <option value="0">Newly Hired</option>
                                            <option value="1">Certification</option>
                                            <option value="2">Re-Certification</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Title: </label>
                                        <textarea class="form-control" rows="2" name="questionnaire_title" id="txtQuestionnaireTitle" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Purpose: </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="questionnaire_purpose" id="txtQuestionnairePurpose" required>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Position: </label>
                                        <select class="form-control select2bs5 get-systemone-hris-position" name="questionnaire_position" id="slctQuestionnairePosition" required></select>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Passing Score: </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="questionnaire_passing_score" id="nmbrQuestionnairePassingScore" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Instruction: </label>
                                        <textarea class="form-control" rows="2" name="questionnaire_instruction" id="txtQuestionnaireInstruction" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Department: </label>
                                        <select class="form-control select2bs5 get-systemone-hris-department" name="questionnaire_department" id="slctQuestionnaireDepartment" required></select>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Product Line: </label>
                                        <select class="form-control select2bs5 get-systemone-hris-section" name="questionnaire_product_line" id="slctQuestionnaireProductLine" required></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" id="btnQuestionnaire" class="btn btn-dark px-4">
                            <i id="iBtnQuestionnaireIcon" class="fa fa-check mr-1"></i>
                            Save Questionnaire
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div><!-- Create / Update Questionnaire Modal End -->

    <!-- Change Questionnaire Status Modal End -->
    <div class="modal fade" id="modalChangeQuestionnaireStatus">
        <div class="modal-dialog">
            <div class="modal-content modal-sm">
                <div class="modal-header">
                    <h4 class="modal-title" id="h4ChangeQuestionnaireStatusTitle"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formChangeQuestionnaireStatus">
                    @csrf
                    <div class="modal-body">
                    <label id="lblChangeQuestionnaireStatusLabel"></label>
                    <input type="hidden" name="questionnaire_id" id="txtChangeQuestionnaireStatusId" placeholder="Questionnaire Id">
                    <input type="hidden" name="status" id="txtChangeQuestionnaireStatus" placeholder="Status">
                    </div>
                    <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    <button type="submit" id="btnChangeQuestionnaireStatus" class="btn btn-dark"><i id="iBtnChangeQuestionnaireStatusIcon" class="fa fa-check"></i> Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Change Questionnaire Status Modal End -->

    <!-- Questionnaire Details Modal Start -->
    <div class="modal fade" id="modalQuestionnaireDetails" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl-custom modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-question-circle"></i> Questionnaire Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="card-body">
                    <h3><strong><center class="questionnaireTitle"></center></strong></h3>
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-dark" id="buttonCreateQuestionnaireDetails" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaireDetails">
                            <i class="fa fa-plus fa-md"></i> Create New Record
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="tableQuestionnaireDetails" class="table table-bordered table-hover nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>No.</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Question</th>
                                    <th>Choices</th>
                                    <th>Answer(s)</th>
                                    <th>Point(s)</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div><!-- Questionnaire Details Modal End -->
    
    <!-- Create / Update Questionnaire Details Modal Start -->
    <div class="modal fade" id="modalCreateUpdateQuestionnaireDetails" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl-custom modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <strong><center class="questionnaireTitle"></center></strong>
                    </h5>
                </div>

                <form method="post" id="formCreateUpdateQuestionnaireDetails" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="questionnaire_details_pkid" id="txtCreateUpdateQuestionnaireDetailsPkid">
                        <input type="hidden" name="questionnaire_details_fkid" id="txtCreateUpdateQuestionnaireDetailsFkid">
                        <input type="hidden" name="questionnaire_details_revision" id="txtCreateUpdateQuestionnaireDetailsRevision">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="input-group mb-1">
                                        <div class="input-group-prepend w-50">
                                            <span class="input-group-text w-100"><strong>Category: &nbsp; </strong></span>
                                        </div>
                                        <select class="form-control reset-value" name="questionnaire_category_type" id="slctQuestionnaireCategoryType" required>
                                            <option value="" selected disabled>-- Select Category --</option>
                                            <option value="0">Single / Multiple Answer</option>
                                            <option value="1">Identification / Essay</option>
                                            <option value="2">Multiple Grid</option>
                                        </select>                                    
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="input-group mb-1">
                                        <div class="input-group-prepend w-50">
                                            <span class="input-group-text w-100"><strong>Point(s): &nbsp; </strong></span>
                                        </div>
                                        <input type="number" class="form-control reset-value" name="questionnaire_points" id="nmbrQuestionnairePoints" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="input-group" id="fileAttachment" name="div_file_attachment">
                                        <div class="input-group-prepend w-25">
                                            <span class="input-group-text w-100"><strong>Upload Image: &nbsp; </strong></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-dark btnViewAttachment">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <input type="file" class="form-control reset-value" id="fileUploadImage" name="upload_image" accept="image/jpeg, image/png">                                    
                                    </div>

                                    <div class="input-group d-none" id="txtAttachment" name="div_txt_attachment">
                                        <button type="button" class="btn btn-dark" id="btnReUploadFile">
                                            <i class="fa fa-file"></i> Click here to re-upload the file
                                        </button>
                                        <button type="button" class="btn btn-secondary btnViewAttachment" id="getAttachment" value="0">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <input type="text" class="form-control" id="txteUploadImage" name="upload_image" readonly disabled>
                                    </div>
                                </div>

                                <!-- Single / Multiple Answer -->
                                <div class="col-md-12">
                                    <div class="input-group mb-1" id="singleMultipleAnswer">
                                    </div>
                                </div>

                                <!-- Identification / Essay -->
                                <div class="col-md-12">
                                    <div class="input-group mb-1" id="identificationEssay">
                                    </div>
                                </div>

                                <!-- Multiple Grid -->
                                <div class="col-md-12">
                                    <div class="input-group mb-1" id="multipleGrid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" id="btnQuestionnaireDetails" class="btn btn-dark px-4">
                            <i id="iBtnQuestionnaireDetailsIcon" class="fa fa-check mr-1"></i>
                            Save Questionnaire
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div><!-- Create / Update Questionnaire Details Modal End -->
@endsection

@section('js_content')
    <script type="text/javascript">
        let dataQuestionnaire
        let dataQuestionnaireDetails
        let questionnaireDetailId
        let questionnaireDetailRevision
        let questionnaireId
        let questionnaireStatus
        let questionnaireRevision
        let getQuestions = [];
        let getOptions = [];
        let getSelectedAnswers = [];
        let html = ''
            
        $(document).ready(function () {
            $('.select2bs5').select2({
                theme: 'bootstrap-5'
            });

            $(document).on('hidden.bs.modal', function () {
                if ($('.modal.show').length) {
                    $('body').addClass('modal-open');
                }

                $(this).find('form').each(function () {
                    this.reset();
                });

                $('#txtAttachment').addClass('d-none')
                $('#fileAttachment').removeClass('d-none')
                
                $('#singleMultipleAnswer').empty();
                $('#identificationEssay').empty();
                $('#multipleGrid').empty();
            });

            // ===============================================================================================================================================
            // ================================================================ QUESTIONNAIRE ================================================================
            // ===============================================================================================================================================
            GetSystemOneHrisDepartment($('.get-systemone-hris-department'))
            GetSystemOneHrisPosition($('.get-systemone-hris-position'))
            GetSystemOneHrisSection($('.get-systemone-hris-section'))

            dataQuestionnaire = $("#tableQuestionnaire").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[3, "asc"],[3, "asc"]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ Questionnaire Record",
                    "lengthMenu": "Show _MENU_ Questionnaire Record",
                },
                "ajax" : {
                    url: "view_questionnaire",
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
                    { "data" : "exam_instruction"},
                    { "data" : "purpose"},
                    { "data" : "department"},
                    { "data" : "position"},
                    { "data" : "product_line"},
                    { "data" : "passing_score"}
                ],
            });

            $("#formCreateUpdateQuestionnaire").submit(function(event){
                event.preventDefault();
                CreateUpdateQuestionnaire();
            });
    
            $(document).on('click', '.actionUpdateQuestionnaire',function(e){
                e.preventDefault();
    
                questionnaireId = $(this).attr('questionnaire-id');
    
                $('#txtCreateUpdateQuestionnaireId').val(questionnaireId);
                GetQuestionnaireById(questionnaireId);
            });
    
            $(document).on('click', '.actionChangeQuestionnaireStatus',function(e){
                e.preventDefault();
    
                questionnaireStatus = $(this).attr('status');
                questionnaireId     = $(this).attr('questionnaire-id');
    
                $("#txtChangeQuestionnaireStatusId").val(questionnaireId);
                $("#txtChangeQuestionnaireStatus").val(questionnaireStatus);
    
                if(questionnaireStatus == 0){
                    $("#lblChangeQuestionnaireStatusLabel").text('Are you sure to activate?');
                    $("#h4ChangeQuestionnaireStatusTitle").html('<i class="fa fa-question-circle"></i> Activate Questionnaire');
                }else{
                    $("#lblChangeQuestionnaireStatusLabel").text('Are you sure to deactivate?');
                    $("#h4ChangeQuestionnaireStatusTitle").html('<i class="fa fa-question-circle"></i> Deactivate Questionnaire');
                }
            });
    
            $("#formChangeQuestionnaireStatus").submit(function(event){
                event.preventDefault();
                ChangeQuestionnaireStatus();
            });

            // ===============================================================================================================================================
            // ============================================================ QUESTIONNAIRE DETAILS ============================================================
            // ===============================================================================================================================================
            $(document).on('click', '.actionQuestionnaireDetails',function(e){
                e.preventDefault();
    
                questionnaireId = $(this).attr('questionnaire-id');
                questionnaireRevision = $(this).attr('questionnaire-revision');
                questionnaireExamTitle = $(this).attr('questionnaire-exam_title');
    
                $('.questionnaireTitle').text(questionnaireExamTitle);
                $('#txtCreateUpdateQuestionnaireDetailsFkid').val(questionnaireId);
                $('#txtCreateUpdateQuestionnaireDetailsRevision').val(questionnaireRevision);
                dataQuestionnaireDetails.draw();
            });
    
            dataQuestionnaireDetails = $("#tableQuestionnaireDetails").DataTable({
                "processing": false,
                "serverSide": true,
                "responsive": true,
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ Questionnaire Record",
                    "lengthMenu": "Show _MENU_ Questionnaire Record",
                },
                "ajax": {
                    url: "view_questionnaire_details",
                    type: "GET",
                    data: function(data){
                        data.questionnaireId = questionnaireId;
                        data.questionnaireRevision = questionnaireRevision;
                    }
                },
                "columns": [
                    { "data": "action", orderable: false, searchable: false },
                    { "data": "status" },
                    { 
                        "data": "category_type",
                        "defaultContent": 'N/A',
                        "name": 'Category',
                        "orderable": true,
                        "searchable": true,
                        "render": function (data, type, row) {
                            switch (Number(row.category_type)) {
                                case 0: return "CHOICES";
                                case 1: return "TEXT";
                                case 2: return "GRID";
                                default: return "Unknown";
                            }
                        }
                    },
                    { "data": "exam_no" },
                    { "data": "image" },
                    { 
                        "data": "description",
                        "createdCell": function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'white-space': 'normal',
                                'word-break': 'break-word'
                            });
                        }
                    },
                    { "data": "question" },
                    { "data": "choices" },
                    { "data": "answer" },
                    { "data": "points" }
                ],
            });
    
            $('.btnViewAttachment').click(function (e) { 
                e.preventDefault();
    
                let checkFile = $(this).closest('.input-group').find('input[type="file"]')[0];  
                if(!checkFile || !checkFile.files){
                    let fileName = $('#txteUploadImage').val()
                    console.log('fileName: ', fileName);
                    let url = `storage/app/public/questionnaire_attachment/${fileName}`;
                    window.open(url, '_blank');
                }else{
                    if(checkFile.files.length === 0){
                        alert('Please upload the attachment first.');
                    }else{
                        let attachment = checkFile.files[0];
                        let view = new FileReader();
    
                        view.onload = function (e) {
                            let newTab = window.open();
                            newTab.document.write(
                                '<iframe width="100%" height="100%" src="' + e.target.result + '"></iframe>'
                            );
                        };
    
                        view.readAsDataURL(attachment);
                    }              
                }
            });
    
            $('#slctQuestionnaireCategoryType').change(function (e) { 
                e.preventDefault();
                let typeOfQuestion = $(this).val()
                html = ''
                switch (typeOfQuestion) {
                    case '0':
                        console.log('CHOICES');
                        html += '<input type="hidden" name="answer[]" id="choiceAnswerHidden">';
                        html += '<div class="input-group-prepend w-25">'
                        html += '   <span class="input-group-text w-100"><strong>Question: &nbsp; </strong></span>'
                        html += '</div>'
                        html += '<textarea class="form-control" name="questionnaire_question[]" id="txtQuestionnaireQuestion" required></textarea>'
                        html += '<div class="col-md-12 mt-3" style="border-top: 1px solid">'
                        html += '   <div class="form-group row mt-2">'
                        html += '       <label class="col-md-8 col-form-label">'
                        html += '           Choices ( Select the correct answer )'
                        html += '       </label>'
                        html += '       <div class="col-md-4 text-right">'
                        html += '           <button type="button" class="btn btn-dark btn-sm" id="btnAddChoice">'
                        html += '               <i class="fa fa-plus"></i> Add Choice'
                        html += '           </button>'
                        html += '       </div>'
                        html += '       <div class="col-12 divChoices mt-2">'
                        html += '           <div class="input-group input-group-md mb-3">'
                        html += '               <div class="input-group-prepend">'
                        html += '                   <span class="input-group-text">'
                        html += '                       <input type="checkbox" class="chkAnswer" value="0">'
                        html += '                   </span>'
                        html += '               </div>'
                        html += '               <input type="text" class="form-control" name="choices[]" placeholder="Option" required>'
                        html += '               <input type="text" class="form-control txtAnswer" style="display:none;">'
                        html += '               <div class="input-group-append">'
                        html += '                   <button type="button" class="btn btn-danger btnRemoveChoice">'
                        html += '                   <i class="fa fa-trash"></i>'
                        html += '                   </button>'
                        html += '               </div>'
                        html += '           </div>'
                        html += '       </div>'
                        html += '   </div>'
                        html += '</div>'
    
                        $('#singleMultipleAnswer').append(html);
                    break;
    
                    case '1':
                        console.log('TEXT');
                        html += '<div class="input-group-prepend w-25">'
                        html += '   <span class="input-group-text w-100"><strong>Question: &nbsp; </strong></span>'
                        html += '</div>'
                        html += '<textarea class="form-control" name="questionnaire_question[]" id="txtQuestionnaireQuestion" required></textarea>'
                        html += '<div class="input-group mt-3 mb-3">'
                        html += '    <div class="input-group-prepend w-25">'
                        html += '        <span class="input-group-text w-100"><strong>Type of Question: &nbsp;</strong></span>'
                        html += '    </div>'
                        html += '    <select class="form-control" name="question_type" id="txtQuestionType" required>'
                        html += '        <option value="" selected disabled>-- Select Question Type --</option>'
                        html += '        <option value="Identification">Identification</option>'
                        html += '        <option value="Essay">Essay</option>'
                        html += '    </select>'
                        html += '</div>'
                        html += '<div class="col-md-12 mb-3">'
                        html += '   <input type="text" class="form-control d-none" name="identification[]" id="txtIdentification" placeholder="Answer for identification" disabled>'
                        html += '</div>'
    
                        $('#identificationEssay').append(html);
                    break;
    
                    case '2':
                        console.log('GRID');
                        html += '<input type="hidden" name="questionnaire_question" id="questionnaireQuestionHidden">';
                        html += '<input type="hidden" name="choices" id="gridChoicesHidden">';
                        html += '<input type="hidden" name="answer" id="gridAnswerHidden">';
                        html += '<div class="input-group-prepend w-25">';
                        html += '   <span class="input-group-text w-100"><strong>Description: &nbsp;</strong></span>';
                        html += '</div>';
                        html += '<textarea class="form-control" name="questionnaire_description" id="txtQuestionnaireDescription" required></textarea>';
                        html += '<div class="col-12 mt-3" style="border-top: 1px solid">';
                        html += '    <div class="input-group input-group-md mt-3 mb-2">';
                        html += '        <div class="input-group-prepend w-25">';
                        html += '            <span class="input-group-text w-100">';
                        html += '                <strong>Question:</strong>';
                        html += '            </span>';
                        html += '        </div>';
                        html += '        <input type="text" class="form-control" id="txtQuestion" placeholder="Question">';
                        html += '        <div class="input-group-append">';
                        html += '            <button type="button" class="btn btn-sm btn-dark" id="btnAddQuestion" style="width:150px;">';
                        html += '               <i class="fa fa-plus"></i> Add Question';
                        html += '            </button>';
                        html += '        </div>';
                        html += '    </div>';
                        html += '    <div class="input-group input-group-md">';
                        html += '        <div class="input-group-prepend w-25">';
                        html += '            <span class="input-group-text w-100">';
                        html += '                <strong>Option:</strong>';
                        html += '            </span>';
                        html += '        </div>';
                        html += '        <input type="text" class="form-control" id="txtOption" placeholder="Option">';
                        html += '        <div class="input-group-append">';
                        html += '            <button type="button" class="btn btn-sm btn-dark" id="btnAddOption" style="width:150px;">';
                        html += '               <i class="fa fa-plus"></i> Add Option';
                        html += '            </button>';
                        html += '        </div>';
                        html += '    </div>';
                        html += '</div>';
                        html += '<div class="table-responsive mt-3">';
                        html += '  <table class="table table-bordered table-striped" id="questionTable">';
                        html += '    <thead><tr><th>Question</th></tr></thead>';
                        html += '    <tbody></tbody>';
                        html += '  </table>';
                        html += '</div>';
    
                        $('#multipleGrid').append(html);
                    break;
    
                    default:
    
                    break;
                }
            });
    
            // ===================================================================================================
            // ========================================== FOR CHOICES ============================================
            // ===================================================================================================
            $(document).on("click", "#btnAddChoice", function (e) {
                e.preventDefault();
    
                html = '';
                html += '<div class="input-group input-group-md mb-3">';
                html += '   <div class="input-group-prepend">';
                html += '       <span class="input-group-text">';
                html += '           <input type="checkbox" class="chkAnswer" value="0">';
                html += '       </span>';
                html += '   </div>';
                html += '   <input type="text" class="form-control" name="choices[]" placeholder="Option" required>';
                html += '   <input type="hidden" class="txtAnswer">';
                html += '   <div class="input-group-append">';
                html += '       <button type="button" class="btn btn-danger btnRemoveChoice">';
                html += '           <i class="fa fa-trash"></i>';
                html += '       </button>';
                html += '   </div>';
                html += '</div>';
    
                $(".divChoices").append(html);
            });
    
            $(document).on("click", ".btnRemoveChoice", function (e) {
                e.preventDefault();
                $(this).closest(".input-group").remove();
            });
    
            $(document).on("change", ".chkAnswer", function (e) {
                e.preventDefault();
    
                let groupContainer = $(this).closest(".divChoices");
                let answers = [];
    
                groupContainer.find(".input-group").each(function () {
                    let checkbox = $(this).find(".chkAnswer");
    
                    if (checkbox.is(":checked")) {
                        let choiceText = $(this).find("input[name='choices[]']").val();
                        answers.push(choiceText);
                    }
                });
    
                $('#choiceAnswerHidden').val(answers)
            });
    
            // ====================================================================================================
            // ============================================ FOR TEXT ==============================================
            // ====================================================================================================
            $(document).on('change', '#txtQuestionType', function (e) { 
                e.preventDefault();
                let questionTypeValue = $(this).val()
    
                if(questionTypeValue == 'Identification'){
                    $('#txtIdentification').removeClass('d-none').prop({'disabled': false, 'required': true})
                }else{
                    $('#txtIdentification').addClass('d-none').prop({'disabled': true, 'required': false})
                }
            });
    
            // ====================================================================================================
            // ============================================== GRID ================================================
            // ====================================================================================================
            // Add question
            $(document).on('click', '#btnAddQuestion', function() {
                let question = $('#txtQuestion').val().trim();
                if (!question) return alert("Please enter a question!");
                getQuestions.push(question);
                renderTable();
                $('#txtQuestion').val('');
            });
    
            // Remove question
            $(document).on('click', '.removeQuestion', function() {
                let index = $(this).data('index');
                getQuestions.splice(index, 1);
                getSelectedAnswers.splice(index, 1);
                renderTable();
            });
    
            // Add option
            $(document).on('click', '#btnAddOption', function() {
                let optionBtn = $('#txtOption').val().trim();
                if (!optionBtn) return alert("Please enter an option!");
                getOptions.push(optionBtn);
                renderTable();
                $('#txtOption').val('');
            });
    
            // Remove option
            $(document).on('click', '.removeOption', function() {
                let index = $(this).data('index');
                getOptions.splice(index, 1);
                renderTable();
            });
    
            // Handle radio click per row
            $(document).on('click', 'input[type=radio]', function() {
                let row = $(this).data('row');
                let column = $(this).data('column');
    
                $(`input[data-row=${row}]`).prop('checked', false);
                $(this).prop('checked', true);
                getSelectedAnswers[row] = column;
                $('#gridAnswerHidden').val(JSON.stringify(getSelectedAnswers));
            });
    
            $("#formCreateUpdateQuestionnaireDetails").submit(function(event){
                event.preventDefault();
                CreateUpdateQuestionnaireDetails();
            });
    
            $(document).on('click', '.actionUpdateQuestionnaireDetails',function(e){
                e.preventDefault();
    
                questionnaireDetailsId = $(this).attr('questionnaire_detail-id');
                questionnaireDetailRevision = $(this).attr('questionnaire_detail-revision');
    
                $('#txtCreateUpdateQuestionnaireDetailsPkid').val(questionnaireDetailsId);
                $('#txtCreateUpdateQuestionnaireDetailsRevision').val(questionnaireDetailRevision);
    
                GetQuestionnaireDetailsById(questionnaireDetailsId,questionnaireDetailRevision)
            });
    
            $('#btnReUploadFile').click(function (e) { 
                e.preventDefault();
                $('#fileAttachment').removeClass('d-none')
                $('#txtAttachment').addClass('d-none')
            });
        });
    </script>
@endsection