<?php
use App\Http\Controllers\HrMemo\HrMemoController;
use App\Http\Controllers\HrMemo\HrMemoExaminationController;
use App\Http\Controllers\QuestionnairesController;
use App\Http\Controllers\TrainingAttendanceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('blank');
})->name('blank');

Route::get('/user_master', function () {
    return view('user');
})->name('user_master');

Route::get('/training_attendance', function () {
    return view('training_attendance');
})->name('training_attendance');

Route::get('/hr_memo_exam', function () {
    return view('hr_memo_examination');
})->name('hr_memo_exam');

Route::get('/hr_memo', function () {
    return view('hr_memo_approval');
})->name('hr_memo');

Route::get('/dashboard', function () {
    return view('blank');
})->name('dashboard');

Route::get('/questionnaire', function () {
    return view('theoretical_exam/questionnaire');
})->name('questionnaire');

// QUESTIONNAIRES CONTROLLER
// Route::get('/view_questionnaire', 'QuestionnairesController@viewQuestionnaire');
// Route::get('/get_systemone_hris_department', 'QuestionnairesController@getSystemoneHrisDepartment');
// Route::get('/get_systemone_hris_position', 'QuestionnairesController@getSystemoneHrisPosition');
// Route::get('/get_systemone_hris_section', 'QuestionnairesController@getSystemoneHrisSection');
// Route::post('/create_update_questionnaire', 'QuestionnairesController@createUpdateQuestionnaire');
// Route::get('/get_questionnaire_by_id', 'QuestionnairesController@getQuestionnaireById');
// Route::post('/change_questionnaire_status', 'QuestionnairesController@changeQuestionnaireStatus');

// Route::get('/view_questionnaire_details', 'QuestionnairesController@viewQuestionnaireDetails');
// Route::post('/create_update_questionnaire_details', 'QuestionnairesController@createUpdateQuestionnaireDetails');
Route::controller(QuestionnairesController::class)->group(function () {
    // Questionnaires main routes
    Route::get('view_questionnaire', 'viewQuestionnaire');
    Route::get('get_systemone_hris_department', 'getSystemoneHrisDepartment');
    Route::get('get_systemone_hris_position', 'getSystemoneHrisPosition');
    Route::get('get_systemone_hris_section', 'getSystemoneHrisSection');
    Route::post('create_update_questionnaire', 'createUpdateQuestionnaire');
    Route::get('get_questionnaire_by_id', 'getQuestionnaireById');
    Route::post('change_questionnaire_status', 'changeQuestionnaireStatus');

    // Questionnaires details routes
    Route::get('view_questionnaire_details', 'viewQuestionnaireDetails');
    Route::post('create_update_questionnaire_details', 'createUpdateQuestionnaireDetails');
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
    Route::get('/get_email_recipients_dropdown_details', 'getEmailRecipientsDropdownDetails')->name('get_email_recipients_dropdown_details');
    Route::get('/send_hr_memo_mail', 'sendHrMemoMail')->name('send_hr_memo_mail');
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

});
Route::controller(TrainingAttendanceController::class)->group(function () {
    Route::get('/view_training_attendance', 'view_training_attendance');
});
// Route::post('/sign_in', 'sign_in')->name('sign_in');
// Route::post('/sign_out', 'sign_out')->name('sign_out');
// Route::post('/change_pass', 'change_pass')->name('change_pass');
// Route::post('/change_user_stat', 'change_user_stat')->name('change_user_stat');
// Route::get('/get_user_by_batch', 'get_user_by_batch');
// Route::get('/get_user_by_stat', 'get_user_by_stat');
// Route::post('/edit_user', 'edit_user');
// Route::post('/reset_password', 'reset_password');
// Route::get('/generate_user_qrcode', 'generate_user_qrcode');
// Route::post('/import_user', 'import_user');
