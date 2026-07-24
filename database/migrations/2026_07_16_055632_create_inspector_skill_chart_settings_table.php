<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectorSkillChartSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspector_skill_chart_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('section');
            $table->string('process_station');
            $table->string('product_line');
            $table->string('status')->default(0)->comment('0 - Active, 1 - Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspector_skill_chart_settings');
    }
}
