<?php

namespace App\Http\Controllers;
use App\Http\Requests\TrainingAttendanceRequest;
use App\Model\TrainingAttendance;
use App\Model\TrainingEndorsement;
use App\Model\TrainingRequest;
use App\Model\TrainingRequestDetails;
use App\Model\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
            ->with('training_request_details')
            ->get();
            // training-requests-id="' . $row->id . '"
        return DataTables::of($trainingRequests)
        ->addColumn('action', function($row){
            $result = '';
            $result .= '<center><div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">';
                $result .= '<button class="dropdown-item aViewTrainingAttendance" type="button" training-requests-id="' . $row->id . '"   training-requests-id="' . $row['training_request_details']. '"  ctrl-no="'.$row->ctrl_number.'"  style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalViewTrainingAttendanceRequest" data-keyboard="false">View</button>';
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
    public function view_training_attendance_request_details_rev1(Request $request){
        $fromDate = $request->fromDate ?? '';
        $toDate = $request->toDate ?? '';
        if($fromDate === '' || $toDate  === ''){
        return datatables()->of(collect([]))
        ->with([
            'totalAbsent'  => 0,
            'totalPresent' => 0,
            // 'error'        => 'Invalid date range provided.'
        ])
        ->make(true);
    }
        // Fallback to today if empty/null, then parse safely
        $startDate = !empty($fromDate) ? Carbon::parse($fromDate)->startOfDay() : now()->startOfDay();
        $endDate   = !empty($toDate)   ? Carbon::parse($toDate)->endOfDay()   : now()->endOfDay();

        // Ensure start date is never after end date to prevent empty/flipped periods
        if ($startDate->gt($endDate)) {
            $startDate = $endDate->copy();
        }

        $trainingId = $request->trainingAttendanceRequest;
        // 1. Fetch training details with eager-loaded nested relationship
        $employees = TrainingRequestDetails::where('training_request_id', $trainingId)
    ->with([
        'training_attendance' => function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('date', [$fromDate, $toDate]);
        },
        'training_endorsement_employee.training_endorsement' // Eager load parent endorsement table
    ])
    ->get();


    // 2. Build the date range
    $period = CarbonPeriod::create($startDate, $endDate);

    // 3. Map over each date in the period x each employee
    $allRows = collect($period)->flatMap(function ($date) use ($employees) {
    $currentDate = $date->toDateString();

    return $employees->map(function ($emp) use ($currentDate) {
        // Safe attendance lookup
        $attendances = collect($emp->training_attendance);
        $attendance = $attendances->first(function ($item) use ($currentDate) {
            $itemDate = is_array($item) ? ($item['date'] ?? null) : ($item->date ?? null);
            return $itemDate ? Carbon::parse($itemDate)->toDateString() === $currentDate : false;
        });

        // Safe check for endorsement date via nested relation: training_endorsement_employee -> training_endorsement
        $endorsements = collect($emp->training_endorsement_employee);
        $hasEndorsementOnDate = $endorsements->contains(function ($endorsementEmp) use ($currentDate) {
            $parentEndorsement = is_array($endorsementEmp)
                ? ($endorsementEmp['training_endorsement'] ?? null)
                : ($endorsementEmp->training_endorsement ?? null);

            if (!$parentEndorsement) {
                return false;
            }

            $eDate = is_array($parentEndorsement)
                ? ($parentEndorsement['date'] ?? null)
                : ($parentEndorsement->date ?? null);

            return $eDate ? Carbon::parse($eDate)->toDateString() === $currentDate : false;
        });

        // Skip employee on this specific date if endorsed
        if ($hasEndorsementOnDate) {
            return null;
        }

        // Extract attendance attributes safely
        $timeIn  = is_array($attendance) ? ($attendance['time_in'] ?? '') : ($attendance->time_in ?? '');
        $timeOut = is_array($attendance) ? ($attendance['time_out'] ?? '') : ($attendance->time_out ?? '');
        $attId   = is_array($attendance) ? ($attendance['id'] ?? '') : ($attendance->id ?? '');
        $status  = is_array($attendance) ? ($attendance['status'] ?? 'ABSENT') : ($attendance->status ?? 'ABSENT');
        $remarks = is_array($attendance) ? ($attendance['remarks'] ?? '') : ($attendance->remarks ?? '');

        // Calculate total duration
        $duration = 'NO RECORD';
        if (!empty($timeIn) && !empty($timeOut)) {
            $in  = Carbon::parse($timeIn);
            $out = Carbon::parse($timeOut);
            $duration = $in->diff($out)->format('%H hours');
        }

        // Action Button
        $button = '<center><div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                        <i class="fa fa-cog"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <button class="dropdown-item aEditAttendance" type="button" attendance-id="' . $attId . '" attendance-details-id="' . $emp->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalTrainingAttendance" data-keyboard="false">Edit</button>
                    </div>
                </div></center>';

        return [
            'emp_no'         => $emp->emp_no,
            'name'           => $emp->name,
            'date'           => $currentDate,
            'training_hours' => $duration,
            'time_in'        => $timeIn ?: null,
            'time_out'       => $timeOut ?: null,
            'status'         => $status ?: 'ABSENT',
            'action'         => $button,
            'remarks'        => $remarks,
        ];
    })->filter(); // Excludes null values where an endorsement match occurred
})->sortByDesc('date');

// 4. Totals for DataTables response
$absentCount  = $allRows->where('status', 'ABSENT')->count();
$presentCount = $allRows->where('status', 'PRESENT')->count();

return datatables()->of($allRows)
    ->with([
        'totalAbsent'  => $absentCount,
        'totalPresent' => $presentCount
    ])
    ->make(true);
    }

    public function view_training_attendance_request_details_rev2(Request $request){
        try {
            date_default_timezone_set('Asia/Manila');

            $fromDate   = Carbon::parse($request->fromDate)->startOfDay();
            $toDate     = Carbon::parse($request->toDate)->startOfDay();
            $trainingId = $request->trainingAttendanceRequest;

            if (blank($fromDate) || blank($toDate) || blank($trainingId)) {
                return datatables()->of(collect([]))
                    ->with([
                        'totalAbsent'  => 0,
                        'totalPresent' => 0
                    ])
                    ->make(true);
            }

            // 1. Fetch all employees without a global endorsement filter.
            //    Scope training_attendance to the requested date range to avoid loading
            //    unnecessary records. Eager-load the full endorsement chain to prevent N+1.
             $employees = TrainingRequestDetails::where('training_request_id', $trainingId)
                ->with([
                    'training_attendance' => function ($query) use ($fromDate, $toDate) {
                        $query->whereBetween('date', [$fromDate, $toDate]);
                    },
                    'training_endorsement_employee.training_endorsement_training_request_id'
                ])
                ->get();

            // 2. Build the date range
            // $period = CarbonPeriod::create($fromDate, $toDate);
            // 2. Create period (use 1 day intervals explicitly)
            $period = CarbonPeriod::create($fromDate, '1 day', $toDate);
            // 3. Generate rows: employee x day matrix with dynamic endorsement exclusion
            return $allRows = collect($period)->flatMap(function ($date) use ($employees) {
                $currentDate = $date->format('Y-m-d');
                // $currentDate = $date->toDateString();

                return $employees->map(function ($emp) use ($currentDate) {
                    // Endorsement exclusion: if any linked training_endorsement has a date
                    // on or before $currentDate, omit this employee for this day and beyond.
                    return $eDate= $emp->training_endorsement_employee->training_endorsement_training_request_id->date ?? null ;
                    return $eDate  <= $currentDate;

                    // return $currentDate;
                   return $isEndorsedByDate = collect($emp->training_endorsement_employee)
                        ->map(function ($endorsementEmp) use ($currentDate) {
                            // return $endorsementEmp;
                        return $parentEndorsement = $endorsementEmp->training_endorsement_training_request_id ?? null;

                            if (!$parentEndorsement) {
                                return false;
                            }

                            $eDate = $parentEndorsement->date ?? null;

                            return $eDate && Carbon::parse($eDate)->toDateString() <= $currentDate;
                        });

                    if ($isEndorsedByDate) {
                        return null;
                    }
    // echo json_encode($isEndorsedByDate);

                    // Safe attendance lookup for this specific date
                    $attendance = collect($emp->training_attendance)->first(function ($item) use ($currentDate) {
                        return ($item->date ?? null) && Carbon::parse($item->date)->toDateString() === $currentDate;
                    });

                    $time_in     = $attendance->time_in ?? '';
                    $time_out    = $attendance->time_out ?? '';
                    $attendanceId = $attendance->id ?? '';

                    $duration = 'NO RECORD';
                    if ($time_in !== '' && $time_out !== '') {
                        $in  = Carbon::parse($time_in);
                        $out = Carbon::parse($time_out);
                        $duration = $in->diff($out)->format('%H hours');
                    }

                    $button = '<center><div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                                <i class="fa fa-cog"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">';
                    $button .= '<button class="dropdown-item aEditAttendance" type="button" attendance-id="' . $attendanceId . '" attendance-details-id="' . $emp->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalTrainingAttendance" data-keyboard="false">Edit</button>';
                    $button .= '</div>
                            </div></center>';

                    return [
                        'emp_no'         => $emp->emp_no,
                        'name'           => $emp->name,
                        'date'           => $currentDate,
                        'isEndorsedByDate'           => $isEndorsedByDate,
                        'training_hours' => $duration,
                        'time_in'        => $attendance->time_in ?? null,
                        'time_out'       => $attendance->time_out ?? null,
                        'status'         => $attendance->status ?? 'ABSENT',
                        'action'         => $button,
                        'remarks'        => $attendance->remarks ?? '',
                    ];
                })->filter(); // Remove null rows (endorsed employees excluded for this date)
            })->sortByDesc('date');
            // 4. Calculate totals for DataTables response
            $absentCount  = $allRows->where('status', 'ABSENT')->count();
            $presentCount = $allRows->where('status', 'PRESENT')->count();

            return datatables()->of($allRows)
                ->with([
                    'totalAbsent'  => $absentCount,
                    'totalPresent' => $presentCount
                ])
                ->make(true);

        } catch (Exception $e) {
            throw $e;
        }
    }
    public function view_training_attendance_request_details(Request $request){
        try {

            $trainingId = $request->trainingAttendanceRequest;
            $fromDate = $request->fromDate;
            $toDate = $request->toDate;
            // $trainingEndorsement = TrainingEndorsement::where('training_request_id',$trainingId)->first('date');
            // $trainingEndorsementDate = $trainingEndorsement->date ?? '';
            // $trainingEndorsementDateValid =$trainingEndorsementDate <= $toDateSelected;
            // $toDate = $trainingEndorsementDate != '' ? $trainingEndorsementDate : $toDateSelected;
            if ( blank($fromDate) || blank($toDate) || blank($trainingId) ) {
                return datatables()->of(collect([]))
                    ->with([
                        'totalAbsent' => 0,
                        'totalPresent' => 0
                    ])
                    ->make(true);
            }
                // Get the "Expected" list of employees
            $employees = TrainingRequestDetails::where('training_request_id', $trainingId)
            ->with(['training_attendance'])
            ->get()->sortBy(function($detail) {
                return $detail->training_attendance['date'] ?? '0000-00-00';
            });
            //THe TO date will be based on Training Endorsement Date

            // $employees = TrainingRequestDetails::where('training_request_id', $trainingId)
            // ->whereDoesntHave('training_endorsement_employee') // Ensures relation is NOT null in database
            // ->with([
            //     'training_attendance',
            //     'training_endorsement_employee'
            // ])
            // ->get()
            // ->sortBy(function($detail) {
            //     return $detail->training_attendance['date'] ?? '0000-00-00';
            // });

            //Create the date range
            $period = CarbonPeriod::create($fromDate, $toDate);

            //Generate all rows (Employees x Days)
            $allRows = collect($period)->flatMap(function ($date) use ($employees) {
                $currentDate = $date->toDateString();

                return $employees->map(function ($emp) use ($currentDate) {
                    // Find specific attendance for this employee on this day
                    // We use optional() or collect() to prevent "contains on null" errors
                    $attendance = collect($emp->training_attendance)->first(function ($item) use ($currentDate) {
                        return Carbon::parse($item->date)->toDateString() == $currentDate;
                    });

                    $time_in =$attendance->time_in ?? '';
                    $time_out =$attendance->time_out ?? '';
                    $attendanceId =$attendance->id ?? '';
                    $duration = 'NO RECORD';
                    if($time_in !='' && $time_out !=''){
                        $in = Carbon::parse($attendance->time_in);
                        $out = Carbon::parse($attendance->time_out);


                        //Get Total Minutes (Best for precise payroll)
                        $totalMinutes = $out->diffInMinutes($in);

                        //Get Hours as a Decimal (e.g., 8.5 hours)
                        $decimalHours = number_format($totalMinutes / 60, 2);

                        //Get Human Readable (e.g., "8 hours 30 minutes")
                        $duration = $in->diff($out)->format('%H hours');
                    }
                    $button = '<center><div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                                <i class="fa fa-cog"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">';
                    $button .= '<button class="dropdown-item aEditAttendance" type="button" attendance-id="' . $attendanceId . '" attendance-details-id="' . $emp->id. '"style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalTrainingAttendance" data-keyboard="false">Edit</button>';
                    $button .= '</div>
                            </div></center>';
                    return [
                        'emp_no'   => $emp->emp_no,
                        'name'     => $emp->name,
                        'date'     => $currentDate,
                        'training_hours'     => $duration,
                        'time_in'     => $attendance->time_in ?? NULL,
                        'time_out'     => $attendance->time_out ?? NULL,
                        'status'   => $attendance->status ?? 'ABSENT',
                        'action'   => $button,
                        'remarks'   => $attendance->remarks ?? '',
                    ];
                });
            })->sortBy([
                ['date', 'desc'],
            ]);
            // Calculate the total absent count from the generated rows
            $absentCount = $allRows->where('status', 'ABSENT')->count();
            $presentCount = $allRows->where('status', 'PRESENT')->count();
            return datatables()->of($allRows)
            ->with([
                'totalAbsent' => $absentCount,
                'totalPresent' => $presentCount
            ])
            ->make(true);
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function get_training_attendance_by_id(Request $request){
        try {
            $trainingAttendance = TrainingAttendance::where('id',$request->getTrainingAttendanceById)->first();
            return response()->json([
                'isSuccess' => 'true',
                'trainingAttendance' => $trainingAttendance,
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function save_training_attendance(TrainingAttendanceRequest $trainingAttendanceRequest){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $trainingAttendanceRequestValidated =[];
            $trainingAttendanceRequestValidated['status'] =  $trainingAttendanceRequest->status;
            if($trainingAttendanceRequest->status === 'ABSENT'){
                $trainingAttendanceRequestValidated['time_in'] =  NULL;
                $trainingAttendanceRequestValidated['time_out'] = NULL;
                $trainingAttendanceRequestValidated['remarks'] =  $trainingAttendanceRequest->remarks;
            }else{
                $trainingAttendanceRequestValidated['time_in'] =  $trainingAttendanceRequest->time_in;
                $trainingAttendanceRequestValidated['time_out'] =  $trainingAttendanceRequest->time_out;
                $trainingAttendanceRequestValidated['remarks'] =  '';
            }
            if( filled($trainingAttendanceRequest['training_attendances_id']) ){
                // return 'true';
                TrainingAttendance::where('id',$trainingAttendanceRequest->training_attendances_id)
                ->update($trainingAttendanceRequestValidated);
            }else{
                // return 'false';
                $trainingAttendanceRequestValidated['date'] =  $trainingAttendanceRequest->date;
                $trainingAttendanceRequestValidated['rapidx_emp_no'] =  $trainingAttendanceRequest->rapidx_emp_no;
                $trainingAttendanceRequestValidated['training_request_details_id'] =  $trainingAttendanceRequest->training_request_details_id;
                // return $trainingAttendanceRequestValidated;
                TrainingAttendance::insert($trainingAttendanceRequestValidated);
            }
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
