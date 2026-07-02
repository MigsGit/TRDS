@php $layout = 'layouts.super_user_layout'; @endphp
@extends($layout)
@section('title', 'Personnel Skill Matrix')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Personnel Skill Matrix</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Personnel Skill Matrix</li>
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
                                <h3 class="card-title">Personnel Skill Matrix Module</h3>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                <div class="float-sm-right">
                                    <button class="btn btn-success" id="btnShowModalRequestTraining">
                                        <i class="fas fa-file-export fa-md me-2"></i> Export Report
                                    </button>
                                </div>
                                <br><br>

                                <ul class="nav nav-tabs" id="employeeTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active"id="direct-tab"data-toggle="tab" href="#directEmployees" role="tab">
                                            Direct Employees
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" id="indirect-tab" data-toggle="tab" href="#subconEmployees" role="tab">
                                            Subcon Employees
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">

                                    <!-- Direct Employees -->
                                    <div class="tab-pane fade show active" id="directEmployees" role="tabpanel">
                                        <div class="table-responsive">
                                            <table id="tblDirectEmployees"
                                                class="table table-bordered table-striped table-hover"
                                                style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th width="10%" class="text-center">Action</th>
                                                        <th width="5%" class="text-center">E.N.</th>
                                                        <th width="20%" class="text-center">Name</th>
                                                        <th width="7%" class="text-center">Date Hired</th>
                                                        <th width="20%" class="text-center">Position</th>
                                                        <th width="20%" class="text-center">Section</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>

                                    </div>

                                    <!-- Indirect Employees -->
                                    <div class="tab-pane fade" id="subconEmployees" role="tabpanel">
                                        <div class="table-responsive">
                                            <table id="tblSubconEmployees"
                                                class="table table-bordered table-striped table-hover"
                                                style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th width="10%" class="text-center">Action</th>
                                                        <th width="5%" class="text-center">E.N.</th>
                                                        <th width="20%" class="text-center">Name</th>
                                                        <th width="7%" class="text-center">Date Hired</th>
                                                        <th width="20%" class="text-center">Position</th>
                                                        <th width="20%" class="text-center">Section</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>

                                    </div>
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
@endsection

@section('js_content')
    <script type="text/javascript">

    </script>
@endsection

