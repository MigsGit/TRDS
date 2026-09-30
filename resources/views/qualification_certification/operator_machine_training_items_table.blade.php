
@php
    // Allow callers to inject a unique suffix; fall back to a safe default.
    $tableId        = $tableId        ?? 'tblTrainingItems_default';
    $accordionParent = $accordionParent ?? '#accordionExampleInsp';
    $collapseId     = 'collapseTrainingItems_' . $tableId;
    $position       = $position       ?? 'QC';
@endphp
<div class="accordion-item">
    <h2 class="card-header">
    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
        <h5>QC</h5>
    </button>
    </h2>
    <div id="{{ $collapseId }}" class="accordion-collapse collapse" data-parent="{{ $accordionParent }}">
    <div class="card-body">
        <div class="row">
                <button style="float: right !important;" type="button" class="btn btn-success btnSavePositionMatrix" id="btnSaveMatrix_{{ $tableId }}" data-table-id="{{ $tableId }}" data-position="{{ $position }}"><i class="fa-solid fa fa-save me-2" style="color: white"></i> SaveMatrix</button>
        </div>

        <hr>
        <div class="table-responsive">
            <table id="{{ $tableId }}" class="table table-bordered table-hover align-middle nowrap w-100 js-training-items-table">
                <thead class="table-secondary text-center">
                    <tr class="table-light">
                        <th colspan="8" class="text-start">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="fw-bold me-2">Checkbox Trainer Validation:</span>
                                <input type="text" class="form-control form-control-sm chk-trainer-scan-input" data-position="{{ $position }}" placeholder="Scan Trainer ID" style="max-width: 160px;">
                                <input type="hidden" class="chk-trainer-emp-no" value="">
                                <span class="chk-trainer-name-display text-muted" style="min-width: 140px;"></span>
                                <label class="mb-0 ms-2">Date:</label>
                                <input type="date" class="form-control form-control-sm input-chk-date" style="max-width: 150px;">
                                <label class="mb-0 ms-2">Time:</label>
                                <input type="time" class="form-control form-control-sm input-chk-time" style="max-width: 120px;">
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th rowspan="2" class="align-middle" style="width: 25%;">Training Items</th>
                        <th rowspan="2" class="align-middle text-center" style="width: 5%;">
                            <input type="checkbox" class="chk-select-all" id="chkSelectAll_{{ $tableId }}" title="Select All">
                        </th>
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
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="text-end align-middle">Trainer ID (Scan):</th>
                        @for ($day = 1; $day <= 5; $day++)
                            <td class="text-center">
                                <input type="text" class="form-control form-control-sm trainer-scan-input" data-day="{{ $day }}" data-position="{{ $position }}" placeholder="Scan Trainer ID">
                                <input type="hidden" class="trainer-emp-no" data-day="{{ $day }}" value="">
                                <div class="trainer-name-display small text-muted mt-1" data-day="{{ $day }}"></div>
                            </td>
                        @endfor
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-end align-middle">Validation Date:</th>
                        @for ($day = 1; $day <= 5; $day++)
                            <td class="text-center">
                                <input type="date" class="form-control form-control-sm input-day-date" data-day="{{ $day }}">
                            </td>
                        @endfor
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-end align-middle">Validation Time:</th>
                        @for ($day = 1; $day <= 5; $day++)
                            <td class="text-center">
                                <input type="time" class="form-control form-control-sm input-day-time" data-day="{{ $day }}">
                            </td>
                        @endfor
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-end align-middle">Overall Result:</th>
                        @for ($day = 1; $day <= 5; $day++)
                            <td class="text-center">
                                <select class="form-control form-control-sm select-day-result" data-day="{{ $day }}">
                                    <option value="">--</option>
                                    <option value="Passed">Passed</option>
                                    <option value="Failed">Failed</option>
                                </select>
                            </td>
                        @endfor
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    </div>
</div>
