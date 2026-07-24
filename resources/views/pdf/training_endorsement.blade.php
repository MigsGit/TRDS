<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Training Endorsement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px 30px;
        }
        h2 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            border: none;
            padding: 2px 5px;
            vertical-align: top;
        }
        .header-table .label {
            font-weight: bold;
            width: 220px;
        }
        hr {
            border: 1px solid #000;
            margin: 10px 0;
        }
        .info-text {
            font-size: 11px;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        .dates-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .dates-table td {
            border: none;
            padding: 3px 5px;
        }
        .dates-table .label {
            font-weight: bold;
            width: 320px;
        }
        
        /* Main Employee Table */
        .emp-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }
        .emp-table thead {
            display: table-header-group; /* Ensures headers repeat nicely on new pages */
        }
        .emp-table tr {
            page-break-inside: avoid !important; /* Prevents Dompdf from splitting employee rows */
        }
        .emp-table th, .emp-table > tbody > tr > td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .emp-table th {
            background-color: #d4edda;
            font-size: 10px;
        }
        .emp-table td {
            font-size: 9px;
        }
        
        /* Seamless Inner Exam Table for multi-exam alignment */
        .inner-exam-cell {
            padding: 0 !important;
            vertical-align: top !important;
        }
        .inner-exam-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .inner-exam-table td {
            border: none;
            border-bottom: 1px solid #000;
            border-right: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 9px;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .inner-exam-table tr:last-child td {
            border-bottom: none; /* Prevents double border at bottom */
        }
        .inner-exam-table td:last-child {
            border-right: none; /* Prevents double border on right side */
        }

        .text-left {
            text-align: left !important;
        }
        .badge-passed {
            color: #155724;
            font-weight: bold;
        }
        .badge-failed {
            color: #721c24;
            font-weight: bold;
        }

        .signatory-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>Memorandum</h2>

    <table class="header-table">
        <tr>
            <td class="label">Document #</td>
            <td>: {{ $endorsement->ctrl_no ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Subject</td>
            <td>: Technical Training Result and Endorsement</td>
        </tr>
        <tr>
            <td class="label">HR Memo #</td>
            <td>: {{ $hr_memo_no ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Training Request CTRL #</td>
            <td>: {{ $training_request_ctrl ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Date</td>
            <td>: {{ $endorsement_date ?? '' }}</td>
        </tr>
    </table>

    <hr>

    <p class="info-text">
        Below is the result of technical training conducted to newly hired personnel in compliance to Personnel training and certification system (PQS-I01-008):
    </p>

    <table class="dates-table">
        <tr>
            <td class="label">HR Endorsement to Operations TU date:</td>
            <td>{{ $hr_endorsement_date ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Operations Training Unit Training Date:</td>
            <td>{{ $training_date ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Operations Training Unit endorsement to requestor:</td>
            <td>{{ $endorsement_to_requestor_date ?? '' }}</td>
        </tr>
    </table>

    <table class="emp-table">
        <colgroup>
            <col style="width: 4%;">  <!-- No. -->
            <col style="width: 10%;"> <!-- Date Hired -->
            <col style="width: 11%;"> <!-- Emp No. -->
            <col style="width: 16%;"> <!-- Name -->
            <col style="width: 17%;"> <!-- Pos/Dept/Sec -->
            <col style="width: 16%;"> <!-- Exam Title -->
            <col style="width: 6%;">  <!-- Score -->
            <col style="width: 6%;">  <!-- Rating -->
            <col style="width: 7%;">  <!-- Remarks -->
            <col style="width: 7%;">  <!-- Immediate Superior -->
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Date Hired</th>
                <th rowspan="2">Emp No.</th>
                <th rowspan="2">Name</th>
                <th rowspan="2">Pos/Dept/Sec</th>
                <th colspan="4">Theoretical Examination</th>
                <th rowspan="2">Immediate Superior</th>
            </tr>
            <tr>
                <th>Title</th>
                <th>Score</th>
                <th>Rating</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNum = 1; @endphp
            @foreach($employees as $employee)
                @php
                    $exams = $employee['exams'] ?? [];
                    $examCount = count($exams);
                @endphp
                <tr>
                    <td>{{ $rowNum }}</td>
                    <td>{{ $employee['date_hired'] ?? '' }}</td>
                    <td>{{ $employee['emp_no'] ?? '' }}</td>
                    <td class="text-left">{{ $employee['name'] ?? '' }}</td>
                    <td>{{ $employee['position'] ?? '' }}</td>
                    
                    <!-- Inner Container for Exams -->
                    <td colspan="4" class="inner-exam-cell">
                        @if($examCount > 0)
                            <table class="inner-exam-table">
                                <colgroup>
                                    <col style="width: 45.7%;"> <!-- Title -->
                                    <col style="width: 17.1%;"> <!-- Score -->
                                    <col style="width: 17.1%;"> <!-- Rating -->
                                    <col style="width: 20.1%;"> <!-- Remarks -->
                                </colgroup>
                                @foreach($exams as $exam)
                                    <tr>
                                        <td>{{ $exam['title'] ?? '' }}</td>
                                        <td>{{ $exam['score'] ?? '' }}</td>
                                        <td>{{ $exam['rating'] ?? '' }}</td>
                                        <td class="{{ str_contains(strtolower($exam['remark'] ?? ''), 'passed') ? 'badge-passed' : 'badge-failed' }}">
                                            {{ $exam['remark'] ?? '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            <div style="padding: 5px;">No exam records</div>
                        @endif
                    </td>

                    <td>{{ $employee['immediate_superior'] ?? '' }}</td>
                </tr>
                @php $rowNum++; @endphp
            @endforeach
        </tbody>
    </table>

    <table class="dates-table" style="margin-top: 15px;">
        <tr>
            <td class="label">Employees will not be endorsed:</td>
        </tr>
        @foreach($employees_will_not_endorse as $employee)
            <tr>
                <td><strong>{{ $employee['name'] ?? '' }} ({{ $employee['emp_no'] ?? '' }})</strong> - {{ $employee['remarks'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>

    {{-- Signatory Section --}}
    @php
        $checkedBy = $endorsement->te_approval_details->where('approval_type', 'checked_by') ?? collect();
        $approvedBy = $endorsement->te_approval_details->where('approval_type', 'approved_by') ?? collect();
    @endphp

    <table class="signatory-table">
        <tr>
            <td style="width:33%; text-align:center; padding-bottom: 10px; font-weight: bold;">
                Prepared by:
            </td>
            <td style="width:33%; text-align:center; padding-bottom: 10px; font-weight: bold;">
                Checked by:
            </td>
            <td style="width:33%; text-align:center; padding-bottom: 10px; font-weight: bold;">
                Approved by:
            </td>
        </tr>
        
        <tr>
            <td style="width:33%; text-align:center; vertical-align: top; padding: 0 10px;">
                @if ($endorsement->status > 0)
                    <img src="http://rapidx/RapidX_E-Signature/{{ $endorsement->created_by_user_details->employee_number.'.png' }}" 
                        alt="Signature" 
                        style="width:80px; height:auto; display:block; margin: 0 auto -20px auto;">
                @endif
                <span style="display:block; font-weight: bold;">
                    {{ $endorsement->created_by_user_details->name ?? '' }}
                    <br>
                    Trainer Inspector
                </span>
            </td>
            
            <td style="width:33%; text-align:center; vertical-align: top; padding: 0 10px;">
                @foreach($checkedBy as $checker)
                    <div style="display: block; text-align: center; margin-bottom: 15px;">
                        @if ($endorsement->status > 2)
                            <img src="http://rapidx/RapidX_E-Signature/{{ $checker->approver_details->employee_number.'.png' }}" 
                                alt="Signature" 
                                style="width:80px; height:auto; display:block; margin: 0 auto -20px auto;">
                        @endif
                        <span style="display:block; font-weight: bold;">
                            {{ $checker->approver_details->name ?? '' }}
                            <br>
                            QC Training Supervisor
                        </span>
                    </div>
                @endforeach
            </td>
            
            <td style="width:33%; text-align:center; vertical-align: top; padding: 0 10px;">
                @foreach($approvedBy as $approver)
                    <div style="display: block; text-align: center; margin-bottom: 15px;">
                        @if ($endorsement->status == 3)
                            <img src="http://rapidx/RapidX_E-Signature/{{ $approver->approver_details->employee_number.'.png' }}" 
                                alt="Signature" 
                                style="width:80px; height:auto; display:block; margin: 0 auto -20px auto;">
                        @endif
                        <span style="display:block; font-weight: bold;">
                            {{ $approver->approver_details->name ?? '' }}
                            <br>
                            @if ($approver->approver_details->employee_number == 2055)
                                TU Head
                            @elseif ($approver->approver_details->employee_number == 'S022')
                                General Manager
                            @else
                                {{ $approver->approver_details->employee_info->Position ?? '' }}
                            @endif
                        </span>
                    </div>
                @endforeach
            </td>
        </tr>
    </table>

    {{-- Attachment Section --}}
    @foreach($employees as $employee)
        @if(!empty($employee['attachment']))
            <div style="page-break-inside: avoid; margin-top: 20px;">
                <p style="font-weight: bold; font-size: 12px; margin-bottom: 10px;">
                    {{ $employee['name'] ?? '' }} - Hands-On Attachment:
                </p>
                <img src="{{ $employee['attachment'] }}" alt="" style="max-width: 100%; max-height: 600px; object-fit: contain; display: block; border: 1px solid #ccc;">
            </div>
        @endif
    @endforeach

</body>
</html>