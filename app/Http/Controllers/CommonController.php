<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class CommonController extends Controller
{
    //

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
                $CC = array_merge($grouped_email['checked_by'], $grouped_email['approved_by']);
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
