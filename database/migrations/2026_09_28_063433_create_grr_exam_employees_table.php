<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrrExamEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grr_exam_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_grouping_id');
            $table->string('emp_id');
            
            $table->foreign('exam_grouping_id')
                ->references('id')
                ->on('grr_exam_settings');
            $table->string('status')
                ->default(0)
                ->comment('0 - Active, 1 - Inactive');
            
            $table->string('exam_status')
                ->default(0)
                ->comment('0 - Not Started, 1 - In Progress, 2 - Completed');

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
        Schema::dropIfExists('grr_exam_employees');
    }
}
