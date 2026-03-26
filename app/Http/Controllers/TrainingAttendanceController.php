<?php

namespace App\Http\Controllers;
use App\Http\Requests\TrainingAttendanceRequest;
use App\Model\TrainingAttendance;
use App\Model\TrainingRequest;
use App\Model\TrainingRequestDetails;
use App\Model\User;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TrainingAttendanceController extends Controller
{
    public function save_attendance(TrainingAttendanceRequest $trainingAttendanceRequest){
    //    $user =  User::with('rapidx_system_one_hris_emp_info','rapidx_system_one_subcon_emp_info')->where('rapidx_emp_no',$request->employeeNo)->first();
       
       try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $trainingRequest =  TrainingRequestDetails::where('emp_no',$trainingAttendanceRequest->employeeNo)->first();
            
            if(filled($trainingRequest)){
                $dateNow = now();

                $trainingAttendanceIsExists =  TrainingAttendance::where('rapidx_emp_no',$trainingAttendanceRequest->employeeNo)
                ->where('date',$dateNow)
                ->whereNotNull('time_in')
                ->whereNotNull('time_out')
                ->first();

                if(filled($trainingAttendanceIsExists)){
                    return response()->json([
                        'isSuccess' => 'true',
                        'trainingAttendanceIsExists'=>'true',
                        'msg'=> 'Duplicate Record!'
                    ]);
                }

                $trainingAttendance =  TrainingAttendance::where('rapidx_emp_no',$trainingAttendanceRequest->employeeNo)
                ->where('date',$dateNow->format('Y-m-d'))
                ->first();

                $trainingAttendanceRequestValidated =[];
                if(blank($trainingAttendance)){ //Save Time In If No Record of Attendance
                    $trainingAttendance =  TrainingAttendance::where('rapidx_emp_no',$trainingAttendanceRequest->employeeNo)->first();
                
                    $dateNow = now();
                    $trainingAttendanceRequestValidated['training_request_details_id'] = $trainingRequest->id;
                    $trainingAttendanceRequestValidated['rapidx_emp_no'] =  $trainingRequest->emp_no;
                    $trainingAttendanceRequestValidated['date'] = $dateNow;
                    $trainingAttendanceRequestValidated['time_in'] =  $dateNow->format('H:i:s');
                    $trainingAttendanceRequestValidated['status'] =  'PRESENT'; 
                    $trainingAttendanceRequestValidated['created_at'] = $dateNow; 
                    TrainingAttendance::insert($trainingAttendanceRequestValidated);
                    
                }else{ //Update Time Out
                    // Find the open attendance record for the current user
                    $attendance = TrainingAttendance::where('rapidx_emp_no',$trainingAttendanceRequest->employeeNo)
                    ->whereNull('time_out')
                    ->latest()
                    ->first();

                    if (!$attendance) {
                        return back()->with('error', 'No active clock-in found.');
                    }

                    // Perform the 10-minute check
                    $minTime = Carbon::parse($attendance->clock_in)->addMinutes(10);

                    if ($dateNow->lt($minTime)) {
                        $secondsLeft = $dateNow->diffInSeconds($minTime);
                        $minutesLeft = ceil($secondsLeft / 60);
                        
                        return response()->json([
                            'isSuccess' => 'true',
                            'trainingAttendanceIsExists'=>'true',
                            'msg'=> 'Duplicate Record!'
                        ]);
                    }
                        $trainingAttendanceRequestValidated['time_out'] =  $dateNow->format('H:i:s');
                        $trainingAttendance->where('id',$trainingAttendance->id)
                        ->update($trainingAttendanceRequestValidated);
                }
            }else{ //No Record of training request
                return response()->json([
                    'isSuccess' => 'false',
                    'trainingAttendanceIsExists'=>'false',
                    'msg' => 'Error: Please register the user to the training request!',
                ],500);
            }
            DB::commit();
            return response()->json([
                'isSuccess' => 'true',
                'trainingAttendanceIsExists'=>'false',
                'msg' => 'Record Save!',
            ]);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function view_training_attendance(Request $request){
        try {
        return $trainingRequests = TrainingRequest::
            where('logdel', 0)
            ->get();
        return DataTables::of($trainingRequests)
        ->addColumn('action', function($row){
            $result = '';
            $result .= '<center><div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">';
                $result .= '<button class="dropdown-item aViewTrainingAttendance" type="button" user-id="' . $row->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalViewTrainingAttendanceRequest" data-keyboard="false">View</button>';
                // $result .= '<button class="dropdown-item aEditModuleAccess" type="button"  rapidx-emp-no= "'.$row->rapidx_emp_no .'"  user-id="' . $row->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalAddUserModuleAccess" data-keyboard="false">View</button>';
                $result .= '</div>
                        </div></center>';
            return $result;
        })
        ->addColumn('status', function($row){
            $result = '';
            return $result;
        })
        ->rawColumns(['action','status'])
        ->make(true);

        } catch (Exception $e) {
            throw $e;
        }
    }
    public function view_training_attendance_summary(Request $request){
        try {
           $trainingRequests = TrainingRequest::
            where('logdel', 0)
            ->get();
        return DataTables::of($trainingRequests)
        ->addColumn('action', function($row){
            $result = '';
            $result .= '<center><div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">';
                $result .= '<button class="dropdown-item aViewTrainingAttendance" type="button" user-id="' . $row->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalViewTrainingAttendanceRequest" data-keyboard="false">View</button>';
                // $result .= '<button class="dropdown-item aEditModuleAccess" type="button"  rapidx-emp-no= "'.$row->rapidx_emp_no .'"  user-id="' . $row->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalAddUserModuleAccess" data-keyboard="false">View</button>';
                $result .= '</div>
                        </div></center>';
            return $result;
        })
        ->addColumn('status', function($row){
            $result = '';
            return $result;
        })
        ->rawColumns(['action','status'])
        ->make(true);

        } catch (Exception $e) {
            throw $e;
        }
    }
    public function view_training_attendance_request(Request $request){
        try {
        /*
            emp_no
            id
            training_request_id
         */
        $trainingRequests = TrainingRequestDetails::with('emp_no_system_one_hris_emp_info',
        'emp_no_system_one_subcon_emp_info')
        // where('training_request_id',$request->trainingRequestsId)
        ->get();
        return DataTables::of($trainingRequests)
        ->addColumn('action', function($row){
            return $result = '';
            $result .= '<center><div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">';
                $result .= '<button class="dropdown-item aViewTrainingAttendanceRequest" type="button" training-request-details-id="' . $row->id . '"  training-request-id="' . $row->training_request_id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#" data-keyboard="false">View</button>';
                $result .= '</div>
                        </div></center>';
            return $result;
        })
        ->addColumn('status', function($row){
            $result = '';
            return $result;
        })
        ->rawColumns(['action','status'])
        ->make(true);

        } catch (Exception $e) {
            throw $e;
        }
    }
}
