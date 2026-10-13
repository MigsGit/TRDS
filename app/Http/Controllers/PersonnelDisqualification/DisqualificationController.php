<?php

namespace App\Http\Controllers\PersonnelDisqualification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

use Yajra\DataTables\Facades\DataTables;
use App\Model\Disqualification\P1PersonnelDisqualification;
use App\Model\Disqualification\P1EmailRecipients;
use App\Model\Disqualification\P2CounselingReorientation;
use App\Model\Disqualification\P2Reorientation;

use App\Model\SystemOneHrisSubcon;
use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetails;

use App\Model\Qc\QcSlipEmployee;
use App\Model\Qc\QcSlip;
use App\RapidXUser;


class DisqualificationController extends Controller
{
    private function actionButton($class, $icon, $id, $extraClass = '', $approval = false, $remarks = ''){
        $remarksSafe = htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8');
        return "<button type='button' class='btn {$class} btn-sm {$extraClass}' data-id='{$id}' data-approval='{$approval}' data-remarks=\"{$remarksSafe}\">
                    <i class='fa-solid {$icon}'></i>
                </button>";
    }

    public function viewDisqualificationInfo(Request $request){
        $globalUser = session('global_user');
        $user_access = explode(',', $globalUser->user_modules_id);
        // $canAccessDisqualification = in_array(22, $user_access); // with the actual module ID for disqualification

        $disquali_info = P1PersonnelDisqualification::with(['email_recipients',
                                                    'disqualified_personnel_info.qcSlip',
                                                    'disqualified_personnel_info.system_one_hris_emp_info',
                                                    'disqualified_personnel_info.system_one_hris_emp_info',
                                                    'classifications',
                                                    'subclassifications',
                                                    'part2_info',
                                                    'station_name',
                                                    'created_by_info'])
                                                    ->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

                                                    // return $disquali_info[0]->operator_station_name;
        return DataTables::of($disquali_info)
        ->addColumn('action', function($disquali_info) use ($user_access, $globalUser){
            $result = "";
            $result .= "<center>";

            // $canManage  = $globalUser && in_array($globalUser->position, [0,1,2,3]);
            // $canReviewCSR  = $globalUser->position == 0 || $globalUser->position == 2; //SuperAdmin & Reviewing Authority only is allowed
            // $canApproveCSR  = $globalUser->position == 0 || $globalUser->position == 3; //SuperAdmin & Approving Authority only is allowed

            $isPending   = $disquali_info->status == 1;
            $isForCounselling = $disquali_info->status == 2;
            $isForMonitoring = $disquali_info->status == 3;
            $isOverallDisposition = $disquali_info->status == 4;
            $isCancelled = $disquali_info->status == 5;
            // $isDone = $disquali_info->status == 6;

            $id = $disquali_info->id;

            if($isPending){
                // if ($canManage) {
                    $result .= $this->actionButton('btn-secondary btnEdit', 'fas fa-edit', $id, 'mr-1');
                    $result .= $this->actionButton('btn-success btnFinalSubmit', 'fas fa-check-square', $id, 'mr-1');
                    $result .= $this->actionButton('btn-danger btnDisable', 'fas fa-ban', $id);
                // }else{
                //     $result .= $this->actionButton('btn-info btnView', 'fa-eye', $id, 'mr-1');
                // }
            }else if($isForCounselling){
                // if($canReviewCSR){
                    $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
                    $result .= $this->actionButton('btn-primary btnEdit', 'fas fa-file-alt', $id, 'mr-1');
                    // $result .= $this->actionButton('btn-secondary btnEdit', 'fas fa-edit', $id, 'mr-1');

                    if($disquali_info->review_info != null){
                        $result .= $this->actionButton('btn-success btnFinalSubmitReview', 'fas fa-check-square', $id, 'mr-1');
                    }
                // }else{
                //     $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
                // }
            }else if($isForMonitoring){
                // if($canReviewCSR){
                    $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
                    $result .= $this->actionButton('btn-primary btnEdit', 'fas fa-file-alt', $id, 'mr-1');

                    // if($disquali_info->review_info != null){
                    //     $result .= $this->actionButton('btn-success btnFinalSubmitReview', 'fas fa-check-square', $id, 'mr-1');
                    // }
                // }else{
                //     $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
                // }
            }else if($isOverallDisposition){
                // if($canReviewCSR){
                    $result .= $this->actionButton('btn-primary btnView', 'fas fa-file-alt', $id, 'mr-1');
                    $result .= $this->actionButton('btn-secondary btnEdit', 'fas fa-edit', $id, 'mr-1');

                    if($disquali_info->review_info != null){
                        $result .= $this->actionButton('btn-success btnFinalSubmitReview', 'fas fa-check-square', $id, 'mr-1');
                    }
                // }else{
                //     $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
                // }
            }else if($isCancelled){
                $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');

                // if ($canManage) {
                    $result .= $this->actionButton('btn-success btnEnable', 'fas fa-undo', $id);
                // }
            }else{
                $result .= $this->actionButton('btn-info btnView', 'fas fa-eye', $id, 'mr-1');
            }

            $result .= "</center>";
            return $result;
        })
        ->addColumn('status_label', function($disquali_info){
            $result = "";
            $result .= "<center>";

            if($disquali_info->status == 1){
                $result .= "<span class='badge rounded-pill bg-info'>Pending</span>";
            }else if($disquali_info->status == 2){
                $result .= "<span class='badge rounded-pill bg-primary'>For Counselling</span>";
            }else if($disquali_info->status == 3){
                $result .= "<span class='badge rounded-pill bg-primary'>Results Monitoring</span>";
            }else if($disquali_info->status == 4){
                $result .= "<span class='badge rounded-pill bg-primary'>Overall Disposition</span>";
            }else if($disquali_info->status == 5){
                $result .= "<span class='badge rounded-pill bg-danger'>Cancelled</span>";
            }else if($disquali_info->status == 6){
                $result .= "<span class='badge rounded-pill bg-success'>Done</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->addColumn('disqualified_personnel_name', function($disquali_info) {
            $employee = $disquali_info->disqualified_personnel_info;

            if (!$employee || !$employee->employee_info) {
                return '<center>N/A</center>';
            }

            return '<center>' . $employee->employee_info->EmpName . '</center>';
        })
        ->addColumn('station_label', function($disquali_info){
            return $disquali_info->station_name
                ? '<center>' . $disquali_info->station_name->dropdown_masters_details . '</center>'
                : '<center>N/A</center>';
        })
        ->addColumn('disqualified_date_label', function($disquali_info){
            return $disquali_info->disqualification_date
                ? '<center>' . date('M j, Y', strtotime($disquali_info->disqualification_date)) . '</center>'
                : '<center>N/A</center>';
        })
        ->addColumn('recertification_date_label', function($disquali_info){
            return $disquali_info->recertification_date
                ? '<center>' . date('M j, Y', strtotime($disquali_info->recertification_date)) . '</center>'
                : '<center>N/A</center>';
        })
        ->rawColumns(['action', 'status_label', 'station_label', 'disqualified_personnel_name', 'disqualified_date_label', 'recertification_date_label'])
        ->make(true);
    }

    public function viewTrainings(Request $request){
        $globalUser = session('global_user');
        $user_access = explode(',', $globalUser->user_modules_id);
        // $canAccessDisqualification = in_array(22, $user_access); // with the actual module ID for disqualification

        $reorientation_info = P2Reorientation::with([
                                            // 'scope_info',
                                            'trained_by_info',
                                            'created_by_info'])
                                        ->whereNull('deleted_at')
                                        ->where('p2_id', $request->part2_id)
                                        ->orderBy('id', 'ASC')
                                        ->get();

        $training_scopes = DropdownMaster::with('dropdown_master_details')->where('id', 15)->first()->dropdown_master_details->keyBy('id'); //15 = id of Training & Orientation Scopes

        return DataTables::of($reorientation_info)
        ->addColumn('action', function($reorientation_info) use ($user_access, $globalUser){
            $id = $reorientation_info->id;

            $result = "";
            $result .= "<center>";

            // $canManage  = $globalUser && in_array($globalUser->position, [0,1,2,3]);
            // $isPending   = $reorientation_info->status == 1;

            // if($isPending){
                // if ($canManage) {
                    $result .= $this->actionButton('btn-secondary btnEditTraining', 'fas fa-edit', $id, 'mr-1');
                // }
            // }

            $result .= "</center>";
            return $result;
        })
        ->addColumn('category_label', function($reorientation_info) {
            $category = $reorientation_info->training_type;

            $result = "";
            $result .= "<center>";

            if($category == 1){
                $result .= "<span class='badge rounded-pill bg-info'>Supervisor</span>";
            }else if($category == 2){
                $result .= "<span class='badge rounded-pill bg-primary'>Engineer</span>";
            }else if($category == 3){
                $result .= "<span class='badge rounded-pill bg-primary'>LQC Supervisor</span>";
            }
            $result .= "</center>";
            
            return $result;
        })
        ->addColumn('scope_label', function($reorientation_info) use ($training_scopes){
            $scopes = explode(',', $reorientation_info->training_scope);
            $result = "";
            $result .= "<center>";
                foreach ($scopes as $scope) {
                    if (isset($training_scopes[$scope])) {
                        $result .= "<span class='badge rounded-pill bg-primary'>" . e($training_scopes[$scope]->dropdown_masters_details) . "</span> ";
                    }
                }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('trained_by_name', function($reorientation_info) {
            $employee = $reorientation_info->trained_by_info;

            if ($employee == '') {
                return '<center>N/A</center>';
            }

            return '<center>' . $employee->empname . '</center>';
        })
        ->rawColumns(['action', 'category_label', 'scope_label', 'trained_by_name'])
        ->make(true);
    }

    public function addDisqualiPart1(Request $request){
        date_default_timezone_set('Asia/Manila');
        $globalUser = session('global_user');

        $validation = array(
            'employee_name' => 'required',
            'attention_to' => 'required',
            'disqualification_date' => 'required',
            'recertification_date' => 'required',
            'reason_for_disqualification' => 'required',
            'mode_of_defect' => 'required',
            'series_model_name' => 'required',
            'classification' => 'required',
        );

        $data = $request->all();

        $validator = Validator::make($data, $validation);

        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }else{
            DB::beginTransaction();

            try{
                //Control Number Generation
                $lastest_id = P1PersonnelDisqualification::whereNull('deleted_at')->latest('id')->first();
                if($lastest_id == null){
                    $counter = 1;
                }else{
                    $last_control_no = $lastest_id->control_no;
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

                $part1_data_array = array(
                    'disqualification_date' => $request->disqualification_date,
                    'disqualified_personnel' => $request->employee_name,
                    'mode_of_defect' => $request->mode_of_defect,
                    'disqualification_reason' => $request->reason_for_disqualification,
                    'recertification_date' => $request->recertification_date,
                    'series_name' => $request->series_model_name,
                    'device_name' => $request->device_name,
                    'station' => $request->station,
                    'classification' => $request->classification,
                    'c1_subclass' => $request->sub_classification,
                    'created_by' => $globalUser->rapidx_emp_id,
                    'last_updated_by' => $globalUser->rapidx_emp_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );

                if(isset($request->part1_id)){ // EDIT
                    $part1_id = $request->part1_id;
                    P1PersonnelDisqualification::where('id', $request->part1_id)->update($part1_data_array);
                }else{ // ADD
                    $control_no = 'PD-TS-'.date('my').'-'.$digit_prefix.$counter;
                    $part1_data_array['control_no'] = $control_no;
                    $part1_id = P1PersonnelDisqualification::insertGetId($part1_data_array);
                }

                // DELETE OLD HrMemo Email Recipients ON UPDATE
                P1EmailRecipients::where('p1_id', $part1_id)->delete();

                // SAVE NEW HrMemo Email Recipients
                $attention_to = [];

                foreach ($request->attention_to as $attentionToId){
                    $attention_to[] = [
                        'p1_id' => $part1_id,
                        'rapidx_id' => $attentionToId,
                        'type' => 'to'
                    ];
                }

                P1EmailRecipients::insert($attention_to);

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Personnel Disqualification saved successfully.']);
            }catch(\Exception $e){
                DB::rollback();
                return $e;
            }
        }
    }

    public function addDisqualiPart2(Request $request){
        date_default_timezone_set('Asia/Manila');
        $globalUser = session('global_user');

        $validation = array(
            'part1_id' => 'required',
            'counselling_date' => 'required',
            'conducted_by' => 'required',
        );

        $data = $request->all();
        $validator = Validator::make($data, $validation);

        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }else{
            DB::beginTransaction();

            try{
                $part2_data_array = array(
                    'p1_id' => $request->part1_id,
                    'conducted_by' => $request->conducted_by,
                    'counselling_date' => $request->counselling_date,
                    'last_updated_by' => $globalUser->rapidx_emp_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );

                if(isset($request->part2_id)){ // EDIT
                    $part2_id = $request->part2_id;
                    P2CounselingReorientation::where('id', $request->part2_id)->update($part2_data_array);
                }else{ // ADD
                    $part2_id = P2CounselingReorientation::insertGetId($part2_data_array);
                }

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Saved successfully.']);
            }catch(\Exception $e){
                DB::rollback();
                return $e;
            }
        }
    }

    public function addPart2Training(Request $request){
        date_default_timezone_set('Asia/Manila');
        $globalUser = session('global_user');

        $validation = array(
            'part2_id'        => 'required',
            'part2_training_status' => 'required',
            'training_scope.*' => 'required',
            'trained_by'     => 'required',
            'training_date'   => 'required'
        );

        $data = $request->all();
        $validator = Validator::make($data, $validation);

        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }else{
            DB::beginTransaction();

            $scopes = implode(',', $request->training_scope);
            try{
                $part2_training_data_array = array(
                    'p2_id' => $request->part2_id,
                    'training_type' => $request->part2_training_status,
                    'training_scope' => $scopes,
                    'training_date' => $request->training_date,
                    'trained_by'    => $request->trained_by,
                    'last_updated_by' => $globalUser->rapidx_emp_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );

                // if(isset($request->part2_training_id)){ // EDIT
                //     $part2_training_id = $request->part2_training_id;
                //     P2Reorientation::where('id', $request->part2_training_id)->update($part2_training_data_array);
                // }else{
                    // ADD
                    P2Reorientation::insert($part2_training_data_array);

                    $new_status = $request->part2_training_status + 1; //increment status upon update
                    P2CounselingReorientation::where('id', $request->part2_id)->update(['status' => $new_status]);

                    if($new_status == 4 ){ //trigger status update of P1 when LQC Supervisor updates
                        P1PersonnelDisqualification::where('id', $request->part1_id)->update(['status' => 3]);
                    }
                // }

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Saved successfully.', 'new_status' => $new_status]);
            }catch(\Exception $e){
                DB::rollback();
                return $e;
            }
        }
    }

    public function getDisqualificationById(Request $request){
        $disqualification = P1PersonnelDisqualification::with(['email_recipients',
                                                    'disqualified_personnel_info.qcSlip',
                                                    'disqualified_personnel_info.system_one_hris_emp_info',
                                                    'disqualified_personnel_info.system_one_hris_emp_info',
                                                    'classifications',
                                                    'subclassifications',
                                                    'part2_info',
                                                    'station_name',
                                                    'created_by_info'])
                                                    ->where('id', $request->id)
                                                    ->first();

        return response()->json($disqualification);
    }

    public function updateDisqualificationStatus(Request $request){
        DB::beginTransaction();
        $globalUser = session('global_user');
        date_default_timezone_set('Asia/Manila');

        try {
            $disqualification = P1PersonnelDisqualification::findOrFail($request->id);
            $disqualification->status = $request->new_status;
            // $disqualification->remarks = $request->remarks;
            // $request->new_status == 6 ? $disqualification->received_by = $globalUser->rapidx_emp_id : null;
            // $request->new_status == 6 ? $disqualification->received_date = date('Y-m-d H:i:s') : null;
            $disqualification->save();

            DB::commit(); // ✅ commit here

            return response()->json([
                'success' => true,
                'new_status' => $disqualification->status,
                // 'remarks' => $disqualification->remarks,
                'message' => 'Disqualification status updated successfully.'
            ]);
        } catch (\Throwable $e) { // ✅ catch everything including DB errors
            DB::rollBack(); // ✅ rollback only if it fails

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Disqualification status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getEmailRecipientsDropdownDetails(Request $request){
        $emails = RapidXUser::select('id', 'name', 'email')
                ->whereNotNull('email')
                ->where('user_stat', 1)
                // ->when($request->hr_only == 'true', function ($query) use ($request) {
                //     // return $query->where('department_id', 29); //ALL HRD
                //     return $query->whereIn('employee_number', ['1810', 'T078']); //ESM & GAC only
                // })
                ->get();
        return response()->json($emails);
    }

    public function getEmpDetailsFromCertification(Request $request){
        $cert_emp_details = DB::table('qc_slip_employees')
                ->join('qc_slips', 'qc_slip_employees.qc_slips_id', '=', 'qc_slips.id')
                ->leftJoin('dropdown_master_details as dm_details', 'qc_slip_employees.station_to', '=', 'dm_details.id')
                // ->where('qc_slip_employees.employee_no', 'V049') //for specific employee number
                ->where('qc_slip_employees.deleted_at', null)
                ->where('qc_slips.deleted_at', null)
                // ->where('qc_slips.status', 'OK') //no status filtering for now
                ->select('qc_slips.position_category as position',
                        'qc_slips.section_category as section',
                        'qc_slip_employees.employee_no',
                        'qc_slip_employees.station_to',
                        'qc_slips.series_name',
                        'dm_details.dropdown_masters_details as station_to_name')
                ->get();

        // Get employee numbers from certification result
        $employeeNos = $cert_emp_details
            ->pluck('employee_no')
            ->unique()
            ->values()
            ->toArray();

        // Separate query to System One employee database
        $employeeDetails = QcSlipEmployee::with([
                'system_one_subcon_emp_info',
                'system_one_hris_emp_info'
            ])
            ->whereIn('employee_no', $employeeNos)
            ->get()
            ->keyBy('employee_no');

        // Group certification records
        $grouped = $cert_emp_details
            ->groupBy('employee_no')
            ->map(function ($records, $employeeNo) use ($employeeDetails) {

                $first = $records->first();

                $employeeRecord = $employeeDetails->get($employeeNo);

                return [
                    'employee_no' => $employeeNo,
                    'employee_name' => $employeeRecord ? $employeeRecord->employee_info->EmpName : null,
                    'position' => $first->position,
                    'section' => $first->section,
                    'stations' => $records
                        ->map(function ($record) {
                            return [
                                'station_to' => $record->station_to,
                                'station_to_name' => $record->station_to_name,
                            ];
                        })
                        ->unique('station_to')
                        ->values(),
                    'series_name' => $records
                        ->map(function ($record) {
                            return [
                                'series_name' => $record->series_name,
                            ];
                        })
                        ->unique('series_name')
                        ->values()
                ];
            })
            ->values();

        return response()->json($grouped);
    }

    public function getModeOfDefect(Request $request){
        $modesOfDefect = DropdownMaster::with(['dropdown_master_details'])
                ->where('id', 14) // actual ID for Mode of Defect
                ->get();

        return response()->json($modesOfDefect->first()->dropdown_master_details);
    }

    public function getClassification(Request $request){
        $classifications = DropdownMaster::with(['dropdown_master_details'])
                ->where('id', 12) // actual ID for Classification
                ->get();

        return response()->json($classifications->first()->dropdown_master_details);
    }

    public function getSubClassification(Request $request){
        $subClass = $request->sub_class;

        if($subClass == 'subclass1'){
            $subClassId = 13;
        }else{
            $subClassId = 0;
        }

        if($subClassId != 0){
            $subClassifications = DropdownMaster::with(['dropdown_master_details'])
                ->where('id', $subClassId) // actual ID for Sub-Classification
                ->get();

            return response()->json($subClassifications->first()->dropdown_master_details);
        }else{
            return response()->json([]);
        }
    }

    // type => DropdownMaster id
    // for dependent dropdowns, map the parent value => id instead
    private const DROPDOWN_MAP = [
        'classification'     => 12,
        'sub_classification' => [       // depends on the ?sub_class= value
            'subclass1' => 13,
        ],
        'mode_of_defect'     => 14,
        'training_scope'   => 15,
    ];

    private function resolveId(string $type, ?string $subClass = null): ?int {
        $entry = self::DROPDOWN_MAP[$type] ?? null;

        // dependent dropdown: look up by the parent value
        if (is_array($entry)) {
            return $entry[$subClass] ?? null;
        }

        return $entry;
    }

    public function getDropdownFromMaster(Request $request){
        $request->validate(['type' => 'required|string']);

        $masterId = $this->resolveId($request->type, $request->sub_class);

        if (!$masterId) {
            return response()->json([]);
        }

        $master = DropdownMaster::with('dropdown_master_details')->find($masterId);

        return response()->json($master ? $master->dropdown_master_details : []);
    }

    public function getDropdownFromSystemone(Request $request){
        $request->validate(['type' => 'required|string']);

        // $systemoneId = $this->resolveId($request->type, $request->sub_class);

        // if (!$systemoneId) {
        //     return response()->json([]);
        // }

        // $systemone_info = SystemOneHrisSubcon::find($systemoneId);
        $systemone_info = SystemOneHrisSubcon::get();
        // where('Department', 'like', $request->depertment . '%')
        // where('Position', 'like', $request->depertment . '%')

        return response()->json($systemone_info ? $systemone_info : []);
    }

    // public function getDropdownFromMaster(Request $request){
    //     $type = $request->type;

    //     $dropdown = DropdownMaster::with(['dropdown_master_details'])
    //             ->where('type', $type)
    //             ->get();

    //     return response()->json($dropdown->first()->dropdown_master_details);
    // }
    // public function getEmpNoDropdownDetails(Request $request){
    //     $hrisQuery = "
    //         SELECT
    //             pkid,
    //             EmpNo,
    //             1 AS emp_type
    //         FROM tbl_EmployeeInfo
    //         WHERE EmpStatus = 1
    //     ";

    //     $subconQuery = "
    //         SELECT
    //             pkid,
    //             EmpNo,
    //             2 AS emp_type
    //         FROM tbl_EmployeeInfo
    //         WHERE EmpStatus = 1
    //     ";

    //     $hris = DB::connection('mysql_systemone')->select($hrisQuery);
    //     $subcon = DB::connection('mysql_subcon')->select($subconQuery);

    //     $merged = array_merge($hris, $subcon);

    //     return response()->json($merged);
    // }

    // public function getEmployeeDetails(Request $request){
    //     $empNo = $request->employee_number;

    //     $hrisQuery = "
    //         SELECT
    //             'db_hris' AS source,
    //             CONCAT(tbl_EmployeeInfo.FirstName, ' ', tbl_EmployeeInfo.LastName) AS EmpName,
    //             tbl_EmployeeInfo.DateHired,
    //             CONCAT_WS(' - ', tbl_Training.PeriodFrom, tbl_Training.PeriodTo) AS fromto,
    //             tbl_Training.Venue,
    //             vw_Trainee.Remarks,
    //             tbl_Training.Title,
    //             tbl_Position.Position AS Position,
    //             tbl_Department.Department AS Department,
    //             tbl_Section.Section AS Section,
    //             tbl_Division.Division AS Division
    //         FROM vw_Trainee
    //         INNER JOIN tbl_EmployeeInfo ON vw_Trainee.fkEmployee = tbl_EmployeeInfo.pkid AND tbl_EmployeeInfo.EmpStatus = 1
    //         INNER JOIN tbl_Position ON tbl_EmployeeInfo.fkPosition = tbl_Position.pkid
    //         INNER JOIN tbl_Section ON tbl_EmployeeInfo.fkSection = tbl_Section.pkid
    //         INNER JOIN tbl_Department ON tbl_EmployeeInfo.fkDepartment = tbl_Department.pkid
    //         INNER JOIN tbl_Division ON tbl_EmployeeInfo.fkDivision = tbl_Division.pkid
    //         INNER JOIN tbl_Training ON vw_Trainee.fkTraining = tbl_Training.pkid
    //     ";

    //     $subconQuery = "
    //         SELECT
    //             'db_subcon' AS source,
    //             CONCAT(tbl_EmployeeInfo.FirstName, ' ', tbl_EmployeeInfo.LastName) AS EmpName,
    //             tbl_EmployeeInfo.DateHired,
    //             CONCAT_WS(' - ', tbl_Training.PeriodFrom, tbl_Training.PeriodTo) AS fromto,
    //             tbl_Training.Venue,
    //             COALESCE(vw_Trainee.Remarks, 'No Record') AS Remarks,
    //             tbl_Training.Title,
    //             tbl_Position.Position AS Position,
    //             tbl_Department.Department AS Department,
    //             tbl_Section.Section AS Section,
    //             tbl_Division.Division AS Division
    //         FROM tbl_EmployeeInfo
    //         LEFT JOIN db_hris.vw_Trainee ON vw_Trainee.fkEmployee = tbl_EmployeeInfo.pkid AND tbl_EmployeeInfo.EmpStatus = 1
    //         LEFT JOIN db_hris.tbl_Training ON vw_Trainee.fkTraining = tbl_Training.pkid
    //         INNER JOIN db_hris.tbl_Position ON tbl_EmployeeInfo.fkPosition = tbl_Position.pkid
    //         INNER JOIN db_hris.tbl_Section ON tbl_EmployeeInfo.fkSection = tbl_Section.pkid
    //         INNER JOIN db_hris.tbl_Department ON tbl_EmployeeInfo.fkDepartment = tbl_Department.pkid
    //         INNER JOIN db_hris.tbl_Division ON tbl_EmployeeInfo.fkDivision = tbl_Division.pkid
    //     ";

    //     $trainingVenueQuery = "
    //         SELECT
    //             Venue
    //         FROM tbl_training_venue
    //         WHERE Venue != '' AND logdel = 0
    //         ORDER BY Venue ASC
    //     ";

    //     // CASE 1: Employee number exists
    //     // if (!empty($empNo)) {

    //         $training_venue = DB::connection('mysql_systemone')->select($trainingVenueQuery);

    //         $hris = DB::connection('mysql_systemone')
    //             ->select($hrisQuery . " WHERE tbl_EmployeeInfo.EmpNo = ? AND tbl_EmployeeInfo.EmpStatus = 1 LIMIT 1", [$empNo]);
                

    //         if (!empty($hris)) {
    //             return response()->json([
    //                 'emp_details' => $hris,
    //                 'training_venue' => $training_venue
    //             ]);
    //         }

    //         // fallback to subcon
    //         $subcon = DB::connection('mysql_subcon')
    //             ->select($subconQuery . " WHERE tbl_EmployeeInfo.EmpNo = ? AND tbl_EmployeeInfo.EmpStatus = 1 LIMIT 1", [$empNo]);

    //         return response()->json([
    //             'emp_details' => $subcon,
    //             'training_venue' => $training_venue
    //         ]);
    //     // }
    // }
}
