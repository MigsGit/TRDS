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
            $table->id();
            $table->foreignId('training_request_details_id')
                ->nullable()
                ->constrained('training_request_details')
                ->cascadeOnDelete();
            $table->string('rapidx_emp_no');
            $table->time('date');
            $table->time('time_in');
            $table->time('time_out')->nullable();
            $table->string('status')->nullable()->default('PRESENT')->comment='PRESENT/ABSENT';
            $table->string('remarks')->nullable();
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
