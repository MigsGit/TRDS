<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rating_description_id')->comment('rating_description_settings.id');
            $table->string('level')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            // Foreign key to main table
            $table->foreign('rating_description_id')
                ->references('id')
                ->on('rating_description_settings')
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
        Schema::dropIfExists('rating_details');
    }
}
