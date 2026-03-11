<?php

namespace App\Http\Controllers\HrMemo;
use App\Http\Controllers\Controller;
use DataTables;
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
        $hr_memo_details = HrMemo::with(['email_recipients.rapidx_user', 'trainee_details.emp_exam_details.exam_info'])->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        return DataTables::of($hr_memo_details)
        ->addColumn('action', function($hr_memo_details){
            $result = "";
            $result .= "<center>";

            // $canManage  = $globalUser && in_array($globalUser->position, [0,1,2,3]);
            $isPending   = $hr_memo_details->status == 1;
            $isForApproval = $hr_memo_details->status == 2;
            $id = $hr_memo_details->id;

            if ($isPending) {
                // if ($canManage) {
                    $result .= $this->actionButton('btn-secondary btnEdit', 'fas fa-edit', $id, 'mr-1');
                    $result .= $this->actionButton('btn-danger btnDisable', 'fas fa-ban', $id);
                // } else {
                //     $result .= $this->actionButton('btn-info btnView', 'fa-eye', $id, 'mr-1');
                // }
            }

            // if ($isForApproval) {
            //     $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
            //     $result .= $this->actionButton('btn-success btnEnable', 'fas fa-undo', $id);
            // }

            $result .= "</center>";
            return $result;
        })
        ->addColumn('status_label', function($pth_details){
            $result = "";
            $result .= "<center>";

            if($pth_details->status == 1){
                $result .= "<span class='badge rounded-pill bg-primary'>Pending</span>";
            }else if($pth_details->status == 2){
                $result .= "<span class='badge rounded-pill bg-info'>For Approval</span>";
            }else{
                $result .= "<span class='badge rounded-pill bg-success'>Done</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->rawColumns(['action', 'status_label'])
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

        // // CASE 2: No employee number → run both and merge
        // $hris = DB::connection('mysql_systemone')->select($hrisQuery);
        // $subcon = DB::connection('mysql_subcon')->select($subconQuery);

        // $merged = array_merge($hris, $subcon);

        // return response()->json($merged);
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
                    $dateCode = date('my'); // month + year (2 digits)
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
                foreach ($request->to as $i => $value){
                    HrMemoEmailRecipients::insert([
                        'hr_memo_id' => $hr_memo_id,
                        'user_id' => $request->to[$i],
                        'type' => 'to'
                    ]);
                }

                foreach ($request->cc as $i => $value){
                    HrMemoEmailRecipients::insert([
                        'hr_memo_id' => $hr_memo_id,
                        'user_id' => $request->cc[$i],
                        'type' => 'cc'
                    ]);
                }

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
                                                'employee_no'         => $td['emp_no']
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
        return HrMemo::with(['email_recipients.rapidx_user', 'trainee_details.emp_exam_details.exam_info'])->where('id', $request->id)->first();
    }

    public function updateHrMemoStatus(Request $request){
        DB::beginTransaction();

        try {
            $defect = HrMemo::findOrFail($request->id);

            $defect->status = $defect->status == 1 ? 0 : 1;
            $defect->save();

            DB::commit(); // ✅ commit here

            return response()->json([
                'success' => true,
                'new_status' => $defect->status,
                'message' => 'Past Trouble History Record status updated successfully.'
            ]);
        } catch (\Throwable $e) { // ✅ catch everything including DB errors
            DB::rollBack(); // ✅ rollback only if it fails

            // log the error so you can see what’s happening
            \Log::error('Past Trouble History Record status update failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Past Trouble History Record status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUsers(Request $request){
        $users = User::where('status', 0)->get();
        return response()->json(['users_data' => $users]);
    }
}
