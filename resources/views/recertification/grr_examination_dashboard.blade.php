@php $layout = 'layouts.super_user_layout'; @endphp
@extends($layout)
@section('title', 'GRR Attributes Examination')

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
        border: 1px solid transparent;
    }

    .exam-card:hover {
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        border: 1px solid #3f2f5c;
    }

    .exam-card.is-clickable {
        cursor: pointer;
    }

    .exam-card.is-selected {
        border: 1px solid #3f2f5c;
        box-shadow: 0 0 0 2px #3f2f5c;
    }

    .exam-purpose {
        text-align: center;
        font-size: 0.95rem;
        color: #444;
        margin-bottom: 15px;
        line-height: 1.5;
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

    .exam-meta {
        font-size: 0.85rem;
        color: #777;
        margin-bottom: 15px;
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
                    <h1>GRR Attributes Examination</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('blank') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">GRR Attributes Examination</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid px-2">

            <!-- SELECT DROPDOWNS -->
            <form id="formGrrDashboardFilter" onsubmit="return false;" autocomplete="off">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <select class="form-control select2bs5" id="grrDashboardSection" name="section">
                            <option value="">-- Select Section --</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section }}">{{ $section }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <span id="grrDashboardLoading" class="text-muted" style="display: none; line-height: 38px;">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </span>
                    </div>
                </div>
            </form>

            <!-- BREADCRUMB TRAIL / BACK -->
            <div id="grrDashboardTrail" class="mb-2" style="display: none;">
                <button type="button" class="btn btn-sm btn-dark mr-2" id="btnGrrDashboardBack">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <span id="grrDashboardTrailText" class="text-muted"></span>
            </div>

            <!-- EXAM CARDS -->
            <div class="exam-wrapper">
                <div class="row" id="grrDashboardCards">
                    <div class="col-12 text-center">
                        <div class="alert alert-warning">
                            Please select a section to display.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
