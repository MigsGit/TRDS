---
description: Update saveQcLqcTrainingItemsByPosition to save training matrix items using the Day 1-5 status progression, keyed per position and day.
agent: agent
tools: ['codebase', 'editFiles', 'search']
---

Modify `#sym:saveQcLqcTrainingItemsByPosition` to save training matrix items according to the status progression below, across Production, Engineering, and QC positions.

## Position check

Wrap the position-specific logic in this check:

```php
if (in_array($params['selectPosition'], ['VisualOperator', 'PartsPrep', 'MachineOperator'], true)) {
    // position-specific logic and status determination
}
```

For every other position, keep the existing code path unchanged. Do not refactor, rename, or reformat it.

## Status sequence

Apply in this exact order when updating the slip and item results:

1. **Initial:** `PB` (Start Checkboxes)
2. **Checkboxes phase:** `AOPERPRDN` (Production) -> `BOPERENGG` (Engineering) -> `COPERQC` (QC)
3. **Day 1:** `AOPERPRDN1` -> `BOPERENGG1` -> `COPERQC1`
4. **Day 2:** `AOPERPRDN2` -> `BOPERENGG2` -> `COPERQC2`
5. **Day 3:** `AOPERPRDN3` -> `BOPERENGG3` -> `COPERQC3`
6. **Day 4:** `AOPERPRDN4` -> `BOPERENGG4` -> `COPERQC4`
7. **Day 5:** `AOPERPRDN5` -> `BOPERENGG5` -> `COPERQC5` -> `DOPEROSC`

Rules:
- Each status advances only to the next one in the sequence. Do not skip or reorder.
- Determine the next status from the current slip status and the acting position.

## Persistence

- Use `updateOrCreate` with the composite match key `qc_slips_id`, `training_item_id`, `day_number`, `position`, so rows for different positions and days never overwrite each other.
- Put only the result and status fields in the update array, not the key fields.

## Transition alignment

- Read `#sym:changeApprovalStatus` and reuse or mirror its transition logic so both methods produce identical status transitions.
- Do not change `changeApprovalStatus` unless needed to share the logic. If so, extract it to a shared private method and keep its public behavior the same.

## Acceptance criteria

- Saving for different positions on the same day creates separate rows.
- Saving for the same position on different days creates separate rows.
- Re-saving the same key updates the existing row and creates no duplicate.
- The final transition `COPERQC5` -> `DOPEROSC` is the next status.
- Saving for any position outside `VisualOperator`, `PartsPrep`, and `MachineOperator` behaves exactly as before.
