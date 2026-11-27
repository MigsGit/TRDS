<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCQcCertificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_qc_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')->constrained('qc_slips')->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->longText('obs_first_result_qcs_oper');
            $table->string('first_sample_qcs_oper');
            $table->string('first_ok_qcs_oper');
            $table->string('first_ng_qcs_oper');
            $table->longText('qcs_station_1st_oper');
            $table->longText('obs_second_result_qcs_oper')->nullable();
            $table->string('second_ok_qcs_oper')->nullable();
            $table->string('second_sample_qcs_oper')->nullable();
            $table->string('second_ng_qcs_oper')->nullable();
            $table->longText('qcs_station_2nd_oper')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('c_qc_certifications');
    }
}
