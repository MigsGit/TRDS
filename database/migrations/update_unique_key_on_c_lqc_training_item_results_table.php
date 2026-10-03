<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            // 1. Drop foreign keys that rely on the old unique index
            // (Adjust foreign key names if yours differ in schema)
            $table->dropForeign(['qc_slips_id']);
            $table->dropForeign(['training_item_id']);

            // 2. Drop the old 3-column unique index
            $table->dropUnique('c_lqc_tir_slip_item_day_unique');

            // 3. Create the new 4-column unique index including position
            $table->unique(
                ['qc_slips_id', 'training_item_id', 'day_number', 'position'],
                'c_lqc_tir_slip_item_day_pos_unique'
            );

            // 4. Re-add the foreign key constraints
            $table->foreign('qc_slips_id')
                  ->references('id')
                  ->on('qc_slips')
                  ->onDelete('cascade');

            $table->foreign('training_item_id')
                  ->references('id')
                  ->on('c_lqc_training_items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c_lqc_training_item_results', function (Blueprint $table) {
            $table->dropForeign(['qc_slips_id']);
            $table->dropForeign(['training_item_id']);

            $table->dropUnique('c_lqc_tir_slip_item_day_pos_unique');

            $table->unique(
                ['qc_slips_id', 'training_item_id', 'day_number'],
                'c_lqc_tir_slip_item_day_unique'
            );

            $table->foreign('qc_slips_id')
                  ->references('id')
                  ->on('qc_slips')
                  ->onDelete('cascade');

            $table->foreign('training_item_id')
                  ->references('id')
                  ->on('c_lqc_training_items')
                  ->onDelete('cascade');
        });
    }
};