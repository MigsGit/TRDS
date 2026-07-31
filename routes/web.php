<?php
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\ExaminationResultController;
use App\Http\Controllers\ExamTitleController;
use App\Http\Controllers\HrMemo\HrMemoController;
use App\Http\Controllers\HrMemo\HrMemoExaminationController;
use App\Http\Controllers\ListOfCertPersonnelController;
use App\Http\Controllers\PersonnelSkillMatrixController;
use App\Http\Controllers\QualificationCertificationController;
use App\Http\Controllers\QuestionnairesController;
use App\Http\Controllers\TrainingAttendanceController;
use App\Http\Controllers\TrainingEndorsementController;
use App\Http\Controllers\TrainingRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ETRController;
use App\Http\Controllers\InspSkillChartSettingController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;


Route::middleware('checkSession')->group(function(){

    Route::get('/', function () {
        return view('blank');
    })->name('blank');

    Route::get('/training_attendance', function () {
        return view('training_attendance');
    })->name('training_attendance');

    Route::get('/hr_memo_exam', function () {
        return view('hr_memo_examination');
    })->name('hr_memo_exam');

    Route::get('/user_master', function () {
        return view('user');
    })->name('user_master');

    Route::get('/insp_skill_chart_setting', function () {
        return view('inspector_skill_chart_settings');
    })->name('insp_skill_chart_setting');

    Route::get('/hr_memo_exam', function () {
        return view('hr_memo_examination');
    })->name('hr_memo_exam');

    Route::get('/hr_memo', function () {
        return view('hr_memo_approval');
    })->name('hr_memo');
    Route::get('/qualification_certification', function () {
        return view('qualification_certification');
    })->name('qualification_certification');

    Route::get('/training_request_conformance', function () {
        return view('training_request_conformance');
    })->name('training_request_conformance');


    Route::get('/training_request_approval', function () {
        return view('training_request_approval');
    })->name('training_request_approval');

    Route::get('/dashboard', function () {
        return view('blank');
    })->name('dashboard');

    Route::get('/questionnaire', function () {
        return view('theoretical_exam/questionnaire');
    })->name('questionnaire');

    Route::get('/test', function () {
        return view('qualification_certification_test');
    })->name('test');

    Route::get('/examination_result', function () {
        return view('theoretical_exam/examination_result');
    })->name('examination_result');

    Route::get('/training_endorsement', function () {
        return view('training_endorsement');
    })->name('training_endorsement');

    Route::get('/ETR', function () {
        return view('ETR');
    })->name('ETR');

    // TRAINING ENDORSEMENT CONTROLLER
    Route::controller(TrainingEndorsementController::class)->group(function () {
        Route::get('/get_training_endorsements', 'getTrainingEndorsements')->name('get_training_endorsements');
        Route::get('/get_training_endorsement_by_id', 'getTrainingEndorsementById')->name('get_training_endorsement_by_id');
        Route::post('/save_training_endorsement', 'saveTrainingEndorsement')->name('save_training_endorsement');
        Route::post('/delete_training_endorsement', 'deleteTrainingEndorsement')->name('delete_training_endorsement');
        Route::get('/get_endorsement_users', 'getEndorsementUsers')->name('get_endorsement_users');
        Route::get('/get_current_user', 'getCurrentUser')->name('get_current_user');
        Route::get('/get_all_email', 'getAllEmail')->name('get_all_email');
        Route::get('/get_training_request_controls', 'getTrainingRequestControls')->name('get_training_request_controls');
        Route::get('/get_training_request_ctrl_details', 'getTrainingRequestDetails')->name('get_training_request_ctrl_details');
        Route::get('/get_employees_for_not_endorsed', 'getEmployeesForNotEndorsed')->name('get_employees_for_not_endorsed');
        Route::post('/add_not_endorsed_emp', 'addNotEndorsedEmp')->name('add_not_endorsed_emp');
        Route::get('/export_endorsement_pdf', 'exportEndorsementPdf')->name('export_endorsement_pdf');
        Route::post('/proceed_endorsement_approval', 'proceedEndorsementApproval')->name('proceed_endorsement_approval');
        Route::post('/approve_endorsement', 'approveEndorsement')->name('approve_endorsement');
        Route::post('/disapprove_endorsement', 'disapproveEndorsement')->name('disapprove_endorsement');
    });
    Route::get('/personnel_skill_matrix', function () {
        return view('personnel_skill_matrix');
    })->name('personnel_skill_matrix');


    // QUESTIONNAIRES CONTROLLER

    Route::controller(QualificationCertificationController::class)->group(function () {
        // Questionnaires main routes
        Route::post('save_qualification_certification_oper', 'saveQualificationCertificationOper');
        Route::post('save_form_send_email', 'saveFormSendEmail');
        Route::post('save_first_take_ins_sequence', 'saveFirstTakeInsSequence');
        Route::post('update_approval', 'updateApproval');

        Route::get('load_qc_slip', 'loadQcSlip');
        Route::get('get_div_dept_sec', 'getDivDeptSec');
        Route::get('get_dropdown_master_details_by_fkid', 'getDropdownMasterDetailsByFkid');
        Route::get('get_qc_slips_by_id', 'getQcSlipsById');
        Route::get('load1st_qc_validation', 'load1stQcValidation');
        Route::get('load2nd_qc_validation', 'load2ndQcValidation');
    });

    Route::controller(QuestionnairesController::class)->group(function () {
        // Questionnaires main routes
        Route::get('view_questionnaire', 'viewQuestionnaire');
        Route::get('get_systemone_hris_department', 'getSystemoneHrisDepartment');
        Route::get('get_systemone_hris_position', 'getSystemoneHrisPosition');
        Route::get('get_systemone_hris_section', 'getSystemoneHrisSection');
        Route::get('get_exam_title', 'getExamTitle');
        Route::post('create_update_questionnaire', 'createUpdateQuestionnaire');
        Route::get('get_questionnaire_by_id', 'getQuestionnaireById');
        Route::post('change_questionnaire_status', 'changeQuestionnaireStatus');
        Route::get('/view_pdf_questionnaire/{id}', 'viewPdfQuestionnaire');

        // Questionnaires details routes
        Route::get('view_questionnaire_details', 'viewQuestionnaireDetails');
        Route::post('create_update_questionnaire_details', 'createUpdateQuestionnaireDetails');
        Route::get('get_questionnaire_details_by_id', 'getQuestionnaireDetailsById');
        Route::post('change_questionnaire_details_status', 'changeQuestionnaireDetailsStatus');
        Route::post('reorder','reorder')->name('reorder');
    });

    Route::controller(ExaminationController::class)->group(function () {
        Route::get('exam', 'examDashboard')->name('examDashboard');
        Route::get('exam/startExam/{id}/{revision}', 'startExam')->name('startExam');

        Route::get('get_exam_training_request_control_no', 'getExamTrainingRequestControlNo')->name('get_exam_training_request_control_no');
        Route::get('get_exam_training_request_employee_no', 'getExamTrainingRequestEmployeeNo')->name('get_exam_training_request_employee_no');
        Route::get('count_exam_training_request_examination_take', 'countExamTrainingRequestExaminationTake')->name('count_exam_training_request_examination_take');

        Route::post('exam_submission', 'examSubmission');
        Route::get('get_exam_training_request_details_by_revision_id', 'getExamTrainingRequestDetailsByRevisionId');
    });

    Route::controller(ExaminationResultController::class)->group(function () {
        Route::get('view_exam_result', 'viewExamResult');
        Route::get('view_exam_result_details', 'viewExamResultDetails');
        Route::get('get_employee_exam_result_by_id', 'getEmployeeExamResultById');
        Route::post('update_exam_score_for_employee', 'updateExamScoreForEmployee');

        Route::get('/view_pdf_examination_result/{id}', 'viewPdfExaminationResult');
        Route::post('update_examination_date', 'updateExaminationDate');
        Route::post('update_examination_date', 'updateExaminationDate');
        Route::post('change_exam_result_status', 'changeExamResultStatus');
    });

    Route::controller(ExamTitleController::class)->group(function () {
        Route::get('view_exam_title', 'viewExamTitle');
        Route::post('create_update_exam_title', 'createUpdateExamTitle');
        Route::get('get_exam_title_by_id', 'getExamTitleById');
        Route::post('change_exam_title_status', 'changeExamTitleStatus');
    });

    // =======================================================================================================
    Route::controller(HrMemoExaminationController::class)->group(function () {
        // HrMemoExaminationController
        Route::get('/view_examinations', 'viewExaminationsInfo')->name('view_examinations');
        Route::post('/add_examinations', 'addExaminationsInfo')->name('add_examinations');
        Route::get('/get_examinations_by_id', 'getExaminationsById')->name('get_examinations_by_id');
        Route::post('/update_examinations_status', 'updateExaminationsStatus')->name('update_examinations_status');
        Route::get('/get_examinations', 'getExaminations')->name('get_examinations');
    });

    Route::controller(HrMemoController::class)->group(function () {
        Route::get('/view_hr_memo', 'viewHrMemoInfo')->name('view_hr_memo');
        Route::get('/view_trainee_details', 'viewTraineeDetails')->name('view_trainee_details');
        Route::post('/add_hr_memo', 'addHrMemoInfo')->name('add_hr_memo');
        Route::get('/get_hr_memo_by_id', 'getHrMemoById')->name('get_hr_memo_by_id');
        Route::post('/update_hr_memo_status', 'updateHrMemoStatus')->name('update_hr_memo_status');
        Route::get('/get_users', 'getUsers')->name('get_users');
        Route::get('/get_employee_details', 'getEmployeeDetails')->name('get_employee_details');
        Route::get('/get_emp_no_dropdown_details', 'getEmpNoDropdownDetails')->name('get_emp_no_dropdown_details');
        Route::get('/get_training_venue_dropdown_details', 'getTrainingVenueDropdownDetails')->name('get_training_venue_dropdown_details');
        Route::get('/get_email_recipients_dropdown_details', 'getEmailRecipientsDropdownDetails')->name('get_email_recipients_dropdown_details');
        Route::get('/send_hr_memo_mail', 'sendHrMemoMail')->name('send_hr_memo_mail');
        Route::get('/get_trainor_dropdown_details', 'getTrainorDropdownDetails')->name('get_trainor_dropdown_details');
        Route::get('/export_inspector_skill_chart', 'exportInspectorSkillChart')->name('export_inspector_skill_chart');
    });

    // =======================================================================================================
    Route::controller(InspSkillChartSettingController::class)->group(function () {
        Route::get('/view_process_stations', 'viewProcessStationsInfo')->name('view_process_stations');
        Route::post('/add_process_station', 'addProcessStationInfo')->name('add_process_station');
        Route::get('/get_process_station_by_id', 'getProcessStationById')->name('get_process_station_by_id');
        Route::post('/update_process_station_status', 'updateProcessStationStatus')->name('update_process_station_status');
        Route::get('/get_process_stations', 'getProcessStations')->name('get_process_stations');
    });

    // USER CONTROLLER
    Route::controller(UserController::class)->group(function () {
        Route::post('/add_user', 'add_user');
        Route::post('/save_user_module_access', 'save_user_module_access');
        Route::get('/view_users', 'view_users')->name('user.view_users');
        Route::get('/view_user_module_access', 'view_user_module_access')->name('user.get_emp_details_by_id');
        Route::get('/get_user_levels', 'get_user_levels')->name('user.get_user_levels');
        Route::get('/get_emp_details_by_id', 'get_emp_details_by_id')->name('user.get_emp_details_by_id');
        Route::get('/get_user_list', 'get_user_list');
        Route::get('/get_user_by_id', 'get_user_by_id');
        Route::get('/get_user_module_access', 'get_user_module_access');
        Route::get('/get_system_one_employee_details', 'getSystemOneEmployeeDetails');
    });

    Route::controller(TrainingRequestController::class)->group(function () {

        //TRAINING REQUEST
        Route::get('/get_training_requests', 'getTrainingRequests')->name('get_training_requests');
        Route::post('/add_training_request', 'addTrainingRequest')->name('add_training_request');
        Route::get('/get_training_request_details', 'getTrainingRequestDetails')->name('get_training_request_details');
        Route::post('/confirm_training_request', 'confirmTrainingRequest')->name('confirm_training_request');
        Route::post('/receive_training_request', 'receiveTrainingRequest')->name('receive_training_request');
        Route::post('/approve_training_request', 'approveTrainingRequest')->name('approve_training_request');
        Route::get('/get_requested_employee_details', 'getRequestedEmployeeDetails')->name('get_requested_employee_details');
        Route::get('/training_request', 'index')->name('training_request');


        // FOR DROPDOWNS
        Route::get('/get_hris_department', 'getHRISSectionByDepartment')->name('get_hris_department');
        Route::get('/get_hris_divisions', 'getHRISDivisions')->name('get_hris_divisions');
        Route::get('/get_hris_sections', 'getHRISSections')->name('get_hris_sections');
        Route::get('/get_user_conformance', 'getUserConformance')->name('get_user_conformance');
        Route::get('/get_requestor', 'getRequestor')->name('get_requestor');
        Route::get('/get_training_request_filter', 'getTrainingRequestsViewFilter')->name('get_training_request_filter');

        // MEMO DOCS
        Route::get('/get_memo_docs', 'getMemoDocs')->name('get_memo_docs');
        Route::get('/get_memo_doc_details', 'getMemoDocsDetails')->name('get_memo_doc_details');
        Route::get('/get_employee_list_by_memo_doc', 'getEmployeeListByMemoDoc')->name('get_employee_list_by_memo_doc');
        Route::post('/save_memo_doc_employees', 'saveMemoDocEmployees')->name('save_memo_doc_employees');

        Route::get('/get_memo_doc_employee_details', 'getMemoDocEmployeeDetails')->name('get_memo_doc_employee_details');

    });



    Route::controller(TrainingAttendanceController::class)->group(function () {
        Route::get('/view_training_attendance', 'view_training_attendance');
    });

    Route::controller(TrainingAttendanceController::class)->group(function () {
        Route::post('/save_attendance', 'save_attendance');
        Route::post('/save_training_attendance', 'save_training_attendance');
        Route::get('/view_training_attendance_summary', 'view_training_attendance_summary');
        Route::get('/view_training_attendance', 'view_training_attendance');
        Route::get('/view_training_attendance_request_details', 'view_training_attendance_request_details');
        Route::get('/get_training_attendance_by_id', 'get_training_attendance_by_id');
    });

    Route::controller(PersonnelSkillMatrixController::class)->group(function () {
        //TRAINING REQUEST
        Route::get('/get_direct_employees', 'getDirectEmployees')->name('get_direct_employees');
        Route::get('/get_subcon_employees', 'getSubconEmployees')->name('get_subcon_employees');
        Route::get('/get_direct_employee_info', 'getDirectEmployeeInfo')->name('get_direct_employee_info');
        Route::get('/get_subcon_employee_info', 'getSubconEmployeeInfo')->name('get_subcon_employee_info');
        Route::get('/view_subcon_employee_info', 'viewSubconEmployeeInfo')->name('view_subcon_employee_info');
        Route::get('/view_direct_employee_info', 'viewDirectEmployeeInfo')->name('view_direct_employee_info');
        Route::get('/get_employee_trainings', 'getEmployeeTrainings')->name('get_employee_trainings');
    });

    Route::controller(ETRController::class)->group(function () {
        Route::get('view_employee_training_record', 'viewEmployeeTrainingRecord');
        Route::get('get_systemone_employee_training_details', 'getSystemoneEmployeeTrainingDetails');

        // Route::get('view_trds_summary', 'viewTRDSSummary');
    });
    Route::controller(ListOfCertPersonnelController::class)->group(function(){
        Route::get('/get_dropdown_select_certpersonnel', 'getDropdownSelectCertPersonnel')->name('get_dropdown_select_certpersonnel');
        Route::get('/export_list_cert_personnel', 'exportListCertPersonnel')->name('export_list_cert_personnel');
    });



});
