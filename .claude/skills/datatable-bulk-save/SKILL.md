---
name: datatable-bulk-save
description: Use when adding row-selection checkboxes to a server-side DataTable and saving the checked rows via AJAX (for example .chk-select-item and .btnSaveMatrix in the QC/LQC training items table).
---

# DataTable Bulk Select and Save

## Step 0 - Verify
- Read the controller method that builds the DataTable (yajra addColumn/rawColumns), the Blade table header, and the JS init function.
- Read the target model and migration to find the unique key for updateOrCreate (for example qc_slip_id + item_id).
- Confirm the route is a named route, not a raw URL.

## Data contract
- Payload: items: [{item_id, sub_description, item_remark, day_1..day_5}]
- Same keys in JS, validation rules and controller. Never rename on one side.
- Response: {success: true} or {success: false, message} with HTTP 422.

## Implementation
1. Controller: add the checkbox column before day_1 and add it to rawColumns.
2. Blade: add a header cell (Select All) before the Day 1 header. Select All affects only the current page.
3. JS: register the column (orderable: false, searchable: false). Collect rows from .chk-select-item:checked using delegated events, because rows redraw on paging.
4. Save: POST the payload. On success call ajax.reload(null, false). On failure show the message.
5. Controller save method: validate items as an array, items.*.item_id with exists rule, other fields nullable. Use updateOrCreate on the verified unique key inside a DB transaction.

## Rules
- PHP 7.4. No raw $request access. Do not touch unrelated columns or methods.
- If the save method already exists, verify and extend it. Do not duplicate it.
