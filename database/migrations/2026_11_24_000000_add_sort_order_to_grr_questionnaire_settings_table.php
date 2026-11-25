<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSortOrderToGrrQuestionnaireSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grr_questionnaire_settings', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('location')
                ->comment('Display order within a GRR sample, starting at 1. 0 = not sequenced (inactive)');

            $table->index(['grr_setting_id', 'sort_order'], 'grr_questionnaire_sample_order_index');
        });

        // Backfill: keep the current (id) order, numbered 1..n per GRR sample.
        // Inactive items (status 1) stay at 0 and are not part of the sequence.
        $positions = [];
        DB::table('grr_questionnaire_settings')
            ->where('status', 0)
            ->orderBy('grr_setting_id')
            ->orderBy('id')
            ->get(['id', 'grr_setting_id'])
            ->each(function ($row) use (&$positions) {
                $positions[$row->grr_setting_id] = ($positions[$row->grr_setting_id] ?? 0) + 1;

                DB::table('grr_questionnaire_settings')
                    ->where('id', $row->id)
                    ->update(['sort_order' => $positions[$row->grr_setting_id]]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grr_questionnaire_settings', function (Blueprint $table) {
            $table->dropIndex('grr_questionnaire_sample_order_index');
            $table->dropColumn('sort_order');
        });
    }
}
