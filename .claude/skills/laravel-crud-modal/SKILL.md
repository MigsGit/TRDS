---
name: laravel-crud-modal
description: Use when adding or editing a Bootstrap modal that loads and saves a database record in this Laravel app (for example "modal to edit op_approver by qc_slips_id"). Covers FormRequest, controller load/save methods, named routes, and Blade/JS wiring.
---

# Laravel CRUD Modal

## Step 0 - Verify before writing (mandatory)
1. Read the model and migration for the target table. Confirm column names, types, nullability, key column and relationship.
2. Grep the controller for an existing load/save method and reuse its style.
3. Read the helper definitions ($this->getSafe, $this->joinSafe) and use them as defined.
4. Check the Bootstrap version used in the target blade file.
5. Report findings in a short list. If something is ambiguous, state the assumption in one line, then proceed.

## Build order
1. FormRequest in app/Http/Requests: authorize() follows the project convention, rules() match the schema, short messages().
2. Controller: one load method and one save method. Save uses the FormRequest and validated(). Wrap in try/catch.
3. web.php: named routes inside the existing qualification_certification group.
4. Blade + JS: modal with hidden key input, load on show, POST on save, 422 errors shown under each field, close on success, refresh only the affected table.

## Response contract
- Load: {success: true, data: {...}}
- Save: {success: true, message: "..."}
- Validation failure (HTTP 422): {success: false, message: "...", errors: {...}}

## Rules
- PHP 7.4 only: no match, enums, nullsafe, constructor promotion.
- No raw $request->field access.
- Do not modify unrelated routes, methods, modals or markup.
- If an existing pattern conflicts with this skill, flag it instead of overwriting.

## Output
Step 0 findings, full FormRequest file, diff snippets per file, short changelog, and a list of what to verify manually.
