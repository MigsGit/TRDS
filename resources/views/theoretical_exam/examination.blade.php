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
                                        <label class="font-weight-bold">Category: </label>
                                        <select class="form-control select2bs5 get-systemone-hris-department" name="questionnaire_department" id="slctQuestionnaireDepartment" required></select>
                                    </div>
        
                                    <div class="form-group">
                                        <label class="font-weight-bold">Title: </label>
                                        <textarea class="form-control" rows="2" name="questionnaire_title" id="txtQuestionnaireTitle" required></textarea>
                                    </div>
        
                                    <div class="form-group">
                                        <label class="font-weight-bold">Purpose: </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="questionnaire_purpose" id="txtQuestionnairePurpose" required>
                                        </div>
                                    </div>
        
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Position: </label>
                                        <select class="form-control select2bs5 get-systemone-hris-position" name="questionnaire_position" id="slctQuestionnairePosition" required></select>
                                    </div>
                                </div>
        
                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Passing Score: </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="questionnaire_passing_score" id="nmbrQuestionnairePassingScore" required>
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label class="font-weight-bold">Instruction: </label>
                                        <textarea class="form-control" rows="2" name="questionnaire_instruction" id="txtQuestionnaireInstruction" required></textarea>
                                    </div>
        
                                    <div class="form-group">
                                        <label class="font-weight-bold">Department: </label>
                                        <select class="form-control select2bs5 get-systemone-hris-department" name="questionnaire_department" id="slctQuestionnaireDepartment" required></select>
                                    </div>
        
                                    <div class="form-group">
                                        <label class="font-weight-bold">Product Line: </label>
                                        <select class="form-control select2bs5 get-systemone-hris-section" name="questionnaire_product_line" id="slctQuestionnaireProductLine" required></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-success">Submit Exam</button>
                        </div>
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