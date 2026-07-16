<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrMemoTraineeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_memo_trainee_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('hr_memo_id')->comment('hr_memos.id');
            $table->string('hris_id')->nullable();
            $table->string('employee_no');
            // $table->string('training_venue')->nullable();
            $table->date('endorsement_date')->nullable();
            $table->string('department')->nullable();
            $table->string('prod_allocation')->nullable();
            $table->timestamps();

            // Foreign key to main table
            $table->foreign('hr_memo_id')
                ->references('id')
                ->on('hr_memos')
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
        Schema::dropIfExists('hr_memo_trainee_details');
    }
}
