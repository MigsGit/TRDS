@php $layout = 'layouts.super_user_layout'; @endphp

{{-- Here I removed the @auth because the dashboard isn't loading properly --}}
@extends($layout)
@section('title', 'Training Record Update')

@section('content_page')
<style>
    .section-divider {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1.25rem;
    }
    .section-divider::after {
        content: "";
        flex-grow: 1;
        background: #e9ecef;
        height: 1px;
        margin-left: 0.75rem;
    }
    form-control:focus {
        border-color: #0056b3;
        box-shadow: 0 0 0 0.2rem rgba(0, 86, 179, 0.15);
    }
    /* Override Select2 inline width and force proper Bootstrap input-group flex behavior */
    .input-group > .select2-container {
        flex: 1 1 auto !important;
        width: 1% !important;
    }
    .input-group > .select2-container .select2-selection--multiple {
        min-height: calc(2.25rem + 2px) !important;
        height: auto !important;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

</style>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>TRDSv2</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('blank') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Training Record Update</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Training Records</h3>
                                <button class="btn btn-primary btn-sm ms-auto" id="btnAddTraining">
                                    <i class="fa fa-plus fa-md me-2"></i> Add Trainings
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover w-100" id="tableTrainingRecords">
                                    {{-- <thead>
                                        <tr>
                                            <th>Training Name</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody> --}}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalTrainingRecord" data-backdrop="static" data-formid="" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-info-circle fa-sm"></i> <span id="modalTrainingRecordTitle">modalTitle</span></h3>
                <button id="close" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="trainingForm" autocomplete="off">
                    @csrf
                    <input type="hidden" id="editingRecordId" name="editing_record_id" value="">

                    <!-- Section 1: Dates & Core Info -->
                    <div class="section-divider">
                        <i class="far fa-calendar-alt text-primary mr-2"></i> Date Range & Details
                    </div>

                    <div class="form-row">
                        <!-- Date Range Group -->
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold small text-muted">
                                TRAINING START DATE <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-check"></i></span>
                                </div>
                                <input type="date" class="form-control" id="startDate" name="start_date" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold small text-muted">
                                TRAINING END DATE <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-minus"></i></span>
                                </div>
                                <input type="date" class="form-control" id="endDate" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Training Title / Reason -->
                        <div class="form-group col-md-6">
                            <label for="trainingTitle" class="font-weight-bold small text-muted">
                                TRAINING TITLE / REASON FOR CERTIFICATION <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="trainingTitle" name="training_title" list="trainingPresets" placeholder="e.g. ESD Level 2 Safe Handling Certification" required>
                            {{-- <datalist id="trainingPresets">
                                <option value="IPC-A-610 Acceptability of Electronic Assemblies">
                                <option value="ESD Prevention & Control Certification">
                                <option value="ISO 9001:2015 Quality Management System">
                                <option value="Safety & Hazard Operator Training">
                                <option value="SMT Machine Operation Standard">
                            </datalist> --}}
                        </div>
                         <div class="form-group col-md-6">
                            <label for="objective" class="font-weight-bold small text-muted">OBJECTIVE</label>
                            <input type="text" class="form-control" id="objective" name="objective" placeholder="Main learning target or expected outcome...">
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks" class="font-weight-bold small text-muted">REMARKS / ADDITIONAL NOTES</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Additional details, score summary, or retake conditions..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Attachments -->
                            <div class="form-group">
                                <label for="attachments" class="font-weight-bold small text-muted">ATTACHMENTS</label>

                                <div id="attachments_current_wrapper" class="d-none mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                                        </div>
                                        {{-- <input type="text" class="form-control" id="attachments_text" readonly> --}}
                                        <textarea class="form-control" id="attachments_text" readonly></textarea>
                                    </div>
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input type="checkbox" class="custom-control-input" id="attachments_checkbox_reupload" name="attachments_checkbox_reupload" value="1">
                                        <label class="custom-control-label small" for="attachments_checkbox_reupload">Reupload / replace current attachment(s)</label>
                                    </div>
                                </div>

                                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                            </div>
                        </div>
                    </div>
                    

                    <!-- Section 2: Logistics & Instruction -->
                    <div class="section-divider mt-3">
                        <i class="fas fa-user-graduate text-primary mr-2"></i> Trainer & Location
                    </div>

                    <div class="form-row">
                        <!-- Trainer -->
                        <div class="form-group col-md-4">
                            <label for="trainer" class="font-weight-bold small text-muted">
                                TRAINER <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                </div>
                                <select class="custom-select select2bs4" id="trainer" name="trainer[]" required multiple>
                                </select>
                            </div>
                        </div>

                        <!-- Venue -->
                        <div class="form-group col-md-4">
                            <label for="venue" class="font-weight-bold small text-muted">
                                VENUE <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                {{-- <input type="text" class="form-control" id="venue" name="venue" placeholder="e.g. Conference Room B / LMS" required> --}}
                                <select class="custom-select select2bs4" id="venue" name="venue" required>
                                </select>
                            </div>
                        </div>

                        <!-- Type of Training -->
                        <div class="form-group col-md-4">
                            <label for="typeOfTraining" class="font-weight-bold small text-muted">
                                TYPE OF TRAINING <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-chalkboard-teacher"></i></span>
                                </div>
                                <select class="custom-select select2bs4" id="typeOfTraining" name="type_of_training" required>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Trainees -->
                    {{-- <div class="section-divider mt-3">
                        <i class="fas fa-users text-primary mr-2"></i> Trainee
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="trainee" class="font-weight-bold small text-muted">
                                    TRAINEE <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="trainee" list="traineeList">
                                <datalist id="traineeList">
                                </datalist>
                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table id="tableTrainees" class="table table-sm table-bordered table-striped table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Employee Number</th>
                                            <th>Trainee Name</th>
                                            <th>Department</th>
                                            <th>Station</th>
                                            <th>Series</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> --}}
                    <!-- Section 3: Trainees -->
                    <div class="section-divider mt-3">
                        <i class="fas fa-users text-primary mr-2"></i> Trainees
                    </div>

                    <!-- Add Options -->
                    <div class="card card-light mb-3">
                        <div class="card-body py-3">

                            <div class="row">

                                <!-- Left -->
                                <div class="col-md-6 border-right">

                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-user-plus mr-1"></i>
                                        Add Individual Trainee
                                    </label>

                                    <small class="text-muted d-block mb-3">
                                        Search an employee then press <b>Enter</b> to add.
                                    </small>

                                    <!-- Employee Search -->
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">
                                            EMPLOYEE
                                        </label>
                                        <input type="text" class="form-control" id="trainee" list="traineeList"
                                            placeholder="Employee No. or Employee Name">

                                        <datalist id="traineeList"></datalist>
                                    </div>

                                    <!-- Station & Series -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="small font-weight-bold text-muted">
                                                STATION
                                            </label>
                                            <input type="text" class="form-control" id="station" name="station"
                                                placeholder="Enter Station">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="small font-weight-bold text-muted">
                                                SERIES
                                            </label>
                                            <input type="text" class="form-control" id="series" name="series"
                                                placeholder="Enter Series">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm w-100" id="btnAddTrainee">
                                        <i class="fas fa-plus mr-1"></i>
                                        Add Trainee
                                    </button>
                                </div>

                                <!-- Right -->
                                <div class="col-md-6">

                                    <label class="font-weight-bold text-success">
                                        <i class="fas fa-file-import mr-1"></i>
                                        Bulk Import Trainees
                                    </label>

                                    <small class="text-muted d-block mb-2">
                                        Upload an Excel or CSV file containing employee numbers.
                                    </small>

                                    <div class="custom-file">
                                        <input type="file" id="importTrainees"
                                            accept=".xlsx,.xls,.csv">
                                    </div>

                                    <div class="mt-2">

                                        <button type="button" class="btn btn-success btn-sm" id="btnImportTrainees">

                                            <i class="fas fa-upload mr-1"></i>
                                            Import Trainees

                                        </button>

                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            id="btnDownloadTemplate">

                                            <i class="fas fa-download mr-1"></i>
                                            Download Template

                                        </button>

                                    </div>

                                    <small class="text-muted d-block mt-2">
                                        Accepted formats:
                                        <strong>.xlsx</strong>,
                                        <strong>.xls</strong>,
                                        <strong>.csv</strong>
                                    </small>

                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <div>
                            <span class="badge badge-primary px-3 py-2">
                                Total Trainees:
                                <span id="totalTrainees">0</span>
                            </span>
                        </div>

                        <div>

                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearTrainees">

                                <i class="fas fa-trash"></i>
                                Clear All

                            </button>

                        </div>

                    </div>

                    <!-- Trainee Table -->
                    <div class="table-responsive">

                        <table id="tableTrainees" class="table table-sm table-bordered table-hover table-striped w-100">

                            <thead class="thead-light">
                                <tr>
                                    <th width="70">Action</th>
                                    <th>Employee Number</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Station</th>
                                    <th>Series</th>
                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>

                    <button type="button" class="btn btn-primary font-weight-bold px-4 btn-sm" id="btnSaveTraining">
                        <i class="fas fa-paper-plane mr-1"></i> Save Training Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js_content')
    <script type="text/javascript">
        let dtTrainingRecords;
        let dtTrainees;
        let traineeArray = [];
        let countTotalTrainees = 0;

        getTypeofTraining($('#typeOfTraining'));
        getVenue($('#venue'));
        getEmployees($('#trainer'));
        getEmployees($('#traineeList'));

        $(document).ready(function() {
            // $(this).find('.select2bs51').each(function () {
            //     $(this).select2({
            //         theme: 'bootstrap-4',
            //         dropdownParent: $(this).closest('.modal'),

            //     });
            // });
            dtTrainingRecords = $("#tableTrainingRecords").DataTable({
                "processing" : true,
                "serverSide" : true,
                "ajax" : {
                    url: "dt_get_training_records",
                },
                fixedHeader: true,
                "columns":[
                    { "data" : "action","title": "Action", orderable:false, searchable:false },
                    { "data" : "id", "title": "Date", 
                        render: function(data, type, row) {
                            return row.start_date + ' - ' + row.end_date;
                        }
                    },
                    { "data" : "training_title", "title": "Title" },
                    { "data" : "objective", "title": "Objective" },
                    { "data" : "trainer", "title": "Trainer",
                        render: function(data, type, row) {
                            // return row.trainer_details ? row.trainer_details.EmpNo + ' - ' + row.trainer_details.FirstName + ' ' + row.trainer_details.LastName : '';
                            let result = "";
                            // Loop through each trainer and append their details to the result
                            if (row.trainer_details && row.trainer_details.length > 0) {
                                row.trainer_details.forEach(function(trainer) {
                                    result += `<div>${trainer.FirstName} ${trainer.LastName}</div>`;
                                    result += ` <div style="font-size:.9rem;color:#adb5bd">
                                                    <span>${trainer.EmpNo}</span>
                                                </div>`;
                                });
                            }
                            return result;
                        }
                    },
                    { "data" : "venue", "title": "Venue",
                        render: function(data, type, row) {
                            return row.venue_details ? row.venue_details.dropdown_masters_details : '';
                        }
                    },
                ],
                "columnDefs": [
                    {"className": "dt-center", "targets": "_all"},
                    
                ],
            });//end of dataTable

            dtTrainees = $('#tableTrainees').DataTable({
                // rowId: 'id', // optional
                "ordering": false,
                "searching": true,
                "paging": false,
                "info" : false,
                "columns": [
                    { "data": "action" },
                    { "data": "empNo" },
                    { "data": "empName" },
                    { "data": "empDept" },
                    { "data": "qcSlipStation" },
                    { "data": "qcSlipSeries" },
                ],
            });
            

            $('#btnAddTraining').on('click', function() {
                $('#modalTrainingRecordTitle').text('Add Training Record');
                $('#modalTrainingRecord').modal('show');
            });

            $('#modalTrainingRecord').on('hidden.bs.modal', function () {
                $('#modalTrainingRecordTitle').text('');
                $('#editingRecordId').val('');
                $('#trainingForm')[0].reset();
                $('#typeOfTraining').val('').trigger('change');
                $('#venue').val('').trigger('change');
                $('#trainer').val('').trigger('change');
                traineeArray = [];
                dtTrainees.clear().draw();

                // reset attachments field back to "add" state
                $('#attachments_current_wrapper').addClass('d-none');
                $('#attachments_text').val('');
                $('#attachments_checkbox_reupload').prop('checked', false);
                $('#attachments').removeClass('d-none').val('');

                $('#trainingForm').find('input, select, textarea, button').prop('disabled', false);

                $('#btnSaveTraining').show();

            });

            $('#btnSaveTraining').on('click', function() {
                $('#trainingForm').submit();
            });

            $('#trainingForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission
                // let data = $(this).serialize();
                // traineeArray.forEach(item => {
                //     data += "&trainee[]=" + JSON.stringify(item);
                // });
                let data = new FormData(this);
                traineeArray.forEach(item => {
                    data.append("trainee[]", JSON.stringify(item));
                });
                
                saveTrainingRecord(data);
            });

            // $('#trainee').on('keyup', function(e) {
            //     e.preventDefault();
            //     if(e.keyCode === 13){
            //         getTraineeDetails($(this).val());
            //     }
            //     // Add your change event logic here
            // });

            $('#btnAddTrainee').on('click', function() {
                let traineeId = $('#trainee').val();
                let station = $('#station').val();
                let series = $('#series').val();
                getTraineeDetails(traineeId, station, series);
            });

            $('#btnClearTrainees').on('click', function() {
                traineeArray = [];
                dtTrainees.clear().draw();
                countTotalTrainees = traineeArray.length;
                $('#totalTrainees').text(countTotalTrainees);
            });

            $('#btnImportTrainees').on('click', function() {
                let importFile = $('#importTrainees')[0].files[0];
                Swal.fire({
                    title: 'Are you sure you want to import trainees?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceedImportTrainees(importFile);
                    }
                });
            });

            $('#btnDownloadTemplate').on('click', function() {
                window.location.href = '{{ asset("public/excel_template/tru_template.xlsx") }}'; // Replace with the actual path to your template file
            });
        });

        $(document).on('click', '.btnEditTraining', function(){
            let trainingRecordId = $(this).data('id');
            getTrainingRecord(trainingRecordId);
        });

        $(document).on('click', '.btnRemoveTrainee', function(){
            let row = $(this).closest('tr');

            let rowIndex = dtTrainees.row(row).index();
            if(rowIndex !== undefined){
                traineeArray.splice(rowIndex, 1);

                dtTrainees.clear().rows.add(traineeArray).draw();
            }
            countTotalTrainees = traineeArray.length;
            $('#totalTrainees').text(countTotalTrainees);

        })

        $(document).on('change', '#attachments_checkbox_reupload', function(){
            if($(this).is(':checked')){
                $('#attachments').removeClass('d-none');
            } else {
                $('#attachments').addClass('d-none').val('');
            }
        });

        $(document).on('click', '.btnViewTraining', function(){
            let trainingRecordId = $(this).data('id');
            $('#modalTrainingRecordTitle').text('View Training Record');
            getTrainingRecord(trainingRecordId);
            $('#trainingForm').find('input, select, textarea, button')
            .not('.dataTables_filter input, .dt-search input')
            .prop('disabled', true);
            $('#btnSaveTraining').hide();
        });

        $(document).on('click', '.btnDeleteTraining', function(){
            let trainingRecordId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteTrainingRecord(trainingRecordId);
                }
            });
        });
    </script>
@endsection
