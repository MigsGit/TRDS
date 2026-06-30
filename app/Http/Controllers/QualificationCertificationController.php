<?php

namespace App\Http\Controllers;

use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetail;
use App\Model\Qc\QcSlipEmployee;
use App\Model\SystemHrisViewDivDeptSec;
use App\Http\Requests\QcSlipEmployeeRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QualificationCertificationController extends Controller
{
    public function saveQualificationCertificationOper(Request $request,QcSlipEmployeeRequest $qcSlipEmployeeRequest){
        try {
          return $request->all();
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $qcSlipId = 1;
            
            $qcSlip =  [
                // 'control_no' =>  $request->control_no,//TODO
                'section_category' =>  $request->select_section,
                'position_category' =>  $request->text_select_position,
                'section' =>  $request->text_section_operator,
                'series_operator' =>  $request->text_series_operator,
                'product_line' =>  $request->text_operator_product_line,
                // 'created_by' =>  $session(''),//TODO
            ];

             return  $reasonOfCertification =  [
                'qc_slip_id' => $qcSlipId,
                'reason_of_certification' => implode(' | ',$request->text_certification_operator),
                'transfer_flexibility' => implode(' | ',$request->transfer_flexibility),
                'others' => $request->others,
            ];

            $collectOperatorEmployees = collect($request->operator_employees)->map(function($rowOperatorEmployees)use ($qcSlipId){
                return [
                        'qc_slip_id' => $qcSlipId,
                        'employee_no' => $rowOperatorEmployees['empId'],
                        'station_from' => $rowOperatorEmployees['stFrom'],
                        'station_to' => $rowOperatorEmployees['stTo'],
                        'remarks' => $rowOperatorEmployees['optRemarks'],
                ];
            })
            ->values()
            ->all();
            //STATUS PB
            $aOperProdTrainingOrientations =  [
                'qc_slip_id' => $qcSlipId,
                'traning_items' => implode(' | ',$request->text_certification_operator),
                'production_abnormality' =>  $request->production_abnormality,
                'orientation_docs' =>  implode(' | ',$request->orientation_docs),
                'defect_escalation' =>  $request->defect_escalation,
            ];
            //STATUS PB,APRODTO
            $aOperToApprovers =  [
                "approval_status" => 'BENGGTQ',
                'first_approver'  =>  implode(' | ', $request->text_first_trainedby_oper),
                'first_mentoredby_approver'  =>  implode(' | ', $request->text_first_mentoredby_oper),
                'first_date'=> $request->text_first_date_oper,
                'first_time'=> $request->text_first_time_oper,
                'first_status'=> $request->first_status,
                'first_remarks'=> "",

                'second_approver' =>  implode(' | ', $request->text_second_trainedby_oper),
                'second_mentoredby_approver' =>  implode(' | ', $request->text_second_mentoredby_oper),
                'second_date'=> $request->text_second_date_oper,
                'second_time'=> $request->text_second_time_oper,
                'second_status'=> $request->first_status,
                'second_remarks'=> "",

                'alert_prod_sec'=> $request->text_alert_prod_sec,
                'alert_prod_cc_sec'=> $request->text_alert_prod_cc_sec,


            ];

            // QcSlipEmployee::insert($collectOperatorEmployees);
            // AOperProdTrainingOrientation::insert($aOperProdTrainingOrientations);
            // OpApprover::insert($aOperToApprovers);
            //Change status into B

            //STATUS BENGGTQ
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
                'second_remarks'=> $request->remarks,
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
         v];

        //Change status into D if the SECTION IS PPS ELSE go to E VALIDATION PROCESS


            
            DB::commit();
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
}
