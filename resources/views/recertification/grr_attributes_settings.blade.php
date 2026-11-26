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
                            <li class="breadcrumb-item active">GRR Attributes Settings</li>
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


                           <ul class="nav nav-tabs" id="employeeTab" role="tablist">

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

                            </ul>

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
                         <div class="tab-pane fade" id="grrQuestionnaire" role="tabpanel" aria-labelledby="grrQuestionnaire-tab">

                                    <div class="mr-2" style="float: right;">
                                        <button type="button" class="btn btn-dark" id="btnShowAddGRRExamGroupingsModal">
                                            <i class="fa fa-plus"></i> Add GRR Exam Grouping
                                        </button>
                                    </div>

                                    <br><br>

                                    <div class="table-responsive">
                                        <table id="tblGRRExamGroupings" class="table table-bordered table-striped table-hover" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Action</th>
                                                    <th class="text-center">Control Number</th>
                                                    <th class="text-center">GRR No. - GRR Sample</th>
                                                    <th class="text-center">Employees</th>
                                                    <th class="text-center">Description</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                        </table>
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


    <div class="modal fade" id="modalAddGRRExamGroupings">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add/Edit GRR Exam Groupings Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formGRRExamGroupings" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="hidden" name="id" id="txtGRRExamGroupingsId">

                                <div class="form-group">
                                    <label>Control Number</label>
                                    <input type="text" class="form-control" name="control_number" id="controlNumber" placeholder="Auto Generated" autocomplete="off" readonly>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
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
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>GRR no</label>
                                    <select class="form-control select2bs5" name="exam_grr_no" id="examGrrNo" required>
                                        <option value="" disabled selected>Select One</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <label>Select Employee</label>
                                <div class="form-group">
                                    <select class="form-control select2bs5" name="employee[]" id="employee" multiple="multiple" required>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" rows="3" name="exam_description" id="examDescription"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Purpose</label>
                                    <textarea class="form-control" rows="3" name="exam_purpose" id="examPurpose"></textarea>
                                </div>
                            </div>

                             <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Instruction</label>
                                    <textarea class="form-control" rows="3" name="exam_instruction" id="examInstruction"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="btnSaveGRRExamGroupings" class="btn btn-dark"><i
                                class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <div class="modal fade" id="modalViewQuestionnaire">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-list-alt"></i> GRR Questionnaire Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>GRR No</label>
                                <input type="text" class="form-control" name="view_grr_sample" id="viewGrrSample" readonly>
                                <input type="hidden" class="form-control" name="view_grr_sample_id" id="viewGrrSampleId">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0">
                                Questionnaire Items
                                <span class="badge badge-secondary ml-1" id="grrQuestionnaireCount">0</span>
                            </h5>
                            <small class="text-muted">
                                <i class="fas fa-grip-vertical"></i> Drag the handle to reorder. The new order is saved automatically.
                            </small>
                        </div>

                        <button type="button" class="btn btn-dark" id="btnShowAddGRRQuestionnaireModal">
                            <i class="fa fa-plus"></i> Add GRR Questionnaire
                        </button>
                    </div>

                    <div id="grrQuestionnaireLoading" class="text-center text-muted py-5" style="display:none;">
                        <i class="fas fa-spinner fa-spin"></i> Loading questionnaire...
                    </div>

                    <div id="grrQuestionnaireEmpty" class="grr-q-empty text-center text-muted" style="display:none;">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <div>No questionnaire items yet. Click <strong>Add GRR Questionnaire</strong> to create one.</div>
                    </div>

                    <div id="grrQuestionnaireList" class="grr-q-list"></div>

                    <div id="grrQuestionnaireInactiveWrap" style="display:none;">
                        <h6 class="text-muted mt-4 mb-2">
                            <i class="fas fa-ban"></i> Inactive
                            <small>(not numbered &mdash; enabling an item adds it back at the end of the sequence)</small>
                        </h6>
                        <div id="grrQuestionnaireInactiveList"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
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

    <style>
        .grr-q-card {
            display: flex;
            align-items: stretch;
            background: #fff;
            border: 1px solid #dee2e6;
            border-left: 4px solid #28a745;
            border-radius: .35rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
            margin-bottom: .75rem;
            transition: box-shadow .15s ease, border-color .15s ease;
        }
        .grr-q-card:hover { box-shadow: 0 3px 8px rgba(0, 0, 0, .1); }
        .grr-q-card.is-inactive { border-left-color: #dc3545; background: #f8f9fa; }
        .grr-q-card.is-inactive .grr-q-body { opacity: .6; }

        .grr-q-handle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            flex: 0 0 2.25rem;
            color: #adb5bd;
            cursor: grab;
            border-right: 1px solid #f1f3f5;
            user-select: none;
        }
        .grr-q-handle:hover { color: #343a40; background: #f1f3f5; }
        .grr-q-handle:active { cursor: grabbing; }
        .grr-q-handle-spacer {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            flex: 0 0 2.25rem;
            color: #dc3545;
            border-right: 1px solid #f1f3f5;
        }

        .grr-q-number {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 3rem;
            font-size: 1.15rem;
            font-weight: 600;
            color: #343a40;
        }

        .grr-q-body {
            display: flex;
            flex-wrap: wrap;
            flex: 1 1 auto;
            min-width: 0;
            padding: .75rem .5rem;
        }
        .grr-q-field { flex: 1 1 10rem; min-width: 0; padding: 0 .5rem; }
        .grr-q-field.grr-q-field-wide { flex: 3 1 14rem; }
        .grr-q-label {
            display: block;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #6c757d;
        }
        .grr-q-value { word-break: break-word; }

        .grr-q-actions {
            display: flex;
            align-items: center;
            padding: 0 .75rem;
            white-space: nowrap;
        }

        .grr-q-card.ui-sortable-helper {
            box-shadow: 0 10px 24px rgba(0, 0, 0, .22);
            border-color: #007bff;
            cursor: grabbing;
            opacity: .95;
        }
        .grr-q-placeholder {
            border: 2px dashed #007bff;
            border-radius: .35rem;
            background: rgba(0, 123, 255, .07);
            margin-bottom: .75rem;
            visibility: visible !important;
        }

        .grr-q-empty {
            border: 2px dashed #ced4da;
            border-radius: .35rem;
            padding: 2.5rem 1rem;
            background: #fff;
        }
        .grr-q-list.is-saving { pointer-events: none; opacity: .75; }
    </style>
@endsection

@section('js_content')
    <!-- jQuery UI (Sortable) - bundled with the template, loaded only on this page -->
    <script>var bsTooltip = $.fn.tooltip, bsButton = $.fn.button;</script>
    <script src="{{ asset('public/template/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>$.fn.tooltip = bsTooltip; $.fn.button = bsButton;</script>
@endsection


