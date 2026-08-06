<?php

namespace App\Http\Controllers;


use App\Http\Controllers\CommonController;
use App\Http\Requests\ALqcTrainingQualificationRequest;
use App\Http\Requests\AOperProdTrainingOrientationRequest;
use App\Http\Requests\BLqcCertificationRequest;
use App\Http\Requests\BOpEnggSectionTrainingOrientationRequest;
use App\Http\Requests\CQcCertificationRequest;
use App\Http\Requests\DPpdCertificationCompletionRequest;
use App\Http\Requests\EEngValidationProcessRequest;
use App\Http\Requests\EQcValidationProcessRequest;
use App\Http\Requests\FQcValidationRequest;
use App\Http\Requests\MachineOperatorRequest;
use App\Http\Requests\QcSlipEmployeeRequest;
use App\Http\Requests\QcSlipRequest;
use App\Http\Requests\SendEmailRequest;
use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetail;
use App\Model\Qc\ALqcTrainingQualification;
use App\Model\Qc\AOperProdTrainingOrientation;
use App\Model\Qc\BLqcCertification;
use App\Model\Qc\BOpEnggSectionTrainingOrientation;
use App\Model\Qc\CLqcOqcValidation;
use App\Model\Qc\CLqcTrainingItemResult;
use App\Model\Qc\CQcCertification;
use App\Model\Qc\DPpdCertificationCompletion;
use App\Model\Qc\EQcValidationProcess;
use App\Model\Qc\FQcValidation;
use App\Model\Qc\QcLqcApprover;
use App\Model\Qc\QcReasonCertification;
use App\Model\Qc\QcSlipEmployee;
use App\Model\QcSlip;
use App\Model\SystemHrisViewDivDeptSec;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneHrisSubcon;
use App\Model\SystemOneSubconEmpInfo;
use  App\Model\Qc\OpApprover;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class QualificationCertificationController extends Controller
{
    protected $commonController;
    public function __construct(CommonController $commonController){
        $this->commonController = $commonController;
    }

    /**
     * Safely read a scalar request field, defaulting to $default when absent or null.
     */
    private function getSafe(Request $request, string $key, $default = NULL)
    {
        return $request->input($key) ?? $default;
    }

    /**
     * Safely collect an array/scalar request field and join items with $separator.
     * Returns $default when the field is absent, null, or empty.
     */
    private function joinSafe(Request $request, string $key, string $separator = ' | ', string $default = NULL)
    {
        $value = $request->input($key);
        if (empty($value)) {
            return $default;
        }
        return collect((array) $value)->filter()->join($separator);
    }

    public function saveQcLqcTrainingItemsByQcSlipId(Request $request){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $qcSlipsId = $request->input('qc_slips_id');
            $matrixData = $request->input('matrix', []);
            $dayDates   = $request->input('day_dates', []);  // e.g. ['day_1' => '2026-07-30', ...]


            // DB::transaction(function () use ($qcSlipsId, $matrixData, $dayDates) {
                foreach ($matrixData as $row) {
                    $itemId         = $row['training_item_id'];
                    $remark         = $row['remark'] ?? null;
                    $subDescription = $row['sub_description'] ?? null;
                    $dayResults     = $row['day_results'] ?? [];

                    foreach ($dayResults as $dayKey => $resultValue) {
                        $dayNumber = (int) str_replace('day_', '', $dayKey);

                        if ($dayNumber >= 1 && $dayNumber <= 5) {
                            CLqcTrainingItemResult::updateOrCreate(
                                [
                                    'qc_slips_id'      => $qcSlipsId,
                                    'training_item_id' => $itemId,
                                    'day_number'       => $dayNumber,
                                ],
                                [
                                    'result'          => $resultValue,
                                    'item_remark'     => $remark,
                                    'sub_description' => $subDescription,
                                    'date'            => $dayDates['day_' . $dayNumber] ?? null,
                                ]
                            );
                        }
                    }
                }
            // });
            DB::commit();
            return response()->json([
                'is_success' => 'true',
                'message'    => 'Training items matrix saved successfully!',
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function saveQualificationCertificationOper(Request $request){
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
            $select_section_production = $this->getSafe($request, 'text_section_operator', '');
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
                // $validatedData = app(QcSlipRequest::class)->validateResolved();
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
                $qcSlipGetId = QcSlip::insertGetId($saveQcSlip);
                $qcSlipId = $qcSlipGetId;
                $reasonOfCertification =  [
                    'qc_slips_id' => $qcSlipId,
                    'reason_of_certification' =>  $this->joinSafe($request, 'text_certification_operator'),
                    'transfer_flexibility' => $this->joinSafe($request, 'transfer_flexibility'),
                    'others' => $this->getSafe($request, 'others'),
                    'created_at' =>  now(),
                ];

                QcReasonCertification::insert($reasonOfCertification);

                $collectOperatorEmployees = collect($request->operator_employees)->map(function($rowOperatorEmployees)use ($qcSlipGetId){
                    return [
                            'qc_slips_id' => $qcSlipGetId,
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
                $qcSlipEmployeeCount = QcSlipEmployee::where('qc_slips_id',$qcSlipGetId)->count();
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
                $qcModelApprover = OpApprover::class;
                $operToApprovers = [];
                if(filled($qcSlipId)){ //UPDATE {
                    $validatedData = app(SendEmailRequest::class)->validateResolved();
                    $currentApprovalStatus = $qcSlipDetails->approval_status;
                    $qcSlipDetails->update([
                        'status' => 'FORAPP'
                    ]);
                    $validatedData = app(ALqcTrainingQualificationRequest::class)->validateResolved();
                    $arrQcSlipId = ["qc_slips_id"  => $qcSlipId];
                    $aLqcTrainingQuali = [
                        "qc_slips_id"   => $qcSlipId,
                        'training_orientation_inspector' => $this->joinSafe($request, 'text_training_orientation_inspector'),
                        'training_orientation_ins_4' => $this->getSafe($request, 'text_training_orientation_ins_4'),//A
                        'training_orientation_ins_13' => $this->getSafe($request, 'text_training_orientation_ins_13'),//B
                        'training_orientation_ins_21' => $this->getSafe($request, 'text_training_orientation_ins_21'),//C
                        'training_orientation_ins_54' => $this->getSafe($request, 'text_training_orientation_ins_54'),//P
                    ];
                    ALqcTrainingQualification::updateOrCreate(
                    $arrQcSlipId,$aLqcTrainingQuali);

                    $operToApprovers = [
                        "qc_slips_id"   => $qcSlipId,
                        'approval_status' => 'ALQCTQ',
                        'decision_status'   => 'APP',
                        // Sec 1 – Training & Qualification
                        'first_approver'    => $this->joinSafe($request, 'text_certified_inspector'),
                        'first_approver_2'  => $this->joinSafe($request, 'text_mentored'),
                        'first_date'        => $this->getSafe($request, 'text_date_inspector'),
                        'first_time'        => $this->getSafe($request, 'text_time_inspector'),
                    ];
                    $qcModelApprover::updateOrCreate( ['qc_slips_id' => $qcSlipId, 'approval_status' => 'ALQCTQ'],$operToApprovers);
                    $validatedData = app(BLqcCertificationRequest::class)->validateResolved();
                    $this->getSafe($request, 'text_result_input1_inspector');
                    $bLqcCertification = [
                        'qc_slips_id' => $qcSlipId,
                        'result_input1_inspector' => $this->getSafe($request, 'text_result_input1_inspector'),
                        'hands_on_inspector'      => $this->joinSafe($request, 'text_hands_on_inspector'),
                        'hands_on_ins_3'          => $this->getSafe($request, 'text_hands_on_ins_3'),
                    ];

                    BLqcCertification::updateOrCreate(
                    $arrQcSlipId,$bLqcCertification);
                    $qcModelApprover::updateOrCreate(
                        ['qc_slips_id' => $qcSlipId, 'approval_status' => 'BLQCTC'],
                        [
                            "qc_slips_id"   => $qcSlipId,
                            'approval_status' => 'BLQCTC',
                            'decision_status' => 'APP',
                            'first_approver'  => $this->joinSafe($request, 'text_sec2_certified_inspector'),
                            'first_date'      => $this->getSafe($request, 'text_sec2_date_inspector'),
                            'first_time'      => $this->getSafe($request, 'text_sec2_time_inspector'),
                            'first_status'    => $this->getSafe($request, 'text_sel_result1_inspector'),
                            'first_status_2'   => $this->getSafe($request, 'text_sec2_result_inspector'),
                            'first_remarks'   => $this->getSafe($request, 'text_result_input1_inspector'),
                            'second_status'  => $this->getSafe($request, 'text_sel_result2_inspector'),
                            'second_remarks'  => $this->getSafe($request, 'text_result_input2_inspector'),
                        ]
                    );
                    BLqcCertification::updateOrCreate(
                    $arrQcSlipId,$bLqcCertification);
                //   return  $qcSlipDetails->approval_status;
                    if($qcSlipDetails->approval_status == 'CLQCOQC'){
                        $qcModelApprover::updateOrCreate(
                        ['qc_slips_id' => $qcSlipId, 'approval_status' => 'CLQCOQC'],
                            [
                                "qc_slips_id"   => $qcSlipId,
                                'approval_status' => 'CLQCOQC',
                                'decision_status' => 'APP',
                            ]
                        );
                        $countCLqcTrainingItemResult = CLqcTrainingItemResult::where('qc_slips_id',$qcSlipId)->count();
                        if($countCLqcTrainingItemResult === 0 ){
                            return response()->json(['is_success' => 'false', "message" => "Please input the C Inspector Training / Certification And Validation Slip"],409);
                        }
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
                        $qcSlipDetails->update([
                            'status' => 'FORAPP'
                        ]);
                        $validatedData = app(AOperProdTrainingOrientationRequest::class)->validateResolved();

                        AOperProdTrainingOrientation::updateOrCreate(
                            ['qc_slips_id' => $qcSlipId],
                            [
                                'traning_items'            => $this->joinSafe($request, 'text_training_orientation_ps_oper'),
                                'defect_escalation'        => $this->joinSafe($request, 'defect_escalation'),
                                'production_abnormality'   => $this->joinSafe($request, 'production_abnormality'),
                                'engg_tq_orientation_docs' => $this->joinSafe($request, 'engg_tq_orientation_docs'),
                                'orientation_docs'         => $this->joinSafe($request, 'orientation_docs'),
                            ]
                        );

                        $operToApprovers = [
                            "decision_status"  => 'APP',
                            'first_approver'   => $this->joinSafe($request, 'text_first_trainedby_oper'),
                            'first_approver_2' => $this->joinSafe($request, 'text_first_mentoredby_oper'),
                            'first_date'       => $this->getSafe($request, 'text_first_date_oper'),
                            'first_time'       => $this->getSafe($request, 'text_first_time_oper'),
                            'first_status'     => $this->getSafe($request, 'text_first_a_prod_result'),
                            'first_remarks'    => '',
                            'second_approver'  => $this->joinSafe($request, 'text_second_trainedby_oper'),
                            'second_approver_2'=> $this->joinSafe($request, 'text_second_mentoredby_oper'),
                            'second_date'      => $this->getSafe($request, 'text_second_date_oper'),
                            'second_time'      => $this->getSafe($request, 'text_second_time_oper'),
                            'second_status'    => $this->getSafe($request, 'text_second_a_prod_result'),
                            'second_remarks'   => '',
                        ];
                    }
                    if($qcSlipDetails->approval_status === 'BENGGTQ'){
                        $validatedData = app(BOpEnggSectionTrainingOrientationRequest::class)->validateResolved();

                        BOpEnggSectionTrainingOrientation::updateOrCreate(
                            ['qc_slips_id' => $qcSlipId],
                            [
                                'traning_items'             => $this->joinSafe($request, 'text_training_orientation_es_oper'),
                                'engg_orientation_docs'     => $this->joinSafe($request, 'engg_orientation_docs'),
                                'obs_first_result_es_oper'  => $this->getSafe($request, 'text_obs_first_result_es_oper'),
                                'first_sample_es_oper'      => $this->getSafe($request, 'text_first_sample_es_oper'),
                                'first_ok_es_oper'          => $this->getSafe($request, 'text_first_ok_es_oper'),
                                'first_ng_es_oper'          => $this->getSafe($request, 'text_first_ng_es_oper'),
                                'obs_second_result_es_oper' => $this->getSafe($request, 'text_obs_second_result_es_oper'),
                                'second_sample_es_oper'     => $this->getSafe($request, 'text_second_sample_es_oper'),
                                'second_ok_es_oper'         => $this->getSafe($request, 'text_second_ok_es_oper'),
                                'second_ng_es_oper'         => $this->getSafe($request, 'text_second_ng_es_oper'),
                            ]
                        );

                        $operToApprovers = [
                            "decision_status" => 'APP',
                            "first_approver"  => $this->joinSafe($request, 'text_1st_qualifiedby_es_oper'),
                            "first_date"      => $this->getSafe($request, 'text_qc_1st_date_es_oper'),
                            "first_time"      => $this->getSafe($request, 'text_qc_1st_time_es_oper'),
                            "first_status"    => $this->getSafe($request, 'text_oa_1st_result_es_oper'),
                            "first_remarks"   => $this->getSafe($request, 'text_1st_disqualification_es_oper'),
                            "second_approver" => $this->joinSafe($request, 'text_2nd_qualifiedby_es_oper'),
                            "second_date"     => $this->getSafe($request, 'text_qc_2nd_date_es_oper'),
                            "second_time"     => $this->getSafe($request, 'text_qc_2nd_time_es_oper'),
                            "second_status"   => $this->getSafe($request, 'text_oa_2nd_result_es_oper'),
                            "second_remarks"  => $this->getSafe($request, 'text_2nd_disqualification_es_oper'),
                        ];
                    }
                    if($qcSlipDetails->approval_status === 'CQCC'){
                        $isMachineOperatorExists = QcSlipEmployee::where("qc_slips_id",$qcSlipId)->where('station_to',4)->count();
                        $validatedData = app(CQcCertificationRequest::class)->validateResolved();

                        QcSlip::where('id', $qcSlipId)->update([
                            'oper_approved_confirmed_by' => $this->joinSafe($request, 'text_oper_approved_confirmed_by'),
                        ]);

                        CQcCertification::updateOrCreate(
                            ['qc_slips_id' => $qcSlipId],
                            [
                                'obs_first_result_qcs_oper'  => $this->getSafe($request, 'text_obs_first_result_qcs_oper'),
                                'obs_second_result_qcs_oper' => $this->getSafe($request, 'text_obs_second_result_qcs_oper'),
                                'first_sample_qcs_oper'      => $this->getSafe($request, 'text_first_sample_qcs_oper'),
                                'second_sample_qcs_oper'     => $this->getSafe($request, 'text_second_sample_qcs_oper'),
                                'first_ok_qcs_oper'          => $this->getSafe($request, 'text_first_ok_qcs_oper'),
                                'first_ng_qcs_oper'          => $this->getSafe($request, 'text_first_ng_qcs_oper'),
                                'second_ok_qcs_oper'         => $this->getSafe($request, 'text_second_ok_qcs_oper'),
                                'second_ng_qcs_oper'         => $this->getSafe($request, 'text_second_ng_qcs_oper'),
                                'updated_by'                 => $rapidxEmpNo->rapidx_emp_no,
                                'qcs_station_1st_oper'       => $this->joinSafe($request, 'text_qcs_station_1st_oper'),
                                'qcs_station_2nd_oper'       => $this->joinSafe($request, 'text_qcs_station_2nd_oper'),
                            ]
                        );

                        $operToApprovers = [
                            "decision_status" => 'APP',
                            "first_approver"  => $this->joinSafe($request, 'text_1st_certifiedby_qcs_oper'),
                            "first_date"      => $this->getSafe($request, 'text_1st_date_qcs_oper'),
                            "first_time"      => $this->getSafe($request, 'text_1st_time_qcs_oper'),
                            "first_status"    => $this->getSafe($request, 'text_oa_1st_result_qcs_oper'),
                            "first_remarks"   => $this->getSafe($request, 'text_1st_disapproval_qcs_oper'),
                            "second_approver" => $this->joinSafe($request, 'text_2nd_certifiedby_qcs_oper'),
                            "second_date"     => $this->getSafe($request, 'text_2nd_date_qcs_oper'),
                            "second_time"     => $this->getSafe($request, 'text_2nd_time_qcs_oper'),
                            "second_status"   => $this->getSafe($request, 'text_oa_2nd_result_qcs_oper'),
                            "second_remarks"  => $this->getSafe($request, 'text_2nd_disapproval_qcs_oper'),
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
                    if($qcSlipDetails->approval_status === 'EQCVP'){
                        $isMachineOperatorExists = QcSlipEmployee::where("qc_slips_id",$qcSlipId)->where('station_to',4)->count();

                        if($isMachineOperatorExists > 0 ){
                            $validatedData = app(MachineOperatorRequest::class)->validateResolved();
                        }
                        $validatedData = app(EQcValidationProcessRequest::class)->validateResolved();

                        EQcValidationProcess::updateOrCreate(
                            ['qc_slips_id' => $qcSlipId],
                            [
                                'vpqcs_oper'             => $this->getSafe($request, 'text_vpqcs_oper'),
                                'application_vpqcs_oper' => $this->getSafe($request, 'text_application_vpqcs_oper'),
                            ]
                        );

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
                    if($qcSlipDetails->approval_status === 'FQCVVO'){
                        app(FQcValidationRequest::class)->validateResolved();

                        FQcValidation::updateOrCreate(
                            ['qc_slips_id' => $qcSlipId],
                            [
                                'refdocno_input_qcvvo_oper'   => $this->getSafe($request, 'text_refdocno_input_qcvvo_oper'),
                                'refdocno_input_qcvvo_oper_2' => $this->getSafe($request, 'text_refdocno_input_qcvvo_oper_2'),
                            ]
                        );

                        $operToApprovers = [
                            "decision_status" => 'APP',
                            "first_approver"  => $this->joinSafe($request, 'text_validated1_qcvvo_oper'),
                            "first_date"      => $this->getSafe($request, 'text_date1_qcvvo_oper'),
                            "first_time"      => '',
                            "first_status"    => '',
                            "first_remarks"   => '',
                            "second_approver" => $this->joinSafe($request, 'text_validated2_qcvvo_oper'),
                            "second_date"     => $this->getSafe($request, 'text_date2_qcvvo_oper'),
                            "second_time"     => '',
                            "second_status"   => '',
                            "second_remarks"  => '',
                        ];
                    }
                    //=== Update the Operator Approvers based on the Current Status
                    if($currentApprovalStatus != "DPPDONLY"){ //OPERATOR
                    // return $qcModelApprover;
                        $qcModelApprover::where('qc_slips_id',$qcSlipId)->where('approval_status',$currentApprovalStatus)
                        // ->get();
                        ->update($operToApprovers);
                    }
                }
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


            if($currentApprovalStatus === 'FQCVVO'){ //OPERATOR FQCVVO to QCAPP Status - Final Approver QC Supervisor
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
            $this->saveFormSendEmail($emailParams);
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
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
            // return $params;
            $rapidxEmpNo =  session('global_user');
            $qcModelApprover = $params['qcModelApprover'] ?? '';
            // $from =$currentSession['email'] ?? '';
            // $from_name = $currentSession['fullName'];
            $emailParams = [
                'qc_slips_id' => $params['qc_slips_id']
            ];
            $to = '';
            $cc = '';
            //FINAL APPROVAL OPERATOR, INSPECTOR
            if($params['approval_status'] === 'OK'){
                $qcSlip = QcSlip::
                where('id' , $params['qc_slips_id'])
                ->whereNull('deleted_at')
                ->first('created_by');
                foreach (explode(' | ',$qcSlip->created_by) as $key => $valueRowEmpNo) {
                    $arrTo[] = $this->commonController->getEmailByRapidxUserId($valueRowEmpNo);
                    if($arrTo[$key]['is_success'] === 'false'){
                        return response()->json(['is_success' => 'false','message' => $arrTo[$key]['message']],409);
                    };
                }
                $collectTo = collect($arrTo)->map(function($rowEmpNo){
                    return [
                        'email' => $rowEmpNo['email'],
                        'fullName' => $rowEmpNo['fullName'],
                    ];
                });
                $to = $collectTo->pluck('email')->join(',');
                $subject = "APPROVED: TRDS - Qualification Certification";
            }else{
                $opApprover =  $qcModelApprover::insert($params['update_data']);
                DB::commit();
                foreach (explode(' | ',$params['update_data']['alert_prod_sec']) as $key => $valueRowEmpNo) {
                   $arrTo[] = $this->commonController->getEmailByRapidxUserId($valueRowEmpNo);
                }

                //Send to Approver Attention To | CC is exclude the FQCVVO TO QCAPP Last Approver
                $collectTo = collect($arrTo)->map(function($rowEmpNo){
                     return [
                        'email' => $rowEmpNo['email'],
                        'fullName' => $rowEmpNo['fullName'],
                    ];
                });
                if($params['approval_status'] != 'FQCVVO'){
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
                $to = $collectTo->pluck('email')->join(',');
                $subject = "FOR YOUR APPROVAL : TRDS - Qualification Certification";
            }
            $from = 'issinfoservice@pricon.ph';
            // $from_name = 'issinfoservice@pricon.ph';
            $message = $this->commonController->emailMsg($emailParams);
            $rapidxEmpNo =  session('global_user');
            $emailData = [
                "to" =>$to,
                // "to" =>"mrronquez@pricon.ph",
                "cc" =>$cc,
                // "cc" =>"",
                "bcc" =>"mclegaspi@pricon.ph",
                "from" => $from,
                "from_name" => $from_name ?? "TRDS Auto Email",
                "subject" =>$subject,
                "message" =>  $message,
                "attachment_filename" => "",
                "attachment" => "",
                "send_date_time" => now(),
                "date_time_sent" => "",
                "date_created" => now(),
                // "created_by" => session('rapidx_username'),
                "created_by" => $rapidxEmpNo->name,
                "system_name" => "rapidx_TRDS",
            ];
            DB::commit();
            $this->commonController->sendEmail($emailData);
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function updateApproval(Request $request){
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $qcSlip = QcSlip::
            where('id',$request->qcSlipsId)
            ->whereNull('deleted_at')->first();

            $qcSlip->update([
                'status' => $request->decision,
                'approval_status' => $request->decision,
                'appproval_at' => now()
            ]);

            if($qcSlip->position_category === 'Operator'){
                $qcModel = OpApprover::class;
            }
            if($qcSlip->position_category === 'Inspector'){
                $qcModel = OpApprover::class;
            }
           $operToApprovers = [
                "decision_status"  => 'APP',
            ];

            $opApprover = $qcModel::where('qc_slips_id',$request->qcSlipsId)->where('decision_status','PEN')
            ->update($operToApprovers);

            //TODO: Email to Created By if Approved

            $approvalStatus = $qcSlip->approval_status;

            $emailParams = [
                'qc_slips_id' => $request->qcSlipsId,
                'update_data'=> [],
                'approval_status'=> $approvalStatus,
            ];

            DB::commit();
            $this->saveFormSendEmail($emailParams);
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
            $columnMap = [
                'firstTakeInsSequence'        => 'first_take_ins_sequence',
                'firstTakeInsAssessmentResult'=> 'first_take_ins_assessment_result',
                'secondTakeInsSequence'       => 'second_take_ins_sequence',
                'secondTakeInsAssessmentResult'=> 'second_take_ins_assessment_result',
            ];
            $column = $columnMap[$request->input('category')] ?? null;
            if (!$column) {
                return response()->json(['is_success' => 'false', 'message' => 'Invalid category.']);
            }
            $arrData = [$column => $this->getSafe($request, 'value')];
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
            $qcSlipPosition = QcSlip::
            where('id',$request->qcSlipsId)
            ->whereNull('deleted_at')
            ->first('position_category');
            $arrRelation = [
                'op_approvers',
                'qc_slip_employees.system_one_hris_subcon',
                'qc_slip_employees.get_station_from',
                'qc_slip_employees.get_station_to',
                'qc_slip_employees',
                'qc_reason_certification',
                'a_oper_prod_training_orientation',
                'b_op_engg_section_training_orientation',
                'c_qc_certification',
                'd_ppd_certification_completion',
                'e_qc_validation_process',
                'f_qc_validation',
            ];
            $qcSlip = QcSlip::with($arrRelation)
            ->where('id',$request->qcSlipsId)
            ->whereNull('deleted_at')
            ->first();
             // Helper closure to cleanly explode piped string values into trimmed arrays
            $explodePipedString = function ($value) {
                if (is_null($value) || trim($value) === '') {
                    return [];
                }
                return array_map('trim', explode('|', $value));
            };

            $rawReasonsString = $qcSlip->qc_reason_certification->reason_of_certification ?? '';
            $rawReasonTransferFlexibility = $qcSlip->qc_reason_certification->transfer_flexibility ?? '';
            if($rawReasonTransferFlexibility != ''){
                 $rawReasonTransferFlexibilityCollection =  collect(explode('|', $rawReasonTransferFlexibility))
                ->map(function($id) {
                    return trim($id);
                })
                ->filter()
                ->values()
                ->all();
            }
            $rawReasonsStringCollection =  collect(explode('|', $rawReasonsString))
                ->map(function($id) {
                    return trim($id);
                })
            ->filter()
            ->values()
            ->all();

            if($qcSlipPosition->position_category === 'Inspector'){
                $arrRelation = [
                    'op_approvers',
                    'qc_slip_employees.system_one_hris_subcon',
                    'qc_slip_employees.get_station_from',
                    'qc_slip_employees.get_station_to',
                    'qc_slip_employees',
                    'qc_reason_certification',
                    'a_lqc_training_qualification',
                    'b_lqc_certification',
                    // 'c_lqc_oqc_validation',
                ];
                $qcSlip = QcSlip::with($arrRelation)
                ->where('id',$request->qcSlipsId)
                ->whereNull('deleted_at')
                ->first();
                $arrApprovers = $qcSlip->op_approvers;
                $json = [
                    'is_success' => 'true',
                    'qcSlip' => $qcSlip,
                    'rawOperApprovedConfirmedBy' => $arrOperApprovedConfirmedBy ?? [],
                    'rawReasonsStringCollection' => $rawReasonsStringCollection,
                    'rawBEnggTrainingItemsCollection' => $rawBOpEnggSectionTrainingOrientationCollection ?? '',
                    'rawAOperProdTrainingOrientationCollection' => $rawAOperProdTrainingOrientationCollection ?? '',
                    'rawReasonTransferFlexibility' => $rawReasonTransferFlexibilityCollection ?? '',

                ];
            }
            if($qcSlipPosition->position_category === 'Operator'){
                //OPERATOR
                $rawOperApprovedConfirmedBy =  collect(explode('|', $qcSlip->oper_approved_confirmed_by))
                    ->map(function($id) {
                        return trim($id);
                    })
                ->filter()
                ->values()
                ->all();

                $employeeOperApprovedConfirmedBy = SystemOneHrisSubcon::whereIn('EmpNo', $rawOperApprovedConfirmedBy)
                    ->get(['EmpNo', 'empname']) // Fetch only needed columns
                    ->keyBy('EmpNo') // Key the collection by EmpNo for O(1) lookup speed
                    ->toArray();
                $arrOperApprovedConfirmedBy = [];
                foreach($employeeOperApprovedConfirmedBy as $key => $value){
                    $array_data 				= array();
                    $array_data['id'] 		= $value['EmpNo'];
                    $array_data['name'] 		= $value['empname'];
                    $arrOperApprovedConfirmedBy[]			= $array_data;
                }
                $rawAOperProdTrainingOrientation = $qcSlip->a_oper_prod_training_orientation->traning_items ?? '';
                $rawBOpEnggSectionTrainingOrientation = $qcSlip->b_op_engg_section_training_orientation->traning_items ?? '';


                if($rawBOpEnggSectionTrainingOrientation != ''){
                    $rawBOpEnggSectionTrainingOrientationCollection =  collect(explode('|', $rawBOpEnggSectionTrainingOrientation))
                    ->map(function($id) {
                        return trim($id);
                    })
                    ->filter()
                    ->values()
                    ->all();
                }
                if($rawAOperProdTrainingOrientation != ''){
                    $rawAOperProdTrainingOrientationCollection =  collect(explode('|', $rawAOperProdTrainingOrientation))
                    ->map(function($id) {
                        return trim($id);
                    })
                    ->filter()
                    ->values()
                    ->all();
                }

                // $qcSlipReasons = collect($qcSlip);
                $json = [
                    'is_success' => 'true',
                    'qcSlip' => $qcSlip,
                    'rawOperApprovedConfirmedBy' => $arrOperApprovedConfirmedBy,
                    'rawReasonsStringCollection' => $rawReasonsStringCollection,
                    'rawBEnggTrainingItemsCollection' => $rawBOpEnggSectionTrainingOrientationCollection ?? [],
                    'rawAOperProdTrainingOrientationCollection' => $rawAOperProdTrainingOrientationCollection ?? [],
                    'rawReasonTransferFlexibility' => $rawReasonTransferFlexibilityCollection ?? [],
                ];
                $arrApprovers = $qcSlip->op_approvers;
            }


            $rawPayload = collect($arrApprovers)->groupBy('approval_status')->toArray();
            $approversCollection = collect($qcSlip)->groupBy('approval_status')->toArray();
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
                        $firstExploded3  = $explodePipedString($itemArray['first_approver_3'] ?? null);
                        $secondExploded = $explodePipedString($itemArray['second_approver'] ?? null);
                        $secondExploded2 = $explodePipedString($itemArray['second_approver_2'] ?? null);
                        $secondExploded3 = $explodePipedString($itemArray['second_approver_3'] ?? null);
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
                    $itemArray['first_approver3_exploded']  = $mapToSelect2Structure($firstExploded3);
                    $itemArray['second_approver_exploded'] = $mapToSelect2Structure($secondExploded);
                    $itemArray['second_approver2_exploded'] = $mapToSelect2Structure($secondExploded2);
                    $itemArray['second_approver3_exploded'] = $mapToSelect2Structure($secondExploded3);
                    $itemArray['alert_prod_sec_exploded']  = $mapToSelect2Structure($alertsExploded);

                    return $itemArray;
                });
            });
            $arrApproversCollection =  [
                 'approversCollection' => $processedData ?? [],
            ];
            return response()->json(array_merge( $json,$arrApproversCollection));
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function loadQcLqcTrainingItemsByQcSlipId(Request $request){
        $qcSlipsId = $request->qc_slips_id;

        // Header dates keyed by day_number
        $headerDates = CLqcTrainingItemResult::where('qc_slips_id', $qcSlipsId)
            ->whereNotNull('date')
            ->orderBy('day_number')
            ->get(['day_number', 'date'])
            ->unique('day_number')
            ->pluck('date', 'day_number');

        $items = DropdownMasterDetail::with(['c_lqc_training_item_results' => function ($query) use ($qcSlipsId) {
            $query->where('qc_slips_id', $qcSlipsId);
        }])
        ->where('dropdown_masters_id', 8)
        ->orderBy('id', 'asc')
        ->get();

        $data = $items->map(function ($item) {
            $resultsByDay = $item->c_lqc_training_item_results->keyBy('day_number');

            return [
                'id'              => $item->id,
                'item_name'       => $item->dropdown_masters_details,
                'sub_description' => $item->c_lqc_training_item_results->first()->sub_description ?? '',
                'day_1_result'    => $resultsByDay->get(1)->result ?? '',
                'day_2_result'    => $resultsByDay->get(2)->result ?? '',
                'day_3_result'    => $resultsByDay->get(3)->result ?? '',
                'day_4_result'    => $resultsByDay->get(4)->result ?? '',
                'day_5_result'    => $resultsByDay->get(5)->result ?? '',
                'item_remark'     => $item->c_lqc_training_item_results->first()->item_remark ?? '',
            ];
        });

        return datatables()->of($data)
        ->editColumn('item_name', function ($row) {
            $html = '<strong>' . e($row['item_name']) . '</strong>';
            $lower = strtolower($row['item_name']);
            if (str_contains($lower, 'systems and procedure') || str_contains($lower, 'work instruction') || str_contains($lower,'point panel inspection guide')) {
                $html .= '<br><input type="text" class="form-control form-control-sm mt-1 input-sub-desc"
                              data-item-id="' . $row['id'] . '"
                              placeholder="Details..."
                              value="' . e($row['sub_description']) . '">';
            }
            return $html;
        })
        ->addColumn('day_1', function ($row) {
            return '<input type="text" class="form-control form-control-sm text-center input-result"
                        data-item-id="' . $row['id'] . '"
                        data-day="1"
                        value="' . e($row['day_1_result']) . '">';
        })
        ->addColumn('day_2', function ($row) {
            return '<input type="text" class="form-control form-control-sm text-center input-result"
                        data-item-id="' . $row['id'] . '"
                        data-day="2"
                        value="' . e($row['day_2_result']) . '">';
        })
        ->addColumn('day_3', function ($row) {
            return '<input type="text" class="form-control form-control-sm text-center input-result"
                        data-item-id="' . $row['id'] . '"
                        data-day="3"
                        value="' . e($row['day_3_result']) . '">';
        })
        ->addColumn('day_4', function ($row) {
            return '<input type="text" class="form-control form-control-sm text-center input-result"
                        data-item-id="' . $row['id'] . '"
                        data-day="4"
                        value="' . e($row['day_4_result']) . '">';
        })
        ->addColumn('day_5', function ($row) {
            return '<input type="text" class="form-control form-control-sm text-center input-result"
                        data-item-id="' . $row['id'] . '"
                        data-day="5"
                        value="' . e($row['day_5_result']) . '">';
        })
        ->addColumn('remarks', function ($row) {
            return '<input type="text" class="form-control form-control-sm input-remark"
                        data-item-id="' . $row['id'] . '"
                        value="' . e($row['item_remark']) . '" placeholder="Add remark...">';
        })
        ->rawColumns(['item_name', 'day_1', 'day_2', 'day_3', 'day_4', 'day_5', 'remarks'])
        ->with('headerDates', $headerDates)
        ->make(true);
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
    public function loadQcSlip(Request $request){ //nmodify
        try {
            $selectAccess = $request->selectAccess;
            $selectMhSortBySection = $request->selectMhSortBySection;
            $selectPosition = $request->selectPosition;
            $rapidxEmpNo           = session('global_user');
            $data = QcSlip::with(
                'product_line',
                'op_approvers',
                'op_approvers_pending',
                'system_one_hris_subcon',
            );


            if(filled($selectPosition) && $selectPosition != 'ALL'){
                $data->where('position_category',$selectPosition);
            }

            if(filled($selectMhSortBySection) && $selectMhSortBySection != 'ALL'){
                $data->where('section_category',$selectMhSortBySection);
            }
            if(filled($selectAccess)){
                $selectedAccess = [
                    'PB',
                    'FORAPP',
                    'OK',
                ];
                if($selectAccess === 'ALL'){
                    $selectedAccess = [
                        'PB',
                        'FORAPP',
                        'OK',
                    ];
                }
                if($selectAccess === 'FORAPP'){
                    $selectedAccess = [
                        'FORAPP',
                    ];
                }
                if($selectAccess === 'OK'){
                    $selectedAccess = [
                        'OK',
                    ];
                }
                $data->whereIn('status',$selectedAccess);
            }
            if($selectAccess === 'MYAPPROVAL' || blank($selectAccess) ){
                $data->whereHas('op_approvers_pending',function($query) use ($rapidxEmpNo){
                $query->where('alert_prod_sec','LIKE','%'.$rapidxEmpNo->rapidx_emp_no.'%');
                    $query->orWhere('alert_prod_cc_sec','LIKE','%'.$rapidxEmpNo->rapidx_emp_no.'%');
                });
            }
            $data->whereNull('deleted_at');
            $data->orderBy('id','DESC');
            $qclixx = $data;
            $qcSlips = $data->get();
            // Convert to a raw array for database handling
            $allEmpIdsTo= $qcSlips->pluck('op_approvers_pending') // Grab all op_approvers collections
                ->flatten()                              // Flatten into a single layer of OpApprover models
                ->pluck('alert_prod_sec')               // Pull out all the pipe-separated strings
                ->filter()                               // Remove null or empty entries
                ->flatMap(function ($item) {             // Split pipes and flatten the resulting array elements
                    return array_map('trim', explode('|', $item));
                })
                ->unique()                               // Drop duplicates
                ->values()                               // Re-index array keys
                ->all();                                 // Convert to a raw array for database handling
            $allEmpIdsCc= $qcSlips->pluck('op_approvers_pending') // Grab all op_approvers collections
                ->flatten()                              // Flatten into a single layer of OpApprover models
                ->pluck('alert_prod_cc_sec')               // Pull out all the pipe-separated strings
                ->filter()                               // Remove null or empty entries
                ->flatMap(function ($item) {             // Split pipes and flatten the resulting array elements
                    return array_map('trim', explode('|', $item));
                })
                ->unique()                               // Drop duplicates
                ->values()                               // Re-index array keys
                ->all();                                 // Convert to a raw array for database handling

            // 3. Fetch all matching names from HRIS into a quick-lookup map array
            $arrHrisSubconEmpNo = array_merge($allEmpIdsTo,$allEmpIdsCc);
            $hrisSubcon = SystemOneHrisSubcon::whereIn('EmpNo', $arrHrisSubconEmpNo)
                ->get()
                ->pluck('empname', 'EmpNo');

            return DataTables($qcSlips)
            ->addColumn('rawAction',function ($row) use ($request){
                $result = '';
                $result .= '<center>';
                $result .= '<button class="btn btn-sm btn-outline-primary" type="button" qc-slips-id="'.$row->id.'" id="btnGetQcSlipsId"><i class="fa-solid fa fa-edit"></i></button> </br></br>';
                $result .= '<button class="btn btn-sm btn-outline-info" type="button" qc-slips-id="'.$row->id.'" id="btnViewQcSlipsId"><i class="fa-solid fa fa-eye"></i></button>';
                $result .= '</center>';
                return $result;
            })
            ->addColumn('rawStatus', function ($row) use ($hrisSubcon) {

                $approvalStatus = $row->approval_status;
                $positionCategory = $row->position_category;
                $current = [];
                $current = $row->op_approvers_pending ?? [];

                $getApprovalStatus = $this->commonController->getApprovalStatus($approvalStatus);

                $resultCurrentApprover = '';

                if (filled($current)) {
                    // Get THIS row's own emp IDs from alert_prod_sec (pipe-separated)
                      // Option B: Using Collection Pipeline (Wrap $current in array brackets)
                    $empIdsTo = collect([$current]) // Wrap in brackets so pluck works on a collection list
                        ->pluck('alert_prod_sec')
                        ->filter()
                        ->flatMap(function ($item) {
                            // Use preg_split to match '|', spaces, or both safely
                            return preg_split('/\s*\|\s*/', trim($item));
                        })
                        ->values();
                        $empIdsCc =
                        collect([$current]) // Wrap in brackets so pluck works on a collection list
                        ->pluck('alert_prod_cc_sec')
                        ->filter()
                        ->flatMap(function ($item) {
                            // Use preg_split to match '|', spaces, or both safely
                            return preg_split('/\s*\|\s*/', trim($item));
                        })
                        ->values();
                    // Map emp IDs -> names using the lookup, drop unmatched
                    $currentApproverTo = $empIdsTo
                        ->map(function ($empId) use ($hrisSubcon) {
                            return $hrisSubcon[$empId] ?? null;
                        })
                        ->filter()
                        ->join(' <br> ');
                    $currentApproverCc = $empIdsCc
                        ->map(function ($empId) use ($hrisSubcon) {
                            return $hrisSubcon[$empId] ?? null;
                        })
                        ->filter()
                        ->join(' <br> ');
                //    return $empIdsCc = array_merge($empIdsTo,$empIdsCc);

                    if ($currentApproverTo !== '') {
                        $resultCurrentApprover = '<span class="badge '.$getApprovalStatus['spanColor'].'">'.$currentApproverTo.' <br> '.$currentApproverCc.' </span></b></br>';
                    }
                }

                return '<center><br>'
                    . $resultCurrentApprover
                    . '<span class="badge rounded-pill '.$getApprovalStatus['spanColor'].'"> '.$getApprovalStatus['statusName'].' </span>'
                    . '</center></br>';
            })
            ->addColumn('trained_by',function ($row){
                $personInCharge = $row->rapid_x_user->name ?? '';
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
            ->addColumn('created_by',function ($row){
                $personInCharge = $row->system_one_hris_subcon[0]['empname'] ?? '';
                // $personInCharge = $row;
                $result = '';
                $result .= '<center>';
                $result .= '<span> '.$personInCharge.' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->addColumn('created_at',function ($row){
                // $personInCharge = $row->rapidx_user_person_in_charge->name ?? '';
                // $personInCharge = $row;
                $result = '';
                $result .= '<center>';
                $result .= '<span> '.$row->created_at->format('F j, Y').' </span>';
                $result .= '<br>';
                $result .= '</br>';
                return $result;
            })
            ->rawColumns(['rawAction','rawStatus','created_by','created_at'])
            ->make(true);
        } catch (Exception $e) {
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
    public function index(Request $request){
        return 'true' ;
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function dPpdProcessOnly($params){
         //Change status into D if the SECTION IS PPS ELSE go to E VALIDATION PROCESS
        //STATUS DPRDPPDONLY DENGGPPDONLY DQCPPDONLY
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            $request = $params['request'];
            $qcSlipsId = $request['qc_slips_id'];
            // $request here is a plain array ($request->all() passed from the caller)
            $joinArr = function ($key) use ($request) {
                return collect((array) ($request[$key] ?? []))->filter()->join(' | ');
            };
            $dPpdCertificationCompletion = [
                'qc_slips_id'               => $qcSlipsId,
                "lot_1st_sample_peqcs_oper" => $request['text_lot_1st_sample_peqcs_oper']  ?? '',
                "1st_injected_ng_peqcs_oper"=> $request['text_1st_injected_ng_peqcs_oper'] ?? '',
                "1st_detected_ng_peqcs_oper"=> $request['text_1st_detected_ng_peqcs_oper'] ?? '',
                "2nd_sample_peqcs_oper"     => $request['text_2nd_sample_peqcs_oper']      ?? '',
                "2nd_injected_ng_peqcs_oper"=> $request['text_2nd_injected_ng_peqcs_oper'] ?? '',
                "2nd_detected_ng_peqcs_oper"=> $request['text_2nd_detected_ng_peqcs_oper'] ?? '',
            ];

            // DPpdCertificationCompletion::insert($dPpdCertificationCompletion);
            $dPpdCertificationCompletionApprover = [
                "decision_status"  => 'APP',
                "first_approver"   => $joinArr('text_1st_certified_prod_peqcs_oper'),
                "first_approver_2" => $joinArr('text_1st_certified_eng_peqcs_oper'),
                "first_approver_3" => $joinArr('text_1st_certified_qc_peqcs_oper'),
                "first_date"       => $request['text_1st_date_peqcs_oper']        ?? '',
                "first_time"       => $request['text_1st_time_peqcs_oper']        ?? '',
                "first_status"     => $request['text_oa_1st_result_peqcs_oper']   ?? '',
                "first_remarks"    => $request['text_1st_disapproval_peqcs_oper'] ?? '',

                "second_approver"  => $joinArr('text_2nd_certified_prod_peqcs_oper'),
                "second_approver_2"=> $joinArr('text_2nd_certified_eng_peqcs_oper'),
                "second_approver_3"=> $joinArr('text_2nd_certified_qc_peqcs_oper'),
                "second_date"      => $request['text_2nd_date_peqcs_oper']        ?? '',
                "second_time"      => $request['text_2nd_time_peqcs_oper']        ?? '',
                "second_status"    => $request['text_oa_2nd_result_peqcs_oper']   ?? '',
                "second_remarks"   => $request['text_2nd_disapproval_peqcs_oper'] ?? '',
            ];

            $opApprover =  OpApprover::where('qc_slips_id',$qcSlipsId)->where('approval_status',$request['approval_status'])
            // ->get();
            ->update($dPpdCertificationCompletionApprover);
            DB::commit();
            $dPpdCertificationCompletionQuery =  OpApprover::where('qc_slips_id',$qcSlipsId)->where('approval_status',$request['approval_status']);

            $requiredApprover = [
               "first_approver",
               "first_approver_2",
               "first_approver_3",
               "first_date",
               "first_time",
               "first_status",
            ];

            // $merge =  array_merge($dPpdCertificationCompletion,$requiredApprover);
           collect($requiredApprover)->each(function ($rowdPpdCertificationCompletion) use ($dPpdCertificationCompletionQuery) {
                $dPpdCertificationCompletionQuery->whereNotNull($rowdPpdCertificationCompletion);
            });
            $count = $dPpdCertificationCompletionQuery->count();
            if($count > 1){
                return [
                    'isChangeStatus' => 'true' //Change E Status
                ];
            }
            return [
                    'isChangeStatus' => 'false' //Change E Status
            ];
            //NULLABLE TABLE D , CHANGE THE VALIDATION TO OP APPROVER , CREATE NEW DATABASE FOR E ENGG
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function index1(Request $request){
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
        $isMachineOperatorExists = $params['isMachineOperatorExists'];
        if($params['selectPosition'] === 'Inspector'){
             switch (true) {
                case ($params['approval_status'] === 'PB'):
                    $newStatus = 'CLQCOQC';
                    $statusName = 'C Inspector Training Certification And Validation';
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
    public function getDivDeptSec(Request $request){
        try {
            $section = SystemHrisViewDivDeptSec::where('Division', '!=', '-')
                ->where('Division', '!=', 'Administration')
                ->whereNotNull('Section')
                ->select('Section')
                ->distinct()
                ->orderBy('Section')
                ->pluck('Section')->toArray();
                $customDivision = [
                    'PPD Grinding',
                    'PPD-TS Molding',
                ];
            $arrMerge = array_merge($section,$customDivision);
            return response()->json(['is_success' => 'true', 'section' => $arrMerge]);
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
        ->where('position_category',$params['positionCategory'])
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
    }
}

