<?php

namespace App\Http\Controllers;

use App\Model\QcSlip;
use Illuminate\Http\Request;

class CommonController extends Controller
{

    public function getApprovalStatus($approvalStatus){
        try {
             switch ($approvalStatus) {
                 case 'PB':
                     $approvalStatus = 'Prepared by:';
                     break;
                //  case 'CB':
                //      $approvalStatus = 'Checked by:';
                //      break;
                //  case 'AB':
                //      $approvalStatus = 'Approved by:';
                //      break;
                //  case 'EXQC':
                //      $approvalStatus = 'QC Head:';
                //      break;
                //  case 'EXOH':
                //      $approvalStatus = 'Operation Head:';
                //      break;
                //  case 'EXQA':
                //      $approvalStatus = 'QA Head:';
                //      break;
                 default:
                     $approvalStatus = '---';
                     break;
             }
             return [
                 'approvalStatus' => $approvalStatus,
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
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function emailMsg($params){
        $qcSlip = QcSlip::with('product_line')->where('id',$params['qc_slips_id'])
        ->whereNull('deleted_at')
        ->first();
        // if($getEcrStatus['status'] == 'DISAPPROVED'){
        //     $header = "Your ECR has been disapproved";
        // }else if($getEcrStatus['status'] == 'APPROVED'){
        //     $header = "Your ECR has been approved";
        // }else{
            // }
        $header = "Please see the Certification/Qualificationfor your update.";
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
                                                    <label class="col-sm-12 col-form-label"><b> Created By : </b><span class="text-black"> '.$qcSlip->created_by.' </span></label>
                                                </div>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label">For more info, please log-in to your Rapidx account. Go to http://rapidx/ and Click http://rapidx/4M/dashboard </label>
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
