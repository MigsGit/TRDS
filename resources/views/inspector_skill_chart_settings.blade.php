@php $layout = 'layouts.super_user_layout'; @endphp

@extends($layout)
@section('title', 'Inspector Skill Chart Settings')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Inspector Skill Chart Settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Process / Station</li>
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
                                <h3 class="card-title">Inspector Skill Chart Settings Module</h3>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                <div style="float: right;">
                                    <button class="btn btn-dark" id="btnShowAddProcessStationModal">
                                        <i class="fa fa-plus"></i> Add Process / Station
                                    </button>
                                </div> <br><br>
                                <div class="table-responsive">
                                    <table id="tblProcessStation" class="table table-bordered table-striped table-hover"
                                        style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Action</th>
                                                <th style="width: 20%;">Section</th>
                                                <th style="width: 20%;">Process / Station</th>
                                                <th style="width: 60%;">Product Line/s</th>
                                                <th style="width: 10%;">Status</th>
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
    <div class="modal fade" id="modalAddProcessStation">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit Process / Station Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formProcessStation" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="hidden" name="id" id="txtProcessId">

                                <div class="form-group">
                                    <label>Section</label>
                                    <select class="form-control select2bs5" name="section" id="section" required>
                                        <option value="" disabled selected>Select One</option>
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

                                <div class="form-group">
                                    <label>Process / Station</label>
                                    <select class="form-control select2bs5" name="process_station" id="processStation" required>
                                        <option value="" disabled selected>Select One</option>
                                        <option value="IQC">IQC</option>
                                        <option value="IPQC">IPQC</option>
                                        <option value="OQC">OQC</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Product Line</label>
                                    <input type="text" class="form-control" name="product_line" id="productLine" placeholder="Enter Product Line" required>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="btnProcess" class="btn btn-dark"><i
                                class="fa fa-check"></i> Save</button>
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
    <script src="{{ asset('public/js/my_js/InspectorSkillChartSettings.js') }}?<?=time()?>"></script>
@endsection

