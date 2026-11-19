<?php

namespace App\Http\Controllers;

use App\Model\QcSlip;
use App\Model\RapidMailer;
use App\Model\RapidXUser;
use App\Model\SystemOneHrisSubcon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;

class CommonController extends Controller
{

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

    public function getApprovalStatus($approvalStatus,$positionCategory){
        // return $approvalStatus;
        try {

             $spanColor = 'bg-danger';
             switch (true) {
            //  switch ($approvalStatus) {
                //VISUAL OPERATOR
                // Checkbox Validation Phase
                case $approvalStatus === 'AOPERPRDN':
                    $statusName = 'A Production Update Checklist';
                    break;

                case $approvalStatus === 'BOPERENGG':
                    $statusName = 'B Engineer Update Checklist';
                    break;

                case $approvalStatus === 'COPERQC':
                    $statusName = 'C QC Update Checklist';
                    break;

                // Day 1 Phase
                case $approvalStatus === 'AOPERPRDN1':
                    $statusName = 'A Production Day 1 Update';
                    break;

                case $approvalStatus === 'BOPERENGG1':
                    $statusName = 'B Engineer Day 1 Update';
                    break;

                case $approvalStatus === 'COPERQC1':
                    $statusName = 'C QC Day 1 Update';
                    break;

                // Day 2 Phase
                case $approvalStatus === 'AOPERPRDN2':
                    $statusName = 'A Production Day 2 Update';
                    break;

                case $approvalStatus === 'BOPERENGG2':
                    $statusName = 'B Engineer Day 2 Update';
                    break;

                case $approvalStatus === 'COPERQC2':
                    $statusName = 'C QC Day 2 Update';
                    break;

                // Day 3 Phase
                case $approvalStatus === 'AOPERPRDN3':
                    $statusName = 'A Production Day 3 Update';
                    break;

                case $approvalStatus === 'BOPERENGG3':
                    $statusName = 'B Engineer Day 3 Update';
                    break;

                case $approvalStatus === 'COPERQC3':
                    $statusName = 'C QC Day 3 Update';
                    break;

                // Day 4 Phase
                case $approvalStatus === 'AOPERPRDN4':
                    $statusName = 'A Production Day 4 Update';
                    break;

                case $approvalStatus === 'BOPERENGG4':
                    $statusName = 'B Engineer Day 4 Update';
                    break;

                case $approvalStatus === 'COPERQC4':
                    $statusName = 'C QC Day 4 Update';
                    break;

                // Day 5 Phase
                case $approvalStatus === 'AOPERPRDN5':
                    $statusName = 'A Production Day 5 Update';
                    break;

                case $approvalStatus === 'BOPERENGG5':
                    $statusName = 'B Engineer Day 5 Update';
                    break;
                case $approvalStatus === 'COPERQC5':
                    $statusName = 'C QC Day 5 Update';
                    break;
                case $approvalStatus === 'DOPEROSC':
                    $statusName = 'D PRODUCT ORIENTATION AND SAMPLE CHECK (QC)';
                    break;
                case $approvalStatus === 'EOPERVISUAL':
                    $statusName = 'E PRODUCT VALIDATION THROUGH GRR (FOR VISUAL STATIONS';
                    break;
                //MH - QCAPP
                //TECHNICIAN
                 case $approvalStatus === 'ENGHEADAPP':
                    $newStatus = 'ENGHEADAPP';
                    $statusName = 'Engineer Head Approval';
                    break;
                // ==== OPERATOR
                case $approvalStatus === 'PB':
                    $approvalStatus = 'APRODTO';
                    $statusName = 'PREPARED BY';
                    break;
                case $approvalStatus === 'APRODTO':
                    $approvalStatus = 'APRODTO';
                    $statusName = 'A Production Training Orientation';
                    break;
                case $approvalStatus === 'BENGGTQ':
                    $approvalStatus = 'BENGGTQ';
                    $statusName = 'B Engineer Training Qualification';
                    break;
                case $approvalStatus === 'CQCC':
                    $approvalStatus = 'CQCC';
                    $statusName = 'C Qc Certification';
                    break;
                case $approvalStatus === 'DPPDONLY':
                    $newStatus = 'DPPDONLY';
                    $statusName = 'D PPD Update';
                    break;
                case $approvalStatus === 'EENGVP':
                    $approvalStatus = 'EENGVP';
                    $statusName = 'E Engineering Validation Process';
                    break;
                // case 'DPRDPPDONLY':
                //     $newStatus = 'DPRDPPDONLY';
                //     $statusName = 'D Production Update';
                //     break;
                // case 'DENGGPPDONLY':
                //     $newStatus = 'DENGGPPDONLY';
                //     $statusName = 'D Engineering Update';
                //     break;
                // case 'DQCPPDONLY':
                //     $newStatus = 'DQCPPDONLY';
                //     $statusName = 'D QC Update';
                //     break;
                case $approvalStatus === 'EQCVP':
                    $approvalStatus = 'EQCVP';
                    $statusName = 'E Qc Validation Process';
                    break;
                case $approvalStatus === 'FQCVVO':
                    $approvalStatus = 'FQCVVO';
                    $statusName = 'F Qc Validation Visual Operator';
                    break; //CLQCOQC
                case ($approvalStatus === 'CLQCOQC'):
                    $newStatus = 'CLQCOQC';
                    $statusName = 'C VALIDATION PROCESS: QUALITY CONTROL SECTION <> OQC only';
                    break;
                case $approvalStatus === 'QCAPP':
                    $approvalStatus = 'QCAPP'; //QC Supervisor Appoval
                    $statusName = 'QC Supervisor Approval';
                    break;
                case $approvalStatus === 'ENGGHEADAPP':
                    $approvalStatus = 'ENGGHEADAPP'; //QC Supervisor Appoval
                    $statusName = 'For Engineering Head Approval';
                    break;
                case $approvalStatus === 'LQCHEADAPP' &&$positionCategory != 'Inspector' ||$positionCategory != 'Supervisor':
                    $approvalStatus = 'QCAPP'; //QC Supervisor Appoval
                    $statusName = 'QC Supervisor Approval';
                    break;
                case $approvalStatus === 'LQCHEADAPP' &&$positionCategory === 'Inspector' ||$positionCategory === 'Supervisor' :
                    $approvalStatus = 'QCAPP'; //QC Supervisor Appoval
                    $statusName = 'LQC Head Approval';
                    break;
                case $approvalStatus === 'OK':
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
        if($qcSlip->approval_status === "OK"){
            $header = "Please see the APPROVED Certification/Qualification.";
        }else{
            $header = "Please see the Certification/Qualification for your update.";
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
    public function sendEmailTrainingEndorsement($details, $type)
    {
        // Send email to the approver
        $grouped_email = $details->te_approval_details->groupBy('approval_type')->map(function ($group) {
            return $group->pluck('approver_details.email')->toArray();
        })->toArray();

        $to = array();
        $cc = array();

        switch($type){
            case 1:
                $subject = "TRDS - Training Endorsement For Checker's Approval ({$details->ctrl_no})";
                $to = $grouped_email['checked_by'];
                $cc = $grouped_email['approved_by'];
                $cc[] = $details->created_by_user_details->email;
                break;
            case 2:
                $subject = "TRDS - Training Endorsement For Approver's Approval ({$details->ctrl_no})";
                $to = $grouped_email['approved_by'];
                $cc = $grouped_email['checked_by'];
                $cc[] = $details->created_by_user_details->email;

                break;
            default:
                $subject = "TRDS - Training Endorsement Notification ({$details->ctrl_no})";
                $to = $details->created_by_user_details->email;
                $cc = array_merge($grouped_email['checked_by'], $grouped_email['approved_by']);
                break;
        }

        $cc = array_merge($cc, explode(',', $details->mail_cc));


        Mail::send('mail.training_endorsement_approval', ['details' => $details, 'type' => $type], function ($message) use ($to, $cc, $subject) {
            $message->to($to)->subject($subject);
            $message->cc($cc);
        });
        return response()->json([
            'details' => $details,
            'grouped_email' => $grouped_email,
            'type' => $type,
            'to' => $to,
            'cc' => $cc,
            'subject' => $subject
        ]);

    }
}
