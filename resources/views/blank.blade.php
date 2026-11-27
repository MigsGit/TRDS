@php $layout = 'layouts.super_user_layout'; @endphp

{{-- Here I removed the @auth because the dashboard isn't loading properly --}}
@extends($layout)
@section('title', 'Blank Page')

@section('content_page')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Training Request Database System v2.1</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('blank') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Blank Page</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h1 class="card-title">
                                    <i class="fas fa-home mr-2"></i>
                                    Welcome to TRDS v2.1
                                </h1>
                            </div>
                            <div class="card-body">
                                For concerns/issues, please contact ISS at local numbers 205, 206, or 208.
                                Or you may send us e-mail at servicerequest@pricon.ph. Thank you!
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-book mr-2"></i>
                                    TRDS v2.1 Manual
                                </h3>
                            </div>

                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-9">
                                        <h5>Training Database System v2.1</h5>
                                        <p class="mb-0">
                                            View the user manual for instructions on how to use
                                            the Training Database System v2.1
                                        </p>
                                    </div>

                                    <div class="col-md-3 text-md-right mt-3 mt-md-0">
                                        <a class="btn btn-outline-dark" href="storage/app/public/TRDS_v2.1_MANUAL/TRDS v2.1 - MANUAL.pdf" target="_blank">
                                            <i class="fas fa-eye mr-1"></i>
                                            Click to View
                                        </a>
                                    </div>
                                </div>
                            </div>
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
            bsCustomFileInput.init();
        });

    </script>
@endsection
