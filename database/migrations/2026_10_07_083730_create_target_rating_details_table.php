<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetRatingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_rating_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('target_rating_id')->comment('target_rating_settings.id');
            $table->string('target_name')->nullable();
            $table->string('rating')->nullable();
            $table->timestamps();

            // Foreign key to main table
            $table->foreign('target_rating_id')
                ->references('id')
                ->on('target_rating_settings')
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
        Schema::dropIfExists('target_rating_details');
    }
}
