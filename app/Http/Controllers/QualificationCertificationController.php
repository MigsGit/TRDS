<?php

namespace App\Http\Controllers;


use App\Http\Controllers\CommonController;
use App\Http\Requests\AOperProdTrainingOrientationRequest;
use App\Http\Requests\BOpEnggSectionTrainingOrientationRequest;
use App\Http\Requests\CQcCertificationRequest;
use App\Http\Requests\EQcValidationProcessRequest;
use App\Http\Requests\QcSlipEmployeeRequest;
use App\Http\Requests\QcSlipRequest;
use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetail;
use App\Model\Qc\AOperProdTrainingOrientation;
use App\Model\Qc\BOpEnggSectionTrainingOrientation;
use App\Model\Qc\CQcCertification;
use App\Model\Qc\EQcValidationProcess;
use App\Model\Qc\FQcValidation;
use App\Model\Qc\QcReasonCertification;
use App\Model\Qc\QcSlipEmployee;
use App\Model\QcSlip;
use App\Model\SystemHrisViewDivDeptSec;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneHrisSubcon;
use App\Model\SystemOneSubconEmpInfo;
use App\OpApprover;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QualificationCertificationController extends Controller
{
    protected $commonController;
    public function __construct(CommonController $commonController){
        $this->commonController = $commonController;
    }
    public function updateApproval(Request $request){
        try {
            // return 'true';
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
           $qcSlip = QcSlip::
            where('id',$request->qcSlipsId)
            ->whereNull('deleted_at')
            // ->get();
            ->update([
                'status' => $request->decision,
                'approval_status' => $request->decision,
                'appproval_at' => now()
            ]);
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function saveFirstTakeInsSequence(Request $request){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            if($request->category === "firstTakeInsSequence"){
                $arrData = [
                    'first_take_ins_sequence' => $request->value,
                ];
            }
            if($request->category === "firstTakeInsAssessmentResult"){
                $arrData = [
                    'first_take_ins_assessment_result' => $request->value,
                ];
            }
            if($request->category === "secondTakeInsSequence"){
                $arrData = [
                    'second_take_ins_sequence' => $request->value,
                ];
            }
            if($request->category === "secondTakeInsAssessmentResult"){
                $arrData = [
                    'second_take_ins_assessment_result' => $request->value,
                ];
            }
            QcSlipEmployee::
            where('qc_slips_id',$request->qcSlipsId)
            ->where('id',$request->qcSlipEmployeesId)
            ->whereNull('deleted_at')
            // ->get();
            ->update($arrData);
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function getQcSlipsById(Request $request){ //nmodify

        try {
            $qcSlip = QcSlip::with(
                'op_approvers',
                'qc_slip_employees',
                'qc_reason_certification',
                'a_oper_prod_training_orientation',
                'b_op_engg_section_training_orientation',
                'c_qc_certification',
                // 'e_qc_validation_processes',
                // 'f_qc_validations',
            )
            ->where('id',$request->qcSlipsId)
            ->whereNull('deleted_at')
            ->first();


            $rawReasonsString = $qcSlip->qc_reason_certification->reason_of_certification;
            $rawAOperProdTrainingOrientation = $qcSlip->a_oper_prod_training_orientation->traning_items;
            $rawBOpEnggSectionTrainingOrientation = $qcSlip->b_op_engg_section_training_orientation->traning_items;

            $rawReasonsStringCollection =  collect(explode('|', $rawReasonsString))
                ->map(function($id) {
                    return trim($id);
                })
            ->filter()
            ->values()
            ->all();

            $rawBOpEnggSectionTrainingOrientationCollection =  collect(explode('|', $rawBOpEnggSectionTrainingOrientation))
                ->map(function($id) {
                    return trim($id);
                })
            ->filter()
            ->values()
            ->all();

            $rawAOperProdTrainingOrientationCollection =  collect(explode('|', $rawAOperProdTrainingOrientation))
                ->map(function($id) {
                    return trim($id);
                })
            ->filter()
            ->values()
            ->all();

           $rawPayload = collect($qcSlip->op_approvers)->groupBy('approval_status')->toArray();


            $approversCollection = collect($qcSlip)->groupBy('approval_status')->toArray();

            // Helper closure to cleanly explode piped string values into trimmed arrays
            $explodePipedString = function ($value) {
                if (is_null($value) || trim($value) === '') {
                    return [];
                }
                return array_map('trim', explode('|', $value));
            };

            // 2. Step One: Parse and collect ALL unique EmpNo values across all status groups
            $allEmployeeNumbers = [];
            foreach ($rawPayload as $status => $items) {
                foreach ($items as $item) {
                    $itemArray = (array) $item;

                    // Collect from all possible employee fields
                    $firstApprovers = $explodePipedString($itemArray['first_approver'] ?? null);
                    $secondApprovers = $explodePipedString($itemArray['second_approver'] ?? null);
                    $alerts = $explodePipedString($itemArray['alert_prod_sec'] ?? null);

                    $allEmployeeNumbers = array_merge($allEmployeeNumbers, $firstApprovers, $secondApprovers, $alerts);
                }
            }

            // Filter to keep only unique, non-empty employee numbers
            $uniqueEmployeeNumbers = array_unique(array_filter($allEmployeeNumbers));

            // 3. Step Two: Bulk fetch employee details from your HRIS table in ONE query
            // This maps 'EmpNo' to their corresponding database columns (e.g., 'First_Name', 'Last_Name', or 'Full_Name')
            $employeeDbMap = SystemOneHrisSubcon::whereIn('EmpNo', $uniqueEmployeeNumbers)
                ->get(['EmpNo', 'empname']) // Fetch only needed columns
                ->keyBy('EmpNo') // Key the collection by EmpNo for O(1) lookup speed
                ->toArray();

            // 4. Step Three: Map the data payload and inject matching Select2 structured object lists
            $processedData = collect($rawPayload)->map(function ($items) use ($explodePipedString, $employeeDbMap) {
                return collect($items)->map(function ($item) use ($explodePipedString, $employeeDbMap) {
                    $itemArray = (array) $item;

                    // Explode the fields
                    $firstExploded  = $explodePipedString($itemArray['first_approver'] ?? null);
                    $firstExploded2  = $explodePipedString($itemArray['first_approver_2'] ?? null);
                    $secondExploded = $explodePipedString($itemArray['second_approver'] ?? null);
                    $secondExploded2 = $explodePipedString($itemArray['second_approver_2'] ?? null);
                    $alertsExploded = $explodePipedString($itemArray['alert_prod_sec'] ?? null);

                    // Helper closure to build Select2 formatting: [{id: "R144", name: "John Doe"}]
                    $mapToSelect2Structure = function ($empNoArray) use ($employeeDbMap) {
                        return array_map(function ($empNo) use ($employeeDbMap) {
                            $cleanEmpNo = trim($empNo);

                            // Look up details from our pre-fetched database map
                            $employeeInfo = $employeeDbMap[$cleanEmpNo] ?? null;

                            // Fallback to the EmpNo if the record is not found in the HRIS table
                            $displayName = $cleanEmpNo;
                            if ($employeeInfo) {
                                // Adjust these keys based on your actual SystemOneHrisSubcon column names
                                $displayName = $employeeInfo['empname'];
                            }

                            return [
                                'id' => $cleanEmpNo,
                                'name' => trim($displayName)
                            ];
                        }, $empNoArray);
                    };

                    // Map and attach the completed objects directly to the output array keys
                    $itemArray['first_approver_exploded']  = $mapToSelect2Structure($firstExploded);
                    $itemArray['first_approver2_exploded']  = $mapToSelect2Structure($firstExploded2);
                    $itemArray['second_approver_exploded'] = $mapToSelect2Structure($secondExploded);
                    $itemArray['second_approver2_exploded'] = $mapToSelect2Structure($secondExploded2);
                    $itemArray['alert_prod_sec_exploded']  = $mapToSelect2Structure($alertsExploded);

                    return $itemArray;
                });
            });


            // $qcSlipReasons = collect($qcSlip);
            return response()->json([
                'is_success' => 'true',
                'qcSlip' => $qcSlip,
                'rawReasonsStringCollection' => $rawReasonsStringCollection,
                'rawBEnggTrainingItemsCollection' => $rawBOpEnggSectionTrainingOrientationCollection,
                'rawAOperProdTrainingOrientationCollection' => $rawAOperProdTrainingOrientationCollection,
                'approversCollection' => $processedData,
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function load1stQcValidation(Request $request){
        try {
           $qcSlipEmployee = QcSlipEmployee::with('system_one_subcon_emp_info','system_one_hris_emp_info')
            ->where('qc_slips_id',$request->qcSlipsId)
            ->where('station_to',2)
            ->whereNull('deleted_at')
            ->get([
                'employee_no',
                'id',
                'qc_slips_id',
                'station_to',
                'first_take_ins_sequence',
                'first_take_ins_assessment_result',
            ]);
            return DataTables($qcSlipEmployee)
            ->addColumn('employee_name',function ($row){
                $pricon = $row->system_one_hris_emp_info->EmpName ?? '';
                $subcon = $row->system_one_subcon_emp_info->EmpName ?? '';
                $employee_name = $pricon != '' ? $pricon : $subcon;
                $result = '';
                $result .= '<center>';
                $result .= '<span> '.$employee_name.' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->addColumn('first_take_ins_sequence',function ($row){
                $isExist = $row->first_take_ins_sequence ?? '';
                $cSelected = $isExist === 'YES' ? 'selected' : '';
                $xSelected = $isExist === 'NO' ? 'selected' : '';
                $naSelected = $isExist === 'N/A' ? 'selected' : '';
                // second_take_ins_sequence
                // second_take_ins_assessment_result
                $result = '';
                $result .= '<center>
                    <select qc-slip-employees-id="'.$row->id.'" qc-slips-id="'.$row->qc_slips_id.'" class="form-control select2bs4 first_take_ins_sequence" style="width: 100%;" name="first_take_ins_sequence" id="first_take_ins_sequence" >
                        <option value="N/A" '.$cSelected.'>N/A</option>
                        <option value="YES" '.$xSelected.'>YES</option>
                        <option value="NO" '.$naSelected.'>NO</option>
                    </select>';
                $result .= '</center>';
                return $result;
            })
            ->addColumn('first_take_ins_assessment_result',function ($row){
                $isExist = $row->first_take_ins_assessment_result ?? '';
                // $isSelected = $isExist != '' ? 'selected' : '';
                $cSelected = $isExist === 'PASSED' ? 'selected' : '';
                $xSelected = $isExist === 'FAILED' ? 'selected' : '';
                $naSelected = $isExist === 'N/A' ? 'selected' : '';
                $result = '';
                $result .= '
                    <select qc-slip-employees-id="'.$row->id.'" qc-slips-id="'.$row->qc_slips_id.'" class="form-control select2bs4 first_take_ins_assessment_result" style="width: 100%;" name="first_take_ins_assessment_result" id="first_take_ins_assessment_result">
                        <option value="N/A" '.$naSelected.'>N/A</option>
                        <option value="PASSED" '.$cSelected.'>PASSED</option>
                        <option value="FAILED" '.$xSelected.'>FAILED</option>
                    </select>';
                $result .= '';
                return $result;
            })
            ->rawColumns(['employee_name','first_take_ins_sequence','first_take_ins_assessment_result'])
            ->make(true);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function load2ndQcValidation(Request $request){
        try {
            $qcSlipEmployee = QcSlipEmployee::with('system_one_subcon_emp_info','system_one_hris_emp_info')
            ->where('qc_slips_id',$request->qcSlipsId)
            ->where('station_to',2)
            ->whereNull('deleted_at')
            ->get([
                'employee_no',
                'id',
                'qc_slips_id',
                'station_to',
                'second_take_ins_sequence',
                'second_take_ins_assessment_result',
            ]);
            return DataTables($qcSlipEmployee)
            ->addColumn('employee_name',function ($row){
                $pricon = $row->system_one_hris_emp_info->EmpName ?? '';
                $subcon = $row->system_one_subcon_emp_info->EmpName ?? '';
                $employee_name = $pricon != '' ? $pricon : $subcon;
                $result = '';
                $result .= '<center>';
                $result .= '<span> '.$employee_name.' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->addColumn('second_take_ins_sequence',function ($row){
                $isExist = $row->second_take_ins_sequence ?? '';
                $cSelected = $isExist === 'YES' ? 'selected' : '';
                $xSelected = $isExist === 'NO' ? 'selected' : '';
                $naSelected = $isExist === 'N/A' ? 'selected' : '';
                $result = '';
                $result .= '<center>
                    <select qc-slip-employees-id="'.$row->id.'" qc-slips-id="'.$row->qc_slips_id.'" class="form-control select2bs4 second_take_ins_sequence" style="width: 100%;" name="second_take_ins_sequence" id="second_take_ins_sequence" >
                        <option value="N/A" '.$cSelected.'>N/A</option>
                        <option value="YES" '.$xSelected.'>YES</option>
                        <option value="NO" '.$naSelected.'>NO</option>
                    </select>';
                $result .= '</center>';
                return $result;
            })
            ->addColumn('second_take_ins_assessment_result',function ($row){
                $isExist = $row->second_take_ins_assessment_result ?? '';
                // $isSelected = $isExist != '' ? 'selected' : '';
                $cSelected = $isExist === 'PASSED' ? 'selected' : '';
                $xSelected = $isExist === 'FAILED' ? 'selected' : '';
                $naSelected = $isExist === 'N/A' ? 'selected' : '';
                $result = '';
                $result .= '
                    <select qc-slip-employees-id="'.$row->id.'" qc-slips-id="'.$row->qc_slips_id.'" class="form-control select2bs4 second_take_ins_assessment_result" style="width: 100%;" name="second_take_ins_assessment_result" id="second_take_ins_assessment_result">
                        <option value="N/A" '.$naSelected.'>N/A</option>
                        <option value="PASSED" '.$cSelected.'>PASSED</option>
                        <option value="FAILED" '.$xSelected.'>FAILED</option>
                    </select>';
                $result .= '';
                return $result;
            })
            ->rawColumns(['employee_name','second_take_ins_sequence','second_take_ins_assessment_result'])
            ->make(true);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function loadQcSlip(Request $request){
    //newStatus
      $qcSlips = QcSlip::with('product_line','op_approvers')
        ->whereNull('deleted_at')
        ->get();
        $allEmpIds = $qcSlips->pluck('op_approvers') // Grab all op_approvers collections
            ->flatten()                              // Flatten into a single layer of OpApprover models
            ->pluck('alert_prod_sec')               // Pull out all the pipe-separated strings
            ->filter()                               // Remove null or empty entries
            ->flatMap(function ($item) {             // Split pipes and flatten the resulting array elements
                return array_map('trim', explode('|', $item));
            })
            ->unique()                               // Drop duplicates
            ->values()                               // Re-index array keys
            ->all();                                 // Convert to a raw array for database handling

        // 3. Fetch all matching names from HRIS into a quick-lookup map array
        $hrisEmployees = SystemOneHrisEmpInfo::whereIn('EmpNo', $allEmpIds)
            ->get()
            ->pluck('EmpName');
        $subconEmployees = SystemOneSubconEmpInfo::whereIn('EmpNo', $allEmpIds)
            ->get()
            ->pluck('EmpName');
        try {
        return DataTables($qcSlips)
            ->addColumn('rawAction',function ($row) use ($request){
                $result = '';
                $result .= '<center>';
                $result .= '<button class="btn btn-sm btn-outline-primary" type="button" qc-slips-id="'.$row->id.'" id="btnGetQcSlipsId"><i class="fa-solid fa fa-edit"></i></button> </br></br>';
                $result .= '<button class="btn btn-sm btn-outline-info" type="button" qc-slips-id="'.$row->id.'" id="btnViewQcSlipsId"><i class="fa-solid fa fa-eye"></i></button>';
                $result .= '</center>';
                return $result;
            })
            ->addColumn('rawStatus',function ($row) use ($request){
                 $result = '';
                $currentApprover = $row->created_by ?? '';
                // $approvalStatusEnvironment = $row->environment->approval_status;
                $approvalStatus = $row->approval_status;
                // $getStatus = $this->commonInterface->getStatus4m($statusEnvironment);
                    $getApprovalStatus = $this->commonController->getApprovalStatus($approvalStatus);
                $result .= '<center>';
                // $result .= '<span class="'.$getStatus['bgStatus'].'"> '.$getStatus['status'].' </span>';
                $result .= '<br>';
                $result .= '<span class="badge rounded-pill '.$getApprovalStatus['spanColor'].'"> Current Approver: '.$currentApprover.' </span> </br>  </br>';
                $result .= '<span class="badge rounded-pill '.$getApprovalStatus['spanColor'].'"> '.$getApprovalStatus['statusName'].'  </span>';
                $result .= '</center>';
                $result .= '</br>';
                return $result;
            })
            ->addColumn('trained_by',function ($row){
                // $personInCharge = $row->rapidx_user_person_in_charge->name ?? '';
                // $personInCharge = $row;
              return  $result = '';
                $result .= '<center>';
                $result .= '<span> '.$personInCharge.' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->addColumn('certified_by',function ($row){
                // $personInCharge = $row->rapidx_user_person_in_charge->name ?? '';
                // $personInCharge = $row;
              return  $result = '';
                $result .= '<center>';
                $result .= '<span> '.$personInCharge.' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->addColumn('approvers',function ($row){
                // $personInCharge = $row->rapidx_user_person_in_charge->name ?? '';
                // $personInCharge = $row;
              return  $result = '';
                $result .= '<center>';
                $result .= '<span> '.$personInCharge.' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->rawColumns(['rawAction','rawStatus','approvers'])
            ->make(true);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function saveOperApprovers($params){
        try {
            OpApprover::insert($params);
            DB::commit();
            return [
                'is_success' => 'true'
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function saveFormSendEmail($params){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $rapidxEmpNo =  session('global_user');
            $to = '';
            $cc = '';
            foreach (explode(' | ',$params['update_data']['alert_prod_sec']) as $key => $valueRowEmpNo) {
                $arrTo[] = $this->commonController->getEmailByRapidxUserId($valueRowEmpNo);
            }
            if($params['approval_status'] != 'FQCVVO'){ //FQCVVO to QCAPP - Final Approver
                foreach (explode(' | ',$params['update_data']['alert_prod_cc_sec']) as $key => $valueRowEmpNo) {
                    $arrCc[] = $this->commonController->getEmailByRapidxUserId($valueRowEmpNo);
                }

                $collectCc = collect($arrCc)->map(function($rowEmpNo){

                    return [
                        'email' => $rowEmpNo['email'],
                        'fullName' => $rowEmpNo['fullName'],
                    ];
                });
                $cc = $collectCc->pluck('email')->join(',');

            }
            $collectTo = collect($arrCc)->map(function($rowEmpNo){

                return [
                    'email' => $rowEmpNo['email'],
                    'fullName' => $rowEmpNo['fullName'],
                ];
            });
            $to = $collectTo->pluck('email')->join(',');
            // $from =$currentSession['email'] ?? '';
            // $from_name = $currentSession['fullName'];
            // $opApprover =  OpApprover::insert($params['update_data']);
            $emailParams = [
                'qc_slips_id' => $params['qc_slips_id']
            ];
            $message = $this->commonController->emailMsg($emailParams);
            $from = 'issinfoservice@pricon.ph';
            $from_name = 'issinfoservice@pricon.ph';
            return  $emailData = [
                // "to" =>$to,
                "to" =>"mrronquez@pricon.ph",
                // "cc" =>$cc,
                "cc" =>"mclegaspi@pricon.ph",
                "bcc" =>"",
                "from" => $from,
                "from_name" => $from_name ?? "TRDS Auto Email",
                // "subject" =>$subject,
                "message" =>  $message,
                "attachment_filename" => "",
                "attachment" => "",
                "send_date_time" => now(),
                "date_time_sent" => "",
                "date_created" => now(),
                "created_by" => session('rapidx_username'),
                "system_name" => "rapidx_TRDS",
            ];
            //TODO: SEND email
            // return $this->commonController->sendEmail($emailData);
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function updateOperApprovers($params){
        try {
            DB::commit();
            return [
                'is_success' => 'true'
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function savePpdOnly($params){ //dPpdProcessOnly

        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            return $request = $params['request'];
            if($qcSlipDetails->approval_status === 'DPRDPPDONLY'){

            }
            if($qcSlipDetails->approval_status === 'DENGGPPDONLY'){

            }
            if($qcSlipDetails->approval_status === 'DQCPPDONLY'){

            }
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function saveQualificationCertificationOper(Request $request){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $rapidxEmpNo =  session('global_user');
            $dateTime = now();
            $date = now()->toDateString();
            $time = now()->format('H:i:s');
            $qcSlipId = $request->qc_slips_id ?? '';
            // $qcSlipId = 2;
            $section = 'QC';
            $select_section = $request->text_select_section;
            $select_position = $request->text_select_position;
            $selectedSection = str_contains($select_section, 'PPD');

            // $select_position = 'Operator';
            // $select_section = 'TSF1';
            $params = [
                'section' => $section,
                'selectSection' => $select_section,
            ];
            $generateControlNumber = $this->generateControlNumber($params);

            if(blank($qcSlipId) || $qcSlipId === ""){ //ADD
                // QcSlipRequest $qcSlipRequest;
                $validatedData = app(QcSlipRequest::class)->validateResolved();
                $saveQcSlip =  [
                    'control_no' =>  $generateControlNumber['currentCtrlNo'],
                    'section_category' =>  $select_section,//TODO
                    'position_category' =>  $select_position,//TODO
                    'section' =>  $request->text_section_operator,
                    'series_name' =>  $request->text_series_operator,
                    'product_line' =>  $request->text_operator_product_line,
                    'created_by' =>  $rapidxEmpNo->rapidx_EmpNo,
                    'created_at' =>  now(),
                ];
                // $qcSlipId = QcSlip::insertGetId($saveQcSlip);
                $reasonOfCertification =  [
                    'qc_slips_id' => $qcSlipId,
                    'reason_of_certification' => implode(' | ',$request->text_certification_operator),
                    'transfer_flexibility' => implode(' | ',$request->transfer_flexibility),
                    'others' => $request->others,
                    'created_at' =>  now(),
                ];

                // $saveQcReasonCertification = QcReasonCertification::insert($reasonOfCertification);

                $collectOperatorEmployees = collect($request->operator_employees)->map(function($rowOperatorEmployees)use ($qcSlipId){
                    return [
                            'qc_slips_id' => $qcSlipId,
                            'employee_no' => $rowOperatorEmployees['empId'],
                            'station_from' => $rowOperatorEmployees['stFrom'],
                            'station_to' => $rowOperatorEmployees['stTo'],
                            'remarks' => $rowOperatorEmployees['optRemarks'],
                            'created_at' =>  now(),
                    ];
                })
                ->values()
                ->all();

                QcSlipEmployee::insert($collectOperatorEmployees);
                //STATUS PB
                $currentApprovalStatus = 'APRODTO';
                // $operPreparedByApprovers =  [
                //     "qc_slips_id" => $qcSlipId,
                //     "approval_status" => $currentApprovalStatus,
                //     'first_approver'  => $rapidxEmpNo->rapidx_EmpNo,
                //     'first_date'=> $date,
                //     'first_time'=> $time,
                //     'first_status'=> 'PEN',
                // ];

                //$this->saveOperApprovers($operPreparedByApprovers);
            }
            if(filled($qcSlipId)){ //UPDATE
                $qcSlipDetails = QcSlip::where('id',$qcSlipId)->first();

                if($qcSlipDetails->approval_status === 'APRODTO'){
                    $currentApprovalStatus = $qcSlipDetails->approval_status;
                    $qcSlipDetails->update([
                        'status' => 'FORAPP'
                    ]);
                    $validatedData = app(AOperProdTrainingOrientationRequest::class)->validateResolved();
                    $aOperProdTrainingOrientations = [
                        "qc_slips_id" => $qcSlipId,
                        'traning_items'  => collect($request->text_training_orientation_ps_oper)->join(' | '),
                        'defect_escalation'  => collect($request->defect_escalation)->join(' | '),
                        'production_abnormality'  => collect($request->production_abnormality)->join(' | '),
                        'engg_tq_orientation_docs'  => collect($request->engg_tq_orientation_docs)->join(' | '),
                        'orientation_docs'  => collect($request->orientation_docs)->join(' | '),
                        'created_at' =>  now(),
                    ];

                    $operToApprovers =  [
                        "decision_status" => 'APP',
                        // "approval_status" => $currentApprovalStatus, //BENGGTQ
                        'first_approver'  =>  implode(' | ', (array) $request->text_first_trainedby_oper),
                        'first_approver_2'  => collect($request->text_first_mentoredby_oper)->join(' | '),
                        'first_date'=> $request->text_first_date_oper,
                        'first_time'=> $request->text_first_time_oper,
                        'first_status'=>$request->text_first_a_prod_result,
                        'first_remarks'=> "",

                        'second_approver' =>  collect($request->text_second_trainedby_oper)->join(' | '),
                        'second_approver_2' =>  collect($request->text_second_mentoredby_oper)->join(' | '),
                        'second_date'=> $request->text_second_date_oper,
                        'second_time'=> $request->text_second_time_oper,
                        'second_status'=>$request->text_second_a_prod_result,
                        'second_remarks'=> "",
                    ];
                    // DB::commit();
                    AOperProdTrainingOrientation::insert($aOperProdTrainingOrientations);
                }
                $currentApprovalStatus = $qcSlipDetails->approval_status;
                if($qcSlipDetails->approval_status === 'BENGGTQ'){
                    $validatedData = app(BOpEnggSectionTrainingOrientationRequest::class)->validateResolved();
                     $bEnggTqDetails =  [
                        "qc_slips_id" => $qcSlipId,
                        "traning_items"  => collect($request->text_training_orientation_es_oper)->join(' | '),
                        "engg_orientation_docs"  => collect($request->engg_orientation_docs)->join(' | '),
                        "obs_first_result_es_oper"=> $request->text_obs_first_result_es_oper,//PASSED
                        "first_sample_es_oper"  =>  $request->text_first_sample_es_oper, //INT
                        "first_ok_es_oper"  =>  $request->text_first_ok_es_oper,//INT
                        "first_ng_es_oper"  =>  $request->text_first_ng_es_oper,//INT
                        // "machine_abnormality"  =>  $request->machine_abnormality,//INT

                        "obs_second_result_es_oper"=> $request->text_obs_second_result_es_oper,//PASSED
                        "second_sample_es_oper"  =>  $request->text_second_sample_es_oper,//INT
                        "second_ok_es_oper"  =>  $request->text_second_ok_es_oper,//INT
                        "second_ng_es_oper"  =>  $request->text_second_ng_es_oper,//INT
                    ];
                    // DB::commit();
                    BOpEnggSectionTrainingOrientation::insert($bEnggTqDetails);
                    $operToApprovers = [ //bEnggTrainingQualificationApprover
                        "decision_status" => 'APP',
                        "first_approver"  =>  collect($request->text_1st_qualifiedby_es_oper)->join(' | '),
                        "first_date"  =>  $request->text_qc_1st_date_es_oper,//date
                        "first_time"  =>  $request->text_qc_1st_time_es_oper,//time
                        "first_status"=> $request->text_oa_1st_result_es_oper,//PASSED
                        "first_remarks"  =>  $request->text_1st_disqualification_es_oper,//remarks

                        "second_approver"  => collect($request->text_2nd_qualifiedby_es_oper)->join(' | '),
                        "second_date"  =>  $request->text_qc_2nd_date_es_oper,//date
                        "second_time"  =>  $request->text_qc_2nd_time_es_oper,//time
                        "second_status"=> $request->text_oa_2nd_result_es_oper,//PASSED
                        "second_remarks"  =>  $request->text_2nd_disqualification_es_oper,//INT
                    ];
                }
                if($qcSlipDetails->approval_status === 'CQCC'){
                    $validatedData = app(CQcCertificationRequest::class)->validateResolved();
                    $cQcCertification = [
                        "qc_slips_id" => $qcSlipId, //decision
                        "obs_first_result_qcs_oper" =>  $request->text_obs_first_result_qcs_oper, //PASSED
                        "obs_second_result_qcs_oper" =>  $request->text_obs_second_result_qcs_oper, //PASSED
                        "first_sample_qcs_oper" =>  $request->text_first_sample_qcs_oper, //1
                        "second_sample_qcs_oper" =>  $request->text_second_sample_qcs_oper,//1
                        "first_ok_qcs_oper" =>  $request->text_first_ok_qcs_oper,//1
                        "first_ng_qcs_oper" =>  $request->text_first_ng_qcs_oper,//1
                        "second_ok_qcs_oper" =>  $request->text_second_ok_qcs_oper,//1
                        "second_ng_qcs_oper" =>  $request->text_second_ng_qcs_oper,//1
                        'updated_by' => $rapidxEmpNo->rapidx_EmpNo,//1
                        "qcs_station_1st_oper"  =>  collect($request->text_qcs_station_1st_oper)->join(' | '),
                        "qcs_station_2nd_oper"  =>  collect($request->text_qcs_station_2nd_oper)->join(' | '),
                    ];
                    $arrFinalApprover = [ //Save the Final Approver
                        "oper_approved_confirmed_by"  =>  collect($request->text_qcs_station_1st_oper)->join(' | '),
                    ];
                    QcSlip::where('id',$qcSlipId)->update($arrFinalApprover);
                    CQcCertification::insert($cQcCertification);
                   $operToApprovers = [
                        "decision_status" => 'APP',
                        "first_approver"  =>  collect($request->text_1st_certifiedby_qcs_oper)->join(' | '),
                        "first_date" =>  $request->text_1st_date_qcs_oper,
                        "first_time" =>  $request->text_1st_time_qcs_oper,
                        "first_status" =>  $request->text_oa_1st_result_qcs_oper,
                        "first_remarks" =>  $request->text_1st_disapproval_qcs_oper,
                        "second_approver"  =>  collect($request->text_2nd_certifiedby_qcs_oper)->join(' | '),
                        "second_date" =>  $request->text_2nd_date_qcs_oper,
                        "second_time" =>  $request->text_2nd_time_qcs_oper,
                        "second_status" =>  $request->text_oa_2nd_result_qcs_oper,
                        "second_remarks" =>  $request->text_2nd_disapproval_qcs_oper,
                    ];

                }
                DB::commit();

                if($selectedSection){
                    $ppdParams = [
                        'request' => $request->all()
                    ];
                    $this->savePpdOnly($ppdParams);
                }
                if($qcSlipDetails->approval_status ==='EQCVP'){
                    // EQCVP- EQcValidationProcess
                    // Change status into Go to PROCESS E
                    // BUKOD Database
                    $validatedData = app(EQcValidationProcessRequest::class)->validateResolved();
                    $eQcValidationProcess = [
                        "qc_slips_id" => $qcSlipId,
                        "vpqcs_oper" => $request->text_vpqcs_oper,//CHECKBOX Production Abnormality Control | Defect Escalation Procedure
                        "application_vpqcs_oper" =>  $request->text_application_vpqcs_oper, //MULTIPLE DROPDOWN Production Abnormality Control | Applicable | Not Applicable |
                    ];
                    // DB::commit();
                    // EQcValidationProcess::insert($eQcValidationProcess);
                    $operToApprovers =  [
                        "decision_status" => 'APP',
                        "first_status" =>  $request->text_first_result_vpqcs_oper,
                        "first_approver"=>  collect($request->text_1st_validatedby_vpqcs_oper)->join(' | '), //R152 - 2trainedby
                        "first_date" =>  $request->text_1st_date_vpqcs_oper,
                        'first_time'=> "",
                        //3rd day
                        "first_status_2" =>  $request->text_first_result_vpes_oper_2, //PASSED
                        "first_approver_2"=>  collect($request->text_1st_validatedby_vpes_oper_2)->join(' | '),//R152 - 2trainedby
                        "first_date_2" =>  $request->text_1st_date_vpes_oper_2,
                        "first_remarks" =>  $request->text_remarks_vpqcs_oper,
                        "second_status" =>  $request->text_second_result_vpqcs_oper,
                        "second_approver"=> collect($request->text_2nd_validatedby_vpqcs_oper)->join(' | '),
                        "second_date" =>  $request->text_2nd_date_vpqcs_oper,
                        "second_status_2" =>  $request->text_second_result_vpes_oper_2, //PASSED
                        "second_approver_2"=> collect($request->text_2nd_validatedby_vpes_oper_2)->join(' | '),//R152 - 2trainedby
                        "second_date_2" =>  $request->text_2nd_date_vpes_oper_2,
                        'second_time'=> "",
                        "second_remarks" =>  $request->text_remarks_vpes_oper_2,

                    ];
                }
                if($qcSlipDetails->approval_status ==='FQCVVO'){
                    $fQcValidationVisualOperator = [
                        "qc_slips_id" => $qcSlipId,
                        'refdocno_input_qcvvo_oper' => $request->text_refdocno_input_qcvvo_oper,
                        'refdocno_input_qcvvo_oper_2' => $request->text_refdocno_input_qcvvo_oper_2,
                    ];
                    $operToApprovers = [
                        "decision_status" => 'APP',
                        "first_approver"  =>  collect($request->text_validated1_qcvvo_oper)->join(' | '),
                        "first_date" =>  $request->text_date1_qcvvo_oper,
                        "first_time" =>  "",
                        "first_status" =>  $request->text_obs_first_result_es_oper,
                        "first_remarks" =>  "",

                        "second_approver"  =>  collect($request->text_validated2_qcvvo_oper)->join(' | '),
                        "second_date" =>  $request->text_date2_qcvvo_oper,
                        "second_time" =>  "",
                        "second_status" =>  $request->text_oa_2nd_result_es_oper,
                        "second_remarks" =>  "",
                    ];
                    // FQcValidation::insert($fQcValidationVisualOperator);
                }
            }
            // $request->all();
            //=== Update the Operator Approvers based on the Current Status
            $opApprover =  OpApprover::where('qc_slips_id',$qcSlipId)->where('approval_status',$currentApprovalStatus)->update($operToApprovers);
            //=== Update the Approval Status and Insert the new Approval Status and Emails to the Next Approvers
           $changeApprovalStatusParams = [
                'qcSlipsId' => $qcSlipId,
                'approval_status'=> $currentApprovalStatus,
                'selectedSection'=> $request->text_select_section,
            ];
            $getNewStatus =  $this->changeApprovalStatus($changeApprovalStatusParams);
            if($currentApprovalStatus === 'FQCVVO'){ //FQCVVO to QCAPP - Final Approver QC Supervisor
                $emailParams = [ //FOR QC
                    'qc_slips_id' => $qcSlipId,
                    'update_data'=> [
                        'qc_slips_id' => $qcSlipId,
                        'approval_status'=> $getNewStatus['newStatus'],
                        'alert_prod_sec' => $qcSlipDetails->oper_approved_confirmed_by,
                        'alert_prod_cc_sec' => '',
                    ],
                    'approval_status'=> $currentApprovalStatus,
                ];
            }else{
                $emailParams = [
                    'qc_slips_id' => $qcSlipId,
                    'update_data'=> [
                        'qc_slips_id' => $qcSlipId,
                        'approval_status'=> $getNewStatus['newStatus'],
                        // "decision_status" => $getNewStatus['newStatus'],
                        'alert_prod_sec' => collect($request->text_alert_prod_sec)->join(' | '),
                        'alert_prod_cc_sec' => collect($request->text_alert_prod_cc_sec)->join(' | '),
                    ],
                    'approval_status'=> $currentApprovalStatus,
                ];
            }
            DB::commit();
            //ADD ELSE TO QC Supervisor Approval for OPERATOR
            // return 'DONE';
            $this->saveFormSendEmail($emailParams);
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function dPpdProcessOnly($request){
         //Change status into D if the SECTION IS PPS ELSE go to E VALIDATION PROCESS
        //STATUS DPRDPPDONLY DENGGPPDONLY DQCPPDONLY
       return $dPpdCertificationCompletionApprover = [
            "1st_certified_prod_peqcs_oper"  =>  implode(' | ', $request->text_1st_certified_prod_peqcs_oper),//R152 - 2trainedby
            "1st_certified_eng_peqcs_oper"  =>  implode(' | ', $request->text_1st_certified_eng_peqcs_oper),//R152 - 2trainedby
            "1st_certified_qc_peqcs_oper"  =>  implode(' | ', $request->text_1st_certified_qc_peqcs_oper),//R152 - 2trainedby
            "first_date" =>  $request->text_1st_date_peqcs_oper,
            "first_time" =>  $request->text_1st_time_peqcs_oper,
            "first_status" =>  $request->text_oa_1st_result_peqcs_oper,
            "first_remarks" =>  $request->text_1st_disapproval_peqcs_oper,

            "2nd_certified_prod_peqcs_oper"  =>  implode(' | ', $request->text_2nd_certified_prod_peqcs_oper),//R152 - 2trainedby
            "2nd_certified_eng_peqcs_oper"  =>  implode(' | ', $request->text_2nd_certified_eng_peqcs_oper),//R152 - 2trainedby
            "2nd_certified_qc_peqcs_oper"  =>  implode(' | ', $request->text_2nd_certified_qc_peqcs_oper),//R152 - 2trainedby
            "second_date" =>  $request->text_2nd_date_peqcs_oper,
            "second_time" =>  $request->text_2nd_time_peqcs_oper,
            "second_status" =>  $request->text_oa_2nd_result_peqcs_oper,
            "second_remarks" =>  $request->text_2nd_disapproval_peqcs_oper,
            'd_alert_prod_sec'=> $request->d_text_alert_prod_sec,
            'd_alert_prod_cc_sec'=> $request->d_text_alert_prod_cc_sec,
        ];
        $dPpdCertificationCompletion =  [
           "lot_1st_sample_peqcs_oper"=>  $request->text_lot_1st_sample_peqcs_oper, //INT
           "1st_injected_ng_peqcs_oper"=>  $request->text_1st_injected_ng_peqcs_oper, //INT
           "1st_detected_ng_peqcs_oper"=>  $request->text_1st_detected_ng_peqcs_oper, //INT
           "2nd_sample_peqcs_oper"=>  $request->text_2nd_sample_peqcs_oper, //INT
           "2nd_injected_ng_peqcs_oper"=>  $request->text_2nd_injected_ng_peqcs_oper, //INT
           "2nd_detected_ng_peqcs_oper"=>  $request->text_2nd_detected_ng_peqcs_oper, //INT
        ];

        try {
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function index(Request $request){
        return $eQcValidationProcess =  [
            //2nd day
            "vpqcs_oper" => $request->text_vpqcs_oper, //CHECKBOX
            "first_status" =>  $request->text_first_result_vpqcs_oper,//PASSED
            "first_approver"=>  collect($request->text_1st_validatedby_vpqcs_oper)->join(' | '), //R152 - 2trainedby
            "first_date" =>  $request->text_1st_date_vpqcs_oper,

            "second_status" =>  $request->text_second_result_vpqcs_oper,
            "text_2nd_validatedby_vpqcs_oper"=> collect($request->text_2nd_validatedby_vpqcs_oper)->join(' | '),//R152 - 2trainedby
            "second_date" =>  $request->text_2nd_date_vpqcs_oper,
            "first_remarks" =>  $request->text_remarks_vpqcs_oper,

            //3rd day
            "text_vpqcs_oper_1_1" =>  $request->text_vpqcs_oper_1_1, //CHECKBOX
            "first_status_2" =>  $request->text_first_result_vpes_oper_2, //PASSED
            "first_approver_2"=>  collect($request->text_1st_validatedby_vpes_oper_2)->join(' | '),//R152 - 2trainedby
            "first_date_2" =>  $request->text_1st_date_vpes_oper_2,
            "text_application_vpqcs_oper" =>  $request->text_application_vpqcs_oper, //DROPDOWN
            "second_status_2" =>  $request->text_second_result_vpes_oper_2, //PASSED
            "second_approver_2"=> collect($request->text_2nd_validatedby_vpes_oper_2)->join(' | '),//R152 - 2trainedby
            "second_date_2" =>  $request->text_2nd_date_vpes_oper_2,
            "text_remarks_vpes_oper_2" =>  $request->text_remarks_vpes_oper_2,
        ];
        try {
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function changeApprovalStatus($params){
        $selectedSection = str_contains($params['selectedSection'], 'PPD');
        switch (true) {
            case ($params['approval_status'] === 'PB'):
                $newStatus = 'APRODTO';
                $statusName = 'A Production Training Orientation';
                break;

            case ($params['approval_status'] === 'APRODTO'):
                $newStatus = 'BENGGTQ';
                $statusName = 'B Engineer Training Qualification';
                break;

            case ($params['approval_status'] === 'BENGGTQ'):
                $newStatus = 'CQCC';
                $statusName = 'C Qc Certification';
                break;
            case ($params['approval_status'] === 'CQCC' && $selectedSection != 1):
                $newStatus = 'EQCVP';
                $statusName = 'E Qc Validation Process';
                break;
            case ($params['approval_status'] === 'CQCC' && $selectedSection):
                $newStatus = 'DPRDPPDONLY';
                $statusName = 'D Production Update';
                break;
            case ($params['approval_status'] === 'DPRDPPDONLY'):
                $newStatus = 'DENGGPPDONLY';
                $statusName = 'D Engineering Update';
                break;
            case ($params['approval_status'] === 'DENGGPPDONLY'):
                $newStatus = 'DQCPPDONLY';
                $statusName = 'D QC Update';
                break;
            case ($params['approval_status'] === 'DQCPPDONLY'):
                $newStatus = 'EQCVP';
                $statusName = 'E Qc Validation Process';
                break;
            case ($params['approval_status'] === 'EQCVP'):
                $newStatus = 'FQCVVO';
                $statusName = 'F Qc Validation Visual Operator';
                break;

            case ($params['approval_status'] === 'FQCVVO'):
                $newStatus = 'QCAPP'; // QC Supervisor Approval
                $statusName = 'CLOSED';
                break;

            default:
                $newStatus = 'N/A';
                $statusName = 'N/A';
                break;
        }

        QcSlip::where('id',$params['qcSlipsId'])->update([
            'approval_status'=> $newStatus
        ]);
        return [
          "newStatus" => $newStatus
        ];
    }
    public function getDivDeptSec(Request $request){

        try {
            $section = SystemHrisViewDivDeptSec::where('Division', '!=', '-')
                ->where('Division', '!=', 'Administration')
                ->whereNotNull('Section')
                ->select('Section')
                ->distinct()
                ->orderBy('Section')
                ->pluck('Section');

            return response()->json(['is_success' => 'true', 'section' => $section]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function getDropdownMasterDetailsByFkid(Request $request){

        try {
         $data = DropdownMasterDetail::
            where('dropdown_masters_id', $request->dropdown_masters_id)
            ->where('status',1)
            ->get(['dropdown_masters_details','id']);
            $masterDetails = collect($data)->map(function($rowMasterDetails){

                   return [
                        'id'=> $rowMasterDetails['id'],
                        'text'=> $rowMasterDetails['dropdown_masters_details'],
                    ];
            });
            return response()->json(['is_success' => 'true', 'data' => $masterDetails]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function generateControlNumber($params){
        date_default_timezone_set('Asia/Manila');
        //Systemon HRIS / Subcon

        $qcSlip = QcSlip::orderBy('id','desc')->whereYear('created_at',now())
        ->whereNull('deleted_at')
        ->limit(1)->get(['control_no']);

        if(count( $qcSlip ) != 0){
            $currentCtrlNo = explode('-',$qcSlip[0]->control_no);
            $arrCtrNo		 	= end($currentCtrlNo);
            $series 	 	= str_pad(($arrCtrNo+1),3,"0",STR_PAD_LEFT);
            $currentCtrlNo = $params['section']."-".$params['selectSection']."-".date('m').date('y').'-'.$series;

        }else{
            $currentCtrlNo = $params['section']."-".$params['selectSection']."-".date('m').date('y').'-001';
        }
        return [
            'currentCtrlNo' => $currentCtrlNo
        ];
        $rapidx_user = DB::connection('mysql_rapidx')
        ->select(" SELECT department_group
            FROM departments
            WHERE department_id = '".session('rapidx_department_id')."'
        ");
        $hris_data = DB::connection('mysql_systemone_hris')
        ->select("SELECT Department,Division,Section FROM vw_employeeinfo WHERE EmpNo = '".session('rapidx_employee_number')."'");
        $subcon_data = DB::connection('mysql_systemone_subcon')
        ->select("SELECT Department,Division,Section FROM vw_employeeinfo WHERE EmpNo = '".session('rapidx_employee_number')."'");
        if(count($hris_data) > 0 && count($rapidx_user)> 0){
            $vwEmployeeinfo =  $hris_data;
            $filteredSection = str_replace("'", "", $this->getFilteredSection($vwEmployeeinfo[0]->Department));
            $division =($rapidx_user[0]->department_group == "PPS" || $rapidx_user[0]->department_group == "PPD") ? "PPD" : (($rapidx_user[0]->department_group == "LOG" || $rapidx_user[0]->department_group == "ISS" || $rapidx_user[0]->department_group == "FIN" ) ? "ADMIN" :
            $rapidx_user[0]->department_group);
        }
        if(count($subcon_data) > 0 && count($rapidx_user) > 0){
            $vwEmployeeinfo =  $subcon_data;
            $filteredSection = str_replace("'", "", $this->getFilteredSection($vwEmployeeinfo[0]->Department));
            $division = ($rapidx_user[0]->department_group == "PPS" || $rapidx_user[0]->department_group == "PPD") ? "PPD" : (($rapidx_user[0]->department_group == "LOG" || $rapidx_user[0]->department_group == "ISS" || $rapidx_user[0]->department_group == "FIN")  ? "ADMIN" :
            $rapidx_user[0]->department_group);
        }
        // Check if the Created At & App No / Division / Material Category is exisiting
        // Example:TS-ADMIN-LOG-PCH-25-01-001
        $ecr = Ecr::orderBy('id','desc')->whereYear('created_at',now())
            ->whereNull('deleted_at')
            ->limit(1)->get(['ecr_no']);
        //If not exist reset the ecr to 1 ???
        if(count( $ecr ) != 0){
            $currentCtrlNo = explode('-',$ecr[0]->ecr_no);
            $arrCtrNo		 	= end($currentCtrlNo);
            $series 	 	= str_pad(($arrCtrNo+1),3,"0",STR_PAD_LEFT);
            $currentCtrlNo = $division."-".$filteredSection."-".date('m').date('y').'-'.$series;

        }else{
            $currentCtrlNo = $division."-".$filteredSection."-".date('m').date('y').'-001';
        }
        return [
            'currentCtrlNo' => $currentCtrlNo
        ];
    }
}

