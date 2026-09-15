
@php
    // Allow callers to inject a unique suffix; fall back to a safe default.
    $tableId        = $tableId        ?? 'tblTrainingItems_default';
    $accordionParent = $accordionParent ?? '#accordionExampleInsp';
    $collapseId     = 'collapseTrainingItems_' . $tableId;
@endphp
<div class="accordion-item">
    <h2 class="card-header">
    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
        <h5>INSPECTOR TRAINING / CERTIFICATION AND VALIDATION SLIP</h5>
    </button>
    </h2>
    <div id="{{ $collapseId }}" class="accordion-collapse collapse" data-parent="{{ $accordionParent }}">
    <div class="card-body">
        <div class="row">
                <button style="float: right !important;" type="button" class="btn btn-success btnSaveMatrix" id="btnSaveMatrix_{{ $tableId }}" data-table-id="{{ $tableId }}"><i class="fa-solid fa fa-save me-2" style="color: white"></i> SaveMatrix</button>
        </div>

        <hr>
        <div class="table-responsive">
            <table id="{{ $tableId }}" class="table table-bordered table-hover align-middle nowrap w-100 js-training-items-table">
                <thead class="table-secondary text-center">
                    <tr>
                        <th rowspan="2" class="align-middle" style="width: 25%;">Training Items</th>
                        <th colspan="5">Result</th>
                        <th rowspan="2" class="align-middle" style="width: 20%;">Remarks</th>
                    </tr>
                    <tr>
                        <th style="width: 11%;">Day 1<br><input type="date" class="form-control form-control-sm mt-1 header-date-input" data-day="1"></th>
                        <th style="width: 11%;">Day 2<br><input type="date" class="form-control form-control-sm mt-1 header-date-input" data-day="2"></th>
                        <th style="width: 11%;">Day 3<br><input type="date" class="form-control form-control-sm mt-1 header-date-input" data-day="3"></th>
                        <th style="width: 11%;">Day 4<br><input type="date" class="form-control form-control-sm mt-1 header-date-input" data-day="4"></th>
                        <th style="width: 11%;">Day 5<br><input type="date" class="form-control form-control-sm mt-1 header-date-input" data-day="5"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables AJAX population -->
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
