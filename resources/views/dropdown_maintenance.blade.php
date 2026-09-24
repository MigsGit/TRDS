@php $layout = 'layouts.super_user_layout'; @endphp

{{-- Here I removed the @auth because the dashboard isn't loading properly --}}
@extends($layout)
@section('title', 'Dropdown Maintenance')

@section('content_page')

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
                        <li class="breadcrumb-item active">Dropdown Maintenance</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            {{-- =========================================================
                 DROPDOWN TYPE SELECTION
            ========================================================== --}}
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-list-ul text-primary mr-2"></i> Dropdown Type
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-6 mb-0">
                            <label for="selectDropdownType" class="font-weight-bold small text-muted">
                                SELECT DROPDOWN TYPE
                            </label>
                            <select class="custom-select select2bs41" id="selectDropdownType">
                                <option value="" selected disabled>-- Select Dropdown Type --</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-0 text-md-right">
                            <button type="button" class="btn btn-success" id="btnAddDropdownType">
                                <i class="fas fa-plus mr-1"></i> Add Dropdown Type
                            </button>
                            
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================
                 DROPDOWN ITEMS DATATABLE
            ========================================================== --}}
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-table text-primary mr-2"></i> Dropdown Items
                        <span class="text-muted font-weight-normal" id="selectedDropdownTypeLabel"></span>
                    </h3>
                    <button type="button" class="btn btn-info btn-sm float-right" id="btnAddDropdownItem" disabled>
                        <i class="fas fa-plus mr-1"></i> Add Dropdown Item
                    </button>
                </div>
                <div class="card-body">

                    {{-- Empty State: shown when no dropdown type is selected --}}
                    <div id="dropdownItemsEmptyState" class="text-center py-5">
                        <i class="fas fa-hand-pointer fa-4x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Please select a Dropdown Type to view its items.</p>
                    </div>

                    {{-- DataTable: shown once a dropdown type is selected --}}
                    <div id="dropdownItemsTableWrapper" class="table-responsive d-none">
                        <table class="table table-bordered table-striped table-hover w-100" id="tblDropdownItems">
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

{{-- =========================================================
     MODAL 1: ADD / EDIT DROPDOWN TYPE
========================================================== --}}
<div class="modal fade" id="modalDropdownType" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalDropdownTypeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDropdownTypeLabel">
                    <i class="fas fa-list-ul text-primary mr-2"></i> Add Dropdown Type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="dropdownTypeForm" autocomplete="off">
                    @csrf
                    <input type="hidden" id="dropdownTypeId" name="dropdown_type_id" value="">
                    <div class="form-group">
                        <label for="dropdownTypeName" class="font-weight-bold small text-muted">
                            TYPE NAME <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="dropdownTypeName" name="type_name" placeholder="e.g. Status, Department, Category" required>
                    </div>
                    <div class="form-group">
                        <label for="dropdownTypeCategory" class="font-weight-bold small text-muted">
                            Category
                        </label>
                        <input type="text" class="form-control" id="dropdownTypeCategory" name="category" placeholder="e.g. Where dropdown is used">
                    </div>
                    <div class="form-group mb-0">
                        <label for="dropdownTypeRemarks" class="font-weight-bold small text-muted">
                            REMARKS
                        </label>
                        <textarea class="form-control" id="dropdownTypeRemarks" name="remarks" rows="3" placeholder="Optional Remarks..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" id="btnSaveDropdownType">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================
     MODAL 2: ADD / EDIT DROPDOWN ITEM
========================================================== --}}
<div class="modal fade" id="modalDropdownItem" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalDropdownItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDropdownItemLabel">
                    <i class="fas fa-plus-circle text-primary mr-2"></i> Add Dropdown Item
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="dropdownItemForm" autocomplete="off">
                    @csrf
                    <input type="hidden" id="dropdownItemId" name="dropdown_item_id" value="">
                    <div class="form-group">
                        <label for="dropdownItemParentType" class="font-weight-bold small text-muted">
                            PARENT TYPE
                        </label>
                        <input type="text" class="form-control" id="dropdownItemParentType" name="dropdown_masters_type" disabled>
                        <input type="hidden" id="dropdownItemParentTypeId" name="dropdown_masters_id" value="">
                    </div>
                    <div class="form-group">
                        <label for="dropdownItemName" class="font-weight-bold small text-muted">
                            OPTION NAME <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="dropdownItemName" name="dropdown_masters_details" placeholder="Select Option" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnSaveDropdownItem">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js_content')
    <script type="text/javascript">
        // ===============================
        // CSRF Setup
        // ===============================
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let dtDropdownItems;

        // ===============================
        // Initialization
        // ===============================
        function initializeDataTable() {
            dtDropdownItems = $("#tblDropdownItems").DataTable({
                "processing" : true,
                "serverSide" : true,
                "order": [[ 1, "desc" ]],
                "ajax" : {
                    url: "dt_get_dropdown_items",
                     data: function (param){
                        param.dropdown_type_id = $('#selectDropdownType').val();
                    }
                },
                fixedHeader: true,
                "columns":[
                    // { "data" : "action", orderable:false, searchable:false },
                    { "data" : "action", "title": "Actions", searchable:false, orderable:false },
                    { "data" : "status", "title": "Status"},
                    { "data" : "dropdown_masters_details", "title": "Dropdown Items"},
                ],
                "columnDefs": [
                    {"className": "dt-center", "targets": "_all"},
                   
                ],
              
            });//end of dataTable
        }

        function toggleEmptyState(showEmpty) {
            if (showEmpty) {
                $('#dropdownItemsEmptyState').removeClass('d-none');
                $('#dropdownItemsTableWrapper').addClass('d-none');
            } else {
                $('#dropdownItemsEmptyState').addClass('d-none');
                $('#dropdownItemsTableWrapper').removeClass('d-none');
            }
        }

        // ===============================
        // Dropdown Type Functions
        // ===============================
        function loadDropdownTypes() {
            $.ajax({
                url: '{{ route("get_dropdown_types") }}',
                type: 'GET',
                beforeSend: function () {
                    $('#selectDropdownType, #dropdownItemParentType')
                        .prop('disabled', true);
                },
                success: function (response) {
                    // Select2 requires an empty top  for placeholders to function properly
                    let optionsHtml = '';

                    if (!response || response.length === 0) {
                        $('#selectDropdownType, #dropdownItemParentType')
                            .html(optionsHtml)
                            .prop('disabled', true);
                        return;
                    }

                    // Group items by category
                    let grouped = {};
                    response.forEach(function (item) {
                        let categoryName = item.category || 'Uncategorized';
                        if (!grouped[categoryName]) {
                            grouped[categoryName] = [];
                        }
                        grouped[categoryName].push(item);
                    });

                    // Build  HTML structure
                    optionsHtml = '<option value="" selected disabled>-- Select Dropdown Type --</option>'; // Reset optionsHtml before building the new structure
                    $.each(grouped, function (category, items) {
                        optionsHtml += `<optgroup label="${category}">`;
                        items.forEach(function (item) {
                            optionsHtml += `<option value="${item.id}">${item.dropdown_masters}</option>`;
                        });
                        optionsHtml += `</optgroup>`;
                    });

                    // Re-initialize Select2 with custom Category Matcher
                    $('#selectDropdownType')
                        .html(optionsHtml)
                        .prop('disabled', false)
                        .select2({
                            placeholder: '-- Select Dropdown Type --',
                            allowClear: true,
                            theme: 'bootstrap-5',
                            matcher: matchCategoryOrOption
                        });
                },
                error: function (xhr) {
                    $('#selectDropdownType, #dropdownItemParentType').prop('disabled', true);
                    toastr.error('Failed to load dropdown types.');
                }
            });
        }

        // Custom matcher function to search options AND category optgroup labels
        function matchCategoryOrOption(params, data) {
            // If there are no search terms, return all data
            if ($.trim(params.term) === '') {
                return data;
            }

            // Do not display data if there's no text/children
            if (typeof data.text === 'undefined') {
                return null;
            }

            let searchTerm = params.term.toLowerCase();

            // 1. Check if the Optgroup (Category) label matches the search term
            if (data.children && data.children.length > 0) {
                let categoryName = data.text.toLowerCase();
                
                if (categoryName.indexOf(searchTerm) > -1) {
                    // Category matches! Return the optgroup with ALL its children intact
                    return data;
                }

                // 2. If category name doesn't match, search individual child options
                let filteredChildren = [];
                $.each(data.children, function (idx, child) {
                    if (child.text.toLowerCase().indexOf(searchTerm) > -1) {
                        filteredChildren.push(child);
                    }
                });

                // If child options matched, return the category with only matching options
                if (filteredChildren.length > 0) {
                    let modifiedData = $.extend({}, data, true);
                    modifiedData.children = filteredChildren;
                    return modifiedData;
                }
            }

            // 3. Fallback for standalone options without optgroups
            if (data.text.toLowerCase().indexOf(searchTerm) > -1) {
                return data;
            }

            return null;
        }

        function resetDropdownTypeForm() {
            $('#dropdownTypeForm')[0].reset();
            $('#dropdownTypeId').val('');
            $('#modalDropdownTypeLabel').html('<i class="fas fa-list-ul text-primary mr-2"></i> Add Dropdown Type');
        }

        function saveDropdownType() {
            $.ajax({
                url: '{{ route("save_dropdown_type") }}',
                type: 'POST',
                data: $('#dropdownTypeForm').serialize(),
                beforeSend: function () {
                    $('#btnSaveDropdownType').prop('disabled', true);
                },
                success: function (response) {
                    // close modal
                    $('#modalDropdownType').modal('hide');
                    // refresh dropdown type list
                    loadDropdownTypes();
                    // show success message
                    if(response.success){
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    if(xhr.status == 422){
                        handleValidatorErrors(xhr.responseJSON.errors);
                        toastr.error(xhr.responseJSON.message);
                    } 
                },
                complete: function () {
                    $('#btnSaveDropdownType').prop('disabled', false);
                }
            });
        }

        // ===============================
        // Dropdown Item Functions
        // ===============================
        function loadDropdownItems(typeId) {
            if(!typeId) return;
            toggleEmptyState(false);
            dtDropdownItems.draw();
        }

        function resetDropdownItemForm() {
            $('#dropdownItemForm')[0].reset();
            $('#dropdownItemId').val('');
            $('#dropdownItemParentType').val($('#selectDropdownType').val());
            $('#modalDropdownItemLabel').html('<i class="fas fa-plus-circle text-primary mr-2"></i> Add Dropdown Item');
        }

        function openEditItemModal(id) {
            resetDropdownItemForm();

            $.ajax({
                url: '{{ route("get_dropdown_item_by_id") }}',
                type: 'GET',
                data: { id: id },
                beforeSend: function () {
                    // show loading state
                },
                success: function (response) {
                    // populate form
                    $('#dropdownItemId').val(response.id);
                    $('#dropdownItemParentType').val(response.dropdown_master.dropdown_masters);
                    $('#dropdownItemParentTypeId').val(response.dropdown_masters_id);
                    $('#dropdownItemName').val(response.dropdown_masters_details);
                    

                    $('#modalDropdownItemLabel').html('<i class="fas fa-edit text-primary mr-2"></i> Edit Dropdown Item');
                    $('#modalDropdownItem').modal('show');
                },
                error: function (xhr) {
                    toastr.error('Failed to fetch dropdown item.');
                }
            });
        }

        function saveDropdownItem() {
            $.ajax({
                url: "{{ route('save_dropdown_items') }}",
                type: 'POST',
                data: $('#dropdownItemForm').serialize(),
                beforeSend: function () {
                    $('#btnSaveDropdownItem').prop('disabled', true);
                },
                success: function (response) {
                    // close modal
                    $('#modalDropdownItem').modal('hide');
                    // reload datatable
                    dtDropdownItems.draw();
                    // show success message
                    toastr.success('Dropdown item saved successfully.');
                },
                error: function (xhr) {
                    $('#btnSaveDropdownItem').prop('disabled', false);
                    if(xhr.status == 422){
                        handleValidatorErrors(xhr.responseJSON.errors);
                    }

                    if(xhr.status == 403){
                        toastr.error('Super Admin or Admin privileges, along with specific module access, are required.', 'Access Denied');
                        return;
                    } 

                    toastr.error('Failed to save dropdown item.');

                },
                complete: function () {
                    $('#btnSaveDropdownItem').prop('disabled', false);
                }
            });
        }

        function deleteDropdownItem(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This dropdown item will be removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it'
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                let typeId = $('#selectDropdownType').val();

                $.ajax({
                    url: "{{ route('delete_dropdown_item') }}",
                    type: 'POST',
                    data: { id: id },
                    beforeSend: function () {
                        // show loading state
                    },
                    success: function (response) {
                        // reload datatable
                        dtDropdownItems.draw();

                        // show success message
                        toastr.success('Dropdown item deleted successfully.');
                    },
                    error: function (xhr) {
                        toastr.error('Failed to delete dropdown item.');
                    }
                });
            });
        }

        // ===============================
        // Event Bindings
        // ===============================
        $('#selectDropdownType').on('change', function () {
            let typeId = $(this).val();

            if (typeId) {
                $('#btnAddDropdownItem').prop('disabled', false);
                $('#selectedDropdownTypeLabel').text('(' + $(this).find(':selected').text() + ')');
                loadDropdownItems(typeId);
            } else {
                $('#btnAddDropdownItem').prop('disabled', true);
                $('#selectedDropdownTypeLabel').text('');
                toggleEmptyState(true);
            }
        });

        $('#btnAddDropdownType').on('click', function () {
            resetDropdownTypeForm();
            $('#modalDropdownType').modal('show');
        });

        $('#btnSaveDropdownType').on('click', function () {
            saveDropdownType();
        });

        $('#btnSaveDropdownItem').on('click', function () {
            saveDropdownItem();
        });

        $(document).on('click', '.btnEditItem', function () {
            openEditItemModal($(this).data('id'));
        });

        $(document).on('click', '.btnDeleteItem', function () {
            deleteDropdownItem($(this).data('id'));
        });

        $('#modalDropdownType').on('hidden.bs.modal', function () {
            resetDropdownTypeForm();
        });

        $('#modalDropdownItem').on('hidden.bs.modal', function () {
            resetDropdownItemForm();
        });

        $('#btnAddDropdownItem').on('click', function () {
            resetDropdownItemForm();
            $('#dropdownItemParentType').val($('#selectDropdownType').find(':selected').text());
            $('#dropdownItemParentTypeId').val($('#selectDropdownType').val());
            $('#modalDropdownItem').modal('show');
        });

        // ===============================
        // Page Bootstrapping
        // ===============================
        $(document).ready(function () {
            initializeDataTable();
            loadDropdownTypes();
        });
    </script>
@endsection
