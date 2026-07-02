<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAOperProdTrainingOrientationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_oper_prod_training_orientations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')
            ->nullable()
            ->constrained('qc_slips')
            ->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->foreignId('traning_items')
            ->nullable()
            ->constrained('dropdown_master_details')
            ->cascadeOnDelete()->comment('reference from dropdown_master_details_id');
            $table->string('defect_escalation')->nullable()->comment('1-Rule when to escalate | 2-Filling-up of forms');
            $table->string('production_abnormality')->nullable()->comment('1-Rule when to escalate | 2-Filling-up of forms');
            $table->string('orientation_docs')->nullable();
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
        Schema::dropIfExists('a_oper_prod_training_orientations');
    }
}
