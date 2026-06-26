<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQcSlipEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_slip_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slip_id')->constrained('qc_slips')->onDelete('cascade')->name('fk_slip_emp_slip_id');
            // $table->foreignId('employee_id')->constrained()->onDelete('restrict')->name('fk_slip_emp_emp_id');
            $table->string('employee_no');

            //  $table->foreignId('training_request_id')
            //     ->nullable()
            //     ->constrained('training_requests')
            //     ->cascadeOnDelete();
            // Pivot context metrics matching your structural HTML table cells
            $table->string('station_from');
            $table->string('station_to');
            $table->string('station_to');
            $table->string('first_take_ins_sequence');
            $table->string('first_take_ins_assessment_result');
            $table->string('second_take_ins_sequence');
            $table->string('second_take_ins_assessment_result');
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
        Schema::dropIfExists('qc_slip_employees');
    }
}
