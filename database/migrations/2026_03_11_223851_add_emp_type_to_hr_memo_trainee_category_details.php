<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmpTypeToHrMemoTraineeCategoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_memo_trainee_details', function (Blueprint $table) {
            $table->string('employment_type')->nullable()->after('hris_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_memo_trainee_details', function (Blueprint $table) {
            $table->dropColumn('employment_type');
        });
    }
}
