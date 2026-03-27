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
       try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $trainingRequest =  TrainingRequestDetails::where('emp_no',$trainingAttendanceRequest->employeeNo)
            ->with('rapidx_system_one_hris_emp_info','rapidx_system_one_subcon_emp_info')
            ->first();

            if(filled($trainingRequest)){
                $dateNow = now();

                if(filled($trainingRequest->rapidx_system_one_hris_emp_info)){
                    $userHris = $trainingRequest->rapidx_system_one_hris_emp_info ?? '';
                }else{
                    $userHris = $trainingRequest->rapidx_system_one_subcon_emp_info?? '';
                }
                $fullName = $userHris->FirstName.' '.$userHris->LastName;

                $trainingAttendance =  TrainingAttendance::where('rapidx_emp_no',$trainingAttendanceRequest->employeeNo)
                ->where('date',$dateNow->format('Y-m-d'))
                ->with('rapidx_system_one_hris_emp_info','rapidx_system_one_subcon_emp_info')
                ->first();

                $trainingAttendanceIsExists =  TrainingAttendance::where('rapidx_emp_no',$trainingAttendanceRequest->employeeNo)
                ->where('date',$dateNow->format('Y-m-d'))
                ->whereNotNull('time_in')
                ->whereNotNull('time_out')
                ->with('rapidx_system_one_hris_emp_info','rapidx_system_one_subcon_emp_info')
                ->first();

                if(filled($trainingAttendanceIsExists)){
                    return response()->json([
                        'isSuccess' => 'true',
                        'trainingAttendanceIsExists'=>'true',
                        'msg'=> 'Duplicate Record!',
                        'userCollection' => $trainingAttendance ?? [],
                        'fullName'=> $fullName ?? '',
                    ]);
                }
                $trainingAttendanceRequestValidated =[];
                if(blank($trainingAttendance)){ //Save Time In If No Record of Attendance
                    $dateNow = now();
                    $trainingAttendanceRequestValidated['training_request_details_id'] = $trainingRequest->id;
                    $trainingAttendanceRequestValidated['rapidx_emp_no'] =  $trainingRequest->emp_no;
                    $trainingAttendanceRequestValidated['date'] = $dateNow;
                    $trainingAttendanceRequestValidated['time_in'] =  $dateNow->format('H:i:s');
                    $trainingAttendanceRequestValidated['status'] =  'PRESENT';
                    $trainingAttendanceRequestValidated['created_at'] = $dateNow;
                    TrainingAttendance::insert($trainingAttendanceRequestValidated);
                    $timeOrTimeOut = 'TimeIn';
                }else{ //Update Time Out
                    // Find the open attendance record for the current user
                    $attendance = TrainingAttendance::where('rapidx_emp_no',$trainingAttendanceRequest->employeeNo)
                    ->whereNull('time_out')
                    ->latest()
                    ->first();
                    if(filled($attendance)){
                          // Perform the 10-minute check
                        $minTime = Carbon::parse($attendance->time_in)->addMinutes(10);
                        if ($dateNow->lt($minTime)) {
                            return response()->json([
                                'isSuccess' => 'true',
                                'trainingAttendanceIsExists'=>'true',
                                'msg'=> 'Duplicate Record!',
                                'userCollection' => $trainingAttendance ?? [],
                                'fullName'=> $fullName ?? '',
                            ]);
                        }
                    }

                    $trainingAttendanceRequestValidated['time_out'] =  $dateNow->format('H:i:s');
                    $trainingAttendance->where('id',$trainingAttendance->id)
                    ->update($trainingAttendanceRequestValidated);
                    $timeOrTimeOut = 'TimeOut';
                }
            }else{ //No Record of training request
                return response()->json([
                    'isSuccess' => 'false',
                    'trainingAttendanceIsExists'=>'false',
                    'msg' => 'Not Found!',
                ],500);
            }

            DB::commit();
            return response()->json([
                'isSuccess' => 'true',
                'timeOrTimeOut' => $timeOrTimeOut ?? '',
                'trainingAttendanceIsExists'=>'false',
                'msg' => 'Record Save!',
                'userCollection' => $trainingAttendance ?? [],
                'fullName'=> $fullName ?? '',

            ]);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function view_training_attendance(Request $request){
        try {
            $trainingRequestDetails =  TrainingAttendance::
            where('rapidx_emp_no',$request->employeeNo)
            ->with('rapidx_system_one_hris_emp_info','rapidx_system_one_subcon_emp_info')
            ->limit(3)->get();

            return DataTables::of($trainingRequestDetails)
            ->addColumn('fullname', function($user){
                if(filled($user->rapidx_system_one_hris_emp_info)){
                    $userHris = $user->rapidx_system_one_hris_emp_info;
                }
                else{
                    $userHris = $user->rapidx_system_one_subcon_emp_info;
                }
                return $userHris->FirstName.' '.$userHris->LastName;
            })
            ->addColumn('position', function($user){
                if(filled($user->rapidx_system_one_hris_emp_info)){
                    $userHris = $user->rapidx_system_one_hris_emp_info;
                }
                else{
                    $userHris = $user->rapidx_system_one_subcon_emp_info;
                }
                return $userHris->Position ?? '';
            })
            ->rawColumns([
                'fullname',
                'position',
            ])
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
                $result .= '<button class="dropdown-item aViewTrainingAttendance" type="button" training-requests-id="' . $row->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalViewTrainingAttendanceRequest" data-keyboard="false">View</button>';
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
    public function view_training_attendance_request_details(Request $request){
        try {
       $trainingRequests = TrainingAttendance::
        where('training_request_details_id',$request->trainingAttendanceRequest)
       ->whereBetween('date',[$request->fromDate,$request->toDate])
        ->with(
            'rapidx_system_one_hris_emp_info',
            'rapidx_system_one_subcon_emp_info',
            'training_request_details',
        )
        ->get();
        return DataTables::of($trainingRequests)
        ->addColumn('fullname', function($row){
            if(filled($row->rapidx_system_one_hris_emp_info)){
                $userHris = $row->rapidx_system_one_hris_emp_info;
            }
            else{
                $userHris = $row->rapidx_system_one_subcon_emp_info;
            }
            return $userHris->FirstName.' '.$userHris->LastName;
        })
        ->addColumn('training_hours', function($row){
            if(blank($row->time_out)){
                return 'NO TIME OUT RECORD!';
            }
            $in = Carbon::parse($row->time_in);
            $out = Carbon::parse($row->time_out);

            // 1. Get Total Minutes (Best for precise payroll)
            $totalMinutes = $out->diffInMinutes($in);

            // 2. Get Hours as a Decimal (e.g., 8.5 hours)
            $decimalHours = number_format($totalMinutes / 60, 2);

            // 3. Get Human Readable (e.g., "8 hours 30 minutes")
            return $duration = $in->diff($out)->format('%H hours %I minutes');
        })
        ->addColumn('training_hours', function($row){
            if(blank($row->time_out)){
                return'<span class="badge badge-pill badge-danger">NO TIME OUT RECORD!</span>';
            }
            $in = Carbon::parse($row->time_in);
            $out = Carbon::parse($row->time_out);

            // 1. Get Total Minutes (Best for precise payroll)
            $totalMinutes = $out->diffInMinutes($in);

            // 2. Get Hours as a Decimal (e.g., 8.5 hours)
            $decimalHours = number_format($totalMinutes / 60, 2);

            // 3. Get Human Readable (e.g., "8 hours 30 minutes")
            return $duration = $in->diff($out)->format('%H hours %I minutes');
        })
        ->addColumn('action', function($row){
            $result = '<center><div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                        <i class="fa fa-cog"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-right">';
            $result .= '<button class="dropdown-item aEditAttendance" type="button" attendance-id="' . $row->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalTrainingAttendance" data-keyboard="false">Edit</button>';
            // $result .= '<button class="dropdown-item aEditModuleAccess" type="button"  rapidx-emp-no= "'.$user->rapidx_emp_no .'"  user-id="' . $user->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalAddUserModuleAccess" data-keyboard="false">Edit Module</button>';
            $result .= '</div>
                    </div></center>';
            return $result;
        })
        ->rawColumns([
            'status',
            'training_hours',
            'fullname',
            'action',
        ])
        ->make(true);

        } catch (Exception $e) {
            throw $e;
        }
    }
    
}
