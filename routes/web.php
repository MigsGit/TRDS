<?php

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
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('blank');
})->name('blank');

Route::get('/user_master', function () {
    return view('user');
})->name('user_master');

Route::get('/hr_memo_exam', function () {
    return view('hr_memo_examination');
})->name('hr_memo_exam');

Route::get('/hr_memo', function () {
    return view('hr_memo_approval');
})->name('hr_memo');

Route::get('/dashboard', function () {
    return view('blank');
})->name('dashboard');

Route::group(['namespace' => 'HrMemo'], function (){
    // HrMemoController
    Route::get('/view_hr_memo', 'HrMemoController@viewHrMemoInfo')->name('view_hr_memo');
    Route::get('/view_trainee_details', 'HrMemoController@viewTraineeDetails')->name('view_trainee_details');
    Route::post('/add_hr_memo', 'HrMemoController@addHrMemoInfo')->name('add_hr_memo');
    Route::get('/get_hr_memo_by_id', 'HrMemoController@getHrMemoById')->name('get_hr_memo_by_id');
    Route::post('/update_hr_memo_status', 'HrMemoController@updateHrMemoStatus')->name('update_hr_memo_status');
    Route::get('/download_file/{id}', 'HrMemoController@downloadFile')->name('download_file');
    Route::get('/get_count_no_of_occurrence', 'HrMemoController@getCountOfNoOfOccurrence')->name('get_count_no_of_occurrence');
    Route::get('/get_device_name', 'HrMemoController@getDeviceName')->name('get_device_name');
    Route::get('/get_users', 'HrMemoController@getUsers')->name('get_users');
    Route::get('/get_employee_details', 'HrMemoController@getEmployeeDetails')->name('get_employee_details');

    // HrMemoExaminationController
    Route::get('/view_examinations', 'HrMemoExaminationController@viewExaminationsInfo')->name('view_examinations');
    Route::post('/add_examinations', 'HrMemoExaminationController@addExaminationsInfo')->name('add_examinations');
    Route::get('/get_examinations_by_id', 'HrMemoExaminationController@getExaminationsById')->name('get_examinations_by_id');
    Route::post('/update_examinations_status', 'HrMemoExaminationController@updateExaminationsStatus')->name('update_examinations_status');
    Route::get('/get_examinations', 'HrMemoExaminationController@getExaminations')->name('get_examinations');
});

// USER CONTROLLER

// Route::group(['namespace' => 'User'], function (){
// });
Route::get('/view_users', 'UserController@view_users')->name('user.view_users');
Route::get('/get_user_levels', 'UserController@get_user_levels')->name('user.get_user_levels');


// Route::post('/sign_in', 'UserController@sign_in')->name('sign_in');
// Route::post('/sign_out', 'UserController@sign_out')->name('sign_out');
// Route::post('/change_pass', 'UserController@change_pass')->name('change_pass');
// Route::post('/change_user_stat', 'UserController@change_user_stat')->name('change_user_stat');
// Route::post('/add_user', 'UserController@add_user');
// Route::get('/get_user_by_id', 'UserController@get_user_by_id');
// Route::get('/get_user_list', 'UserController@get_user_list');
// Route::get('/get_user_by_batch', 'UserController@get_user_by_batch');
// Route::get('/get_user_by_stat', 'UserController@get_user_by_stat');
// Route::post('/edit_user', 'UserController@edit_user');
// Route::post('/reset_password', 'UserController@reset_password');
// Route::get('/generate_user_qrcode', 'UserController@generate_user_qrcode');
// Route::post('/import_user', 'UserController@import_user');
