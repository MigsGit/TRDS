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
            border-collapse: collapse;
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

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7px;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-primary {
            background-color: #007bff;
            color: #fff;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: #fff;
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
            <td class="label">
                Employee #:
            </td>

            <td class="value">
                {{ $employeeInfo->EmpNo ?? '-' }}
            </td>

            <td class="label">
                Division:
            </td>

            <td class="value">
                {{ $employeeInfo->Division ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Name:
            </td>

            <td class="value">
                {{ $employeeInfo->EmpName ?? '-' }}
            </td>

            <td class="label">
                Department:
            </td>

            <td class="value">
                {{ $employeeInfo->Department ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Position:
            </td>

            <td class="value">
                {{ $employeeInfo->Position ?? '-' }}
            </td>

            <td class="label">
                Section:
            </td>

            <td class="value">
                {{ $employeeInfo->Section ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Date Hired:
            </td>

            <td class="value">
                {{ $employeeInfo->DateHired ?? '-' }}
            </td>
        </tr>

    </table>


    {{-- TRAINING SUMMARY --}}
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
                <th class="date">
                    Date
                </th>

                <th class="title">
                    Title
                </th>

                <th class="objective">
                    Objective
                </th>

                <th class="trainor">
                    Trainor
                </th>

                <th class="result">
                    Results
                </th>

                <th class="venue">
                    Venue
                </th>

                <th class="mechanics">
                    Station
                </th>

                <th class="type">
                    Type of Training
                </th>

                <th class="remark">
                    Remark
                </th>
            </tr>
        </thead>


        <tbody>

            @forelse ($records as $record)

                <tr>

                    {{-- DATE --}}
                    <td class="text-center">
                        {{ $record->trainingDate ?? '-' }}
                    </td>


                    {{-- TITLE --}}
                    <td>
                        {{ $record->title ?? '-' }}
                    </td>


                    {{-- OBJECTIVE --}}
                    <td>
                        {{ $record->objective ?? '-' }}
                    </td>


                    {{-- TRAINOR --}}
                    <td>
                        {{ $record->trainor ?? '-' }}
                    </td>


                    {{-- RESULT --}}
                    <td class="text-center">

                        @if (($record->result ?? '') === 'Passed')

                            <span class="badge badge-success">
                                Passed
                            </span>

                        @elseif (($record->result ?? '') === 'Failed')

                            <span class="badge badge-danger">
                                Failed
                            </span>

                        @elseif (($record->result ?? '') === 'Complied')

                            <span class="badge badge-primary">
                                Complied
                            </span>

                        @else

                            <span class="badge badge-secondary">
                                N/A
                            </span>

                        @endif

                    </td>


                    {{-- VENUE --}}
                    <td>
                        {{ $record->trainingVenue ?? '-' }}
                    </td>


                    {{-- STATION --}}
                    <td>
                        {{ $record->station ?? '-' }}

                        @if (!empty($record->detailedStation))
                            <br>
                            <small>
                                {{ $record->detailedStation }}
                            </small>
                        @endif
                    </td>


                    {{-- TYPE --}}
                    <td>
                        {{ $record->typeOfTraining ?? '-' }}
                    </td>


                    {{-- REMARK --}}
                    <td>
                        {{ $record->training_remarks ?? '-' }}
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
