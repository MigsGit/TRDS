<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingEndorsementEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_endorsement_employees', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('training_endorsement_id');
            $table->unsignedBigInteger('training_request_detail_id');
            $table->string('emp_no');
            $table->boolean('will_endorse')->nullable()->comment= "0 = will endorse, 1 = will not endorse";
            $table->longText('will_not_endorse_remarks')->nullable();
            // $table->string('hands_on_image')->nullable();
            $table->string('hands_on_filename')->nullable();
            $table->string('hands_on_filename_ext')->nullable();
            $table->string('hands_on_filename_ext')->nullable();
            $table->string('hands_on_rating')->nullable();
            $table->string('hands_on_remarks')->nullable();
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
        Schema::dropIfExists('training_endorsement_employees');
    }
}
