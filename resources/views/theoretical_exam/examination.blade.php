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

            <div class="d-none" id="stepTwo"> 
                <form action="" method="POST">
                    @csrf
                    <div class="exam-scroll-container">
                        @forelse($questions as $question)
                            @php
                                $items = json_decode($question->answer_choices_question, true);
                            @endphp

                            <div class="card mb-3">
                                <div class="card-body">

                                    <p>
                                        <strong>{{ $loop->iteration }}. 
                                        @if($question->category_type == 2)
                                            {{ $question->question_text ?? 'Grid Question' }}
                                        @else
                                            {{ $items[0]['question'] ?? '' }}
                                        @endif
                                        </strong>
                                    </p>

                                    @if($question->image)
                                        <!-- Image Preview -->
                                        <div class="text-center mb-2">
                                            <img 
                                                src="{{ asset('storage/app/public/questionnaire_attachment/' . $question->image) }}" 
                                                style="max-width:200px; cursor:pointer;"
                                                data-toggle="modal" 
                                                data-target="#imageModal"
                                                class="previewImage"
                                            >
                                        </div>
                                    @endif

                                    @switch($question->category_type)
                                        @case(0)
                                            <!-- MULTIPLE CHOICE -->
                                            @foreach($items as $item)
                                                @foreach($item['choices'] as $choice)
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                            type="radio"
                                                            name="answers[{{ $question->id }}]"
                                                            value="{{ $choice }}">
                                                        <label class="form-check-label">{{ $choice }}</label>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                            @break

                                        @case(1)
                                            <!-- IDENTIFICATION / ESSAY -->
                                            @foreach($items as $item)
                                                @if(empty($item['choices']) && $question->type == 'Identification')
                                                    <input type="text"
                                                        name="answers[{{ $question->id }}]"
                                                        class="form-control"
                                                        placeholder="Enter your answer">
                                                @endif

                                                @if($question->type == 'Essay')
                                                    <textarea name="answers[{{ $question->id }}]"
                                                            class="form-control"
                                                            rows="4"
                                                            placeholder="Write your answer here..."></textarea>
                                                @endif
                                            @endforeach
                                            @break

                                        @case(2)
                                            <!-- GRID / TABLE TYPE -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>Process</th>
                                                            @foreach($items[0]['choices'] as $col)
                                                                <th>{{ $col }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($items as $rowIndex => $row)
                                                            <tr>
                                                                <td class="text-start">{{ $row['question'] }}</td>
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
                                    @endswitch

                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning">No questions available.</div>
                        @endforelse
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <button type="button" id="btnPrev" class="btn btn-secondary">Previous</button>
                        <button type="submit" class="btn btn-success">Submit Exam</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <img src="" id="modalImage" style="width: 100%; max-height: 80vh; object-fit: contain;">
                    </div>
                </div>
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

            $('#slctExamTrainingRequestCtrlNo').change(function (e) { 
                e.preventDefault();
                const selectedControlNo = $(this).val();
                console.log('Selected Control No:', selectedControlNo);

                $('#slctExamTrainingRequestEmployeeNo').prop('disabled', false);
                GetExamTrainingRequestEmployeeNo($('.get-training_request-employee_no'), selectedControlNo);
            });

            $('#btnNext').click(function (e) { 
                e.preventDefault();
                $('#stepOne').addClass('d-none');
                $('#stepTwo').removeClass('d-none');
            });

            $('#btnPrev').click(function (e) { 
                e.preventDefault();
                $('#stepTwo').addClass('d-none');
                $('#stepOne').removeClass('d-none');
            });

            $(document).on('click', '.previewImage', function() {
                $('#modalImage').attr('src', $(this).attr('src'));
            });
        });
    </script>
@endsection