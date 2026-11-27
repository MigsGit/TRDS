---
description: Lock CLqcTrainingItemResult saves to the role and day allowed by the slip's current approval_status.
agent: agent
tools: ['codebase', 'search', 'editFiles']
---

# Problem
`saveQcLqcTrainingItemsByPosition` only updates `qc_slips.approval_status` (via `determineNextOperPositionStatus`). It does not restrict which `CLqcTrainingItemResult` rows may be saved. Result: at status `AOPERPRDN1`, Production Day 2 checkbox inputs can still be edited and saved.

# Reference files (read fully before editing)
- #file:app\Http\Controllers\QualificationCertificationController.php: controller (`saveQcLqcTrainingItemsByPosition`, `determineNextOperPositionStatus`, `changeApprovalStatus`)
- #file:app\Model\Qc\CLqcTrainingItemResult.php
- #file:public\js\my_js\QualificationCertification.js:

# Required behavior
At the slip's current `approval_status`, only one role and one day is editable:

| Status | Editable role | Editable day |
|---|---|---|
| `AOPERPRDN` / `BOPERENGG` / `COPERQC` | Production / Engineering / QC | checkbox phase (`<PLACEHOLDER: 0 or null>`) |
| `AOPERPRDN1`..`5` | Production | 1..5 |
| `BOPERENGG1`..`5` | Engineering | 1..5 |
| `COPERQC1`..`5` | QC | 1..5 |
| `PB`, `DOPEROSC` |

# Backend rules
- Add a private helper `parseApprovalStatus(string $status): ?array` returning `['role' => ..., 'day' => ...]` or `null`. Reuse the status constants already used by `determineNextOperPositionStatus`; do not hard-code a second copy of the sequence.
- Read the status from the database (`$qcSlipDetails->approval_status`). Never trust `$params['approval_status']` for this check.
- Before any `CLqcTrainingItemResult` write, compare every incoming item's role and `day_number` with the helper's result. If any item does not match, return 422 with the controller's existing error format and write nothing.
- Run the guard, the item saves, and the slip status update in one `DB::transaction`.
- Keep the existing `in_array($params['selectPosition'], [...], true)` check and `determineNextOperPositionStatus` transition logic unchanged.
- Do not change routes, response shapes, or other methods.

# Frontend rules
- Disable (not hide) checkbox inputs where role or day differs from the editable pair derived from the current `approval_status`. Use a computed property built from one shared status-parsing function.
- Treat the frontend as UX only. The backend guard is the source of truth.

# Output
1. Modified `saveQcLqcTrainingItemsByPosition` and the new helper.
2. The frontend change.
3. A short list of what changed and why.

# Acceptance checks
- At `AOPERPRDN1`, saving Production Day 1 items succeeds; saving Production Day 2 returns 422 and writes nothing.
- At `AOPERPRDN1`, saving Engineering or QC items returns 422.
- At `COPERQC3`, only QC Day 3 is editable.
- At `DOPEROSC`, nothing is editable.
- Sending a forged `approval_status` in the request does not bypass the check.
- A failed save leaves the slip status and all item rows unchanged.
