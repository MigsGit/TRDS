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
    </div>

    <div class="title">
        HR MEMO For Approval
    </div>

    <br>

    <div class="text-center">
        <b>
            <p style="font-size:12px;">
                Hi, you have a HR Memo for approval! See details below:
            </p>
        </b>

        <br>

        <table class="details">
            <tr>
                <td><strong>Document Number</strong></td>
                <td>{{ $hr_memo->document_no }}</td>
            </tr>
            <tr>
                <td><strong>Prepared By</strong></td>
                <td>{{ $hr_memo->prepared_by_info->name }}</td>
            </tr>
            <tr>
                <td><strong>Date Filed</strong></td>
                <td>{{ $hr_memo->date_filed }}</td>
            </tr>
        </table>

        <br><br>

        <a href="http://rapidx/TRDSv2" class="button" style="display: inline-block; background: #4A90E2; color: #ffffff; font-family: Ubuntu, Helvetica, Arial, sans-serif, Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 100%; Margin: 0; text-decoration: none; text-transform: none; padding: 9px 26px 9px 26px; mso-padding-alt: 0px; border-radius: 24px;" target="_blank">
            Open TRDSV2 on RapidX
        </a>

    </div>

    <div class="divider"></div>

    <div class="footer">
        <p class="text-center">
            <strong><em>If there are any concerns with this HR Memo, call local 208.</em></strong>
        </p>

        <p style="text-align:justify;">
        Notice of Disclaimer:<br>
        This message contains confidential information intended for a specific individual and purpose,
        and is protected by law. If you are not the intended recipient, you should delete this message.
        Any disclosure, copying, or distribution of this message, or the taking of any action based on it,
        is strictly prohibited.
        </p>
    </div>

</div>

</body>
</html>
