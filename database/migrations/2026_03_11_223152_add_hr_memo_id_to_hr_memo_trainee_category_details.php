<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHrMemoIdToHrMemoTraineeCategoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_memo_trainee_category_details', function (Blueprint $table) {
            $table->unsignedBigInteger('hr_memo_id')->comment('hr_memos.id')->after('id');

            // Foreign key to main table
            $table->foreign('hr_memo_id')
                ->references('id')
                ->on('hr_memos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_memo_trainee_category_details', function (Blueprint $table) {
            $table->dropColumn('hr_memo_id');
        });
    }
}
