@php $layout = 'layouts.super_user_layout'; @endphp

@extends($layout)
@section('title', 'ETR')

@section('content_page')
    <style type="text/css">
        table.table thead th{
            text-align: center;
            vertical-align: middle;
        }

        table.table tbody td{
            vertical-align: middle;
        }

        #tableQuestionnaire thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa; /* Light header color */
            z-index: 5;
        }

        .removeQuestion {
            position: absolute;
            top: 2px;
            left: 2px;
            padding: 0 4px;
            font-size: 0.75rem;
        }

        th.position-relative {
            position: relative;
        }

        .removeOption {
            position: absolute;
            top: 2px;
            right: 2px;
            padding: 0 4px;
            font-size: 0.75rem;
        }
    </style>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>ETR</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('blank') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">ETR</li>
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
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" style="margin-top: 8px;"><strong>ETR</strong></h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" class="btn btn-dark" id="buttonCreateQuestionnaire" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaire">
                                        <i class="fa fa-plus fa-md"></i> Create New Record
                                    </button>
                                </div>
                                <div class="table-responsive" style="max-height: 80vh; overflow-y: auto;">
                                    <table id="tableQuestionnaire" class="table table-bordered table-hover w-100">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Category</th>
                                                <th>Exam Title</th>
                                                <th>Exam Instruction</th>
                                                <th>Purpose</th>
                                                <th>Department</th>
                                                <th>Position</th>
                                                <th>Product Line</th>
                                                <th>Passing Score</th>
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
@endsection

@section('js_content')
    <script type="text/javascript">
        let dataQuestionnaire

        $(document).ready(function () {
            $('.select2bs5').select2({
                theme: 'bootstrap-5'
            });

            $(document).on('hidden.bs.modal', function () {
                if ($('.modal.show').length) {
                    $('body').addClass('modal-open');
                }

                $(this).find('form').each(function () {
                    this.reset();
                });
            });

            // ===============================================================================================================================================
            // ================================================================ QUESTIONNAIRE ================================================================
            // ===============================================================================================================================================
            GetSystemOneHrisDepartment($('.get-systemone-hris-department'))
            GetSystemOneHrisPosition($('.get-systemone-hris-position'))
            GetSystemOneHrisSection($('.get-systemone-hris-section'))

            dataQuestionnaire = $("#tableQuestionnaire").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[3, "asc"],[3, "asc"]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ Questionnaire Record",
                    "lengthMenu": "Show _MENU_ Questionnaire Record",
                },
                "ajax" : {
                    url: "view_questionnaire",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "status"},
                    { "data" : "category",
                        "defaultContent": 'N/A',
                        "name": 'Category',
                        "orderable": true,
                        "searchable": true,
                        "render": function (data, type, row) {

                            switch (row.category) {
                                case 0:
                                    return "Newly Hired";
                                case 1:
                                    return "Certification";
                                case 2:
                                    return "Re-Certification";
                                default:
                                    return "Unknown";
                            }
                        },
                    },
                    { "data" : "exam_title"},
                    { "data" : "exam_instruction"},
                    { "data" : "purpose"},
                    { "data" : "department"},
                    { "data" : "position"},
                    { "data" : "product_line"},
                    { "data" : "passing_score"}
                ],
            });
        });
    </script>
@endsection
