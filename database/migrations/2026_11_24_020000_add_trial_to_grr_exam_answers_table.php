<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrialToGrrExamAnswersTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('grr_exam_answers', 'trial')) {
            return;
        }

        Schema::table('grr_exam_answers', function (Blueprint $table) {
            $table->unsignedTinyInteger('trial')->default(1)->after('questionnaire_id')
                ->comment('1 - Chronological, 2 - Even & Odd, 3 - High & Low');
        });

        // Add the new key first: the FK on exam_employee_id needs a usable index while the old one is dropped.
        Schema::table('grr_exam_answers', function (Blueprint $table) {
            $table->unique(['exam_employee_id', 'trial', 'questionnaire_id'], 'grr_exam_answers_employee_trial_question_unique');
        });

        Schema::table('grr_exam_answers', function (Blueprint $table) {
            $table->dropUnique('grr_exam_answers_employee_question_unique');
        });
    }

    public function down()
    {
        if (!Schema::hasColumn('grr_exam_answers', 'trial')) {
            return;
        }

        Schema::table('grr_exam_answers', function (Blueprint $table) {
            $table->unique(['exam_employee_id', 'questionnaire_id'], 'grr_exam_answers_employee_question_unique');
        });

        Schema::table('grr_exam_answers', function (Blueprint $table) {
            $table->dropUnique('grr_exam_answers_employee_trial_question_unique');
            $table->dropColumn('trial');
        });
    }
}
