<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetRatingSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_rating_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('status')->default(1)->comment('1-Active, 0-Inactive');
            $table->string('section')->nullable();
            $table->string('position')->nullable();
            $table->unsignedBigInteger('product_line')->comment('dropdown_master_details.id');
            $table->unsignedBigInteger('created_by')->nullable()->comment('db_rapidx.id');
            $table->softDeletes();
            $table->timestamps();

            // Foreign key to main table
            $table->foreign('product_line')
                ->references('id')
                ->on('dropdown_master_details')
                ->onDelete('cascade');
                
            $table->foreign('created_by')
                ->references('id')
                ->on('db_rapidx.users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('target_rating_settings');
    }
}
