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
            ->nullable()
            ->constrained('qc_slips')
            ->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->string('employee_no');

            
            $table->string('status')->default('-')->comment('PEN-Pending | PA-PASSED | FA-FAILED');
            $table->string('approval_status')->default('PB');
            $table->longText('remarks')->nullable();
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
