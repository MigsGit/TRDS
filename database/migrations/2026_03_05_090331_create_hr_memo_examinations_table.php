<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrMemoExaminationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_memo_examinations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('examination_name');
            $table->text('description')->nullable();
            $table->string('status')->default(0)->comment('0 - Active, 1 - Inactive');
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
        Schema::dropIfExists('hr_memo_examinations');
    }
}
