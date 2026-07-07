<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEQcValidationProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_qc_validation_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')->constrained('qc_slips')->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->tinyInteger('vpqcs_oper')->comment('1 Production Abnormality Control | 2 Defect Escalation Procedure');;
            $table->tinyInteger('application_vpqcs_oper')->comment('1 Production Abnormality Control | 2 Applicable | 3 Not Applicable |');
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
        Schema::dropIfExists('e_qc_validation_processes');
    }
}
