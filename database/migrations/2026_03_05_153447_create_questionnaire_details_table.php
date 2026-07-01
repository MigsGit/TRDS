<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionnaireDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('questionnaire_id')->comment ='questionnaires ID';
            $table->unsignedTinyInteger('revision')->default(0);
            $table->unsignedTinyInteger('category_type')->comment = '0-Single/Multiple Answer, 1-Identification/Essay, 2-Multiple Grid';
            $table->unsignedTinyInteger('points')->nullable();
            $table->string('type')->nullable();
            $table->unsignedTinyInteger('exam_no')->nullable();
            $table->string('image')->nullable();
            $table->longText('description')->nullable();
            $table->json('answer_choices_question')->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment = '0-active, 1-deactivate';
            $table->unsignedTinyInteger('logdel')->default(0)->comment = '0-Show, 1-Hide';
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            // Foreign Keys
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
        Schema::dropIfExists('questionnaire_details');
    }
}
