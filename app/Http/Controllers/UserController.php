<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAccessModuleRequest;
use App\Http\Requests\UserRequest;
use App\Imports\CSVUserImport;
use App\Jobs\SendUserPasswordJob;
use App\Model\OQCStamp;
use App\Model\User;
use App\Model\UserAccessModule;
use App\Model\UserLevel;
use App\Model\UserModule;
use App\RapidXUser;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use QrCode;

class UserController extends Controller
{

    public function save_user_module_access(UserAccessModuleRequest $userAccessModuleRequest){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $arrUserModulesId = $userAccessModuleRequest->arrUserModulesId;
            $selectedEmployeeNumber = $userAccessModuleRequest->selectedEmployeeNumber;
            UserAccessModule::whereIn('users_id',$selectedEmployeeNumber)->delete();
            // UserAccessModule::whereIn('users_id',$selectedEmployeeNumber)->update([
            //     'deleted_at' => now(),
            // ]);
            collect($selectedEmployeeNumber)->map(function($rowSelectedEmployeeNumber) use ($arrUserModulesId){
                UserAccessModule::insert([
                    'users_id' => $rowSelectedEmployeeNumber,
                    'user_modules_id' => implode(',',$arrUserModulesId),
                ]);
            });
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    //View Users
	public function view_users(){
    $users = User::with([
                    'user_level',
                    'rapidx_system_one_subcon_emp_info',
                    'rapidx_system_one_hris_emp_info',
                ])
                ->get();

        return DataTables::of($users)
            ->addColumn('label1', function($user){
                $result = "";

                if(blank($user->deleted_at)){
                    $result .= '<span class="badge badge-pill badge-success">Active</span>';
                }
                else{
                    $result .= '<span class="badge badge-pill badge-danger">Inactive</span>';
                }

                return $result;
            })
            ->addColumn('fullname', function($user){
                $result = "";

                if(filled($user->rapidx_system_one_hris_emp_info)){
                    $userHris = $user->rapidx_system_one_hris_emp_info;
                }
                else{
                    $userHris = $user->rapidx_system_one_subcon_emp_info;

                    $result .= '<span class="badge badge-pill badge-danger">Inactive</span>';
                }
                return $userHris->FirstName.' '.$userHris->LastName;
                return $result;
            })
            ->addColumn('action1', function($user){
                $result = '<center><div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">';
                $result .= '<button class="dropdown-item aEditUser" type="button" user-id="' . $user->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalAddUser" data-keyboard="false">Edit</button>';
                $result .= '<button class="dropdown-item aEditModuleAccess" type="button"  rapidx-emp-no= "'.$user->rapidx_emp_no .'"  user-id="' . $user->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalAddUserModuleAccess" data-keyboard="false">Edit Module</button>';
                // $result .= '<button class="dropdown-item aGenUserBarcode" user-id="' . $user->id . '" employee-id="' . $user->employee_id . '" type="button" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalGenUserBarcode">Generate Barcode</button>';
                $result .= '</div>
                        </div></center>';
                return $result;
            })
            ->addColumn('checkbox', function($user){
                return '<center><input type="checkbox" class="chkUser" user-id="' . $user->id . '"></center>';
            })
            ->rawColumns(['label1', 'action1', 'checkbox','fullname'])
            ->make(true);
    }
    //View Users
	public function view_user_module_access(Request $request){
        $usersId =  $request->users_id ?? '';
        // usersId
    	$userModule = UserModule::with([
                    'rapidx_user_updated_by',
                ])
        ->orderBy('id','asc');
        $count = 0;
        $usersId;
        $userAccessModule = [];
        // if(filled($usersId)){
        //     $userAccessModule =  UserAccessModule::where('users_id',$usersId)->first('user_modules_id');
        //     $userAccessModule = explode(',',$userAccessModule->user_modules_id);
        // }

        if (filled($usersId)) {
            $userAccess = UserAccessModule::where('users_id', $usersId)->first();
            $userAccessModule = $userAccess ? explode(',', $userAccess->user_modules_id) : [];
        } else {
            $userAccessModule = [];
        }
        return DataTables::of($userModule)
            ->addColumn('rawBulkCheckBox', function($row) use($userAccessModule,$usersId){
                $isChecked = "";
                if (filled($usersId)) {
                    // Check if the current row ID is inside the user's access array
                    if (in_array($row->id, $userAccessModule)) {
                        $isChecked = "checked";
                    }
                }
                $result = '';
                $result .= '<center>';
                $result .= "<input class='checkBulkUserModule' $isChecked type='checkbox' pkid-received='".$row->id."' id='checkBulkUserModule'>";
                $result .= '</center>';
                return $result;
            })
            ->addColumn('action', function($row){
                $result = '<center><div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">';
                if($row->status == 1){
                	$result .= '<button class="dropdown-item aEditUser" type="button" user-id="' . $row->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalEditUser" data-keyboard="false">Edit</button>';

                    $result .= '<button class="dropdown-item aChangeUserStat" type="button" user-id="' . $row->id . '" status="2" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalChangeUserStat" data-keyboard="false">Deactivate</button>';

                    $result .= '<button class="dropdown-item aResetUserPass" user-id="' . $row->id . '" type="button" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalResetUserPass" data-keyboard="false">Reset Password</button>';

                    // $result .= '<button class="dropdown-item aGenUserBarcode" user-id="' . $row->id . '" employee-id="' . $row->employee_id . '" type="button" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalGenUserBarcode">Generate Barcode</button>';
                }
                else{
                    $result .= '<button class="dropdown-item aChangeUserStat" type="button" style="padding: 1px 1px; text-align: center;" user-id="' . $row->id . '" status="1" data-toggle="modal" data-target="#modalChangeUserStat" data-keyboard="false">Activate</button>';
                }

                $result .= '</div>
                        </div></center>';

                return $result;
            })
            ->addColumn('updated_by', function($row){
                return $row->rapidx_user_updated_by->name ?? "" ;
            })
            ->rawColumns(['action', 'updated_by','rawBulkCheckBox'])
            ->make(true);
    }

    // Add User
    public function add_user(UserRequest $userRequest){
        date_default_timezone_set('Asia/Manila');
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $userRequestValidated = $userRequest->validated();
            $rapidxUserId = $userRequest->user_id;
            if(blank($rapidxUserId)){ //add
                $userRequestValidated['created_at'] = now();
                $userId = User::insertGetId(
                    $userRequestValidated
                );
            }else{ //edit
                $userId = User::where('id',$rapidxUserId)->update(
                    $userRequestValidated
                );
            }
            DB::commit();
            return response()->json(['result' => "1",'is_success' => 'true']);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        return;
        $email = '';
        // $password = 'pmi1234' . Str::random(10);
        $password = 'pmi12345';

        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'user_level_id' => 'required|string|max:255',
        ];

        if(isset($request->with_email)){
            $rules['email'] = 'required|string|max:255|unique:users';
            $has_email = 1;
            // $has_email = 0;
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }
        else{
            DB::beginTransaction();

            // try{
                $user_id = User::insertGetId([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($password),
                    'is_password_changed' => 0,
                    'status' => 1,
                    'user_level_id' => $request->user_level_id,
                    'created_by' => Auth::user()->id,
                    'last_updated_by' => Auth::user()->id,
                    'update_version' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                if(isset($request->send_email)){
                    $subject = 'PATS User Registration';
                    $email = $request->email;
                    $message = 'This is a notification from PATS. Your PATS user account was successfully registered.';

                    dispatch(new SendUserPasswordJob($subject, $message, $request->username, $password, $email));
                }

                DB::commit();

                return response()->json(['result' => "1", 'password' => $password, 'has_email' => $has_email, 'username' => $request->username]);
            // }
            // catch(\Exception $e) {
            //     DB::rollback();
            //     // throw $e;
            //     return response()->json(['result' => "0"]);
            // }
        }
    }

    // Get User By Id
    public function get_user_by_id(Request $request){
        $users = User::with([
            'users',
            'rapidx_system_one_subcon_emp_info',
            'rapidx_system_one_hris_emp_info'
        ])
        ->where('id',$request->user_id)
        ->get();

       $userCollection =  collect($users)->map(function($rowUsers){
            if($rowUsers->rapidx_system_one_subcon_emp_info!= null){
                $userHris = $rowUsers->rapidx_system_one_subcon_emp_info;
            }
            else if($rowUsers->rapidx_system_one_hris_emp_info != null){
                $userHris = $rowUsers->rapidx_system_one_hris_emp_info;
            }
            return [
             'users' => $rowUsers,
             'userDetails' => $userHris,
            ];
        });


        return response()->json(['userCollection' => $userCollection]);
    }

    public function get_user_list(Request $request){ //nmodify

        $users = User::with([
            'users',
            'rapidx_system_one_subcon_emp_info',
            'rapidx_system_one_hris_emp_info'
        ])->get();

       $userCollection =  collect($users)->map(function($rowUsers){
            if($rowUsers->rapidx_system_one_subcon_emp_info!= null){
                $userHris = $rowUsers->rapidx_system_one_subcon_emp_info;
            }
            else if($rowUsers->rapidx_system_one_hris_emp_info != null){
                $userHris = $rowUsers->rapidx_system_one_hris_emp_info;
            }
            return [
             'users' => $rowUsers,
             'userDetails' => $userHris,
            ];
        });


        return response()->json(['userCollection' => $userCollection]);
    }

    // Get User By Batch
    public function get_user_by_batch(Request $request){
     return   $users;

        if($request->user_id == 0){
            $users = User::all();
        }
        else{
            $users = User::whereIn('id', $request->user_id)->get();
        }
        $qrcode = [];

        if($users->count() > 0){
            for($index = 0; $index < $users->count(); $index++){
                $qrcode[] = "data:image/png;base64," . base64_encode(QrCode::format('png')
                                    ->size(200)->errorCorrection('H')
                                    ->generate($users[$index]->employee_id));
            }
        }

        return response()->json(['users' => $users, 'qrcode' => $qrcode]);
    }

    // Get User By Status
    public function get_user_by_stat(Request $request){
        $user = User::where('status', $request->status)->get();
        return response()->json(['user' => $user]);
    }

    // Edit User
    public function edit_user(Request $request){
        date_default_timezone_set('Asia/Manila');

        $data = $request->all();

        // $password = 'pmi1234' . Str::random(10);
        $password = 'pmi12345';

        if(isset($request->with_email)){
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255|unique:users,name,'. $request->user_id,
                'username' => 'required|string|max:255|unique:users,username,'. $request->user_id,
                'email' => 'required|string|max:255|unique:users,email,'. $request->user_id,
                'user_level_id' => 'required|string|max:255|',
            ]);
        }
        else{
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255|unique:users,name,'. $request->user_id,
                'username' => 'required|string|max:255|unique:users,username,'. $request->user_id,
                'user_level_id' => 'required|string|max:255|',
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }
        else{
            DB::beginTransaction();

            try{
                User::where('id', $request->user_id)
                ->increment('update_version', 1,
                [
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'user_level_id' => $request->user_level_id,
                    'last_updated_by' => Auth::user()->id,
                    'update_version' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::commit();

                return response()->json(['result' => "1"]);
            }
            catch(\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => "0"]);
            }
        }
    }

    public function generate_user_qrcode(Request $request){
        // action: 1-Add, 2-Edit, 3-Generate Only

        // $user = [];
        // if($request->action == "1" || $request->action == "3"){
        //     $user = User::where('employee_id', $request->qrcode)->get();
        // }
        // else if($request->action == "2"){
        //     $user = User::where('employee_id', $request->qrcode)
        //                 ->where('id', '!=', $request->user_id)
        //                 ->get();
        // }

        // $user = User::where('id', $request->user_id)->get();

        // $qrcode = $user[0]->barcode;

        try{
            if(isset($request->qrcode)){
                $user = User::where('employee_id', $request->qrcode)->get();

                $qrcode = QrCode::format('png')
                        ->size(200)->errorCorrection('H')
                        ->generate($request->qrcode);

                return response()->json(['result' => "1", 'qrcode' => "data:image/png;base64," . base64_encode($qrcode), 'user' => $user]);
            }
            else{
                return response()->json(['result' => "0"]);
            }
        }
        catch(\Exception $e){
            return response()->json(['result' => "0"]);
        }
    }

    public function import_user(Request $request)
    {
        $collections = Excel::toCollection(new CSVUserImport, request()->file('import_file'));

        // $password = 'pmi1234' . Str::random(10);
        $password = 'pmi12345';
        $user_level_id = 3;

        DB::beginTransaction();
        try{
            for($index = 1; $index < count($collections[0]); $index++){
                if($collections[0][$index][4] == 0){
                    $user_level_id = 2;
                }
                else{
                    $user_level_id = 3;
                }

                $user_id = User::insertGetId([
                    'name' => $collections[0][$index][0],
                    'username' => $collections[0][$index][1],
                    'email' => $collections[0][$index][2],
                    'employee_id' => $collections[0][$index][3],
                    'password' => Hash::make($password),
                    'position' => $collections[0][$index][4],
                    'user_level_id' => $user_level_id,
                    'is_password_changed' => 0,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'last_updated_by' => Auth::user()->id,
                    'update_version' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                if(trim($collections[0][$index][5]) != ""){
                    OQCStamp::insert([
                        'user_id' => $user_id,
                        'oqc_stamp' => $collections[0][$index][5],
                        'created_by' => Auth::user()->id,
                        'last_updated_by' => Auth::user()->id,
                        'update_version' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            DB::commit();

            return response()->json(['result' => "1"]);
        }
        catch(\Exception $e) {
            DB::rollback();
            return response()->json(['result' => $e]);
        }
    }
    public function get_user_levels(Request $request){
    	$user_levels = UserLevel::all();

    	return response()->json(['user_levels' => $user_levels]);
    }

    public function get_emp_details_by_id(Request $request){


        $hris_data = DB::connection('mysql_systemone')
        ->select("SELECT * FROM vw_employeeinfo WHERE EmpNo = '".$request->empId."'");
        $rapidxUser = RapidXUser::where('employee_number',$request->empId)->first();
        if(count($hris_data) > 0){
            return response()->json(['empInfo' => $hris_data, 'rapidxUser' => $rapidxUser]);
        }
        else{
            $subcon_data = DB::connection('mysql_systemone')
            ->select("SELECT * FROM vw_employeeinfo WHERE EmpNo = '".$request->empId."'");
            return response()->json(['empInfo' => $subcon_data,'rapidxUser' => $rapidxUser]);
        }

    }

    public function get_user_module_access(Request $request){ //nmodify view
        return $userAccessModule =  UserAccessModule::where('users_id',$request->user_id)
       ->get();
        try {
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
