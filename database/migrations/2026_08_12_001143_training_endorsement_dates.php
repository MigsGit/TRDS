<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrainingEndorsementDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_endorsements', function (Blueprint $table) {
            $table->string('hr_to_tu')->nullable()->after('mail_cc');
            $table->string('op_tu_training_date_from')->nullable()->after('hr_to_tu');
            $table->string('op_tu_training_date_to')->nullable()->after('op_tu_training_date_from');
            $table->string('op_tu_endorsement_to_req')->nullable()->after('op_tu_training_date_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_endorsements', function (Blueprint $table) {
            //
        });
    }
}
