<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 15px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .employee-info {
            width: 100%;
            margin-bottom: 15px;
        }

        .employee-info td {
            padding: 3px 5px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 10%;
        }

        .value {
            width: 23%;
        }

        .summary {
            width: 100%;
            margin-bottom: 15px;
        }

        .summary td {
            border: 1px solid #999;
            padding: 5px;
            text-align: center;
        }

        .summary .label {
            background-color: #343a40;
            color: #fff;
            font-weight: bold;
        }

        .summary .value {
            font-size: 11px;
            font-weight: bold;
        }

        .training-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .training-table th {
            background-color: #343a40;
            color: #fff;
            border: 1px solid #222;
            padding: 5px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
        }

        .training-table td {
            border: 1px solid #999;
            padding: 5px 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .training-table tr:nth-child(even) td {
            background-color: #f7f7f7;
        }

        .text-center {
            text-align: center;
        }

        .date {
            width: 8%;
        }

        .title {
            width: 13%;
        }

        .objective {
            width: 19%;
        }

        .trainor {
            width: 10%;
        }

        .result {
            width: 7%;
        }

        .venue {
            width: 9%;
        }

        .mechanics {
            width: 10%;
        }

        .type {
            width: 14%;
        }

        .remark {
            width: 10%;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <h2>EMPLOYEE TRAINING RECORD</h2>
    </div>

    {{-- EMPLOYEE INFORMATION --}}
    <table class="employee-info">
        <tr>
            <td class="label">Employee #:</td>
            <td class="value">
                {{ $employeeInfo->EmpNo ?? '-' }}
            </td>
                
            <td class="label">Division:</td>
            <td class="value">
                {{ $employeeInfo->Division ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">Name:</td>
            <td class="value">
                {{ $employeeInfo->EmpName ?? '-' }}
            </td>
            
            <td class="label">Department:</td>
            <td class="value">
                {{ $employeeInfo->Department ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">Position:</td>
            <td class="value">
                {{ $employeeInfo->Position ?? '-' }}
            </td>
            
            <td class="label">Section:</td>
            <td class="value" colspan="3">
                {{ $employeeInfo->Section ?? '-' }}
            </td> 
        </tr>

        <tr>
            <td class="label">Date Hired:</td>
            <td class="value">
                {{ $employeeInfo->DateHired ?? '-' }}
            </td>
        </tr>
    </table>


    {{-- TRAINING SUMMARY --}}
    @php
        $passed = $etrRecords->where('Result', 'Passed')->count();
        $complied = $etrRecords->where('Result', 'Complied')->count();
        $failed = $etrRecords->where('Result', 'Failed')->count();

        // If "Actual Hands On" has a different condition in your system,
        // replace this condition with your actual logic.
        $actualHandsOn = $etrRecords->filter(function ($record) {
            return stripos(
                $record->employee_training_record_info->Mechanics ?? '',
                'hands on'
            ) !== false;
        })->count();

        $total = $etrRecords->count();
    @endphp

    <table class="summary">
        <tr>
            <td class="label">Passed</td>
            <td class="label">Complied</td>
            <td class="label">Failed</td>
            <td class="label">Actual Hands On</td>
            <td class="label">Total</td>
        </tr>

        <tr>
            <td class="value">
                {{ $passed }}
            </td>

            <td class="value">
                {{ $complied }}
            </td>

            <td class="value">
                {{ $failed }}
            </td>

            <td class="value">
                {{ $actualHandsOn }}
            </td>

            <td class="value">
                {{ $total }}
            </td>
        </tr>
    </table>


    {{-- TRAINING RECORDS --}}
    <table class="training-table">
        <thead>
            <tr>
                <th class="date">Date</th>
                <th class="title">Title</th>
                <th class="objective">Objective</th>
                <th class="trainor">Trainor</th>
                <th class="result">Results</th>
                <th class="venue">Venue</th>
                <th class="mechanics">Mechanics</th>
                <th class="type">Type of Training</th>
                <th class="remark">Remark</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($etrRecords as $record)

                <tr>

                    {{-- DATE --}}
                    <td class="text-center">
                        {{ $record->employee_training_record_info->PeriodFrom ?? "-" }}
                        <br>
                        {{ $record->employee_training_record_info->PeriodTo ?? "-" }}
                    </td>

                    {{-- TITLE --}}
                    <td>
                        {{ $record->employee_training_record_info->Title ?? "-" }}
                    </td>

                    {{-- OBJECTIVE --}}
                    <td>
                        {{ $record->employee_training_record_info->Objective ?? "-" }}
                    </td>

                    {{-- TRAINOR --}}
                    <td>
                        {{ $record->employee_training_record_info->Trainor ?? "-" }}
                    </td>

                    {{-- RESULT --}}
                    <td class="text-center">
                        {{ $record->Result ?? "-" }}
                    </td>

                    {{-- VENUE --}}
                    <td>
                        {{ $record->employee_training_record_info->Venue ?? "-" }}
                    </td>

                    {{-- MECHANICS --}}
                    <td>
                        {{ $record->employee_training_record_info->Mechanics ?? "-" }}
                    </td>

                    {{-- TYPE --}}
                    <td>
                        {{ $record->employee_training_record_info->TypeTraining ?? "-" }}
                    </td>

                    {{-- REMARK --}}
                    <td>
                        {{ $record->Remarks ?? "-" }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="9" class="text-center">
                        No training records found.
                    </td>
                </tr>

            @endforelse
        </tbody>
    </table>

</body>
</html>
