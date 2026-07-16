<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDPpdCertificationCompletionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('d_ppd_certification_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')->constrained('qc_slips')->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->tinyInteger('lot_1st_sample_peqcs_oper')->comment('1 Production Abnormality Control | 2 Defect Escalation Procedure');
            $table->tinyInteger('1st_injected_ng_peqcs_oper')->comment('1 Production Abnormality Control | 2 Defect Escalation Procedure');
            $table->tinyInteger('1st_detected_ng_peqcs_oper')->comment('1 Production Abnormality Control | 2 Defect Escalation Procedure');
            $table->tinyInteger('2nd_sample_peqcs_oper')->comment('1 Production Abnormality Control | 2 Defect Escalation Procedure');
            $table->tinyInteger('2nd_injected_ng_peqcs_oper')->comment('1 Production Abnormality Control | 2 Defect Escalation Procedure');
            $table->tinyInteger('2nd_detected_ng_peqcs_oper')->comment('1 Production Abnormality Control | 2 Defect Escalation Procedure');
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
        Schema::dropIfExists('d_ppd_certification_completions');
    }
}
