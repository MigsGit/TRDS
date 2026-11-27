<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamResultDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_result_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_result_id')->comment = 'exam_results ID';
            $table->unsignedBigInteger('questionnaire_id')->comment = 'questionnaires ID';
            $table->unsignedBigInteger('questionnaire_revision_no')->nullable()->comment ='questionnaires revision no';
            $table->json('questionnaire')->nullable();
            $table->json('questionnaire_details')->nullable();
            $table->json('exam_result')->nullable();
            $table->unsignedTinyInteger('identification_essay_score')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('remark')->nullable();
            $table->date('date_examination')->nullable();
            $table->unsignedTinyInteger('exam_result_status')->default(0)->comment = '0-For Review, 1-Completed';
            $table->unsignedTinyInteger('attempt')->comment = 'Conditional: 1st attempt = 70-99%, 2nd Attempt = 90-99%, 3rd Attempt = 100%';
            $table->unsignedTinyInteger('status')->default(0)->comment = '0-active, 1-deactivate';
            $table->unsignedTinyInteger('logdel')->default(0)->comment = '0-Show, 1-Hide';
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
        Schema::dropIfExists('exam_result_details');
    }
}
