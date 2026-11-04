# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

TRDS (Training Record / Qualification & Certification system) is a Laravel 8 app (PHP 7.4) that runs as a sub-module of the **RapidX** portal. It covers training requests/endorsements/attendance, examinations and HR memos, personnel skill matrices, inspector certification, personnel disqualification, and the QC qualification/certification workflow ("QC slips"). `readme.md` is the stock Laravel readme and has no project-specific content.

## Commands

```bash
composer install                 # PHP deps (platform pinned to php 7.4 in composer.json)
php artisan migrate              # migrations in database/migrations (default `mysql` connection only)
php artisan serve                # local dev server
./clear-cache.sh                 # clears view/route/config/app caches, re-caches config, dumps autoload
vendor/bin/phpunit               # tests (PHPUnit 7.5)
vendor/bin/phpunit --filter testName
```

- `config:cache` is run by `clear-cache.sh`; after editing `.env` or `config/*`, run `php artisan config:clear` (or the script) or changes won't take effect.
- `tests/` contains only the Laravel example tests — there is no real test suite. Verify changes by exercising the page in the browser.
- `webpack.mix.js` / `npm run dev` only build the unused `resources/js/app.js`. Page JavaScript is **not** built (see Frontend below).

## Architecture

### Authentication & access (RapidX session)
There is no Laravel auth login. Every route in `routes/web.php` sits inside `Route::middleware('checkSession')` (`app/Http/Middleware/CheckSession.php`), which:
- calls native `session_start()` and reads `$_SESSION['rapidx_user_id']` / `$_SESSION['rapidx_name']` set by the parent RapidX app; if missing, AJAX gets a 401 JSON and normal requests redirect to `../`.
- requires the user to have RapidX module access `module_id = 54` (queried on the `rapidx` connection) **and** a row in the local `users` + `user_access_modules` tables.
- stores the resulting user as `session('global_user')` and shares `$globalUser` with all views. Controllers use these for the acting user.

### Multiple databases
`config/database.php` defines several MySQL connections; models pin theirs via `protected $connection`:
- `mysql` — this app's own DB (all migrations, QC/training/exam tables).
- `rapidx` — RapidX portal users/departments/access (`RapidXUser`, `RapidXDepartment`).
- `mysql_systemone` — HRIS employee/position/section/training data (`SystemOneHris*`, `SystemHrisViewDivDeptSec`).
- `mysql_subcon`, `mysql_hris_subcon` — subcontractor employee data.
- `mysql_rapid_auto_mailer` — `RapidMailer`; outgoing email is queued by inserting rows here (see `CommonController::sendEmail`), not via Laravel Mail.

Cross-connection relationships (e.g. `QcSlipEmployee` → `SystemOneSubconEmpInfo` by `EmpNo`) cannot be joined in SQL; use eager loading. External DBs are read-only from this app's perspective and have no migrations here.

### Backend layout
- Models live in `app/Model/` (not `app/Models`), with subfolders `Qc/`, `Hr/`, `Disqualification/`, `InspectorSkillChart/`.
- Controllers are large and hold the business logic directly (no service layer). `CommonController` provides shared helpers (approval status mapping, email message building, `sendEmail`, RapidX user email lookup) and is constructor-injected into other controllers.
- Routes: page routes are closures returning a Blade view; data endpoints are grouped with `Route::controller(X::class)->group(...)` using snake_case URIs mapped to camelCase methods. Endpoints return JSON for AJAX.
- Writes typically use `DB::beginTransaction()` / `commit()` / `rollback()` inside `try/catch`, returning JSON errors.
- Validation uses Form Requests in `app/Http/Requests/`, often resolved conditionally inside one controller action via `app(XRequest::class)->validateResolved()` depending on which form/stage is being saved (see `saveQualificationCertificationOper`).
- Excel exports/imports via `maatwebsite/excel` (`app/Exports`, `app/Imports`); PDFs via `barryvdh/laravel-dompdf` (`resources/views/pdf`); DataTables via `yajra/laravel-datatables-oracle`.

### QC Qualification & Certification workflow
`QualificationCertificationController` (largest controller) drives the QC slip process. A `QcSlip` has `QcSlipEmployee`s and a sequence of stage records in `app/Model/Qc/`, whose letter prefix encodes the stage order:
- `A*` — training/orientation (`ALqcTrainingQualification`, `AMhTrainingOrientation`, `AOperProdTrainingOrientation`, `ASepTrainingOrientation`, `ATechEngTrainingQualification`)
- `B*` — certification / engineering section orientation
- `C*` — LQC/OQC validation, QC certification, training item results (`CLqcTrainingItemResult`)
- `D*` — PPD certification completion
- `E*`, `F*` — engineering/QC validation process and final QC validation

Each stage has a matching Form Request with the same name + `Request`. Approvers are stored in `OpApprover` / `QcLqcApprover`, and approval state transitions go through `changeApprovalStatus` + email notification. The UI is split by personnel type (operator, inspector, MH, supervisor, technician) in `resources/views/qualification_certification/modal_*.blade.php`.

### Frontend
- Blade views in `resources/views/` extend `layouts/layout.blade.php` (or `super_user_layout`); shared header/nav/footer are in `shared/pages`, and all CSS/JS includes are in `shared/css_links` and `shared/js_links`.
- Page logic is plain jQuery in `public/js/my_js/<Page>.js`, loaded directly with `asset('public/js/my_js/...')?<?=time()?>` (cache-busted, no build step). New page scripts must be added to `js_links.blade.php` or the page view. Some pages also have large inline `<script>` blocks in the Blade file.
- `public/js/my_js/Common.js` defines shared helpers such as `ajaxRequest(options)` and global Select2 (`theme: 'bootstrap-5'`) initialization.
- Files suffixed `_backup`, `_test`, `REV1`, `_rev1` are kept old copies; don't edit them as if they were live code.

## Coding conventions (from `.github/copilot-instructions.md`)
- Stay PHP 7.4 / Laravel 8 compatible. Legacy Composer environment: **do not add new Composer or npm packages** unless explicitly asked.
- Frontend is jQuery + Bootstrap 5 + Select2 + DataTables; no ES modules or build-dependent code unless the file already uses it.
- Select2: feed options as `{ id, text }` JSON arrays (never concatenate `<option>` strings in loops), normalize preselected values with `Array.isArray(v) ? v : [v]`, and call `.select2('destroy').empty()` before re-initializing.
- Eager-load relations with `with()` to avoid N+1; wrap multi-table writes in transactions; return structured JSON (`['success' => ..., 'data' => ...]`) for AJAX; make sure CSRF tokens are sent with AJAX.
- `.github/prompts/` contains task-specific prompt files for past QC/LQC training-item changes, useful as context for that feature.

## Environment constraints
- PHP 7.3, Laravel 8, maatwebsite/excel 3.1.31 (PhpSpreadsheet 1.x).
- No PHP 7.4+ syntax: no arrow functions (`fn`), typed properties, `match`, named arguments, `??=`, constructor promotion.
- MySQL has no window functions: never use `ROW_NUMBER() OVER`. Rank with correlated subqueries or derived tables.
- Files are UTF-8 without BOM, starting with `<?php` then `namespace`. A BOM causes "Namespace declaration statement has to be the very first statement".
- PSR-12.

## Working rules
- Make minimal, targeted changes. Never rewrite, reformat or rename working code.
- Show a plan and the files you will touch before editing.
- Don't create new classes or files unless asked. Only reference classes that exist (a missing `TSF1Layout` broke the export once). Keep constants inside the class that uses them.
- After editing PHP, run `php -l` on each changed file.
- When asked for a change, output only the changed code, not whole files.

## Inspector Skill Chart export
- `app/Exports/InspectorSkillChart.php`: builds employee data (`$this->grouped`) and creates one `TSF1` sheet per entry in `$selectedSheets`.
- `app/Exports/InspectorSkillChartSheets/TSF1.php`: writes one sheet. Constructor: `new TSF1($categories, $group, $sheet, $employees)`.

### Data shapes
- `$categories`: `[['name' => category, 'processes' => [['name', 'order', 'sub_stations' => []]]]]`. The settings table is `InspectorSkillChartSetting`.
- `$employees` item: `employee_no`, `employee_name`, `date_hired`, `length_of_service_year`, `length_of_service_months`, `sections` (Collection).
- `sections` is keyed by section name: `main` is the sheet's own section, the other keys are the employee's other sections. Each has `['section', 'stations' => Collection]`.
- A station: `slip_id`, `station_to`, `station_to_name`, `training_result` (null when sub-stations exist), `sub_stations` = `[['name', 'training_result']]`. `remarks` holds comma-separated sub-stations.
- Station and result are paired by rank (`rn`, newest first), not by a real key. Rows are ordered newest first, and `unique('station_to')` keeps the newest per station.

### Selection and grouping rules
- Current slip = the employee's slip with the highest `qc_slips.id` (Inspector, not soft-deleted, non-deleted `qc_slip_employees` row). Current section = its `section_category`. Current station = its newest `qc_slip_employees` row.
- Compute the current slip from the slip tables, not from the joined result (the result join is inner and can drop the real latest slip).
- An employee appears only on the sheet whose section equals their current section, and only if that sheet was selected. Match the **exact** sheet section: TSF1 and TSF3 are separate, and PPDCN, PPDTS and PPDF3 are separate.
- Filter by employee in SQL, not by row. The employee's older rows in other sections must still be loaded.
- The sheet-to-group mapping (TSF1/TSF3 => TS, CN/CNF3 => CN, PPDCN/PPDTS/PPDF3 => PPD, YF => YF) applies **only** to the OTHER PROCESS / SYSTEM SKILLS columns (AG:AO). Non-main sections are rolled up by group, and the sheet's own group is excluded.
- Use one shared mapping constant for the constructor and `sheets()`. Skip unknown sheet names.

## Template rules (QC Inspectors Skill Chart)
- Layout: title A1:A2, headers rows 3-5, employees from row 6, footer 2 rows after the last employee, legend after the footer.
- Skill area H:Z. Other-process matrix AG:AO (IQC/IPQC/OQC x CN/PPD/YF). Row totals AB:AE and AQ:AT. Footer counts H60:Z63 (template addresses, positions are dynamic).
- A process with sub-stations is merged horizontally. One without is merged vertically over rows 4:5.
- Lookup key for a skill cell is **process name + sub-station**, never the sub-station alone (e.g. "Contact Force" appears under two processes).
- Formulas are `COUNTIF(range,"1")` to `"4"`: row totals over H:Z and AG:AO, footer totals per column over the real first and last employee rows. Don't copy the template's `H6:H54` off-by-one. Build the footer from the same column map as the main table.
- `training_result` in the skill area H:Z is written as an integer, and `0` when missing, null or empty. The other-process matrix AG:AO stays blank when there is no data.

### Styles
- Fonts: headers and title Calibri 10 (bold on the title, category and process rows, not on the sub-station row). Employee data Calibri 11. AB:AE, AQ:AT and H60:Z63 are Consolas 11, color 242424. AG:AO is Arial 12.
- Fills follow the **process name**, never the column letter. Header fills: IQC `AEAAAA`, IPQC `F4B084`, OQC `A9D08E`, QS/TU `9BC2E6`. Sub-station row: `EDEDED` (IQC), `FCE4D6` (IPQC), `E2EFDA` (OQC). Data fills: `AEAAAA`, `F4B084`, `A9D08E`, QS/TU `8EA9DB`. Category band and legend numbers: `FFFF00`. Level headers: `FFFFCC`. E:F data cells are white.
- Borders are thin everywhere, except a medium left border on column A.
- Keep all style values in one constant array in `TSF1`. Apply them with range-based `getStyle()` after the data is written.

## Known gotchas
- Section names in `section_category` may be group-level (`TS`) or sheet-level (`TSF1`). Check `SELECT DISTINCT section_category FROM qc_slips WHERE position_category = 'Inspector'` before relying on exact matches.
- `employee_info` can be null when the System One lookup finds nothing. Read it with `isset` or `optional`.
- Present Allocation (column G) isn't in the query data. Don't invent it.
   @.github/copilot-instructions.md
<!-- 
   ## Claude-specific
   - Plan first for changes touching 3+ files.
   - Skills live in .claude/skills/. -->
