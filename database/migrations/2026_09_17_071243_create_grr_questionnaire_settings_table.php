<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrrQuestionnaireSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grr_questionnaire_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('grr_setting_id');
            $table->integer('reference');
            $table->string('defect');
            $table->string('location');

            $table->string('status')
                ->default(0)
                ->comment('0 - Active, 1 - Inactive');

            $table->timestamps();

            $table->foreign('grr_setting_id')
                ->references('id')
                ->on('grr_settings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grr_questionnaire_settings');
    }
}
