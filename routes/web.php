<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionnairesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('blank');
})->name('blank');

Route::get('/user_master', function () {
    return view('user');
})->name('user_master');

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
// =======================================================================================================
// =======================================================================================================
// USER CONTROLLER
// Route::post('/sign_in', 'UserController@sign_in')->name('sign_in');
// Route::post('/sign_out', 'UserController@sign_out')->name('sign_out');
// Route::post('/change_pass', 'UserController@change_pass')->name('change_pass');
// Route::post('/change_user_stat', 'UserController@change_user_stat')->name('change_user_stat');
// Route::get('/view_users', 'UserController@view_users');
// Route::post('/add_user', 'UserController@add_user');
// Route::get('/get_user_by_id', 'UserController@get_user_by_id');
// Route::get('/get_user_list', 'UserController@get_user_list');
// Route::get('/get_user_by_batch', 'UserController@get_user_by_batch');
// Route::get('/get_user_by_stat', 'UserController@get_user_by_stat');
// Route::post('/edit_user', 'UserController@edit_user');
// Route::post('/reset_password', 'UserController@reset_password');
// Route::get('/generate_user_qrcode', 'UserController@generate_user_qrcode');
// Route::post('/import_user', 'UserController@import_user');