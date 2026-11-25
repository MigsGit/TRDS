<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>
        {{ $questionnaire->exam_title }}
    </title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            background: #f8f9fa;
            color: #202124;
            font-size: 12px;
        }

        /* GOOGLE FORM HEADER */
        .form-header {
            background: #3d2b5c;
            height: 90px;
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }

        .form-header h1 {
            margin: 0;
            font-size: 22px;
        }

        .form-header p {
            margin-top: 8px;
            font-size: 12px;
        }

        /* INFORMATION CARD */
        .card {
            background: white;
            border: 1px solid #dadce0;
            border-radius: 10px;
            padding: 18px;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #523f74;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px;
            border: none;
        }

        .label {
            font-weight: bold;
        }

        /* QUESTION */
        .question-card {
            background: white;
            border: 1px solid #dadce0;
            border-radius: 10px;
            padding: 18px;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .question-header {
            font-size: 13px;
            margin-bottom: 12px;
        }

        .question-number {
            font-weight: bold;
            color: #673ab7;
        }

        .points {
            float: right;
            color: #402472;
            font-weight: bold;
        }

        /* GOOGLE FORM OPTIONS */
        .option {
            padding: 8px;
            margin: 6px 0;
            border-radius: 6px;
        }

        .option:hover {
            background: #f3e8ff;
        }


        .radio {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid #361f5f;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid #463761;
            margin-right: 10px;
            vertical-align: middle;
        }

        /* IMAGE */
        .image-container {
            text-align: center;
            margin: 15px;
        }

        .image-container img {
            max-width: 90%;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        /* ESSAY */
        .answer-line {
            height: 28px;
            border-bottom: 1px solid #bdbdbd;
            margin-top: 10px;
        }

        /* GRID QUESTION */
        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .grid th {
            background: #2c184e;
            color: white;
            padding: 8px;
            border: 1px solid #aaa;
        }

        .grid td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .text-start {
            text-align: left !important;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- GOOGLE FORM TITLE -->
    <div class="form-header">
        <p>
            Theoretical Exam
        </p>
        <h1>
            {{ $questionnaire->exam_title }}
        </h1>
    </div>

    <!-- DESCRIPTION -->
    <div class="card">
        <div class="section-title">
            Description:
        </div>
        <div class="section-content">
            {{ $questionnaire->description }}
        </div><br>

        <div class="section-title">
            Purpose:
        </div>
        <div class="section-content">
            {{ $questionnaire->purpose }}
        </div><br>

        <div class="section-title">
            Instructions:
        </div>
        <div class="section-content">
            {{ $questionnaire->exam_instruction }}
        </div>
    </div>

    <!-- DETAILS -->
    <div class="card">
        <table class="info-table" cellspacing="0" cellpadding="2">
            <tr>
                <td class="section-title" style="width:20px;">Department:</td>
                <td>{{ $questionnaire->department }}</td>

                <td class="section-title" style="width:20px;">Position:</td>
                <td>{{ $questionnaire->position }}</td>
            </tr>

            <tr>
                <td class="section-title" style="width:20px;">Product Line:</td>
                <td>{{ $questionnaire->product_line }}</td>

                <td class="section-title" style="width:20px;">Passing Score:</td>
                <td>{{ $questionnaire->passing_score }}</td>
            </tr>
        </table>

    </div>


    <!-- QUESTIONS -->
    @foreach ($questionnaire->questionnaire_details->sortBy('exam_no') as $question)
        <div class="question-card">
            @php
                $items = json_decode($question->answer_choices_question, true);
            @endphp

            <div class="question-header">
                <span class="question-number">
                    {{ $question->exam_no }}.
                </span>

                <strong>
                    @if ($question->category_type == 2)
                        {{ $question->description }}
                    @else
                        {{ $items[0]['question'] ?? '' }}
                    @endif
                </strong>

                <span class="points">
                    {{ $question->points }}
                    {{ $question->points > 1 ? 'pts' : 'pt' }}
                </span>
            </div>


            @if (!empty($question->image))
                <div class="image-container">
                    <img src="{{ storage_path('app/public/questionnaire_attachment/' . $question->image) }}">
                </div>
            @endif

            {{-- MULTIPLE CHOICE --}}
            @if ($question->category_type == 0)
                @foreach ($items as $item)
                    @foreach ($item['choices'] as $choice)
                        <div class="option">

                            @if ($question->points > 1)
                                <span class="checkbox"></span>
                            @else
                                <span class="radio"></span>
                            @endif
                            {{ $choice }}
                        </div>
                    @endforeach
                @endforeach

                {{-- ESSAY --}}
            @elseif($question->category_type == 1)
                @if ($question->type == 'Identification' || $question->type == 'Essay')
                    @for ($i = 0; $i < 3; $i++)
                        <div class="answer-line"></div>
                    @endfor
                @endif

                {{-- GRID --}}
            @elseif($question->category_type == 2)
                <table class="grid">
                    <thead>
                        <tr>
                            <th>
                                Process
                            </th>

                            @foreach ($items[0]['choices'] as $choice)
                                <th>
                                    {{ $choice }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $row)
                            <tr>
                                <td class="text-start">
                                    {{ $row['question'] }}
                                </td>

                                @foreach ($row['choices'] as $choice)
                                    <td>
                                        &nbsp;
                                    </td>
                                @endforeach

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
    <div class="footer">
        Generated Examination Form
    </div>
</body>
</html>
