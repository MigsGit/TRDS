<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommonController;
use App\Http\Requests\QcSlipEmployeeRequest;
use App\Http\Requests\QcSlipRequest;
use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetail;
use App\Model\Qc\QcReasonCertification;
use App\Model\Qc\QcSlipEmployee;
use App\Model\QcSlip;
use App\Model\SystemHrisViewDivDeptSec;
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

    public function getQcSlipsById(Request $request){
      
        try {
            return $qcSlip = QcSlip::with('product_line','op_approvers')
            ->where('id',$request->qcSlipsId)
            ->whereNull('deleted_at')
            ->get();    
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function loadQcSlip(Request $request){
       $qcSlip = QcSlip::with('product_line','op_approvers')
        ->whereNull('deleted_at')
        ->get();
        try {
            return DataTables($qcSlip)
            ->addColumn('rawAction',function ($row) use ($request){
                $result = '';
                $result .= '<center>';
                $result .= '<button class="btn btn-sm btn-outline-primary" type="button" qc-slips-id="'.$row->id.'" id="btnGetQcSlipsId"><i class="fa-solid fa fa-edit"></i></button>';
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
                $result .= '<span class="badge rounded-pill bg-danger"> '.$getApprovalStatus['approvalStatus'].' '.$currentApprover.' </span>';
                $result .= '</center>';
                $result .= '</br>';
                return $result;
            })
            ->addColumn('trained_by',function ($row){
                // $personInCharge = $row->rapidx_user_person_in_charge->name ?? '';
                // $personInCharge = $row;
                return $result = '';
                $result .= '<center>';
                $result .= '<span> '.$personInCharge.' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->addColumn('certified_by',function ($row){
                // $personInCharge = $row->rapidx_user_person_in_charge->name ?? '';
                // $personInCharge = $row;
                return $result = '';
                $result .= '<center>';
                $result .= '<span> '.$personInCharge.' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->addColumn('approvers',function ($row){
                // $personInCharge = $row->rapidx_user_person_in_charge->name ?? '';
                // $personInCharge = $row;
                return $result = '';
                $result .= '<center>';
                $result .= '<span> '.$personInCharge.' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->rawColumns(['rawAction','rawStatus','approvers'])
            ->make(true);
            return response()->json(['is_success' => 'true']);
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
            $opApprover =  OpApprover::where('qc_slips_id',$params['qc_slips_id'])
            ->where('approval_status',$params['approval_status'])
            ->get();
            // $opApprover->update($params['update_data']);
            $emailParams = [
                'qc_slips_id' => $params['qc_slips_id']
            ];
            $message = $this->commonController->emailMsg($emailParams);
            $from = 'issinfoservice@pricon.ph';
            $from_name = 'issinfoservice@pricon.ph';
            $emailData = [
                // "to" =>$to,
                "to" =>"mrronquez@pricon.ph",
                "cc" =>"",
                "bcc" =>"mclegaspi@pricon.ph",
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

            // DB::commit();
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

    public function saveQualificationCertificationOper(Request $request,QcSlipRequest $qcSlipRequest){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $rapidxEmpNo =  session('global_user');
            $dateTime = now();
            $date = now()->toDateString();
            $time = now()->format('H:i:s');
            $qcSlipId = $request->qc_slips_id;
            $qcSlipId = 2;
            $section = 'QC';
            // $select_section = $request->select_section;
            // $select_position = $request->text_select_position;
            $select_position = 'Operator';
            $select_section = 'TSF1';
            $params = [
                'section' => $section,
                'selectSection' => $select_section,
            ];
            $generateControlNumber = $this->generateControlNumber($params);
                // $qcSlipId = QcSlip::insertGetId($saveQcSlip);
            $emailParams = [
                'qc_slips_id' => $qcSlipId,
                'update_data'=> [
                    'alert_prod_sec' => implode(' | ',$request->text_alert_prod_sec),
                    'alert_prod_cc_sec' => implode(' | ',$request->text_alert_prod_cc_sec),
                ],
                'approval_status'=> 'PB',
            ];
            // DB::commit();
            // return $this->saveFormSendEmail($emailParams); //put in the end of else

            $saveQcSlip =  [
                'control_no' =>  $generateControlNumber['currentCtrlNo'],
                'section_category' =>  $select_section,//TODO
                'position_category' =>  $select_position,//TODO
                'section' =>  $request->text_section_operator,
                'series_name' =>  $request->text_series_operator,
                'product_line' =>  $request->text_operator_product_line,
                'created_by' =>  $rapidxEmpNo->rapidx_emp_no,
                'created_at' =>  now(),
            ];
        

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

            //  return   QcSlipEmployee::insert($collectOperatorEmployees);
            //STATUS PB
            $operPreparedByApprovers =  [
                "qc_slips_id" => $qcSlipId,
                "approval_status" => 'PB',
                'first_approver'  => $rapidxEmpNo->rapidx_emp_no,
                'first_date'=> $date,
                'first_time'=> $time,
                'first_status'=> 'PEN',
            ];
        //    return  $this->saveOperApprovers($operPreparedByApprovers);
            //ELSE
            //CHANGE STATUS PB TO APRODTO
            return $qcSlipDetails = QcSlip::where('id',$qcSlipId)->first();
            $qcSlipDetails->approval_status;
            return $aOperToApprovers =  [
                "qc_slips_id" => $qcSlipId,
                "approval_status" => 'APRODTO', //BENGGTQ
                'first_approver'  =>  implode(' | ', $request->text_first_trainedby_oper),
                'first_approver_2'  =>  implode(' | ', $request->text_first_mentoredby_oper),
                'first_date'=> $request->text_first_date_oper,
                'first_time'=> $request->text_first_time_oper,
                'first_status'=> $request->first_status,
                'first_remarks'=> "",

                'second_approver' =>  implode(' | ', $request->text_second_trainedby_oper),
                'second_approver_2' =>  implode(' | ', $request->text_second_mentoredby_oper),
                'second_date'=> $request->text_second_date_oper,
                'second_time'=> $request->text_second_time_oper,
                'second_status'=> $request->first_status,
                'second_remarks'=> "",
            ];
            // 'alert_prod_sec'=> $request->text_alert_prod_sec,
            // 'alert_prod_cc_sec'=> $request->text_alert_prod_cc_sec,
            // DB::commit();
            OpApprover::insert($aOperToApprovers);

            // AOperProdTrainingOrientation::insert($aOperProdTrainingOrientations);
            //Change status into B
            //CHANGE STATUS BENGGTQ
             $bEnggTqDetails =  [
                "text_training_orientation_es_oper"  =>  implode(' | ', $request->text_training_orientation_es_oper),//multiple DP
                "engg_orientation_docs"  =>  implode(' | ', $request->engg_orientation_docs),//multiple DP
                "obs_first_result_es_oper"=> $request->text_obs_first_result_es_oper,//PASSED
                "first_sample_es_oper"  =>  $request->text_first_sample_es_oper, //INT
                "first_ok_es_oper"  =>  $request->text_first_ok_es_oper,//INT
                "first_ng_es_oper"  =>  $request->text_first_ng_es_oper,//INT
                "1st_disqualification_es_oper"  =>  $request->text_1st_disqualification_es_oper,//INT
                "machine_abnormality"  =>  $request->machine_abnormality,//INT


                "obs_second_result_es_oper"=> $request->text_obs_second_result_es_oper,//PASSED
                "second_sample_es_oper"  =>  $request->text_second_sample_es_oper,//INT
                "second_ok_es_oper"  =>  $request->text_second_ok_es_oper,//INT
                "second_ng_es_oper"  =>  $request->text_second_ng_es_oper,//INT
                "2nd_disqualification_es_oper"  =>  $request->text_2nd_disqualification_es_oper,//INT
            ];
            $bEnggTrainingQualificationApprover = [
                "first_approver"  =>  implode(' | ', $request->text_1st_qualifiedby_es_oper),//R152 - 1trainedby
                "first_date"  =>  $request->text_qc_1st_date_es_oper,//date
                "first_time"  =>  $request->text_qc_1st_time_es_oper,//time
                "first_status"=> $request->text_oa_1st_result_es_oper,//PASSED
                'first_remarks'=> "",

                "second_approver"  =>  implode(' | ', $request->text_2nd_qualifiedby_es_oper),//R152 - 2trainedby
                "second_date"  =>  $request->text_qc_2nd_date_es_oper,//date
                "second_time"  =>  $request->text_qc_2nd_time_es_oper,//time
                "second_status"=> $request->text_oa_2nd_result_es_oper,//PASSED
                'second_remarks'=> "",
                "approval_status" => 'BENGGTQ'
            ];

            // BEnggTrainingQualification::insert($bEnggTqApprovers);
            // OpApprover::insert($bEnggTrainingQualificationApprover);
            //Change status into C

        $cQcCertification = [
             "text_obs_first_result_qcs_oper" =>  $request->text_obs_first_result_qcs_oper, //PASSED
            "text_obs_second_result_qcs_oper" =>  $request->text_obs_second_result_qcs_oper, //PASSED
            "text_first_sample_qcs_oper" =>  $request->text_first_sample_qcs_oper, //1
            "text_second_sample_qcs_oper" =>  $request->text_second_sample_qcs_oper,//1
            "text_first_ok_qcs_oper" =>  $request->text_first_ok_qcs_oper,//1
            "text_first_ng_qcs_oper" =>  $request->text_first_ng_qcs_oper,//1
            "text_second_ok_qcs_oper" =>  $request->text_second_ok_qcs_oper,//1
            "text_second_ng_qcs_oper" =>  $request->text_second_ng_qcs_oper,//1
            "text_qcs_station_1st_oper"  =>  implode(' | ', $request->text_qcs_station_1st_oper),//MULTIPLE
            "text_qcs_station_2nd_oper"  =>  implode(' | ', $request->text_qcs_station_2nd_oper),//MULTIPLE
        ];

         $cQcCertificationApprover = [
            "first_approver"  =>  implode(' | ', $request->text_1st_certifiedby_qcs_oper),//R152 - 2trainedby
            "first_date" =>  $request->text_1st_date_qcs_oper,
            "first_time" =>  $request->text_1st_time_qcs_oper,
            "first_status" =>  $request->text_oa_1st_result_qcs_oper,
            "first_remarks" =>  $request->text_1st_disapproval_qcs_oper,
            "second_approver"  =>  implode(' | ', $request->text_2nd_certifiedby_qcs_oper),//R152 - 2trainedby
            "second_date" =>  $request->text_2nd_date_qcs_oper,
            "second_time" =>  $request->text_2nd_time_qcs_oper,
            "second_status" =>  $request->text_oa_2nd_result_qcs_oper,
            "second_remarks" =>  $request->text_2nd_disapproval_qcs_oper,
         ];

        //Change status into D if the SECTION IS PPS ELSE go to E VALIDATION PROCESS
        //STATUS DPRDPPDONLY DENGGPPDONLY DQCPPDONLY
        $cQcCertificationApprover = [
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
        return $dPpdCertificationCompletion =  [
           "lot_1st_sample_peqcs_oper"=>  $request->text_lot_1st_sample_peqcs_oper, //INT
           "1st_injected_ng_peqcs_oper"=>  $request->text_1st_injected_ng_peqcs_oper, //INT
           "1st_detected_ng_peqcs_oper"=>  $request->text_1st_detected_ng_peqcs_oper, //INT
           "2nd_sample_peqcs_oper"=>  $request->text_2nd_sample_peqcs_oper, //INT
           "2nd_injected_ng_peqcs_oper"=>  $request->text_2nd_injected_ng_peqcs_oper, //INT
           "2nd_detected_ng_peqcs_oper"=>  $request->text_2nd_detected_ng_peqcs_oper, //INT
        ];
        // EQCVP- EQcValidationProcess
        // Change status into Go to PROCESS E
        // BUKOD Database
        return $dPpdCertificationCompletion =  [
            //2nd day
            "vpqcs_oper" => $request->text_vpqcs_oper,
            "first_status" =>  $request->text_first_result_vpqcs_oper,//PASSED
            "first_approver"=> implode(' | ', $request->text_1st_validatedby_vpqcs_oper),//R152 - 2trainedby
            "first_date" =>  $request->text_1st_date_vpqcs_oper,

            "second_status" =>  $request->text_second_result_vpqcs_oper,
            "text_2nd_validatedby_vpqcs_oper"=> implode(' | ', $request->text_2nd_validatedby_vpqcs_oper),//R152 - 2trainedby
            "second_date" =>  $request->text_2nd_date_vpqcs_oper,
            "first_remarks" =>  $request->text_remarks_vpqcs_oper,

            //3rd day
            "text_vpqcs_oper_1_1" =>  $request->text_vpqcs_oper_1_1, //CHECKBOX
            "first_status_2" =>  $request->text_first_result_vpes_oper_2, //PASSED
            "first_approver_2"=> implode(' | ', $request->text_1st_validatedby_vpes_oper_2),//R152 - 2trainedby
            "first_date_2" =>  $request->text_1st_date_vpes_oper_2,
            "text_application_vpqcs_oper" =>  $request->text_application_vpqcs_oper, //DROPDOWN
            "second_status_2" =>  $request->text_second_result_vpes_oper_2, //PASSED
            "second_approver_2"=> implode(' | ', $request->text_2nd_validatedby_vpes_oper_2),//R152 - 2trainedby
            "second_date_2" =>  $request->text_2nd_date_vpes_oper_2,
            "text_remarks_vpes_oper_2" =>  $request->text_remarks_vpes_oper_2,
        ];

            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function changeStatus($params){
        QcSlip::where('id',$params->qcSlipsId)->update([
            'status'=> $params->status
        ]);
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
