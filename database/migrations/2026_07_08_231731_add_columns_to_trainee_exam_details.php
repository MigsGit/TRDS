<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTraineeExamDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_memo_trainee_category_details', function (Blueprint $table) {
            $table->date('date_start')->nullable()->after('trainee_details_id');
            $table->date('date_end')->nullable()->after('date_start');
            $table->string('objective')->nullable()->after('category');
            $table->string('trainor')->nullable()->comment('db_rapidx.employee_number')->after('objective');
            $table->string('mechanics')->nullable()->after('trainor');
            $table->string('type_of_training')->nullable()->after('mechanics');
            $table->string('training_venue')->nullable()->after('result');
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
            $table->dropColumn('date_start');
            $table->dropColumn('date_end');
            $table->dropColumn('objective');
            $table->dropColumn('trainor');
            $table->dropColumn('mechanics');
            $table->dropColumn('type_of_training');
            $table->dropColumn('training_venue');
        });
    }
}
