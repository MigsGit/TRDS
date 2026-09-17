<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCLqcTrainingItemResultsAddDateSubdescUnique extends Migration
{
    public function up()
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->date('date')->nullable()->after('day_number')->comment('Calendar date for this day slot');
            $table->string('sub_description')->nullable()->after('result')->comment('Inline note e.g. Part Prep / Visual Inspection');
            $table->unique(['qc_slips_id', 'training_item_id', 'day_number'], 'c_lqc_tir_slip_item_day_unique');
        });
    }

    public function down()
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->dropUnique('c_lqc_tir_slip_item_day_unique');
            $table->dropColumn(['date', 'sub_description']);
        });
    }
}
