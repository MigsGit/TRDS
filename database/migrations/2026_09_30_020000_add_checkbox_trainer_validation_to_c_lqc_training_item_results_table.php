<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds an ADDITIONAL, single "Checkbox Trainer" validation (independent of the
 * Day 1-5 trainer_emp_no/trainer_name/validation_date/validation_time/overall_result
 * fields added by AddTrainerValidationToCLqcTrainingItemResultsTable). This
 * Checkbox Trainer is responsible for validating that all item row checkboxes
 * (is_checked) are ticked BEFORE Day 1-5 training begins. Like `date` and the
 * Day 1-5 trainer fields, these values are NOT day/row-scoped — the same
 * single verification is broadcast to every item row / day_number that shares
 * the same qc_slips_id + position by saveQcLqcTrainingItemsByPosition().
 */
class AddCheckboxTrainerValidationToCLqcTrainingItemResultsTable extends Migration
{
    public function up()
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->string('chk_trainer_emp_no')->nullable()->after('overall_result')->comment('Scanned Checkbox Trainer employee number');
            $table->string('chk_trainer_name')->nullable()->after('chk_trainer_emp_no')->comment('Resolved Checkbox Trainer name from employee lookup');
            $table->date('chk_validation_date')->nullable()->after('chk_trainer_name')->comment('Checkbox Trainer validation date for the matrix');
            $table->time('chk_validation_time')->nullable()->after('chk_validation_date')->comment('Checkbox Trainer validation time for the matrix');
        });
    }

    public function down()
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->dropColumn(['chk_trainer_emp_no', 'chk_trainer_name', 'chk_validation_date', 'chk_validation_time']);
        });
    }
}
