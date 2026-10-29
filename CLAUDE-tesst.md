# Project: QC/LQC Training System (Laravel)

## Stack
- Laravel, PHP 7.4, Bootstrap modals, DataTables (server-side), Select2

## Conventions
- Controllers: app/Http/Controllers/QualificationCertificationController.php
- Helpers: $this->getSafe($request, $key, $default) / $this->joinSafe($data, $key, $default). Always use them, never raw $request->field.
- Validation: FormRequest classes in app/Http/Requests
- JSON: {success, message, data?, errors?}, 422 for validation failures
- Named routes only; JS reads URLs from route()

## Don'ts
- No PHP 8 syntax. No hardcoded credentials. Don't edit unrelated routes or modals.
- Never read or print .env contents.

## Workflow
- Verify schema (model + migration) before writing code.
- Plan first for changes touching 3 or more files.