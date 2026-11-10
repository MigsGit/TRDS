---
name: laravel-endpoint-review
description: Use when reviewing or hardening a PHP or Laravel endpoint, route, controller method, or legacy PHP script (SQL building, CORS headers, validation, secrets).
---

# Endpoint Review Checklist
- SQL: no string-concatenated user input. Use bindings or the query builder.
- Secrets: no credentials in source. Move to .env and config().
- Validation: FormRequest or validate(), with explicit rules.
- JSON endpoints: headers sent first, before any logic that could fail. Errors return JSON with a proper status code, never a PHP warning page.
- CORS: restrict origins instead of "*" when credentials or internal data are involved.
- Authorization: confirm routes sit behind the right middleware.
- Mass assignment: check $fillable/$guarded for fields being saved.
Report findings as: issue, file/line, fix. Do not change code unless asked.
