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
        }
        .dates-table td {
            border: none;
            padding: 3px 5px;
        }
        .dates-table .label {
            font-weight: bold;
            width: 320px;
        }
        .emp-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .emp-table th, .emp-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            text-align: center;
            vertical-align: middle;
        }
        .emp-table th {
            background-color: #d4edda;
            font-size: 10px;
        }
        .emp-table td {
            font-size: 10px;
        }
        .text-left {
            text-align: left;
        }
        .badge-passed {
            color: #155724;
            font-weight: bold;
        }
        .badge-failed {
            color: #721c24;
            font-weight: bold;
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
        {{-- <tr>
            <td class="label">To</td>
            <td>: {{ $to ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Attn</td>
            <td>: {{ $attn ?? '' }}</td>
        </tr> --}}
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
                    $rowSpan = $examCount > 0 ? $examCount : 1;
                @endphp

                @if($examCount > 0)
                    @foreach($exams as $index => $exam)
                        <tr>
                            @if($index === 0)
                                <td rowspan="{{ $rowSpan }}">{{ $rowNum }}</td>
                                <td rowspan="{{ $rowSpan }}">{{ $employee['date_hired'] ?? '' }}</td>
                                <td rowspan="{{ $rowSpan }}">{{ $employee['emp_no'] ?? '' }}</td>
                                <td rowspan="{{ $rowSpan }}" class="text-left">{{ $employee['name'] ?? '' }}</td>
                                <td rowspan="{{ $rowSpan }}">{{ $employee['position'] ?? '' }}</td>
                            @endif
                            <td>{{ $exam['title'] ?? '' }}</td>
                            <td>{{ $exam['score'] ?? '' }}</td>
                            <td>{{ $exam['rating'] ?? '' }}</td>
                            <td class="{{ strtolower($exam['remark'] ?? '') === 'passed' ? 'badge-passed' : 'badge-failed' }}">
                                {{ $exam['remark'] ?? '' }}
                            </td>
                            @if($index === 0)
                                <td rowspan="{{ $rowSpan }}">{{ $employee['immediate_superior'] ?? '' }}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ $rowNum }}</td>
                        <td>{{ $employee['date_hired'] ?? '' }}</td>
                        <td>{{ $employee['emp_no'] ?? '' }}</td>
                        <td class="text-left">{{ $employee['name'] ?? '' }}</td>
                        <td>{{ $employee['position'] ?? '' }}</td>
                        <td colspan="4">No exam records</td>
                        <td>{{ $employee['immediate_superior'] ?? '' }}</td>
                    </tr>
                @endif

                @php $rowNum++; @endphp
            @endforeach
        </tbody>
    </table>
    <table class="dates-table">
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

    <br><br><br>
    <table style="width:100%; border:none; margin-top:20px;">
        <tr>
            <td style="width:33%; text-align:center; vertical-align:top;">
                <strong>Prepared by:</strong><br><br>
                {{ $endorsement->created_by_user_details->name ?? '' }}
            </td>
            <td style="width:33%; text-align:center; vertical-align:top;">
                <strong>Checked by:</strong><br><br>
                @foreach($checkedBy as $checker)
                    {{-- {{ getUserDetails($checker->rapidx_id) }}<br> --}}
                    {{ $checker->approver_details->name ?? '' }}<br>
                @endforeach
            </td>
            <td style="width:33%; text-align:center; vertical-align:top;">
                <strong>Approved by:</strong><br><br>
                @foreach($approvedBy as $approver)
                    {{-- {{ getUserDetails($approver->rapidx_id) }}<br> --}}
                    {{ $approver->approver_details->name ?? '' }}<br>
                @endforeach
            </td>
        </tr>
    </table>
    
    @foreach($employees as $employee)
        @if(!empty($employee['attachment']))
            <div style="page-break-before: always;">
                <p style="font-weight: bold; font-size: 12px; margin-bottom: 10px;">{{ $employee['name'] ?? '' }} - Hands-On Attachment:</p>
                <img src="{{ $employee['attachment'] }}" alt="" style="width:100%; max-height:700px; object-fit:contain; display:block; border:1px solid #ccc;">
            </div>
        @endif
    @endforeach



</body>
</html>
