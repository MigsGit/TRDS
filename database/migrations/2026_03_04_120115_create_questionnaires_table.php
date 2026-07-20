<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionnairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('revision')->default(0);
            $table->unsignedTinyInteger('category')->comment = '0-Newly Hired, 1-Certification, 2-Re-Certification';
            $table->string('exam_title')->nullable();
            $table->string('description')->nullable();
            $table->longText('exam_instruction')->nullable();
            $table->string('purpose')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('product_line')->nullable();
            $table->unsignedTinyInteger('passing_score')->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment = '0-active, 1-deactivate';
            $table->unsignedTinyInteger('logdel')->default(0)->comment = '0-Show, 1-Hide';
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('questionnaires');
    }
}
