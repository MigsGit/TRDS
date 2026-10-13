@php $layout = 'layouts.super_user_layout'; @endphp

@extends($layout)
@section('title', 'Target Ratings Settings')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Target Ratings Settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Target Ratings</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-sm-12">
                        <!-- general form elements -->
                        <div class="card card-dark">
                            <div class="card-header">
                                <h3 class="card-title">Target Ratings Settings Module</h3>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                <div style="float: right;">
                                    <button class="btn btn-dark" id="btnAddTargetRating">
                                        <i class="fa fa-plus"></i> Add Target Rating
                                    </button>
                                </div> <br><br>
                                <div class="table-responsive">
                                    <table id="tblTargetRatings" class="table table-bordered table-striped table-hover"
                                        style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" style="width: 10%;">Action</th>
                                                <th rowspan="2" style="width: 5%;">Status</th>
                                                <th rowspan="2" style="width: 10%;">Section</th>
                                                <th rowspan="2" style="width: 10%;">Position</th>
                                                <th rowspan="2" style="width: 15%;">Product Line</th>
                                                <th colspan="2" class="text-center">Target Ratings</th>
                                            </tr>
                                            <tr>
                                                <th style="width: 40%;" class="text-center">Functional Competencies</th>
                                                <th style="width: 10%;" class="text-center">Rating</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <!-- !-- End Page Content -->

                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- MODALS -->
    <div class="modal fade" id="modalAddTargetRating">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit Target Rating Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formTargetRating" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="targetRatingId">
                        <div class="row">
                            <!-- <div class="col-sm-12"> -->
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Section</label>
                                        <select class="form-control select2bs5" name="section" id="section" required>
                                            <option value="" disabled selected>Select Section</option>
                                            <option value="TS-F1">TS-F1</option>
                                            <option value="TS-F3">TS-F3</option>
                                            <option value="CN">CN</option>
                                            <option value="CN-F3">CN-F3</option>
                                            <option value="PPD-CN">PPD-CN</option>
                                            <option value="PPD-TS">PPD-TS</option>
                                            <option value="PPD-F3">PPD-F3</option>
                                            <option value="YF">YF</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Position</label>
                                        <select class="form-control select2bs5" name="position" id="position" required>
                                            <option value="" disabled selected>Select Position</option>
                                            <option value="Operator">Operator</option>
                                            <option value="Inspector">Inspector</option>
                                            <option value="Technician">Tecnnician</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Product Line</label>
                                        <select class="form-control selectProductLine select2bs5" name="product_line" id="productLine" required></select>
                                    </div>
                                </div>
                            <!-- </div> -->
                        </div>

                        <!-- {{-- Multiple Data for Target Ratings --}} -->
                        <div class="row">
                            <div class="col">
                                <div class="table-responsive">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" id="btnAddTargetRatingDetails" class="btn btn-primary"><i class="fa fa-plus"></i> Add Rating Details</button>
                                    </div>
                                    <br>
                                    <table class="table table-sm" id="tblTargetRatingDetails">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Action</th>
                                                <th style="width: 75%;">Functional Competencies</th>
                                                <th style="width: 15%;">Rating</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- {{-- Multiple Data for Target Ratings --}} -->
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="btnSaveTargetRating" class="btn btn-dark"><i class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('js_content')
    <script src="{{ asset('public/js/my_js/TargetRatingSettings.js') }}?<?=time()?>"></script>
    <link href="{{ asset('public/css/target_rating_style.css') }}" rel="stylesheet">
@endsection

