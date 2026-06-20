@php 
    $layout = 'layouts.super_user_layout';
    $session = session('global_user');
    $exploded_u_access = explode(',', $session->user_modules_id);

@endphp
@extends($layout)
@section('title', 'Training Endorsement')

@section('content_page')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Training Endorsement</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Training Endorsement</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h3 class="card-title">Training Endorsement Module</h3>
                            </div>

                            <div class="card-body">
                                {{-- <div class="d-flex justify-content-between align-items-center mb-3"> --}}
                                <div class="d-flex {{ in_array('17', $exploded_u_access) ? 'justify-content-between' : 'justify-content-end' }} align-items-center mb-3">
                                    @if (in_array('17', $exploded_u_access))
                                        <select id="endorsementStatus" class="form-control form-control-sm" style="width: 200px;">
                                            <option value="" selected disabled>-- SELECT --</option>
                                            <option value="0">Pending</option>
                                            <option value="1">Endorsement Checker</option>
                                            <option value="2">Endorsement Approver</option>
                                            <option value="3">Approved</option>
                                        </select>
                                    @endif
                                    
                                    <button class="btn btn-primary btn-sm ms-auto" id="btnShowModalAddEndorsement">
                                        <i class="fa fa-plus fa-md me-2"></i> Add Endorsement
                                    </button>
                                </div>
                                {{-- <div class="float-sm-right mb-3">
                                    <button class="btn btn-primary btn-sm" id="btnShowModalAddEndorsement">
                                        <i class="fa fa-plus fa-md me-2"></i> Add Endorsement
                                    </button>
                                </div> --}}

                                <div class="table-responsive">
                                    <table id="tblTrainingEndorsement" class="table table-bordered table-striped table-hover table-sm" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Action</th>
                                                <th style="width: 15%;" class="text-center">Status</th>
                                                <th style="width: 20%;" class="text-center">Endorsement Ctrl #</th>
                                                <th style="width: 20%;" class="text-center">HR Memo</th>
                                                <th style="width: 20%;" class="text-center">Training Request Ctrl #</th>
                                                <th style="width: 15%;" class="text-center">Date Created</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Endorsement Modal -->
    <div class="modal fade" id="modalAddEndorsement" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-info-circle"></i> Training Endorsement Form</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="formAddEndorsement" autocomplete="off">
                    @csrf
                    <div class="modal-body">

                        <input type="hidden" name="endorsement_id" id="endorsementId" value="">
                        <input type="hidden" name="hr_memo_id" id="hrMemoId" value="">
                        <input type="hidden" name="training_req_id" id="trainingReqId" value="">

                        <!-- Top inputs -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Document Number</span>
                                    </div>
                                    <input type="text" class="form-control" name="document_no" id="documentNo" placeholder="Auto Generated" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Training Req Ctrl #</span>
                                    </div>
                                    <input type="text" class="form-control" name="training_req_ctrl" id="trainingReqCtrl" list="trainingReqCtrlList" required>
                                    <datalist id="trainingReqCtrlList"></datalist>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">HR Memo Ctrl #</span>
                                    </div>
                                    <input type="text" class="form-control" name="hr_memo_ctrl" id="hrMemoCtrl" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Date</span>
                                    </div>
                                    <input type="text" class="form-control" name="endorsement_date" value="<?= date('Y-m-d') ?>" id="endorsementDate" readonly>
                                </div>
                            </div>
                           
                        </div>

                        <!-- Employee DataTable -->
                        <div class="row mt-2">
                            <div class="col">
                                <h5 class="text-info"><i class="fa fa-info-circle"></i> Employee List</h5>
                                <div class="table-responsive">
                                    <table id="tblEndorsementEmployees" class="table table-sm table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="align-middle text-center bg-secondary text-white">Action</th>
                                                <th rowspan="2" class="align-middle text-center bg-secondary text-white">Status</th>
                                                <th rowspan="2" class="align-middle text-center bg-secondary text-white">Date Hired</th>
                                                <th rowspan="2" class="align-middle text-center bg-secondary text-white">Emp #</th>
                                                <th rowspan="2" class="align-middle text-center bg-secondary text-white">Name</th>
                                                <th colspan="3" class="align-middle text-center bg-info text-white">Exam</th>
                                                <th rowspan="2" class="align-middle text-center bg-secondary text-white">Hands-On</th>
                                            </tr>
                                            <tr>
                                                <th class="align-middle text-center bg-light">Rating</th>
                                                <th class="align-middle text-center bg-light">Title</th>
                                                <th class="align-middle text-center bg-light">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom section: Prepared By, Checked By, Approved By -->
                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Prepared By</span>
                                    </div>
                                    <input type="text" class="form-control" name="prepared_by" id="preparedBy" readonly>
                                </div>
                            </div>
                             <div class="col-sm-6">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">ATTN (CC)</span>
                                    </div>
                                    <select name="attn[]" id="attn" class="form-control select2bs5" multiple required></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Checked By</span>
                                    </div>
                                    <select name="checked_by[]" id="selectCheckedBy" class="form-control select2bs5" required multiple></select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Approved By</span>
                                    </div>
                                    <select name="approved_by[]" id="selectApprovedBy" class="form-control select2bs5" required multiple></select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        
                        <div>
                            <button type="button" id="btnExportEndorsement" class="btn btn-info" hidden><i class="fa fa-file-export"></i> Export</button>
                            <button type="button" id="btnSubmitEndorsement" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hands-On Upload Modal -->
    <div class="modal fade" id="modalHandsOnUpload" data-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fa fa-upload"></i> Upload Hands-On Image</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="handsOnRowIndex" value="">
                    <div class="form-group">
                        <label for="handsOnImage">Select Image <small class="text-muted">(jpg, jpeg, png only)</small></label>
                        <input type="file" class="form-control-file" id="handsOnImage" accept="image/jpeg,image/png,image/jpg">
                    </div>
                    <div id="handsOnPreview" class="text-center mt-3" style="display:none;">
                        <img id="handsOnPreviewImg" src="" alt="Preview" style="max-width:100%; max-height:300px; border:1px solid #ccc; border-radius:4px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="btnSaveHandsOn" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddNotEndorsed" data-backdrop="static" data-formid="" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"><i class="fas fa-info-circle fa-sm"></i> modalTitle</h3>
                    <button id="close" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped table-hover w-100" id="tableForNotEndorsedEmployee">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Employee No.</th>
                                            <th>Date Hired</th>
                                            <th>Name</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    

@endsection

@section('js_content')
<script>
    var endorsementEmpList = [];
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('.select2bs5').each(function () {
            $(this).select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this).closest('.modal'),
                
            });
        });
    });

     $('#modalAddEndorsement').on('hidden.bs.modal', function () {
        $('#trainingReqCtrl').prop('disabled', false);
        $('#btnSubmitEndorsement').show();
        $('#btnExportEndorsement').prop('hidden', true);

        $('#attn').prop('disabled', false);
        $('#selectApprovedBy').prop('disabled', false);
        $('#selectCheckedBy').prop('disabled', false);


        formAddEndorsement[0].reset();
        $('#endorsementId').val('');
        endorsementEmployeeTable.clear().draw();
    });
    
    
    getCheckedByUsers();
    getApprovedByUsers();
    getAllEmail();
</script>
@endsection
