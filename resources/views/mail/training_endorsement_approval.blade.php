<!DOCTYPE html>
<html>
<head>
    <title>Action Required: Training Endorsement Approval</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            line-height: 1.5;
        }
        p, label, td {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div>
        <span style="font-size: 18px; font-weight: bold;">Good day,</span>
        <br><br>
        @if ($type > 2)
            <p>
                Please be informed that the <strong>Training Endorsement</strong> has been approved.
            </p>
            <strong>Training Endorsement Details</strong>
            <table>
                <tr>
                    <td><strong>Endorsement Control No.:</strong></td>
                    <td>{{ $details->ctrl_no }}</td>
                </tr>
                <tr>
                    <td><strong>Created By:</strong></td>
                    <td>{{ $details->created_by_user_details->name }}</td>
                </tr>
                <tr>
                    <td><strong>Created Date:</strong></td>
                    <td>{{ $details->created_at }}</td>
                </tr>
            </table>
            <br>
        @else
            <p>
            {{-- An **{{ $type }}** has been submitted and is currently awaiting your review and approval. --}}
                A <strong>Training Endorsement</strong> has been submitted and is currently awaiting for <strong>{{ $type == 1 ? 'Checker\'s' : 'Approver\'s' }}</strong> approval.
            </p>
            <strong>Training Endorsement Details</strong>
            <table>
                <tr>
                    <td><strong>Endorsement Control No.:</strong></td>
                    <td>{{ $details->ctrl_no }}</td>
                </tr>
                <tr>
                    <td><strong>Created By:</strong></td>
                    <td>{{ $details->created_by_user_details->name }}</td>
                </tr>
                <tr>
                    <td><strong>Created Date:</strong></td>
                    <td>{{ $details->created_at }}</td>
                </tr>
            </table>
            <br>

            <p>
                Please review the training endorsement details and take the necessary actions.
            </p>

        @endif

        <span style="font-size: 14px;">Thank you.</span>
    </div>

    {{-- Email Footer --}}
    <hr style="border: 0; border-top: 1px solid #ccc; margin-top: 30px;">

    <div style="margin-top: 15px;">
        <div class="col-sm-12">
            <div class="form-group row">
                    <label class="col-sm-12 col-form-label">For more info, please log-in to your Rapidx account. Go to http://rapidx/ and Click http://rapidx/TRDSv2/training_endorsement </label>
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

    {{-- <div style="margin-top: 15px;">
        <p style="font-size: 12px; color: #666;">
            <b>Notice of Disclaimer:</b><br>
            <i>This message contains confidential information intended for a specific individual and purpose. If you are not the intended recipient, you should delete this message. Any disclosure, copying, or distribution of this message, or the taking of any action based on it, is strictly prohibited.</i>
        </p>
    </div>

    <div style="margin-top: 20px;">
        <span style="font-size: 16px; color: #0056b3;">
            <b>For concerns regarding this system, please contact ISS at local numbers 205, 206, or 208.</b>
        </span>
    </div> --}}
</body>
</html>
