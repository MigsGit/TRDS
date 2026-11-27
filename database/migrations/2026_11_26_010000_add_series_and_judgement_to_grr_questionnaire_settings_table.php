<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeriesAndJudgementToGrrQuestionnaireSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('grr_questionnaire_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('grr_questionnaire_settings', 'series_id')) {
                $table->unsignedBigInteger('series_id')->nullable()->after('grr_setting_id');
                $table->foreign('series_id', 'grr_questionnaire_settings_series_id_foreign')
                    ->references('id')
                    ->on('grr_series_settings');
            }

            if (!Schema::hasColumn('grr_questionnaire_settings', 'judgement')) {
                $table->string('judgement')->nullable()->after('location')
                    ->comment('Good, Marginal Good, NG, Marginal NG');
            }
        });
    }

    public function down()
    {
        Schema::table('grr_questionnaire_settings', function (Blueprint $table) {
            if (Schema::hasColumn('grr_questionnaire_settings', 'series_id')) {
                $table->dropForeign('grr_questionnaire_settings_series_id_foreign');
                $table->dropColumn('series_id');
            }

            if (Schema::hasColumn('grr_questionnaire_settings', 'judgement')) {
                $table->dropColumn('judgement');
            }
        });
    }
}
