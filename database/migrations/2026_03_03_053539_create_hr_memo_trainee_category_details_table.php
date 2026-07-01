<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrMemoTraineeCategoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_memo_trainee_category_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trainee_details_id')->comment('hr_memo_trainee_details.id');
            $table->string('category')->nullable();
            $table->string('result')->nullable();
            $table->string('training_remarks')->nullable();
            $table->timestamps();

            // Foreign key to main table
            $table->foreign('trainee_details_id')
                ->references('id')
                ->on('hr_memo_trainee_details')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_memo_trainee_category_details');
    }
}
