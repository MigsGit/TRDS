<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBOpEnggSectionTrainingOrientationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b_op_engg_section_training_orientations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')
            ->constrained('qc_slips')
            ->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->longText('traning_items')->comment('reference from dropdown_master_details_id');
            $table->longText('engg_orientation_docs');
            $table->string('obs_first_result_es_oper');
            $table->integer('first_sample_es_oper');
            $table->integer('first_ok_es_oper');
            $table->integer('first_ng_es_oper');
            $table->string('obs_second_result_es_oper')->nullable();
            $table->integer('second_sample_es_oper')->nullable();
            $table->integer('second_ok_es_oper')->nullable();
            $table->integer('second_ng_es_oper')->nullable();
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
        Schema::dropIfExists('b_op_engg_section_training_orientations');
    }
}
