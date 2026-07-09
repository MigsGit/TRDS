<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    margin:0;
    padding:0;
    background:#ffffff;
    font-family: Arial, Helvetica, sans-serif;
}

.container{
    max-width:600px;
    margin:auto;
}

.header{
    text-align:center;
    padding:20px 15px;
}

.title{
    text-align:center;
    font-size:18px;
    font-weight:bold;
}

.text-center{
    text-align:center;
}

.details{
    margin:auto;
    font-size:12px;
}

.details td{
    padding:4px 8px;
}

.button{
    display:inline-block;
    background:#4A90E2;
    color:#ffffff !important;
    padding:10px 26px;
    border-radius:24px;
    font-size:13px;
}

.divider{
    border-top:1px solid #000;
    margin:20px 0;
}

.footer{
    font-size:11px;
    line-height:1.5;
    padding:15px;
}
</style>
</head>

<body>

<div class="container">

    <div class="header"> 
        <h2>Training Records Database System</h2> 
    </div> <div class="title"> Receive Training Request </div> <br> 
    <div class="text-center"> 
        <p style="font-size:14px; margin-bottom:10px;"> 
            {{-- Hi <strong>{{ $receivingRecipients }}</strong>,  --}}
            Hi
        </p> 
        <p style="font-size:14px; margin-bottom:10px;"> 
            Good day! 
        </p> 
        <p style="font-size:14px; margin-bottom:20px;"> 
            A new <strong>Training Request</strong> has been submitted for your receipt. 
            Kindly review the request details below and acknowledge receipt through the Training Records Database System. 
        </p> 
        <table class="details"> 
            <tr> 
                <td>
                    <strong>Control Number</strong>
                </td> <td>{{ $ctrlNumber }}</td> 
            </tr> 
            <tr> <td><strong>HR Memo Document No.</strong></td> 
                <td>{{ $documentNo }}</td> 
            </tr> 
            <tr> 
                <td><strong>Date Filed</strong></td> 
                <td>{{ $dateFiled }}</td> 
            </tr> 
        </table> <br><br> 
        {{-- <a href="{{ config('app.url') }}/TRDSv2" style="display:inline-block; background:#0d6efd; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:5px; font-weight:bold;"> Open Training Records Database System </a>  --}}
    </div> 
    <div class="divider"></div> 
        <div class="footer"> 
            <p class="text-center"> 
                <strong>
                    <em>If you have any questions or concerns regarding this Training Request, please contact local 208.
                    </em></strong> 
                </p> 
                
                <p style="text-align:justify;"> <strong>Notice of Disclaimer:</strong><br> This email contains confidential information intended solely for the designated recipient(s). If you have received this email in error, please notify the sender immediately and delete it from your system. Any unauthorized review, disclosure, copying, distribution, or use of its contents is strictly prohibited. </p> 
        </div>

</div>

</body>
</html>