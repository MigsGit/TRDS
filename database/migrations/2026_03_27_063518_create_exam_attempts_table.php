<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_result_id')->comment = 'exam_results ID';
            $table->unsignedBigInteger('questionnaire_id')->comment = 'questionnaires ID';
            $table->unsignedTinyInteger('attempt')->nullable();
            $table->timestamps();

            $table->foreign('exam_result_id')->references('id')->on('exam_results');
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_attempts');
    }
}
