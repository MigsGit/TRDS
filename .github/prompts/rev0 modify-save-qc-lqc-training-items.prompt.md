---
description: Modify saveQcLqcTrainingItemsByPosition to save training matrix items through the Day 1-5 status progression, aligned with changeApprovalStatus.
agent: agent
tools: ['codebase', 'search', 'editFiles']
---

# Task
Modify `saveQcLqcTrainingItemsByPosition` so it saves training matrix items and advances the slip and item-result statuses along the sequence below. Align transition logic with `changeApprovalStatus`. Change only these two methods and any private helper you extract; do not alter routes, response shapes, or unrelated methods.

# Reference files (read fully before editing)
- #file:app/Http/Controllers/QualificationCertificationController.php (contains `saveQcLqcTrainingItemsByPosition` and `changeApprovalStatus`)
- #file:<database\migrations\2026_06_17_094953_create_qc_slips_table.php>
- #file:<database\migrations\2026_07_29_062043_create_c_lqc_training_item_results_table.php>
- #file:<database\migrations\2026_07_30_000001_alter_c_lqc_training_item_results_add_date_subdesc_unique.php>
- #file:<database\migrations\update_unique_key_on_c_lqc_training_item_results_table.php>

# Status sequence (single source of truth)
Define it once as a constant array (or enum) and use it for every transition. Do not hard-code status strings elsewhere.

| Phase | Production | Engineering | QC |
|---|---|---|---|
| Start | `PB` | | |
| Checkboxes | `AOPERPRDN` | `BOPERENGG` | `COPERQC` |
| Day 1 | `AOPERPRDN1` | `BOPERENGG1` | `COPERQC1` |
| Day 2 | `AOPERPRDN2` | `BOPERENGG2` | `COPERQC2` |
| Day 3 | `AOPERPRDN3` | `BOPERENGG3` | `COPERQC3` |
| Day 4 | `AOPERPRDN4` | `BOPERENGG4` | `COPERQC4` |
| Day 5 | `AOPERPRDN5` | `BOPERENGG5` | `COPERQC5` |
| End | `COMPLETED` | | |

Order within each phase is Production → Engineering → QC. After `COPERQC` of one phase, the next status is `AOPERPRDN` of the following phase. After `COPERQC5`, the next status is `COMPLETED`.

# Position handling
Use this check for the production-line positions:

```php
$productionPositions = ['VisualOperator', 'PartsPrep', 'MachineOperator'];

if (in_array($params['selectPosition'], $productionPositions, true)) {
    // position-specific logic and status determination
}
```

<!-- - Validate `selectPosition` against the allowed positions before use. -->
<!-- - For any other position: <PLACEHOLDER: keep existing behavior / reject with 422>. -->

# Save rules
- Use `updateOrCreate` with the composite key `['qc_slips_id', 'training_item_id', 'day_number', 'position']` as the match array. Put status and result fields in the values array.
- `day_number` for the Checkboxes phase is <PLACEHOLDER: 0 / null>. For Day N phases it is N. Derive `day_number` from the current status; do not trust a client-supplied value that conflicts with it.
- Different positions and different days must never overwrite each other's rows.
- Update the slip status and each item-result status together inside one `DB::transaction`.
- Make saves idempotent: saving the same input twice must not advance the status twice.

# Transition rules (align with `changeApprovalStatus`)
- Read how `changeApprovalStatus` determines and validates the next status. Reuse its logic or extract a shared private helper (e.g. `nextStatus(string $current): string`). Do not duplicate it.
- Advance only to the immediate next status in the sequence. Reject skipped steps and backward moves with a 422 response, using the controller's existing error format.
- Only the matching actor may advance each step (Production → Engineering → QC), using the controller's existing authorization pattern.
- Statuses are stored on: <PLACEHOLDER: slip table.column and item-result table.column>. The slip status is the source of truth; item results must stay consistent with it.

# Constraints
- Follow the controller's existing conventions for validation, responses, and error handling.
- Validate all inputs (`qc_slips_id`, `training_item_id`, `selectPosition`, `day_number`) before writing.
- No unused variables or imports. Do not add dependencies.

# Output
1. The modified `saveQcLqcTrainingItemsByPosition`.
2. Any new or changed helper, plus the status constant.
3. A short list of what changed and why.

# Acceptance checks
- A new slip starts at `PB` and moves through every status above in order to `COMPLETED`.
<!-- - Skipping a step, going backward, or an unauthorized actor returns 422/403 and writes nothing. -->
- Saving the same payload twice produces the same database state.
- Rows for different positions or different days under the same slip and item stay separate.
- If any write fails, the transaction rolls back completely.
