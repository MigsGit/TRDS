<?php

namespace App\Http\Controllers;

use App\Model\QcSlip;
use App\Model\RapidMailer;
use App\Model\RapidXUser;
use App\Model\SystemOneHrisSubcon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
     public function saveQualificationCertificationOperREV1(Request $request){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $rapidxEmpNo           = session('global_user');
            $dateTime              = now();
            $date                  = now()->toDateString();
            $time                  = now()->format('H:i:s');
            $qcSlipId              = $this->getSafe($request, 'qc_slips_id', '');
            $isMachineOperatorExists = 0;
            $staticQc              = 'QC';
            $select_section            = $this->getSafe($request, 'select_section', '');
            $select_section_production = $this->getSafe($request, 'text_select_section', '');
            $select_position           = $this->getSafe($request, 'text_select_position', '');
            $text_date_of_transfer           = $this->getSafe($request, 'text_date_of_transfer', '');
            $selectedSection = str_contains($select_section, 'PPD');
            $params = [
                'section' => $staticQc,
                'selectSection' => $select_section,
                'positionCategory' => $select_position,
            ];
            $generateControlNumber = $this->generateControlNumber($params);
            if(blank($qcSlipId) || $qcSlipId === ""){ //ADD
                $validatedData = app(SendEmailRequest::class)->validateResolved();
                $validatedData = app(QcSlipRequest::class)->validateResolved();
                $saveQcSlip =  [
                    'control_no' =>  $generateControlNumber['currentCtrlNo'],
                    'section_category' =>  $select_section,
                    'position_category' =>  $select_position,
                    'section' =>  $select_section_production,
                    'date_of_transfer' =>  $text_date_of_transfer,
                    'series_name' =>  $this->getSafe($request, 'text_series_operator'),
                    'product_line' =>  $this->joinSafe($request, 'text_operator_product_line'),
                    'created_by' =>  $rapidxEmpNo->rapidx_emp_no,
                    'created_at' =>  now(),
                ];
                // $qcSlipId = 100;
                $qcSlipId = QcSlip::insertGetId($saveQcSlip);
                $reasonOfCertification =  [
                    'qc_slips_id' => $qcSlipId,
                    'reason_of_certification' =>  $this->joinSafe($request, 'text_certification_operator'),
                    'transfer_flexibility' => $this->joinSafe($request, 'transfer_flexibility'),
                    'others' => $this->getSafe($request, 'others'),
                    'created_at' =>  now(),
                ];

                 QcReasonCertification::insert($reasonOfCertification);

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
                $qcSlipEmployeeCount = QcSlipEmployee::where('qc_slips_id',$qcSlipId)->count();
                if ($qcSlipEmployeeCount === 0) {
                    return response()->json([
                        'is_success' => 'true',
                        'message' => 'Please Add Employee Details Above!'
                    ],409);

                }
                //STATUS PB
                $operToApprovers =  [];
            }
            //UPDATE OPERATOR APPROVAL STATUS
            $qcSlipDetails = QcSlip::where('id',$qcSlipId)->first();
            $currentPositionCategory = $qcSlipDetails->position_category;
            //Inspector PROCESS
            if($currentPositionCategory === 'Inspector'){ //ADD
                $currentApprovalStatus= 'ALQCTQ'; //LINE QUALITY CONTROL SECTION (Training and Qualification)
                $qcModelApprover = QcLqcApprover::class;
                $operToApprovers = [];
                if(filled($qcSlipId)){ //UPDATE {
                    // return 'true';
                    $currentApprovalStatus = $qcSlipDetails->approval_status;
                    if($qcSlipDetails->approval_status === 'ALQCTQ'){
                        $qcSlipDetails->update([
                            'status' => 'FORAPP'
                        ]);
                        $validatedData = app(ALqcTrainingQualificationRequest::class)->validateResolved();

                        $aLqcTrainingQuali = [
                            "qc_slips_id"              => $qcSlipId,
                            'training_orientation_inspector' => $this->joinSafe($request, 'text_training_orientation_inspector'),
                            // 'traning_items' => $this->joinSafe($request, 'text_training_orientation_ps_oper'),
                            'training_orientation_ins_4' => $this->getSafe($request, 'text_training_orientation_ins_4'),//A
                            'training_orientation_ins_13' => $this->getSafe($request, 'text_training_orientation_ins_13'),//B
                            'training_orientation_ins_21' => $this->getSafe($request, 'text_training_orientation_ins_21'),//C
                            'training_orientation_ins_54' => $this->getSafe($request, 'text_training_orientation_ins_54'),//P
                        ];
                        ALqcTrainingQualification::insert($aLqcTrainingQuali);
                        $operToApprovers = [
                            'decision_status'   => 'APP',
                            // Sec 1 – Training & Qualification
                            'first_approver'    => $this->joinSafe($request, 'text_certified_inspector'),
                            'first_approver_2'  => $this->joinSafe($request, 'text_mentored'),
                            'first_date'        => $this->getSafe($request, 'text_date_inspector'),
                            'first_time'        => $this->getSafe($request, 'text_time_inspector'),
                        ];
                    }
                    if($qcSlipDetails->approval_status === 'BLQCTC'){
                        $bLqcCertification = [
                            'qc_slips_id' => $qcSlipId,
                            'result_input1_inspector' => $this->getSafe($request, 'text_result_input1_inspector'),
                            'hands_on_inspector' => $this->joinSafe($request, 'text_hands_on_inspector'),
                            'hands_on_ins_3' => $this->getSafe($request, 'text_hands_on_ins_3'),
                        ];
                        BLqcCertification::insert($bLqcCertification);
                        $operToApprovers = [
                            'decision_status'   => 'APP',
                            'first_approver'   => $this->joinSafe($request, 'text_sec2_certified_inspector'),
                            'first_date'       => $this->getSafe($request, 'text_sec2_date_inspector'),
                            'first_time'       => $this->getSafe($request, 'text_sec2_time_inspector'),
                            'first_status'      => $this->getSafe($request, 'text_sel_result1_inspector'),
                            'first_remarks'     => $this->getSafe($request, 'text_result_input1_inspector'),
                            'first_status_2'    => $this->getSafe($request, 'text_sel_result2_inspector'),

                            'second_remarks'    => $this->getSafe($request, 'text_result_input2_inspector'),
                            'second_status'     => $this->getSafe($request, 'text_sec2_result_inspector'),
                        ];
                    }
                    if($qcSlipDetails->approval_status === 'CLQCOQC'){
                        // $cLqcOnly = [
                        //     'qc_slips_id' => $qcSlipId,
                        //     'ref_docno_input_inspector' => $this->getSafe($request, 'text_ref_docno_input_inspector'),
                        //     'ins_seq_inspector' => $this->joinSafe($request, 'text_ins_seq_inspector'),
                        // ];
                        $countCLqcTrainingItemResult = CLqcTrainingItemResult::where('qc_slips_id',$qcSlipId)->count();
                        if($countCLqcTrainingItemResult === 0 ){
                            return response()->json(['is_success' => 'false', "message" => "Please input the C Inspector Training / Certification And Validation Slip"],409);
                        }
                        // CLqcOqcValidation::insert($cLqcOnly);
                    }
                }

            }
            //OPERATOR PROCESS
            if($currentPositionCategory === 'Operator'){  //ADD
                $currentApprovalStatus = 'APRODTO';
                $qcModelApprover = OpApprover::class;
                if(filled($qcSlipId)){ //UPDATE OPERATOR DETAILS
                    $currentApprovalStatus = $qcSlipDetails->approval_status;
                    if($qcSlipDetails->approval_status != 'FQCVVO'){
                        $validatedData = app(SendEmailRequest::class)->validateResolved();
                    }
                    if($qcSlipDetails->approval_status === 'APRODTO'){
                        // $currentApprovalStatus = $qcSlipDetails->approval_status;
                        $qcSlipDetails->update([
                            'status' => 'FORAPP'
                        ]);
                        $validatedData = app(AOperProdTrainingOrientationRequest::class)->validateResolved();
                        $aOperProdTrainingOrientations = [
                            "qc_slips_id"              => $qcSlipId,
                            'traning_items'            => $this->joinSafe($request, 'text_training_orientation_ps_oper'),
                            'defect_escalation'        => $this->joinSafe($request, 'defect_escalation'),
                            'production_abnormality'   => $this->joinSafe($request, 'production_abnormality'),
                            'engg_tq_orientation_docs' => $this->joinSafe($request, 'engg_tq_orientation_docs'),
                            'orientation_docs'         => $this->joinSafe($request, 'orientation_docs'),
                            'created_at'               => now(),
                        ];

                        $operToApprovers = [
                            "decision_status"  => 'APP',
                            'first_approver'   => $this->joinSafe($request, 'text_first_trainedby_oper'),
                            'first_approver_2' => $this->joinSafe($request, 'text_first_mentoredby_oper'),
                            'first_date'       => $this->getSafe($request, 'text_first_date_oper'),
                            'first_time'       => $this->getSafe($request, 'text_first_time_oper'),
                            'first_status'     => $this->getSafe($request, 'text_first_a_prod_result'),
                            'first_remarks'    => '',

                            'second_approver'   => $this->joinSafe($request, 'text_second_trainedby_oper'),
                            'second_approver_2' => $this->joinSafe($request, 'text_second_mentoredby_oper'),
                            'second_date'       => $this->getSafe($request, 'text_second_date_oper'),
                            'second_time'       => $this->getSafe($request, 'text_second_time_oper'),
                            'second_status'     => $this->getSafe($request, 'text_second_a_prod_result'),
                            'second_remarks'    => '',
                        ];
                        AOperProdTrainingOrientation::insert($aOperProdTrainingOrientations);
                    }
                    // $currentApprovalStatus = $qcSlipDetails->approval_status;
                    if($qcSlipDetails->approval_status === 'BENGGTQ'){
                        $validatedData = app(BOpEnggSectionTrainingOrientationRequest::class)->validateResolved();
                        $bEnggTqDetails = [
                            "qc_slips_id"               => $qcSlipId,
                            "traning_items"             => $this->joinSafe($request, 'text_training_orientation_es_oper'),
                            "engg_orientation_docs"     => $this->joinSafe($request, 'engg_orientation_docs'),
                            "obs_first_result_es_oper"  => $this->getSafe($request, 'text_obs_first_result_es_oper'),
                            "first_sample_es_oper"      => $this->getSafe($request, 'text_first_sample_es_oper'),
                            "first_ok_es_oper"          => $this->getSafe($request, 'text_first_ok_es_oper'),
                            "first_ng_es_oper"          => $this->getSafe($request, 'text_first_ng_es_oper'),
                            "obs_second_result_es_oper" => $this->getSafe($request, 'text_obs_second_result_es_oper'),
                            "second_sample_es_oper"     => $this->getSafe($request, 'text_second_sample_es_oper'),
                            "second_ok_es_oper"         => $this->getSafe($request, 'text_second_ok_es_oper'),
                            "second_ng_es_oper"         => $this->getSafe($request, 'text_second_ng_es_oper'),
                        ];
                        BOpEnggSectionTrainingOrientation::insert($bEnggTqDetails);
                        $operToApprovers = [
                            "decision_status"  => 'APP',
                            "first_approver"   => $this->joinSafe($request, 'text_1st_qualifiedby_es_oper'),
                            "first_date"       => $this->getSafe($request, 'text_qc_1st_date_es_oper'),
                            "first_time"       => $this->getSafe($request, 'text_qc_1st_time_es_oper'),
                            "first_status"     => $this->getSafe($request, 'text_oa_1st_result_es_oper'),
                            "first_remarks"    => $this->getSafe($request, 'text_1st_disqualification_es_oper'),

                            "second_approver"  => $this->joinSafe($request, 'text_2nd_qualifiedby_es_oper'),
                            "second_date"      => $this->getSafe($request, 'text_qc_2nd_date_es_oper'),
                            "second_time"      => $this->getSafe($request, 'text_qc_2nd_time_es_oper'),
                            "second_status"    => $this->getSafe($request, 'text_oa_2nd_result_es_oper'),
                            "second_remarks"   => $this->getSafe($request, 'text_2nd_disqualification_es_oper'),
                        ];
                    }
                    if($qcSlipDetails->approval_status === 'CQCC'){
                        $isMachineOperatorExists = QcSlipEmployee::where("qc_slips_id",$qcSlipId)->where('station_to',4)->count();
                        $validatedData = app(CQcCertificationRequest::class)->validateResolved();
                        //   "qc_slips_id"               => $qcSlipId,
                        $cQcCertification = [
                            "qc_slips_id"               => $qcSlipId,
                            "obs_first_result_qcs_oper"  => $this->getSafe($request, 'text_obs_first_result_qcs_oper'),
                            "obs_second_result_qcs_oper" => $this->getSafe($request, 'text_obs_second_result_qcs_oper'),
                            "first_sample_qcs_oper"     => $this->getSafe($request, 'text_first_sample_qcs_oper'),
                            "second_sample_qcs_oper"    => $this->getSafe($request, 'text_second_sample_qcs_oper'),
                            "first_ok_qcs_oper"         => $this->getSafe($request, 'text_first_ok_qcs_oper'),
                            "first_ng_qcs_oper"         => $this->getSafe($request, 'text_first_ng_qcs_oper'),
                            "second_ok_qcs_oper"        => $this->getSafe($request, 'text_second_ok_qcs_oper'),
                            "second_ng_qcs_oper"        => $this->getSafe($request, 'text_second_ng_qcs_oper'),
                            'updated_by'                => $rapidxEmpNo->rapidx_emp_no,
                            "qcs_station_1st_oper"      => $this->joinSafe($request, 'text_qcs_station_1st_oper'),
                            "qcs_station_2nd_oper"      => $this->joinSafe($request, 'text_qcs_station_2nd_oper'),
                        ];
                        $arrFinalApprover = [
                            "oper_approved_confirmed_by" => $this->joinSafe($request, 'text_oper_approved_confirmed_by'),
                        ];
                        QcSlip::where('id',$qcSlipId)->update($arrFinalApprover);
                        CQcCertification::insert($cQcCertification);
                        $operToApprovers = [
                            "decision_status"  => 'APP',
                            "first_approver"   => $this->joinSafe($request, 'text_1st_certifiedby_qcs_oper'),
                            "first_date"       => $this->getSafe($request, 'text_1st_date_qcs_oper'),
                            "first_time"       => $this->getSafe($request, 'text_1st_time_qcs_oper'),
                            "first_status"     => $this->getSafe($request, 'text_oa_1st_result_qcs_oper'),
                            "first_remarks"    => $this->getSafe($request, 'text_1st_disapproval_qcs_oper'),
                            "second_approver"  => $this->joinSafe($request, 'text_2nd_certifiedby_qcs_oper'),
                            "second_date"      => $this->getSafe($request, 'text_2nd_date_qcs_oper'),
                            "second_time"      => $this->getSafe($request, 'text_2nd_time_qcs_oper'),
                            "second_status"    => $this->getSafe($request, 'text_oa_2nd_result_qcs_oper'),
                            "second_remarks"   => $this->getSafe($request, 'text_2nd_disapproval_qcs_oper'),
                        ];

                    }
                    if($selectedSection && $qcSlipDetails->approval_status === "DPPDONLY"){
                        $validatedData = app(DPpdCertificationCompletionRequest::class)->validateResolved();

                        $ppdParams = [
                            'request' => $request->all()
                        ];
                        DB::commit();
                        $dPppdOnly=  $this->dPpdProcessOnly($ppdParams);
                        if($dPppdOnly === 'false'){
                            return response()->json(['is_success' => 'true']);
                        }
                    }
                    if($qcSlipDetails->approval_status === 'EENGVP'){
                        $validatedData = app(EEngValidationProcessRequest::class)->validateResolved();
                        $eEngVp = [
                            'qc_slips_id'               => $qcSlipId,
                            'engg_application_vpes_oper'=> $this->getSafe($request, 'text_application_vpes_oper'),
                            'engg_vpes_oper'            => $this->getSafe($request, 'text_vpes_oper'),
                        ];
                        EQcValidationProcess::insert($eEngVp);
                        // EQcValidationProcess::where('qc_slips_id',$qcSlipId)->update($eEngVp);
                        $operToApprovers = [
                            'first_approver'  => $this->joinSafe($request, 'text_1st_validatedby_vpes_oper'),
                            'second_approver' => $this->joinSafe($request, 'text_2nd_validatedby_vpes_oper'),
                            'first_date'      => $this->getSafe($request, 'text_1st_date_vpes_oper'),
                            'first_status'    => $this->getSafe($request, 'text_first_result_vpes_oper'),
                            'first_remarks'   => $this->getSafe($request, 'text_remarks_vpes_oper'),
                            'second_status'   => $this->getSafe($request, 'text_second_result_vpes_oper'),
                            'second_date'     => $this->getSafe($request, 'text_2nd_date_vpes_oper'),
                        ];

                    }
                    if($qcSlipDetails->approval_status ==='EQCVP'){
                    $isMachineOperatorExists = QcSlipEmployee::where("qc_slips_id",$qcSlipId)->where('station_to',4)->count();

                        if($isMachineOperatorExists > 0 ){
                            $validatedData = app(MachineOperatorRequest::class)->validateResolved();
                        }
                        $validatedData = app(EQcValidationProcessRequest::class)->validateResolved();
                        $eQcValidationProcess = [
                            "qc_slips_id"            => $qcSlipId,
                            "vpqcs_oper"             => $this->getSafe($request, 'text_vpqcs_oper'),
                            "application_vpqcs_oper" => $this->getSafe($request, 'text_application_vpqcs_oper'),
                        ];
                        EQcValidationProcess::where('qc_slips_id',$qcSlipId)->update($eQcValidationProcess);
                        $operToApprovers = [
                            "decision_status"   => 'APP',
                            "first_status"      => $this->getSafe($request, 'text_first_result_vpqcs_oper'),
                            "first_approver"    => $this->joinSafe($request, 'text_1st_validatedby_vpqcs_oper'),
                            "first_date"        => $this->getSafe($request, 'text_1st_date_vpqcs_oper'),
                            'first_time'        => '',
                            "first_status_2"    => $this->getSafe($request, 'text_first_result_vpes_oper_2'),
                            "first_approver_2"  => $this->joinSafe($request, 'text_1st_validatedby_vpes_oper_2'),
                            "first_date_2"      => $this->getSafe($request, 'text_1st_date_vpes_oper_2'),
                            "first_remarks"     => $this->getSafe($request, 'text_remarks_vpqcs_oper'),
                            "second_status"     => $this->getSafe($request, 'text_second_result_vpqcs_oper'),
                            "second_approver"   => $this->joinSafe($request, 'text_2nd_validatedby_vpqcs_oper'),
                            "second_date"       => $this->getSafe($request, 'text_2nd_date_vpqcs_oper'),
                            "second_status_2"   => $this->getSafe($request, 'text_second_result_vpes_oper_2'),
                            "second_approver_2" => $this->joinSafe($request, 'text_2nd_validatedby_vpes_oper_2'),
                            "second_date_2"     => $this->getSafe($request, 'text_2nd_date_vpes_oper_2'),
                            'second_time'       => '',
                            "second_remarks"    => $this->getSafe($request, 'text_remarks_vpes_oper_2'),
                        ];
                    }
                    if($qcSlipDetails->approval_status ==='FQCVVO'){
                        app(FQcValidationRequest::class)->validateResolved();
                        $fQcValidationVisualOperator = [
                            "qc_slips_id" => $qcSlipId,
                            'refdocno_input_qcvvo_oper' => $this->getSafe($request, 'text_refdocno_input_qcvvo_oper'),
                            'refdocno_input_qcvvo_oper_2' => $this->getSafe($request, 'text_refdocno_input_qcvvo_oper_2'),
                        ];
                        $operToApprovers = [
                            "decision_status"  => 'APP',
                            "first_approver"   => $this->joinSafe($request, 'text_validated1_qcvvo_oper'),
                            "first_date"       => $this->getSafe($request, 'text_date1_qcvvo_oper'),
                            "first_time"       => '',
                            "first_status"     => '',
                            "first_remarks"    => '',

                            "second_approver"  => $this->joinSafe($request, 'text_validated2_qcvvo_oper'),
                            "second_date"      => $this->getSafe($request, 'text_date2_qcvvo_oper'),
                            "second_time"      => '',
                            "second_status"    =>  '',
                            "second_remarks"   => '',
                        ];
                        FQcValidation::insert($fQcValidationVisualOperator);
                        // DB::commit();
                    }
                    //=== Update the Operator Approvers based on the Current Status
                }
            }

        if($currentPositionCategory === 'Operator'){  //ADD


        }
        if($currentApprovalStatus != "DPPDONLY"){ //OPERATOR
            // return $qcModelApprover;
                $qcModelApprover::where('qc_slips_id',$qcSlipId)->where('approval_status',$currentApprovalStatus)
                // ->get();
                ->update($operToApprovers);
            }
            //=== Update the Approval Status and Insert the new Approval Status and Emails to the Next Approvers
            $changeApprovalStatusParams = [
                'qcSlipsId' => $qcSlipId,
                'approval_status'=> $currentApprovalStatus,
                'selectedSection'=> $select_section,
                'selectPosition'=> $select_position,
                'isMachineOperatorExists'=> $isMachineOperatorExists,
            ];
            $getNewStatus =  $this->changeApprovalStatus($changeApprovalStatusParams);
            if($currentApprovalStatus === 'FQCVVO'){ //FQCVVO to QCAPP - OPERATOR Final Approver QC Supervisor
                $emailParams = [ //FOR QC
                    'qc_slips_id' => $qcSlipId,
                    'update_data'=> [
                        'qc_slips_id' => $qcSlipId,
                        'approval_status'=> $getNewStatus['newStatus'],
                        'alert_prod_sec' => $this->joinSafe($request, 'text_oper_approved_confirmed_by'),
                        'alert_prod_cc_sec' => '',
                    ],
                    'approval_status'=> $currentApprovalStatus,
                    'qcModelApprover' => $qcModelApprover,
                ];
            }else{
                $emailParams = [
                    'qc_slips_id' => $qcSlipId,
                    'update_data'=> [
                        'qc_slips_id' => $qcSlipId,
                        'approval_status'=> $getNewStatus['newStatus'],
                        // "decision_status" => $getNewStatus['newStatus'],
                        'alert_prod_sec'    => $this->joinSafe($request, 'text_alert_prod_sec'),
                        'alert_prod_cc_sec' => $this->joinSafe($request, 'text_alert_prod_cc_sec'),
                    ],
                    'approval_status'=> $currentApprovalStatus,
                    'qcModelApprover' => $qcModelApprover,
                ];
            }
            DB::commit();
            return $this->saveFormSendEmail($emailParams);
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
     public function changeApprovalStatusREV1($params){
        $selectedSection = str_contains($params['selectedSection'], 'PPD');
         $isMachineOperatorExists = $params['isMachineOperatorExists'];
        if($params['selectPosition'] === 'Inspector'){
             switch (true) {
                case ($params['approval_status'] === 'PB'):
                    $newStatus = 'ALQCTQ';
                    $statusName = 'A LINE QUALITY CONTROL SECTION (Training and Qualification)';
                    break;
                case ($params['approval_status'] === 'ALQCTQ'):
                    $newStatus = 'BLQCTC';
                    $statusName = 'B LINE QUALITY CONTROL SECTION (Certification)';
                    break;
                case ($params['approval_status'] === 'BLQCTC'):
                    $newStatus = 'CLQCOQC';
                    $statusName = 'C VALIDATION PROCESS: QUALITY CONTROL SECTION <> OQC only';
                    break;
                case ($params['approval_status'] === 'CLQCOQC'):
                    $newStatus = 'LQCHEADAPP';
                    $statusName = 'For LQC Head Approval';
                    break;
                default:
                    $newStatus = 'N/A';
                    $statusName = 'N/A';
                    break;
            }
        }
        if($params['selectPosition'] === 'Operator'){
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
                case ($params['approval_status'] === 'CQCC' && $selectedSection != 1 && $isMachineOperatorExists > 0): //For Machine Operator Only
                    $newStatus = 'EENGVP';
                    $statusName = 'E Engineering Validation Process';
                    break;
                case ($params['approval_status'] === 'CQCC'  && $selectedSection != 1 && $isMachineOperatorExists === 0): // QC Validation Process
                    $newStatus = 'EQCVP';
                    $statusName = 'E Qc Validation Process';
                    break;
                case ($params['approval_status'] === 'CQCC' && $selectedSection):
                    $newStatus = 'DPPDONLY';
                    $statusName = 'D PPD Production, Engg, QC Update';
                    break;

                // case ($params['approval_status'] === 'CQCC' && $selectedSection):
                //     $newStatus = 'DPRDPPDONLY';
                //     $statusName = 'D Production Update';
                //     break;
                // case ($params['approval_status'] === 'DPRDPPDONLY'):
                //     $newStatus = 'EQCVP';
                //     $statusName = 'D Engineering Update';
                //     break;
                // case ($params['approval_status'] === 'DENGGPPDONLY'):
                //     $newStatus = 'DQCPPDONLY';
                //     $statusName = 'D QC Update';
                //     break;

                case ($params['approval_status'] === 'DPPDONLY' && $isMachineOperatorExists === 0): // QC Validation Pr
                    $newStatus = 'EENGVP'; //For Machine Operator Only
                    $statusName = 'E Engineering Validation Process';
                    break;
                case ($params['approval_status'] === 'DPPDONLY' && $isMachineOperatorExists > 0): // QC Validation Pr
                    $newStatus = 'EQCVP';  // QC Validation Process
                    $statusName = 'E Qc Validation Process';
                    break;
                case ($params['approval_status'] === 'EENGVP'):
                    $newStatus = 'EQCVP';  // QC Validation Process
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
        }


        QcSlip::where('id',$params['qcSlipsId'])->update([
            'approval_status'=> $newStatus
        ]);
        return [
          "newStatus" => $newStatus
        ];
    }

    public function getApprovalStatus($approvalStatus){
        try {
             switch ($approvalStatus) {
                //==== INSPECTOR
                case 'ALQCTQ':
                    $newStatus = 'ALQCTQ';
                    $statusName = 'A LINE QUALITY CONTROL SECTION (Training and Qualification)';
                    $spanColor = 'bg-danger';
                    break;
                case 'BLQCTC':
                    $newStatus = 'BLQCTC';
                    $statusName = 'B LINE QUALITY CONTROL SECTION (Certification)';
                    $spanColor = 'bg-danger';
                    break;
                case 'CLQCOQC':
                    $newStatus = 'CLQCOQC';
                    $statusName = 'C VALIDATION PROCESS: QUALITY CONTROL SECTION <> OQC only';
                    $spanColor = 'bg-danger';
                    break;
                case 'LQCHEADAPP':
                    $newStatus = 'LQCHEADAPP';
                    $statusName = 'LQC HEAD APPROVAL';
                    $spanColor = 'bg-danger';
                    break;

                // ==== OPERATOR
                case 'PB':
                    $approvalStatus = 'APRODTO';
                    $statusName = 'PREPARED BY';
                    $spanColor = 'bg-danger';
                    break;
                case 'APRODTO':
                    $approvalStatus = 'APRODTO';
                    $statusName = 'A Production Training Orientation';
                    $spanColor = 'bg-danger';
                    $spanColor = 'bg-danger';
                    break;
                case 'BENGGTQ':
                    $approvalStatus = 'BENGGTQ';
                    $statusName = 'B Engineer Training Qualification';
                    $spanColor = 'bg-danger';
                    break;
                case 'CQCC':
                    $approvalStatus = 'CQCC';
                    $statusName = 'C Qc Certification';
                    $spanColor = 'bg-danger';
                    break;
                case 'DPPDONLY':
                    $newStatus = 'DPPDONLY';
                    $statusName = 'D PPD Update';
                    $spanColor = 'bg-danger';
                    break;
                case 'EENGVP':
                    $approvalStatus = 'EENGVP';
                    $statusName = 'E Engineering Validation Process';
                    $spanColor = 'bg-danger';
                    break;
                // case 'DPRDPPDONLY':
                //     $newStatus = 'DPRDPPDONLY';
                //     $statusName = 'D Production Update';
                //     $spanColor = 'bg-danger';
                //     break;
                // case 'DENGGPPDONLY':
                //     $newStatus = 'DENGGPPDONLY';
                //     $statusName = 'D Engineering Update';
                //     $spanColor = 'bg-danger';
                //     break;
                // case 'DQCPPDONLY':
                //     $newStatus = 'DQCPPDONLY';
                //     $statusName = 'D QC Update';
                //     $spanColor = 'bg-danger';
                //     break;
                case 'EQCVP':
                    $approvalStatus = 'EQCVP';
                    $statusName = 'E Qc Validation Process';
                    $spanColor = 'bg-danger';
                    break;
                case 'FQCVVO':
                    $approvalStatus = 'FQCVVO';
                    $statusName = 'F Qc Validation Visual Operator';
                    $spanColor = 'bg-danger';
                    break;
                case 'QCAPP':
                    $approvalStatus = 'QCAPP'; //QC Supervisor Appoval
                    $statusName = 'QC Supervisor Approval';
                    $spanColor = 'bg-danger';
                    break;
                case 'OK':
                    $approvalStatus = 'CLOSED';
                    $statusName = 'CLOSED';
                    $spanColor = 'bg-success';
                    break;
                 default:
                    $approvalStatus = '---';
                    $statusName = '---';
                    $spanColor = '';
                    break;
             }
             return [
                 'approvalStatus' => $approvalStatus,
                 'statusName' => $statusName,
                 'spanColor' => $spanColor,
             ];
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function sendEmail($data){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            RapidMailer::insert($data);
            RapidMailer::where('pkid',1000)->get();
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function getEmailByRapidxUserId($empNo){
        try {
         DB::beginTransaction();
        $user = RapidXUser::where('employee_number',$empNo)->first();
            if (!$user) {
                DB::rollback();
                return [
                    'is_success' => 'false',
                    'message' => 'User not found. Please add to Rapidx User Module! '.$empNo.'',
                ];
                // throw new \Exception('User not found. Please add to Rapidx User Module!');
            }
            if (!$user->email) {
                DB::rollback();
                return [
                    'is_success' => 'false',
                    'message' => 'User Email not found.  Please add to Rapidx User Module! '.$empNo.'',
                ];
            }
           return [
            'is_success' => 'true',
            'message' => 'true',
            'fullName' => $user->name,
            'email' => $user->email,
        ];
        DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
<<<<<<< HEAD
=======

>>>>>>> cb76cc488b9e72ef66f4a7fca7216e7563d22673
    public function emailMsg($params){
        $qcSlip = QcSlip::with('product_line','system_one_hris_subcon')->where('id',$params['qc_slips_id'])
        ->whereNull('deleted_at')
        ->first();
        // if($getEcrStatus['status'] == 'DISAPPROVED'){
        //     $header = "Your ECR has been disapproved";
        // }else if($getEcrStatus['status'] == 'APPROVED'){
        //     $header = "Your ECR has been approved";
        // }else{
            // }
        if($qcSlip->approval_status === "QCAPP"){
            $header = "Please see the Certification/Qualification for your update.";
        }else{
            $header = "Please see the APPROVED Certification/Qualification.";
        }
        return $msg = '<!DOCTYPE html>
            <html>
                <head>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
                    <style type="text/css">
                        body{
                            font-family: Arial;
                            font-size: 15px;
                        }
                        .text-green{
                            color: green;
                            font-weight: bold;
                        }
                    </style>
                </head>
                <body>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row" style="margin: 1px 10px;">
                                <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label style="font-size: 18px;">Good day!</label><br>
                                                <label style="font-size: 18px;">'.$header.'</label>
                                                <br>
                                                <hr>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b>Ecr Control No. : </b><span class="text-black"> '. $qcSlip['control_no'].' </span></label>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b> Factory : </b><span class="text-black"> '.$qcSlip->section_category.' </span></label>
                                                </div>
                                                <div   div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b>Approval Status: </b> '. $qcSlip['approval_status'].' </label>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b>Position : </b><span class="text-black"> '.$qcSlip->position_category.'</span></label>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b>Section Name: </b><span class="text-black"> '.$qcSlip->section.' </span></label>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b>Product Line : </b><span class="text-black"> '.$qcSlip->product_line.' </span></label>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b> Series Name : </b><span class="text-black"> '.$qcSlip->series_name.' </span></label>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b> Product Line : </b><span class="text-black"> '.$qcSlip->product_line.' </span></label>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b> Created By : </b><span class="text-black"> '.$qcSlip->system_one_hris_subcon[0]['empname'].' </span></label>
                                                </div>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label">For more info, please log-in to your Rapidx account. Go to http://rapidx/ and Click http://rapidx/TRDSv2/qualification_certification/ </label>
                                                </div>
                                            </div>

                                            <br>
                                            <br>

                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label"><b> Notice of Disclaimer: </b></label>
                                                    <br>
                                                    <label class="col-sm-12 col-form-label"></label>   This message contains confidential information intended for a specific individual and purpose. If you are not the intended recipient, you should delete this message. Any disclosure,copying, or distribution of this message, or the taking of any action based on it, is strictly prohibited.</label>
                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <br><br>
                                                <label style="font-size: 18px;"><b>For concerns on using the form, please contact ISS at local numbers 205, 206, or 208. You may send us e-mail at <a href="mailto: servicerequest@pricon.ph">servicerequest@pricon.ph</a></b></label>
                                            </div>
                                        </div>

                                        </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </body>
            </html>';
    }

}
