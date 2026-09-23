<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->string('start_date');
            $table->string('end_date');
            $table->string('training_title');
            $table->string('trainer')->nullable()->comment = "Employee Number";
            $table->smallInteger('venue')->nullable()->comment = "fkid dropdown details";
            $table->smallInteger('type_of_training')->nullable()->comment = "fkid dropdown details";
            // $table->string('result')->nullable();
            $table->longText('objective')->nullable();
            $table->longText('remarks')->nullable();
            $table->string('attachments')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_records');
    }
}
