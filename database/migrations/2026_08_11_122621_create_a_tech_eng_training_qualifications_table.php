<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateATechEngTrainingQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_tech_eng_training_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')->constrained('qc_slips')->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->longText('text_es_tech_training_orientation')->nullable();
            $table->longText('text_es_tech_training_orientation_14');
            $table->longText('text_es_tech_training_orientation_15')->nullable();
            $table->longText('text_es_tech_training_orientation_16')->nullable();
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
        Schema::dropIfExists('a_tech_eng_training_qualifications');
    }
}
