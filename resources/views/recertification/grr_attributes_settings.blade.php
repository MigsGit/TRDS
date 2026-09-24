@php $layout = 'layouts.super_user_layout'; @endphp

@extends($layout)
@section('title', 'GRR Attributes Settings')
@section('content_page')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>GRR Attributes Settings</h1>
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
                                <h3 class="card-title">GRR Attributes Settings Module</h3>
                            </div>

                            
                            {{-- <ul class="nav nav-tabs" id="employeeTab" role="tablist">

                                <li class="nav-item"> 
                                    <a class="nav-link active" id="grrSample-tab" data-toggle="tab" href="#grrSample" role="tab" aria-controls="grrSample" aria-selected="true">
                                        GRR Sample Settings
                                    </a>
                                </li>

                                <li class="nav-item"> 
                                    <a class="nav-link" id="grrQuestionnaire-tab" data-toggle="tab" href="#grrQuestionnaire" role="tab" aria-controls="grrQuestionnaire" aria-selected="false">
                                        GRR Questionnaire Settings
                                    </a>
                                </li>

                            </ul> --}}

                            <div class="tab-content mt-3">

                                <!-- GRR Sample Settings -->
                                <div class="tab-pane fade show active" id="grrSample" role="tabpanel" aria-labelledby="grrSample-tab">

                                    <div class="mr-2" style="float: right;">
                                        <button type="button" class="btn btn-dark" id="btnShowAddGRRSamplesModal">
                                            <i class="fa fa-plus"></i> Add GRR Samples
                                        </button>
                                    </div>

                                    <br><br>

                                    <div class="table-responsive">
                                        <table id="tblGRRSamples" class="table table-bordered table-striped table-hover" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Action</th>
                                                    <th class="text-center">Section</th>
                                                    <th class="text-center">GRR No.</th>
                                                    <th class="text-center">GRR Sample</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                </div>

                                <!-- GRR Questionnaire Settings -->
                                {{-- <div class="tab-pane fade" id="grrQuestionnaire" role="tabpanel" aria-labelledby="grrQuestionnaire-tab">
                                    
                                    <div class="mr-2" style="float: right;">
                                        <button type="button" class="btn btn-dark" id="btnShowAddGRRQuestionnaireModal">
                                            <i class="fa fa-plus"></i> Add GRR Questionnaire
                                        </button>
                                    </div>

                                    <br><br>
                                    <div class="table-responsive">
                                        <table id="tblGrrQuestionnaire" class="table table-bordered table-striped table-hover" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Action</th>
                                                    <th class="text-center">Section</th>
                                                    <th class="text-center">GRR No.</th>
                                                    <th class="text-center">GRR Sample</th>
                                                    <th class="text-center">Reference</th>
                                                    <th class="text-center">Defect</th>
                                                    <th class="text-center">Location</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                </div> --}}

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
    <div class="modal fade" id="modalAddGRRSamples">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit GRR Samples Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formGRRSamples" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="hidden" name="id" id="txtGRRSamplesId">

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
                                    <input type="text" class="form-control" name="grr_no" id="grrNo" placeholder="Enter GRR No." required>
                                </div>

                                 <div class="form-group">
                                    <input type="text" class="form-control" name="grr_sample" id="grrSampleId" placeholder="Enter GRR Sample" required>
                                </div>


                                {{-- <div class="form-group">
                                    <label>Skill Category</label> 
                                    <!-- <input type="text" class="form-control" name="process_station" id="processStation" placeholder="Enter Process Station" required> -->
                                
                                    <select class="form-control select2bs5" name="skill_category" id="skillCategory" required>
                                        <option value="" disabled selected>Select Skill Category</option>
                                        <option value="PROCESS / SYSTEM SKILLS">Process / System Skills</option>
                                        <option value="MACHINE OPERATION SKILLS">Machine Operation Skills</option>
                                        <option value="QC & CORE TOOLS">QC & Core tools</option>
                                    </select>
                                </div> --}}


                                {{-- <div class="form-group">
                                    <label>Product Line (Select In-order)</label>
                                    <!-- <input type="text" class="form-control" name="product_line" id="productLine" placeholder="Enter Product Line" required> -->

                                    <select class="form-control select2bs5" name="product_line[]" id="productLine" multiple required>
                                        <option value="" disabled selected>Select One</option>
                                        <option value="N/A" disabled>Not Applicable</option>
                                        <option value="Appearance">Appearance</option>
                                        <option value="Dimension">Dimension</option>
                                        <option value="BGA-FP">BGA-FP</option>
                                        <option value="BGA-LGA">BGA-LGA</option>
                                        <option value="QFP-TSOP">QFP-TSOP</option>
                                        <option value="Dimension (COC)">Dimension (COC)</option>
                                        <option value="Packing">Packing</option>
                                        <option value="Holding Force">Holding Force</option>
                                        <option value="Contact Force">Contact Force</option>
                                        <option value="Actuation Force">Actuation Force</option>
                                        <option value="Probe Pin">Probe Pin</option>
                                        <option value="Card Connectors">Card Connectors</option>
                                        <option value="TC/DC Connectors">TC/DC Connectors</option>
                                        <option value="Flexicon Connectors">Flexicon Connectors</option>
                                        <option value="Mounting Socket Connectors">Mounting Socket Connectors</option>
                                        <option value="Battery Connectors">Battery Connectors</option>
                                        <option value="Molding Connectors">Molding Connectors</option>
                                        <option value="Stamping">Stamping</option>
                                        <option value="Burn-In Memory Socket">Burn-In Memory Socket</option>
                                        <option value="Burn-In Other Socket">Burn-In Other Socket</option>
                                        <option value="Burn-In Other Socket (Probe)">Burn-In Other Socket (Probe)</option>
                                        <option value="Connector Type">Connector Type</option>
                                        <option value="Adapter Type">Adapter Type</option>
                                        <option value="Straight Type">Straight Type</option>
                                        <option value="Stiffener Type">Stiffener Type</option>
                                        <option value="Tape Type">Tape Type</option>
                                    </select>

                                    <label>
                                        <input type="checkbox" id="naProductLineCheckbox" name="product_line" value="N/A"> Not Applicable
                                    </label>
                                </div> --}}
                                
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


     <div class="modal fade" id="modalViewQuestionnaire">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit GRR Questionnaire Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- <form method="post" id="formGRRQuestionnaire" autocomplete="off"> --}}
                    {{-- @csrf --}}
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>GRR No</label>
                                    <input type="text" class="form-control" name="view_grr_sample" id="viewGrrSample" readonly>
                                    <input type="hidden" class="form-control" name="view_grr_sample_id" id="viewGrrSampleId">
                                    {{-- <select class="form-control select2bs5" name="grr_sample_id" id="grrQuestionnaireSampleId" required>
                                        <option value="" disabled selected>Select One</option>
                                    </select> --}}
                                </div>
                            </div>

                           <div class="col-sm-12">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fa fa-list-alt"></i> GRR Questionnaire Settings
                                    </h5>

                                    <button type="button" class="btn btn-dark" id="btnShowAddGRRQuestionnaireModal">
                                        <i class="fa fa-plus"></i> Add GRR Questionnaire
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table id="tblGrrQuestionnaire"
                                        class="table table-bordered table-striped table-hover"
                                        style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Action</th>
                                                <th class="text-center">Reference</th>
                                                <th class="text-center">Defect</th>
                                                <th class="text-center">Location</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                {{-- </form> --}}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modalAddQuestionnaire">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit GRR Questionnaire Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formGRRQuestionnaire" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" class="form-control" name="q_sample_id" id="qSampleId">
                            <div class="col-sm-12">
                                <input type="hidden" class="form-control" name="questionnaire_id" id="questionnaireId">
                                <div class="form-group">
                                    <label>Reference</label>
                                    <select class="form-control" name="reference" id="reference" required>
                                    <option value="" disabled selected>Select One</option>
                                    <option value="1">1</option>
                                    <option value="0">0</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Defect</label>
                                    <input type="text" class="form-control" name="defect" id="defect" required>
                                </div>

                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" class="form-control" name="location" id="location" required>
                                </div>
                            </div>
                            
                            
                        

                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="btnSaveQuestionnaire" class="btn btn-dark"><i
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


