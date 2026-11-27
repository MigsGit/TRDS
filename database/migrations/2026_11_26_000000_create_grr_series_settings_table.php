<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrrSeriesSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('grr_series_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('section');
            $table->string('series_name');

            $table->string('status')
                ->default(0)
                ->comment('0 - Active, 1 - Inactive');

            $table->timestamps();

            $table->unique(['section', 'series_name'], 'grr_series_settings_section_series_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('grr_series_settings');
    }
}
