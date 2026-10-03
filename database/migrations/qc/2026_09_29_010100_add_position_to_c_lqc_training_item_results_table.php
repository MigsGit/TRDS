<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionAndIsCheckedToCLqcTrainingItemResultsTable extends Migration
{ //add_position_and_is_checked_to_c_lqc_training_item_results_table
    public function up()
    {
        // 1. Add new columns
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->string('position')->nullable()->after('training_item_id')
                ->comment('Production | Engineer | QC; NULL for legacy rows');
            
            $table->unsignedTinyInteger('is_checked')->nullable()->default(null)->after('position')
                ->comment('1 if checked, NULL if unticked');
        });

        // // 2. Drop old unique constraint and add new composite unique constraint
        // Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
        //     $table->dropUnique('c_lqc_tir_slip_item_day_unique');
        //     $table->unique(
        //         ['qc_slips_id', 'training_item_id', 'day_number', 'position'],
        //         'c_lqc_tir_slip_item_day_position_unique'
        //     );
        // });
    }

    public function down()
    {
        // 1. Restore old unique constraint
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->dropUnique('c_lqc_tir_slip_item_day_position_unique');
            $table->unique(
                ['qc_slips_id', 'training_item_id', 'day_number'],
                'c_lqc_tir_slip_item_day_unique'
            );
        });

        // 2. Drop both added columns
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->dropColumn(['position', 'is_checked']);
        });
    }
}