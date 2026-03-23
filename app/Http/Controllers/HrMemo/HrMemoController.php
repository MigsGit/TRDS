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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class HrMemoController extends Controller
{
    private function actionButton($class, $icon, $id, $extraClass = ''){
        return "<button class='btn {$class} btn-sm {$extraClass}' data-id='{$id}'>
                    <i class='fa-solid {$icon}'></i>
                </button>";
    }

    public function viewHrMemoInfo(Request $request){
        // $globalUser = session('global_user');
        $hr_memo_details = HrMemo::with(['prepared_by_info', 'noted_by_info', 'email_recipients.rapidx_user', 'trainee_details.emp_exam_details.exam_info'])->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        return DataTables::of($hr_memo_details)
        ->addColumn('action', function($hr_memo_details){
            $result = "";
            $result .= "<center>";

            // $canManage  = $globalUser && in_array($globalUser->position, [0,1,2,3]);

            $id = $hr_memo_details->id;

            $isPending   = $hr_memo_details->status == 1;
            $isCancelled = $hr_memo_details->status == 2;
            $isForApproval = $hr_memo_details->status == 3;
            $isApproved = $hr_memo_details->status == 4;
            $isDisapproved = $hr_memo_details->status == 5;

            if ($isPending) {
                // if($canManage){
                    $result .= $this->actionButton('btn-secondary btnEdit', 'fas fa-edit', $id, 'mr-1');
                    $result .= $this->actionButton('btn-success btnFinalSubmit', 'fas fa-check-square', $id, 'mr-1');
                    $result .= $this->actionButton('btn-danger btnDisable', 'fas fa-ban', $id);
                // }else{
                //     $result .= $this->actionButton('btn-info btnView', 'fa-eye', $id, 'mr-1');
                // }
            }

            if ($isCancelled){
                $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
                $result .= $this->actionButton('btn-success btnEnable', 'fas fa-undo', $id);
            }

            if ($isForApproval){
                $result .= $this->actionButton('btn-success btnView', 'fas fa-check-square', $id, 'mr-1');
            }

            if ($isApproved){
                $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
            }

            if ($isDisapproved){
                $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
            }

            $result .= "</center>";
            return $result;
        })
        ->addColumn('status_label', function($pth_details){
            $result = "";
            $result .= "<center>";

            if($pth_details->status == 1){
                $result .= "<span class='badge rounded-pill bg-info'>Pending</gspan>";
            }else if($pth_details->status == 2){
                $result .= "<span class='badge rounded-pill bg-secondary'>Cancelled</span>";
            }else if($pth_details->status == 3){
                $result .= "<span class='badge rounded-pill bg-primary'>For Approval</span>";
            }else if($pth_details->status == 4){
                $result .= "<span class='badge rounded-pill bg-success'>Approved</span>";
            }else if($pth_details->status == 5){
                $result .= "<span class='badge rounded-pill bg-danger'>Disapproved</span>";
            }else{
                $result .= "<span class='badge rounded-pill bg-info'>N/A</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->addColumn('reason_label', function($pth_details){
            $result = "";
            $result .= "<center>";

            if($pth_details->status == 1){
                $result .= "<span'>Newly Hired</span>";
            }else if($pth_details->status == 2){
                $result .= "<span'>Maternity Leave</span>";
            }else if($pth_details->status == 3){
                $result .= "<span'>Sick Leave</span>";
            }else if($pth_details->status == 4){
                $result .= "<span'>Vacation Leave</span>";
            }else if($pth_details->status == 5){
                $result .= "<span'>Promoted</span>";
            }else if($pth_details->status == 6){
                $result .= "<span'>Transferred</span>";
            }else if($pth_details->status == 7){
                $result .= "<span'>Regularization</span>";
            }else{
                $result .= "<span'>N/A</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->rawColumns(['action', 'reason_label', 'status_label'])
        ->make(true);
    }

    public function getEmailRecipientsDropdownDetails(Request $request)
    {
        $emails = RapidXUser::select('id', 'name', 'email')->whereNotNull('email')->where('user_stat', 1)->get();
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

        // CASE 1: Employee number exists
        // if (!empty($empNo)) {

            $hris = DB::connection('mysql_systemone')
                ->select($hrisQuery . " WHERE tbl_EmployeeInfo.EmpNo = ? LIMIT 1", [$empNo]);

            if (!empty($hris)) {
                return response()->json($hris);
            }

            // fallback to subcon
            $subcon = DB::connection('mysql_subcon')
                ->select($subconQuery . " WHERE tbl_EmployeeInfo.EmpNo = ? LIMIT 1", [$empNo]);

            return response()->json($subcon);
        // }
    }

    public function addHrMemoInfo(Request $request){
        // return $request->all();
        // return $trainees = json_decode($request->trainee_details, true);
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
                    'date_filed' => $request->date_filed
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
                                'category'            => $ed['exam_title'],
                                'result'              => $ed['result'],
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
            'trainee_details'  // Load trainee_details first
        ])
        ->where('id', $request->id)
        ->first();

        // return $hrMemo;
        // if ($hrMemo && $hrMemo->trainee_details) {
            foreach($hrMemo->trainee_details as $td){
                if ($td->employment_type == 1) {
                    // HRIS employee
                    $td->load(['hris_emp_info' => function ($q) {
                        $q->join('vw_Trainee', 'vw_employeeinfo.pkid', '=', 'vw_Trainee.fkEmployee')
                        ->join('tbl_Training', 'vw_Trainee.fkTraining', '=', 'tbl_Training.pkid')
                        ->select(
                            'vw_employeeinfo.*',
                            'tbl_Training.Venue as Venue'
                        );
                    }]);
                } else {
                    // Subcon employee
                    $td->load(['subcon_emp_info' => function ($q) {
                        $q->join('vw_Trainee', 'vw_employeeinfo.pkid', '=', 'vw_Trainee.fkEmployee')
                        ->join('tbl_Training', 'vw_Trainee.fkTraining', '=', 'tbl_Training.pkid')
                        ->select(
                            'vw_employeeinfo.*',
                            'tbl_Training.Venue as Venue'
                        );
                    }]);
                }
            }
        // }

        return response()->json($hrMemo);
    }

    public function updateHrMemoStatus(Request $request){
        DB::beginTransaction();

        try {
            $memo = HrMemo::findOrFail($request->id);

            $memo->status = $request->new_status;
            $memo->save();

            DB::commit(); // ✅ commit here

            return response()->json([
                'success' => true,
                'new_status' => $memo->status,
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
        // return $hr_memo;
        // $data = ['application' => $hr_memo, 'approver_details' => $approver_details];

        $send_to = [];
        $send_cc = [];

        foreach ($hr_memo->email_recipients as $recipient){
            if($recipient->type == 'to'){
                $send_to[] = $recipient->rapidx_user->email;
            }else if($recipient->type == 'cc'){
                $send_cc[] = $recipient->rapidx_user->email;
            }
        }

        // if($hr_memo){
            switch ($request->status) {
                case 3: { //FOR APPROVAL
                        Mail::send('mail.hr_memo_mail', ['hr_memo' => $hr_memo], function ($message) use ($send_to, $send_cc, $hr_memo) {
                            $message->to($send_to)->subject('TRDSv2 Memo: ' . $hr_memo->subject);

                            if(!empty($send_cc)){
                                $message->cc($send_cc);
                            }
                        });
                        
                        break;
                    }
                case 4: { //APPROVED
                        
                        break;
                    }
                case 5: { //DISAPPROVED
                        
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
}
