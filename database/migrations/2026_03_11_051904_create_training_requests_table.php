<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    
    public function up()
    {
        Schema::create('training_requests', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('ctrl_number')->unique();
            $table->date('date_filed'); // use date
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('section_id');
            $table->integer('job_function')->unsigned();
            $table->integer('area_allocation')->unsigned();
            $table->integer('reason')->unsigned();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('section_head')->nullable();
            $table->integer('status')->unsigned()->default(0);
            $table->timestamps();
            $table->integer('logdel')->unsigned()->default(0)->comment('0-active, 1-inactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_requests');
    }
}
