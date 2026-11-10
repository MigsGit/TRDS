@php $layout = 'layouts.super_user_layout'; @endphp

@extends($layout)
@section('title', 'Rating Description Settings')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Rating Description Settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Rating Description</li>
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
                                <h3 class="card-title">Rating Description Settings Module</h3>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                <div style="float: right;">
                                    <button class="btn btn-dark" id="btnAddRatingDescription">
                                        <i class="fa fa-plus"></i> Add Rating Description
                                    </button>
                                </div> <br><br>
                                <div class="table-responsive">
                                    <table id="tblRatingDescription" class="table table-bordered table-striped table-hover"
                                        style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" style="width: 10%;">Action</th>
                                                <th rowspan="2" style="width: 5%;">Status</th>
                                                <th rowspan="2" style="width: 20%;">Position</th>
                                                <th rowspan="2" style="width: 20%;">Designation</th>
                                                <th colspan="2" class="text-center">Rating Description</th>
                                            </tr>
                                            <tr>
                                                <th style="width: 10%;" class="text-center">Level</th>
                                                <th style="width: 35%;" class="text-center">Description</th>
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
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit Rating Description Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formRatingDescription" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="ratingDescriptionId">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Position</label>
                                    <select class="form-control select2bs5" name="position" id="position" required>
                                        <option value="" selected disabled>Select Position</option>
                                        <option value="Operator">Operator</option>
                                        <option value="VisualOperator" >Visual Operator</option>
                                        <option value="PartsPrep" >Parts Prep / Assembly / Packing</option>
                                        <option value="MachineOperator">Machine Operator</option>
                                        <option value="Inspector">Inspector</option>
                                        <option value="Technician">Technician</option>
                                        <option value="Supervisor">Supervisor</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Designation</label>
                                    <select class="form-control selectDesignation select2bs5" name="designation[]" id="designationSelect" multiple required></select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- {{-- Multiple Data for Rating Description --}} -->
                        <div class="row">
                            <div class="col">
                                <div class="table-responsive">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" id="btnAddRatingDescriptionDetails" class="btn btn-primary"><i class="fa fa-plus"></i> Add Rating Details</button>
                                    </div>
                                    <br>
                                    <table class="table table-sm" id="tblRatingDescriptionDetails">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Action</th>
                                                <th style="width: 15%;">Level</th>
                                                <th style="width: 75%;">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- {{-- Multiple Data for Rating Description --}} -->
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="btnSaveRatingDescription" class="btn btn-dark"><i class="fa fa-check"></i> Save</button>
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
    <script src="{{ asset('public/js/my_js/RatingDescriptionSettings.js') }}?<?=time()?>"></script>
    <link href="{{ asset('public/css/rating_description_style.css') }}" rel="stylesheet">
@endsection

