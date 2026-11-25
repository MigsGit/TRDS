<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>Questionnaire</title>

        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }

            .card {
                border: 1px solid #757171;
                margin-bottom: 15px;
                padding: 12px;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            table,
            th,
            td {
                border: 1px solid #000;
            }

            th,
            td {
                padding: 6px;
                text-align: center;
            }

            .text-start {
                text-align: left;
            }

            .answer-line {
                border-bottom: 1px solid #999;
                height: 25px;
                margin-top: 5px;
            }
        </style>
    </head>

    <body>
        <h2 style="text-align:center;">
            {{ $questionnaire->exam_title }}
        </h2>
        <hr>

        <strong>Description:</strong>
        <br>
        {{ $questionnaire->description }}
        <br><br>

        <strong>Purpose:</strong>
        <br>
        {{ $questionnaire->purpose }}
        <br><br>

        <strong>Instruction:</strong>
        <br>
        {{ $questionnaire->exam_instruction }}
        <br>

        <hr>

        <table style="border:none; font-size:13px; width:100%;">
            <tr>
                <td style="border:none; text-align:left; width:15%;">
                    <strong>Department</strong>
                </td>
                <td style="border:none; text-align:left; width:35%;">
                    : {{ $questionnaire->department }}
                </td>

                <td style="border:none; text-align:left; width:15%;">
                    <strong>Position</strong>
                </td>
                <td style="border:none; text-align:left; width:35%;">
                    : {{ $questionnaire->position }}
                </td>
            </tr>

            <tr>
                <td style="border:none; text-align:left;">
                    <strong>Product Line</strong>
                </td>
                <td style="border:none; text-align:left;">
                    : {{ $questionnaire->product_line }}
                </td>

                <td style="border:none; text-align:left;">
                    <strong>Passing Score</strong>
                </td>
                <td style="border:none; text-align:left;">
                    : {{ $questionnaire->passing_score }}
                </td>
            </tr>
        </table>
        <hr>
        <br>
        @foreach ($questionnaire->questionnaire_details->sortBy('exam_no') as $question)
            <div class="card">
                @php
                    $items = json_decode($question->answer_choices_question, true);
                @endphp
                <div style="margin-bottom:10px;">
                    <strong>
                        {{ $question->exam_no }}.
                        @if ($question->category_type == 2)
                            {{ $question->description }}
                        @else
                            {{ $items[0]['question'] ?? '' }}
                        @endif
                    </strong>

                    <span style="float:right;">
                        {{ $question->points }}
                        {{ $question->points > 1 ? 'pts' : 'pt' }}
                    </span>
                </div>

                {{-- IMAGE --}}
                @if (!empty($question->image))
                    <div style="text-align:center;margin:10px;">
                        <img src="{{ storage_path('app/public/questionnaire_attachment/' . $question->image) }}"
                            style="max-width:100%;">
                    </div>
                @endif

                {{-- MULTIPLE CHOICE --}}
                @if ($question->category_type == 0)
                    @foreach ($items as $item)
                        @foreach ($item['choices'] as $choice)
                            <div style="margin:8px 0;">
                                @if ($question->points > 1)
                                    <input type="checkbox">
                                @else
                                    <input type="radio">
                                @endif
                                <span style="margin-left:8px;">
                                    {{ $choice }}
                                </span>
                            </div>
                        @endforeach
                    @endforeach

                {{-- IDENTIFICATION / ESSAY --}}
                @elseif($question->category_type == 1)
                    @if ($question->type == 'Identification')
                        @for ($i = 0; $i < 3; $i++)
                            <div class="answer-line"></div>
                        @endfor
                        <br>
                    @elseif($question->type == 'Essay')
                        @for ($i = 0; $i < 3; $i++)
                            <div class="answer-line"></div>
                        @endfor
                        <br>
                    @endif

                {{-- GRID / TABLE --}}
                @elseif($question->category_type == 2)
                    <table>
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
    </body>
</html>
