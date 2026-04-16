<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingEndorsementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_endorsements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_request_id');
            $table->unsignedBigInteger('hr_memo_id');
            $table->string('ctrl_no')->nullable();
            $table->string('date')->nullable();
            $table->string('mail_cc')->nullable();
            $table->string('created_by');
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
        Schema::dropIfExists('training_endorsements');
    }
}
