@php $layout = 'layouts.super_user_layout'; @endphp

@extends($layout)
@section('title', 'Examinations')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>List of Examinations</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">List of Examinations</li>
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
                                <h3 class="card-title">Examinations Module</h3>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                <div style="float: right;">
                                    <button class="btn btn-dark" id="btnShowAddExaminationModal">
                                        <i class="fa fa-plus"></i> Add Examination
                                    </button>
                                </div> <br><br>
                                <div class="table-responsive">
                                    <table id="tblExaminations" class="table table-bordered table-striped table-hover"
                                        style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Action</th>
                                                <th style="width: 20%;">Examination Name</th>
                                                <th style="width: 60%;">Objective</th>
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
    <div class="modal fade" id="modalAddExaminations">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit Examination Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formExaminations" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="hidden" id="txtExaminationId" name="id">

                                <div class="form-group">
                                    <label>Examination Name</label>
                                    <input type="text" class="form-control" name="exam_name" id="txtExaminationName" placeholder="Enter Exam Name">
                                </div>

                                <div class="form-group">
                                    <label>Objective</label>
                                    <textarea class="form-control" name="objective" id="txtObjective" placeholder="Enter Objective"></textarea>
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
    {{-- <script type="text/javascript"> --}}
        <script src="{{ asset('public/js/my_js/HrMemoExamination.js') }}?<?=time()?>"></script>
    {{-- </script> --}}
@endsection

