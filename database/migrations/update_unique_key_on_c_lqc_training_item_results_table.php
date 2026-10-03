<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            // Position & Checkbox status
            $table->string('position')->nullable()->after('item_remark')->comment('VisualOperator | Engineer | QC; NULL for legacy rows');
            $table->unsignedTinyInteger('is_checked')->nullable()->default(null)->after('position')->comment('1 if checked, NULL if unticked');

            // Day-level Trainer Validation
            $table->string('trainer_emp_no')->nullable()->after('is_checked')->comment('Scanned trainer employee number');
            $table->string('trainer_name')->nullable()->after('trainer_emp_no')->comment('Resolved trainer name from employee lookup');
            $table->date('validation_date')->nullable()->after('trainer_name')->comment('Trainer validation date for this day');
            $table->time('validation_time')->nullable()->after('validation_date')->comment('Trainer validation time for this day');
            $table->string('overall_result')->nullable()->after('validation_time')->comment('Passed | Failed');

            // Global Checkbox Trainer Validation
            $table->string('chk_trainer_emp_no')->nullable()->after('overall_result')->comment('Scanned Checkbox Trainer employee number');
            $table->string('chk_trainer_name')->nullable()->after('chk_trainer_emp_no')->comment('Resolved Checkbox Trainer name from employee lookup');
            $table->date('chk_validation_date')->nullable()->after('chk_trainer_name')->comment('Checkbox Trainer validation date for the matrix');
            $table->time('chk_validation_time')->nullable()->after('chk_validation_date')->comment('Checkbox Trainer validation time for the matrix');
            $table->string('chk_overall_result')->nullable()->after('chk_validation_time')->comment('Checkbox Trainer validation result for the matrix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('c_lqc_training_item_results');
    }
};