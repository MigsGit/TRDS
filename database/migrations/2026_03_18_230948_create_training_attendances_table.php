<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_attendances', function (Blueprint $table) {
            //NOTE: present = scan, manual the absent, other status
            //FAQS: Manual din ba ang pag create ng attendance or automatic get sa training request details?
            $table->id();
            $table->foreignId('training_request_details_id')
                ->nullable()
                ->constrained('training_request_details')
                ->cascadeOnDelete();
            $table->string('status')->nullable()->comment='PRESENT/ABSENT/DONE';
            $table->string('section')->comment = 'CN/TS/YF/PPS';
            $table->int('training_hours')->nullable();
            $table->int('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_attendances');
    }
}
