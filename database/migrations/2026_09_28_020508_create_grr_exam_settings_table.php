<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrrExamSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grr_exam_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grr_setting_id');
            $table->string('ctrl_number')->nullable();
            $table->string('section')->nullable();
            // $table->string('emp_id')->nullable();
            $table->string('description')->nullable();
            $table->string('purpose')->nullable();
            $table->string('instructions')->nullable();

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
        Schema::dropIfExists('grr_exam_settings');
    }
}
