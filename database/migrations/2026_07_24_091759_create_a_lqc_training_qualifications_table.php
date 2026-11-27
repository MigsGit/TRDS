<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateALqcTrainingQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_lqc_training_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')->constrained('qc_slips')->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->longText('training_orientation_inspector');
            $table->longText('training_orientation_ins_4')->nullable();
            $table->longText('training_orientation_ins_13')->nullable();
            $table->longText('training_orientation_ins_21')->nullable();
            $table->longText('training_orientation_ins_54')->nullable();
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
        Schema::dropIfExists('a_lqc_training_qualifications');
    }
}
