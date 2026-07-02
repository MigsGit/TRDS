<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')
            ->constrained('qc_slips')
            ->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->longText('first_trainedby_oper');
            $table->longText('first_mentoredby_oper');
            $table->longText('first_date_oper');
            $table->longText('first_time_oper');
            $table->longText('second_trainedby_oper');
            $table->longText('second_mentoredby_oper');
            $table->longText('second_date_oper');
            $table->longText('second_time_oper');
            $table->longText('alert_prod_sec');
            $table->longText('alert_prod_cc_sec');
            $table->string('first_status')->default('-')->comment('PEN-Pending | PA-PASSED | FA-FAILED');
            $table->string('second_status')->default('-')->comment('PEN-Pending | PA-PASSED | FA-FAILED');
            $table->string('approval_status')->default('PB');
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
        Schema::dropIfExists('op_approvers');
    }
}
