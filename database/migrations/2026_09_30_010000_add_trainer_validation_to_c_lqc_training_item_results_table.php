<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds embedded Trainer Barcode Validation fields (Day 1-5) to
 * c_lqc_training_item_results. These fields are day-scoped (like the
 * existing `date` column) and are broadcast to every item row that shares
 * the same qc_slips_id + day_number + position by
 * saveQcLqcTrainingItemsByPosition().
 */
class AddTrainerValidationToCLqcTrainingItemResultsTable extends Migration
{
    public function up()
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->string('trainer_emp_no')->nullable()->after('is_checked')->comment('Scanned trainer employee number');
            $table->string('trainer_name')->nullable()->after('trainer_emp_no')->comment('Resolved trainer name from employee lookup');
            $table->date('validation_date')->nullable()->after('trainer_name')->comment('Trainer validation date for this day');
            $table->time('validation_time')->nullable()->after('validation_date')->comment('Trainer validation time for this day');
            $table->string('overall_result')->nullable()->after('validation_time')->comment('Passed | Failed');
        });
    }

    public function down()
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->dropColumn(['trainer_emp_no', 'trainer_name', 'validation_date', 'validation_time', 'overall_result']);
        });
    }
}
