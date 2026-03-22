@php $layout = 'layouts.layout'; @endphp
@extends($layout)
@section('title', 'Theoretical Exam')

@section('content_page')
<style>
    .content-wrapper {
        min-height: 100vh;
        background-color: #f5f5f5;
        padding: 20px 0;
    }

    .exam-wrapper {
        width: 100%;
        padding: 0 20px;
        margin: auto;
        max-height: 80vh;
        overflow-y: auto;
    }

    .exam-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .exam-card:hover {
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        border: 1px solid #3f2f5c;
    }

    .exam-title {
        text-align: center;
        font-size: 1.3rem;
        font-weight: 600;
        color: #202124;
        margin-bottom: 5px;
    }

    .exam-subtitle {
        text-align: center;
        font-size: 1rem;
        color: #5f6368;
        margin-bottom: 15px;
    }

    .exam-purpose {
        text-align: center;
        font-size: 0.95rem;
        color: #444;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .exam-meta {
        font-size: 0.85rem;
        color: #777;
        margin-bottom: 15px;
    }

    .exam-footer {
        margin-top: auto;
        display: flex;
        justify-content: flex-end;
    }

    .btn-start {
        background-color: #080808;
        color: #fff;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 0.9rem;
        border: none;
        transition: all 0.25s ease;
        text-decoration: none;
    }

    .btn-start:hover {
        background-color: #564c69;
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(90,50,163,0.3);
        color: #fff;
    }

    .exam-wrapper::-webkit-scrollbar {
        width: 6px;
    }
    .exam-wrapper::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 3px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Theoretical Examination</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('blank') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"> Examination</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid px-2">
            <div class="exam-wrapper">
                <div class="row">
                    @forelse($examCategories as $category)
                        <div class="col-lg-3 col-md-4 col-sm-6 mt-4">
                            <div class="exam-card">
                                <div>
                                    <div class="exam-title">{{ $category->exam_title }}</div>
                                    <div class="exam-subtitle">For {{ $category->position }}</div>

                                    <div class="exam-purpose">
                                        {{ $category->purpose }}
                                    </div>

                                    <div class="exam-meta">
                                        <div><strong>Department:</strong> {{ $category->department }}</div>
                                        <div><strong>Product Line:</strong> {{ $category->product_line }}</div>
                                    </div>
                                </div>

                                <div class="exam-footer">
                                    <a href="{{ route('startExam', ['id' => $category->id, 'revision' => $category->revision]) }}"
                                        class="btn-start">
                                        Start Exam
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <div class="alert alert-warning">No exam categories available.</div>
                        </div>
                    @endforelse
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
    });
</script>
@endsection