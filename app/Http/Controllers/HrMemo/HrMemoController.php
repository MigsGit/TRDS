<?php

namespace App\Http\Controllers\HrMemo;
use App\Http\Controllers\Controller;
use DataTables;
// use Mail;
use Illuminate\Support\Facades\Mail;

use App\RapidXUser;
use App\Model\Hr\HrMemo;
use App\Model\Hr\HrMemoEmailRecipients;
use App\Model\Hr\HrMemoTraineeDetails;
use App\Model\Hr\HrMemoTraineeCategoryDetails;
use App\Exports\InspectorSkillChart;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class HrMemoController extends Controller
{
    private function actionButton($class, $icon, $id, $extraClass = '', $approval = false, $remarks = ''){
        $remarksSafe = htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8');
        return "<button class='btn {$class} btn-sm {$extraClass}' data-id='{$id}' data-approval='{$approval}' data-remarks=\"{$remarksSafe}\">
                    <i class='fa-solid {$icon}'></i>
                </button>";
    }

    public function viewHrMemoInfo(Request $request){
        date_default_timezone_set('Asia/Manila');
        $globalUser = session('global_user');
        // return $globalUser;
        $user_access = explode(',', $globalUser->user_modules_id);

        $hr_memo_details = HrMemo::with([
            'prepared_by_info',
            'received_by_info',
            'noted_by_info',
            'email_recipients.rapidx_user',
            'trainee_details.emp_exam_details.exam_info',
            'trainee_details.hris_emp_info',
            'trainee_details.subcon_emp_info',
            ])->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        // return $hr_memo_details;
        
        // foreach($hr_memo_details as $memo_detail){
        //     foreach($memo_detail->trainee_details as $td){
        //         if ($td->employment_type == 1) {
        //             // HRIS employee
        //             $td->load(['hris_emp_info' => function ($q) {
        //                 $q->select(
        //                     'vw_employeeinfo.*',
        //                 );
        //             }]);
        //         } else {
        //             // Subcon employee
        //             $td->load(['subcon_emp_info' => function ($q) {
        //                 $q->select(
        //                     'vw_employeeinfo.*',
        //                 );
        //             }]);
        //         }
        //     }
        // }
        
        return DataTables::of($hr_memo_details)
        ->addColumn('action', function($hr_memo_details) use ($user_access, $globalUser){
            $result = "";   
            $result .= "<center>";

            $canApproveHR  = $globalUser->rapidx_emp_id == $hr_memo_details->noted_by || $globalUser->user_level_id == 1; //Noted By Person & SuperAdmin Userlevel only is allowed
            // $canApproveHR  = in_array(9, $user_access);
            $canApproveTU  = in_array(2, $user_access) || $globalUser->user_level_id == 1;

            $id = $hr_memo_details->id;

            $isPending   = $hr_memo_details->status == 1;
            $isCancelled = $hr_memo_details->status == 2;
            $isForHRApproval = $hr_memo_details->status == 3;
            $isHRDisapproved = $hr_memo_details->status == 4;
            $isForTUReceiving = $hr_memo_details->status == 5;
            // $isTUReceived = $hr_memo_details->status == 6;
            $isTUDisapproved = $hr_memo_details->status == 7;

            if($isPending){
                // if($canManage){
                    $result .= $this->actionButton('btn-secondary btnEdit', 'fas fa-edit', $id, 'mr-1');
                    $result .= $this->actionButton('btn-success btnFinalSubmit', 'fas fa-check-square', $id, 'mr-1');
                    $result .= $this->actionButton('btn-danger btnDisable', 'fas fa-ban', $id);
                // }else{
                //     $result .= $this->actionButton('btn-info btnView', 'fa-eye', $id, 'mr-1');
                // }
            } else if ($isCancelled){
                $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
                $result .= $this->actionButton('btn-success btnEnable', 'fas fa-undo', $id);
            }else if($isForHRApproval){
                if($canApproveHR){
                    $result .= $this->actionButton('btn-success btnView', 'fas fa-check-square', $id, 'mr-1', 'true');
                }else{
                    $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
                }
            }else if ($isForTUReceiving){
                if($canApproveTU){
                    $result .= $this->actionButton('btn-success btnView', 'fas fa-check-square', $id, 'mr-1', 'true');
                }else{
                    $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
                }
            }else if ($isHRDisapproved || $isTUDisapproved){
                $result .= $this->actionButton('btn-secondary btnEdit', 'fas fa-edit', $id, 'mr-1');
                // $remarksSafe = json_encode($hr_memo_details->remarks);
                $result .= $this->actionButton('btn-danger btnViewRemarks', 'fas fa-comment-dots', $id, 'mr-1', 'false', $hr_memo_details->remarks); //CLARK TESTING
                $result .= $this->actionButton('btn-success btnFinalSubmit', 'fas fa-check-square', $id, 'mr-1');
            }else{
                $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
            }

            $result .= "</center>";
            return $result;
        })
        ->addColumn('trainee_names', function($hr_memo_details){
            
            $trainee_names = [];

            foreach($hr_memo_details->trainee_details as $td){
                if ($td->employment_type == 1) {
                    $trainee_names[] = $td->hris_emp_info->EmpName;
                } else {
                    $trainee_names[] = $td->subcon_emp_info->EmpName;
                }
            }

            $result = implode(', ', $trainee_names);
            return $result;
        })
        ->addColumn('status_label', function($hr_memo_details){
            $result = "";
            $result .= "<center>";

            if($hr_memo_details->status == 1){
                $result .= "<span class='badge rounded-pill bg-info'>Pending</gspan>";
            }else if($hr_memo_details->status == 2){
                $result .= "<span class='badge rounded-pill bg-secondary'>Cancelled</span>";
            }else if($hr_memo_details->status == 3){
                $result .= "<span class='badge rounded-pill bg-primary'>For HR Approval</span>";
            }else if($hr_memo_details->status == 4){
                $result .= "<span class='badge rounded-pill bg-danger'>HR Disapproved</span>";
            }else if($hr_memo_details->status == 5){
                $result .= "<span class='badge rounded-pill bg-primary'>For TU Receiving</span>";
            }else if($hr_memo_details->status == 6){
                $result .= "<span class='badge rounded-pill bg-success'>TU Received</span>";
            }else if($hr_memo_details->status == 7){
                $result .= "<span class='badge rounded-pill bg-danger'>TU Disapproved</span>";
            }else{
                $result .= "<span class='badge rounded-pill bg-info'>N/A</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->addColumn('reason_label', function($hr_memo_details){
            $result = "";
            $result .= "<center>";

            if($hr_memo_details->reason == 1){
                $result .= "<span'>Newly Hired</span>";
            }else if($hr_memo_details->reason == 2){
                $result .= "<span'>Maternity Leave</span>";
            }else if($hr_memo_details->reason == 3){
                $result .= "<span'>Sick Leave</span>";
            }else if($hr_memo_details->reason == 4){
                $result .= "<span'>Vacation Leave</span>";
            }else if($hr_memo_details->reason == 5){
                $result .= "<span'>Promoted</span>";
            }else if($hr_memo_details->reason == 6){
                $result .= "<span'>Transferred</span>";
            }else if($hr_memo_details->reason == 7){
                $result .= "<span'>Regularization</span>";
            }else{
                $result .= "<span'>N/A</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->addColumn('prepared_by_label', function($hr_memo_details){
            $prepared_by_name = $hr_memo_details->prepared_by_info->name ?? (object) ['name' => 'N/A'];
            $created_at_date = $hr_memo_details->created_at ? date("M j, Y h:i:s A", strtotime($hr_memo_details->created_at)) : '---';

            $result = "
                <center>
                    <strong>{$prepared_by_name}<strong><br>
                    <span class='badge badge-success'>Applied</span><br>
                    <small class='text-muted'>$created_at_date</small>
                </center>";

            return $result;
        })
        ->addColumn('received_by_label', function($hr_memo_details){
            $received_by_name = $hr_memo_details->received_by_info->name ?? 'Not Yet Received';
            $received_date = !empty($hr_memo_details->received_date) ? date('M j, Y h:i:s A', strtotime($hr_memo_details->received_date)) : '---';

            if($hr_memo_details->status < 5){
                $received_status = 'N/A';
                $badge_status = 'badge-secondary';
            }else if($hr_memo_details->status >= 5 && $hr_memo_details->status <= 6){
                $received_status = !empty($hr_memo_details->received_date) ? 'Received' : 'Pending';
                $badge_status = !empty($hr_memo_details->received_date) ? 'badge-success' : 'badge-warning';
            }else{
                $received_status = 'Disapproved';
                $badge_status = 'badge-danger';
            }

            // $received_status = !empty($hr_memo_details->received_date) ? 'Received' : 'Pending';
            // $badge_status = !empty($hr_memo_details->received_date) ? 'badge-success' : 'badge-secondary';
                
            $result = "
                <center>
                    <strong>{$received_by_name}<strong><br>
                    <span class='badge {$badge_status}'>{$received_status}</span><br>
                    <small class='text-muted'>$received_date</small>
                </center>";

            return $result;
        })
        ->rawColumns(['action', 'trainee_names','reason_label', 'status_label', 'prepared_by_label', 'received_by_label']) // Specify the columns that contain HTML
        ->make(true);
    }

    public function getEmailRecipientsDropdownDetails(Request $request)
    {
        $emails = RapidXUser::select('id', 'name', 'email')
                ->whereNotNull('email')
                ->where('user_stat', 1)
                ->when($request->hr_only == 'true', function ($query) use ($request) {
                    // return $query->where('department_id', 29); //ALL HRD
                    return $query->whereIn('employee_number', ['1810', 'T078']); //ESM & GAC only
                })
                ->get();
        return response()->json($emails);
    }

    public function getEmpNoDropdownDetails(Request $request)
    {
        $hrisQuery = "
            SELECT
                pkid,
                EmpNo,
                1 AS emp_type
            FROM tbl_EmployeeInfo
            WHERE EmpStatus = 1
        ";

        $subconQuery = "
            SELECT
                pkid,
                EmpNo,
                2 AS emp_type
            FROM tbl_EmployeeInfo
            WHERE EmpStatus = 1
        ";

        $hris = DB::connection('mysql_systemone')->select($hrisQuery);
        $subcon = DB::connection('mysql_subcon')->select($subconQuery);

        $merged = array_merge($hris, $subcon);

        return response()->json($merged);
    }

    public function getEmployeeDetails(Request $request)
    {
        $empNo = $request->employee_number;

        $hrisQuery = "
            SELECT
                'db_hris' AS source,
                CONCAT(tbl_EmployeeInfo.FirstName, ' ', tbl_EmployeeInfo.LastName) AS EmpName,
                tbl_EmployeeInfo.DateHired,
                CONCAT_WS(' - ', tbl_Training.PeriodFrom, tbl_Training.PeriodTo) AS fromto,
                tbl_Training.Venue,
                vw_Trainee.Remarks,
                tbl_Training.Title,
                tbl_Position.Position AS Position,
                tbl_Department.Department AS Department,
                tbl_Section.Section AS Section,
                tbl_Division.Division AS Division
            FROM vw_Trainee
            INNER JOIN tbl_EmployeeInfo ON vw_Trainee.fkEmployee = tbl_EmployeeInfo.pkid AND tbl_EmployeeInfo.EmpStatus = 1
            INNER JOIN tbl_Position ON tbl_EmployeeInfo.fkPosition = tbl_Position.pkid
            INNER JOIN tbl_Section ON tbl_EmployeeInfo.fkSection = tbl_Section.pkid
            INNER JOIN tbl_Department ON tbl_EmployeeInfo.fkDepartment = tbl_Department.pkid
            INNER JOIN tbl_Division ON tbl_EmployeeInfo.fkDivision = tbl_Division.pkid
            INNER JOIN tbl_Training ON vw_Trainee.fkTraining = tbl_Training.pkid
        ";

        $subconQuery = "
            SELECT
                'db_subcon' AS source,
                CONCAT(tbl_EmployeeInfo.FirstName, ' ', tbl_EmployeeInfo.LastName) AS EmpName,
                tbl_EmployeeInfo.DateHired,
                CONCAT_WS(' - ', tbl_Training.PeriodFrom, tbl_Training.PeriodTo) AS fromto,
                tbl_Training.Venue,
                COALESCE(vw_Trainee.Remarks, 'No Record') AS Remarks,
                tbl_Training.Title,
                tbl_Position.Position AS Position,
                tbl_Department.Department AS Department,
                tbl_Section.Section AS Section,
                tbl_Division.Division AS Division
            FROM tbl_EmployeeInfo
            LEFT JOIN db_hris.vw_Trainee ON vw_Trainee.fkEmployee = tbl_EmployeeInfo.pkid AND tbl_EmployeeInfo.EmpStatus = 1
            LEFT JOIN db_hris.tbl_Training ON vw_Trainee.fkTraining = tbl_Training.pkid
            INNER JOIN db_hris.tbl_Position ON tbl_EmployeeInfo.fkPosition = tbl_Position.pkid
            INNER JOIN db_hris.tbl_Section ON tbl_EmployeeInfo.fkSection = tbl_Section.pkid
            INNER JOIN db_hris.tbl_Department ON tbl_EmployeeInfo.fkDepartment = tbl_Department.pkid
            INNER JOIN db_hris.tbl_Division ON tbl_EmployeeInfo.fkDivision = tbl_Division.pkid
        ";

        $trainingVenueQuery = "
            SELECT
                Venue
            FROM tbl_training_venue
            WHERE Venue != '' AND logdel = 0
            ORDER BY Venue ASC
        ";

        // CASE 1: Employee number exists
        // if (!empty($empNo)) {
        
            $training_venue = DB::connection('mysql_systemone')->select($trainingVenueQuery);

            $hris = DB::connection('mysql_systemone')
                ->select($hrisQuery . " WHERE tbl_EmployeeInfo.EmpNo = ? LIMIT 1", [$empNo]);
                

            if (!empty($hris)) {
                return response()->json([
                    'emp_details' => $hris,
                    'training_venue' => $training_venue
                ]);
            }

            // fallback to subcon
            $subcon = DB::connection('mysql_subcon')
                ->select($subconQuery . " WHERE tbl_EmployeeInfo.EmpNo = ? LIMIT 1", [$empNo]);

            return response()->json([
                'emp_details' => $subcon,
                'training_venue' => $training_venue
            ]);
        // }
    }

    public function getTrainingVenueDropdownDetails(Request $request)
    {
        $trainingVenueQuery = "
            SELECT
                Venue
            FROM tbl_training_venue
            WHERE Venue != '' AND logdel = 0
            ORDER BY Venue ASC
        ";

        $training_venue = DB::connection('mysql_systemone')->select($trainingVenueQuery);

        return response()->json([
            'training_venue' => $training_venue
        ]);
    }

    public function addHrMemoInfo(Request $request){
        date_default_timezone_set('Asia/Manila');

        $validation = array(
            'subject' => 'required',
            'from' => 'required',
            'classification' => 'required',
            'reason' => 'required',
            'date_filed' => 'required',
            'to' => 'required',
            'cc' => 'required',
            'trainee_details' => 'required'
        );

        $data = $request->all();
        $validator = Validator::make($data, $validation);

        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }else{
            DB::beginTransaction();

            try{
                //Control Number Generation
                $lastest_id = HrMemo::whereNull('deleted_at')->latest('id')->first();
                if($lastest_id == null){
                    $counter = 1;
                }else{
                    $last_control_no = $lastest_id->document_no;
                    $last_control_no = explode("-", $last_control_no);
                    $dateCode = date('ym'); // month + year (2 digits)
                    if($last_control_no[1] == $dateCode){
                        $counter = $last_control_no[2];
                        $counter++;
                    }else{
                        $counter = 1;
                    }
                }

                if(strlen($counter) == 1){
                    $digit_prefix = '00';
                }else if(strlen($counter) == 2){
                    $digit_prefix = '0';
                }

                $hr_memo_data_array = array(
                    'classification' => $request->classification,
                    'reason' => $request->reason,
                    'from' => $request->from,
                    'subject' => $request->subject,
                    'date_filed' => $request->date_filed,
                    'prepared_by' => $request->prepared_by,
                    'noted_by' => $request->noted_by,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );

                if(isset($request->hr_memo_id)){ // EDIT
                    $hr_memo_id = $request->hr_memo_id;

                    HrMemo::where('id', $request->hr_memo_id)
                    ->update($hr_memo_data_array);

                }else{ // ADD
                    $document_no = 'HRS TRAINING-'.date('ym').'-'.$digit_prefix.$counter;
                    $hr_memo_data_array['document_no'] = $document_no;
                    $hr_memo_id = HrMemo::insertGetId($hr_memo_data_array);
                }

                // DELETE OLD HrMemo Email Recipients ON UPDATE
                HrMemoEmailRecipients::where('hr_memo_id', $request->hr_memo_id)->delete();
                // SAVE NEW HrMemo Email Recipients
                $to = [];
                $cc = [];

                foreach ($request->to as $toUserId){
                    $to[] = [
                        'hr_memo_id' => $hr_memo_id,
                        'user_id' => $toUserId,
                        'type' => 'to'
                    ];
                }

                foreach ($request->cc as $ccUserId){
                    $cc[] = [
                        'hr_memo_id' => $hr_memo_id,
                        'user_id' => $ccUserId,
                        'type' => 'cc'
                    ];
                }

                HrMemoEmailRecipients::insert($to);
                HrMemoEmailRecipients::insert($cc);

                // DELETE OLD HrMemo Trainee Details ON UPDATE
                HrMemoTraineeDetails::where('hr_memo_id', $request->hr_memo_id)->delete();

                // DELETE OLD HrMemo Trainee Category Details ON UPDATE
                HrMemoTraineeCategoryDetails::where('hr_memo_id', $request->hr_memo_id)->delete();

                $trainees = json_decode($request->trainee_details, true);
                if ($trainees){
                    foreach ($trainees as $td) {
                        // SAVE NEW HrMemo Trainee Details
                        $trainee_detail_id = HrMemoTraineeDetails::insertGetId([
                                                'hr_memo_id'          => $hr_memo_id,
                                                'hris_id'             => $td['action']['emp_id'],
                                                'employment_type'     => $td['action']['emp_type'],
                                                'employee_no'         => $td['emp_no'],
                                                'endorsement_date'    => $td['endorsement_date']
                                            ]);

                        foreach ($td['exam_details'] as $ed) {
                            // SAVE NEW HrMemo Trainee Category Details
                            HrMemoTraineeCategoryDetails::insert([
                                'hr_memo_id'          => $hr_memo_id,
                                'trainee_details_id'  => $trainee_detail_id,
                                'date_start'          => $td['date_start'],
                                'date_end'            => $td['date_end'],
                                'category'            => $ed['exam_title'],
                                'objective'           => $ed['objective'],
                                'trainor'             => $td['trainor'],
                                'type_of_training'    => $td['type_of_training'],
                                'result'              => $ed['result'],
                                'training_venue'      => $td['training_venue'],
                                'training_remarks'    => $ed['remarks']
                            ]);
                        }
                    }
                }

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Hr Memo information saved successfully.']);
            }catch(Exemption $e){
                DB::rollback();
                return $e;
            }
        }
    }

    public function getHrMemoById(Request $request){
        $hrMemo = HrMemo::with([
            'email_recipients.rapidx_user',
            'trainee_details.emp_exam_details.exam_info',
            'trainee_details',  // Load trainee_details first
            'prepared_by_info',
        ])
        ->where('id', $request->id)
        ->first();

        // return $hrMemo;
        // if ($hrMemo && $hrMemo->trainee_details) {
            foreach($hrMemo->trainee_details as $td){
                if ($td->employment_type == 1) {
                    // HRIS employee
                    $td->load(['hris_emp_info' => function ($q) {
                        // $q->join('vw_Trainee', 'vw_employeeinfo.pkid', '=', 'vw_Trainee.fkEmployee')
                        // ->join('tbl_Training', 'vw_Trainee.fkTraining', '=', 'tbl_Training.pkid')
                        $q->select(
                            'vw_employeeinfo.*',
                            // 'tbl_Training.Venue as Venue'
                        );
                    }]);
                } else {
                    // Subcon employee
                    $td->load(['subcon_emp_info' => function ($q) {
                        // $q->join('vw_Trainee', 'vw_employeeinfo.pkid', '=', 'vw_Trainee.fkEmployee')
                        // ->join('tbl_Training', 'vw_Trainee.fkTraining', '=', 'tbl_Training.pkid')
                        $q->select(
                            'vw_employeeinfo.*',
                            // 'tbl_Training.Venue as Venue'
                        );
                    }]);
                }
            }
        // }

        return response()->json($hrMemo);
    }

    public function updateHrMemoStatus(Request $request){
        DB::beginTransaction();
        $globalUser = session('global_user');
        date_default_timezone_set('Asia/Manila');

        try {
            $memo = HrMemo::findOrFail($request->id);
            $memo->status = $request->new_status;
            $memo->remarks = $request->remarks;
            $request->new_status == 6 ? $memo->received_by = $globalUser->rapidx_emp_id : null;
            $request->new_status == 6 ? $memo->received_date = date('Y-m-d H:i:s') : null;
            $memo->save();

            DB::commit(); // ✅ commit here

            return response()->json([
                'success' => true,
                'new_status' => $memo->status,
                // 'remarks' => $memo->remarks,
                'message' => 'Hr Memo status updated successfully.'
            ]);
        } catch (\Throwable $e) { // ✅ catch everything including DB errors
            DB::rollBack(); // ✅ rollback only if it fails

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Hr Memo status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUsers(Request $request){
        $users = User::where('status', 0)->get();
        return response()->json(['users_data' => $users]);
    }

    public function sendHrMemoMail(Request $request){
        $hr_memo = HrMemo::with(['prepared_by_info', 'noted_by_info', 'email_recipients.rapidx_user'])->where('id', $request->hr_memo_id)->whereNull('deleted_at')->first();
        // return $hr_memo->noted_by_info->email;
        // $data = ['application' => $hr_memo, 'approver_details' => $approver_details];
        $send_hr_to = $hr_memo->noted_by_info->email;
        // $send_hr_cc = $hr_memo->prepared_by_info->email;
        $send_hr_cc = [$hr_memo->prepared_by_info->email, 'cdcasuyon@pricon.ph'];

        $send_tu_to = [];
        $send_tu_cc = [];

        foreach ($hr_memo->email_recipients as $recipient){
            if($recipient->type == 'to'){
                $send_tu_to[] = $recipient->rapidx_user->email;
            }else if($recipient->type == 'cc'){
                $send_tu_cc[] = $recipient->rapidx_user->email;
            }
        }

        // if($hr_memo){
            switch ($request->status) {
                case 3: { //FOR APPROVAL
                        Mail::send('mail.hr_memo_mail', ['hr_memo' => $hr_memo], function ($message) use ($send_hr_to, $send_hr_cc, $hr_memo) {
                            $message->to($send_hr_to)->subject('TRDSv2 Memo: ' . $hr_memo->subject);

                            if(!empty($send_hr_cc)){
                                $message->cc($send_hr_cc);
                            }
                        });

                        break;
                    }
                case 4: { //HR DISAPPROVED

                        break;
                    }
                case 5: { //HR APPROVED, FOR TU RECEIVING
                        Mail::send('mail.hr_memo_mail', ['hr_memo' => $hr_memo], function ($message) use ($send_tu_to, $send_tu_cc, $hr_memo) {
                            $message->to($send_tu_to)->subject('TRDSv2 Memo: ' . $hr_memo->subject);

                            if(!empty($send_tu_cc)){
                                $message->cc($send_tu_cc);
                            }
                        });
                        break;
                    }
                case 6: { //TU RECEIVED

                        break;
                    }
                case 7: { //TU DISAPPROVED

                        break;
                    }
                default: {
                        $result = "---";
                        break;
                    }
            }
            return response()->json(['result' => 1]);
        // }else{
        //     return response()->json(['result' => 2]);
        // }
    }

    public function getTrainorDropdownDetails(Request $request)
    {
        $pmiTrainorQuery = "
            SELECT
                pkid,
                EmpNo,
                CONCAT(FirstName, ' ', LastName) AS TrainorName
            FROM tbl_EmployeeInfo
            WHERE fkSection = 401 AND fkPosition IN (80, 97) AND EmpStatus = 1
            ORDER BY TrainorName ASC
        ";

        $subconTrainorQuery = "
            SELECT
                pkid,
                EmpNo,
                CONCAT(FirstName, ' ', LastName) AS TrainorName
            FROM tbl_EmployeeInfo
            WHERE fkSection = 401 AND fkPosition IN (21, 87, 106, 123, 134) AND EmpStatus = 1
            ORDER BY TrainorName ASC
        ";

        $hris = DB::connection('mysql_systemone')->select($pmiTrainorQuery);
        $subcon = DB::connection('mysql_subcon')->select($subconTrainorQuery);

        $merged_trainor_list = array_merge($hris, $subcon);
            
        return response()->json([
            'trainor_list' => $merged_trainor_list
        ]);
    }

    public function exportInspectorSkillChart(Request $request)
    {
        $request->validate([
            'section_export'   => 'required',
        ]);

        $selectedSheets = $request->input('section_export', []);

        return Excel::download(
            new InspectorSkillChart($selectedSheets),
            'QC Inspectors Skill Chart.xlsx'
        );
    }
}
