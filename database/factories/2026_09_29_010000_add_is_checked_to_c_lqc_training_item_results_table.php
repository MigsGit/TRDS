<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCheckedToCLqcTrainingItemResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->tinyInteger('is_checked')->nullable()->default(null)->after('item_remark')->comment('1 if checked, NULL if unticked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->dropColumn('is_checked');
            // $table->string('is_checked')->nullable()->after('virgin_material');
        });
    }
}
