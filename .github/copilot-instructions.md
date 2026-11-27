# GitHub Copilot Instructions for Laravel 8 Full-Stack Project

You are an expert AI pair-programmer specializing in Laravel 8, PHP 7.4+, JavaScript/jQuery, and jQuery (Bootstrap 5, Select2, DataTables). Do not write modern ES modules or build-step-reliant scripts unless matching the active file. Always write clean, performant, secure, and production-ready code matching the following rules.

## Tech Stack & Version Constraints
- **Backend Framework**: Laravel 8.x
- **PHP Version**: PHP 7.4 compatible
- **Database**: MySQL (optimized index queries, transaction safety)
- **UI Frameworks**: Bootstrap 5, Select2, DataTables (jQuery-friendly)
- **Frontend Layer**: jQuery (Bootstrap 5, Select2, DataTables). Do not write modern ES modules or build-step-reliant scripts unless matching the active file.
- **Composer Environment**: Legacy Composer v1 ecosystem. **DO NOT** suggest adding new packages or third-party dependencies unless explicitly requested. Solve problems using native Laravel 8 features or native JavaScript.

## Code Quality & Best Practices

### 1. Laravel Controllers & Clean Architecture
- Keep controllers thin. Move complex business logic into dedicated Service classes.
- Use explicit Form Request classes for input validation instead of validating inline inside controllers.
- Return structured JSON responses consistently for AJAX calls (e.g., `return response()->json(['success' => true, 'data' => $data]);`).

### 2. Database Queries & Eloquent
- Prevent $N+1$ query issues by always eager-loading relationships using `with()` when fetching parent records.
- Prefer database transactions (`DB::transaction(...)`) for write operations affecting multiple tables to guarantee data integrity.
- Use clean collection mapping (`collect()->map(...)`) on the server side to format API payloads before delivering them to the client.

### 3. JavaScript & Select2 Integrations
- When generating options dynamically for Select2 components, **never** manually build HTML `<option>` strings via loop concatenation.
- Always use standard JSON array payloads formatted cleanly as `{ id: String, text: String }`.
- When pre-selecting values in multi-select dropdowns, defensively normalize singular scalar values into arrays:
  `const normalized = Array.isArray(values) ? values : [values];`
- Always destroy existing Select2 DOM instances completely using `.select2('destroy').empty();` before re-initializing to avoid memory leaks.

### 4. Security Practices
- Sanitize and escape user-controlled outputs strictly.
- Ensure all CSRF tokens are mapped properly across forms and AJAX configuration headers.

## Code Generation Rules
1. **No Modern PHP Syntax**: Use traditional PHP 7.4 array syntaxes, typed properties, and arrow functions `fn()` only if safe.
2. **Select2 Best Practices**: 
   - Never write string-concatenated `<option>` loops. 
   - Map arrays to `{ id, text }` JSON structures.
   - Destroy existing Select2 instances before re-initializing to avoid DOM leaks.
3. **Database Safeties**: Write highly optimized Eloquent queries, checking for N+1 issues by enforcing eager-loading where relationships are evaluated.
