<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_request_id')
                ->nullable()
                ->constrained('training_requests')
                ->cascadeOnDelete();

            $table->foreignId('training_memo_doc_id')
                ->nullable()
                ->constrained('hr_memos')
                ->cascadeOnDelete();

            $table->foreignId('hr_memo_trainee_details_id')
                ->nullable()
                ->constrained('hr_memo_trainee_details')
                ->cascadeOnDelete();

            $table->string('emp_no')->nullable();
            $table->date('date_hired')->nullable();

            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('section')->nullable();

            $table->string('training_title')->nullable();
            $table->string('training_result')->nullable();
            $table->string('remarks')->nullable();

            $table->string('training_venue')->nullable();
            $table->date('training_endorsement_date')->nullable();

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
        Schema::dropIfExists('training_request_details');
    }
}
