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

    public function getApprovalStatus($approvalStatus){
        try {
             switch ($approvalStatus) {
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
                throw new \Exception('User not found. Please add to Rapidx User Module!');
            }
            if (!$user->email) {
                DB::rollback();
                throw new \Exception('User Email not found.  Please add to Rapidx User Module!');
            }
           return [
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
