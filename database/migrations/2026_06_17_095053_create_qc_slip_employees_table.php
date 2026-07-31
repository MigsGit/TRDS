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
            $table->foreignId('qc_slips_id')->constrained('qc_slips')->onDelete('cascade')->name('fk_slip_emp_slip_id');
            $table->string('employee_no');
            // Pivot context metrics matching your structural HTML table cells
            $table->foreignId('station_from')->references('id')->on('dropdown_master_details')->comment ='id from dropdown_master_details';
            $table->foreignId('station_to')->references('id')->on('dropdown_master_details')->comment ='id from dropdown_master_details';
            $table->string('first_take_ins_sequence')->comment('FOR VISUAL INSPECTOR ONLY')->nullable();
            $table->string('first_take_ins_assessment_result')->comment('FOR VISUAL INSPECTOR ONLY')->nullable();
            $table->string('second_take_ins_sequence')->comment('FOR VISUAL INSPECTOR ONLY')->nullable();
            $table->string('second_take_ins_assessment_result')->comment('FOR VISUAL INSPECTOR ONLY')->nullable();
            $table->longText('remarks')->nullable();
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
