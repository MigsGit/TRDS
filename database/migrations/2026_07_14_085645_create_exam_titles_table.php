<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_titles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('exam_title')->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment = '0-active, 1-deactivate';
            $table->unsignedTinyInteger('logdel')->default(0)->comment = '0-Show, 1-Hide';
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
        Schema::dropIfExists('exam_titles');
    }
}
