@php $layout = 'layouts.layout'; @endphp
@extends($layout)
@section('title', 'Start Exam')

@section('content_page')

<style>
    .exam-scroll-container {
        max-height: 70vh;
        overflow-y: auto;
        padding-right: 10px;
    }

    .exam-scroll-container::-webkit-scrollbar {
        width: 8px;
    }

    .exam-scroll-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }

    .exam-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .class-disabled{
        pointer-events: none;
    }
</style>

<div class="content-wrapper">

    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ $exam->exam_title }}</h1>
            <p>{{ $exam->exam_instruction }}</p>
        </div>
    </section>

    <!-- Content -->
    <section class="content">
        <div class="container-fluid">
            <div id="stepOne">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Training Request Ctrl No.: </label>
                                        <select class="form-control select2bs5 get-training_request-ctrl_no" name="exam_training_request_ctrl_no" id="slctExamTrainingRequestCtrlNo" required></select>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Employee No.: </label>
                                        <select class="form-control select2bs5 get-training_request-employee_no" name="exam_training_request_employee_no" id="slctExamTrainingRequestEmployeeNo" required disabled></select>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Name: </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="exam_training_request_name" id="txtExamTrainingRequestName" required readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Date Hired: </label>
                                            <input type="date" class="form-control" name="exam_training_request_date_hired" id="txtExamTrainingRequestDateHired" required readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Date Examination: </label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="exam_training_request_date_examination" id="nmbrExamTrainingRequestDateExamination" required readonly>
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label class="font-weight-bold">Examination Take: </label>
                                            <input type="number" class="form-control" name="exam_training_request_examination_take" id="txtExamTrainingRequestExaminationTake" required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark" id="btnNext">
                            <i class="fas fa-arrow-right"></i> Next
                        </button>
                    </div>
                </div>
            </div>

            <div class="stepTwo d-none">
                <form action="" method="POST">
                    @csrf
                    <!-- ✅ SCROLLABLE AREA -->
                    <div class="exam-scroll-container">
                        @forelse($questions as $question)
                            @php
                                $items = json_decode($question->answer_choices_question, true);
                            @endphp
    
                            @foreach($items as $item)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <!-- Question Number -->
                                        <p>
                                            <strong>
                                                {{ $loop->parent->iteration }}. {{ $item['question'] }}
                                            </strong>
                                        </p>
    
                                        <!-- IMAGE -->
                                        @if($question->image)
                                            <img src="{{ asset('storage/app/public/questionnaire_attachment/' . $question->image) }}" style="max-width:200px;" class="mb-2">
                                        @endif
    
                                        @switch($question->category_type)
                                            @case(0)
                                                <!-- MULTIPLE CHOICE -->
                                                    @if(!empty($item['choices']))
                                                    @foreach($item['choices'] as $choice)
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                type="radio"
                                                                name="answers[{{ $question->id }}]"
                                                                value="{{ $choice }}">
    
                                                            <label class="form-check-label">
                                                                {{ $choice }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @break
                                            @case(1)
                                                <!-- IDENTIFICATION -->
                                                @if(empty($item['choices']) && $question->type == 'Identification')
                                                <input type="text"
                                                    name="answers[{{ $question->id }}]"
                                                    class="form-control"
                                                    placeholder="Enter your answer">
                                                @endif
    
                                                <!-- ESSAY -->
                                                @if($question->type == 'Essay')
                                                    <textarea name="answers[{{ $question->id }}]"
                                                            class="form-control"
                                                            rows="4"
                                                            placeholder="Write your answer here..."></textarea>
                                                @endif
                                                @break
                                            @case(2)
                                                <!-- GRID / TABLE TYPE -->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered text-center">
                                                        
                                                        <!-- TABLE HEADER (COLUMNS) -->
                                                        <thead>
                                                            <tr>
                                                                <th>Process</th>
                                                                @foreach($items[0]['choices'] as $col)
                                                                    <th>{{ $col }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
    
                                                        <!-- TABLE BODY (ROWS) -->
                                                        <tbody>
                                                            @foreach($items as $rowIndex => $row)
                                                                <tr>
                                                                    <!-- Question (ROW NAME) -->
                                                                    <td class="text-start">
                                                                        {{ $row['question'] }}
                                                                    </td>
    
                                                                    <!-- Choices per column -->
                                                                    @foreach($row['choices'] as $choice)
                                                                        <td>
                                                                            <input type="radio"
                                                                                name="answers[{{ $question->id }}][{{ $rowIndex }}]"
                                                                                value="{{ $choice }}">
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
    
                                                    </table>
                                                </div>
    
                                                @break
                                            @default
                                                
                                        @endswitch
    
                                    </div>
                                </div>
                            @endforeach
    
                        @empty
                            <div class="alert alert-warning">No questions available.</div>
                        @endforelse
    
                    </div>
    
                    <!-- ✅ Submit OUTSIDE scroll -->
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-success">Submit Exam</button>
                    </div>
    
                </form>
            </div>

        </div>
    </section>
</div>
@endsection


@section('js_content')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2bs5').select2({ theme: 'bootstrap-5' });

            GetExamTrainingRequestControlNo($('.get-training_request-ctrl_no'));

            $('#btnNext').click(function (e) { 
                e.preventDefault();
                console.log('object');
            });
        });
    </script>
@endsection