<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Theoretical Exam Result</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .card {
            border: 1px solid #757171;
            margin-bottom: 10px;
            padding: 10px;
            page-break-inside: avoid;
            break-inside: avoid;   
        }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 5px; text-align: center; }
        .text-start { text-align: left; }
    </style>
</head>
<body>

<h2 style="text-align:center; margin-bottom:20px;">Theoretical Exam Result</h2>
<hr>
<div style="text-align:center;">
    <span style="margin-right: 25px;"><strong>Score:</strong> {{ $examResultDetails[0]->identification_essay_score + $examResultDetails[0]->score }} / {{ $questions['summary']['total_points'] }}</span>
    <span style="margin-right: 25px;"><strong>Passing Score:</strong> {{ $questionnaireInfo['passing_score'] }}</span>
    <span style="margin-right: 25px;"><strong>Percentage:</strong> {{ $examResultDetails[0]['rating'] }}</span>
    <span style="margin-right: 25px;">
        <strong>Rating:</strong> 
        <span style="padding: 2px 10px; border-radius: 12px; color: #fff; font-weight: bold; background-color: {{ str_contains(strtolower($examResultDetails[0]['remark']), 'passed') ? '#28a745' : '#dc3545' }};">
            {{ $examResultDetails[0]['remark'] }}
        </span>
    </span>
</div>
<hr>
<table style="width:100%; margin-bottom: 15px; border-collapse: collapse; border: none; font-size: 13px;">
    <tr style="border: none;">
        <td style="padding: 5px; font-weight: bold; text-align: left; border: none; width: 25%;">Exam Name</td>
        <td style="padding: 5px; text-align: left; border: none;">: {{ $questionnaireInfo['exam_title'] }}</td>
    </tr>
    <tr style="border: none;">
        <td style="padding: 5px; font-weight: bold; text-align: left; border: none;">Examination Date</td>
        <td style="padding: 5px; text-align: left; border: none;">: {{ $examResultDetails[0]['date_examination'] }}</td>
    </tr>
    <tr style="border: none;">
        <td style="padding: 5px; font-weight: bold; text-align: left; border: none;">Examination Taken</td>
        <td style="padding: 5px; text-align: left; border: none;">: {{ $takeExam }}</td>
    </tr>
    <tr style="border: none;">
        <td style="padding: 5px; font-weight: bold; text-align: left; border: none;">Employee No</td>
        <td style="padding: 5px; text-align: left; border: none;">: {{ $examResultDetails[0]['exam_result_info']['employee_no'] }}</td>
    </tr>
    <tr style="border: none;">
        <td style="padding: 5px; font-weight: bold; text-align: left; border: none;">Employee Name</td>
        <td style="padding: 5px; text-align: left; border: none;">: {{ $examResultDetails[0]['exam_result_info']['employee_name'] }}</td>
    </tr>
    <tr style="border: none;">
        <td style="padding: 5px; font-weight: bold; text-align: left; border: none;">Date Hired</td>
        <td style="padding: 5px; text-align: left; border: none;">: {{ $examResultDetails[0]['exam_result_info']['date_hired'] }}</td>
    </tr>
    <tr style="border: none;">
        <td style="padding: 5px; font-weight: bold; text-align: left; border: none;">Position</td>
        <td style="padding: 5px; text-align: left; border: none;">: {{ $questionnaireInfo['position'] }}</td>
    </tr>
</table>
<hr>
<span style="width:100%; font-size: 13px;">
    <strong>
        Instruction:
    </strong>
</span><br><br>
<span>
    {{ $questionnaireInfo['exam_instruction'] }}
</span>
<hr>
@foreach($questions as $key => $question)
    @if($key === 'summary')
        @continue
    @endif

    <div class="card" style="margin-top: 20px;">

        <!-- QUESTION HEADER -->
        <div>
            <strong>
                {{ $question['exam_no'] }}.
                @if($question['category_type'] == 2)
                    {{ $question['description'] }}
                @else
                    {{ $question['answer_choices_question'][0]['question'] }}
                @endif
            </strong>

            <span style="float:right;">
                {{ $question['points'] }} {{ $question['points'] == 1 ? 'pt' : 'pts' }}
            </span>
        </div>

        <!-- IMAGE -->
        @if(!empty($question['image']))
            <div style="text-align:center; margin:5px 0;">
                <img src="{{ storage_path() . ("/app/public/questionnaire_attachment/". $question['image']) }}">
            </div>
        @endif

        <!-- QUESTION TYPES -->
        @switch($question['category_type'])

        {{-- ================= MULTIPLE CHOICE ================= --}}
            @case(0)
                @foreach($question['answer_choices_question'] as $item)
                    @php
                        $userAnswers = array_map('trim', explode(',', $item['user_answer'] ?? ''));
                    @endphp

                    @foreach($item['choices'] as $choice)
                        <div style="margin-bottom: 5px;">
                            <input type="{{ $question['points'] > 1 ? 'checkbox' : 'radio' }}"
                                {{ in_array(trim($choice), $userAnswers) ? 'checked' : '' }}>
                            
                            <span style="margin-left: 5px;">{{ $choice }}</span>
                        </div>
                    @endforeach
                @endforeach
            @break


            {{-- ================= IDENTIFICATION / ESSAY ================= --}}
            @case(1)
                @foreach($question['answer_choices_question'] as $item)

                    @if($question['type'] == 'Identification')
                        <div>
                            <strong>Answer:</strong> {{ $item['user_answer'] }}
                        </div>
                    @endif

                    @if($question['type'] == 'Essay')
                        <div>
                            <strong>Answer:</strong><br>
                            {{ $item['user_answer'] }}
                        </div>
                    @endif

                @endforeach
            @break


            {{-- ================= GRID TYPE ================= --}}
            @case(2)
                <table>
                    <thead>
                        <tr>
                            <th>Process</th>
                            @foreach($question['answer_choices_question'][0]['choices'] as $col)
                                <th>{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($question['answer_choices_question'] as $row)
                            <tr>
                                <td class="text-start">{{ $row['question'] }}</td>

                                @foreach($row['choices'] as $choice)
                                    <td>
                                        {{ $row['user_answer'] == $choice ? '*' : '' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @break

        @endswitch

    </div>

@endforeach


{{-- ================= SUMMARY ================= --}}
{{-- @if(isset($questions['summary']))
    <div class="card">
        <h3>Summary</h3>
        <p><strong>Result:</strong> {{ $questions['summary']['result'] }}</p>
        <p><strong>Score:</strong> {{ $questions['summary']['total_score'] }} / {{ $questions['summary']['total_points'] }}</p>
        <p><strong>Percentage:</strong> {{ $questions['summary']['percentage'] }}%</p>
        <p><strong>Passing Score:</strong> {{ $questions['summary']['passing_score'] }}</p>
    </div>
@endif --}}

</body>
</html>