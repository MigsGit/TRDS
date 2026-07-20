<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceivedByToHrMemos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_memos', function (Blueprint $table) {
           $table->string('received_by')->nullable()->after('noted_by');
           $table->date('received_date')->nullable()->after('received_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_memos', function (Blueprint $table) {
            $table->dropColumn('received_by');
            $table->dropColumn('received_date');
        });
    }
}
