<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrrSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('grr_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('section');
            $table->string('grr_no');
            $table->string('grr_sample');
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
        Schema::dropIfExists('grr_settings');
    }
}
