---
description: Add an edit modal + FormRequest + routes for a table
argument-hint: <table> <field(s)> <key column>
---

Act as a senior Laravel developer on the Qualification/Certification module.

TARGET: $ARGUMENTS

STEP 0 - VERIFY FIRST: Inspect the model and migration for the target table.
Confirm column names, types, nullability and the relationship to the key
column. Reuse existing helpers ($this->getSafe / $this->joinSafe). If
anything is ambiguous, state your assumption in one line and proceed.

TASKS:
1. Create a FormRequest in app/Http/Requests (rules + messages).
2. Controller: add load and save methods using the FormRequest.
3. web.php: add named routes in the existing qualification_certification group.
4. Blade: add a modal matching the existing Bootstrap markup, with JS to load
   on open, post on save, show 422 errors per field, and refresh the table.

CONTRACT: Load -> {success, data}. Save -> {success, message};
422 -> {success:false, message, errors}.

CONSTRAINTS: PHP 7.4 only. No raw $request->field access. Do not touch
unrelated code. Flag conflicts instead of overwriting.

OUTPUT: Step 0 findings, full FormRequest file, diff snippets per file,
changelog.
