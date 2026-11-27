<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrrExamAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grr_exam_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_employee_id');
            $table->unsignedBigInteger('questionnaire_id');
            $table->unsignedTinyInteger('trial')->default(1)->comment('1 - Chronological, 2 - Even & Odd, 3 - High & Low');
            $table->text('answer');
            $table->timestamps();

            $table->unique(['exam_employee_id', 'trial', 'questionnaire_id'], 'grr_exam_answers_employee_trial_question_unique');

            $table->foreign('exam_employee_id')
                ->references('id')
                ->on('grr_exam_employees');

            $table->foreign('questionnaire_id')
                ->references('id')
                ->on('grr_questionnaire_settings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grr_exam_answers');
    }
}
