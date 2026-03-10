<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('systemone_user_id');
            $table->unsignedBigInteger('rapidx_emp_no');
            $table->unsignedTinyInteger('status')->default(1)->comment = '1-active,2-inactive';
            $table->foreignId('user_level_id')->references('id')->on('user_levels')->comment ='User Levels Id';
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign Key
            // $table->foreign('user_level_id')->references('id')->on('user_levels');
            // $table->foreign('created_by')->references('id')->on('users');
            // $table->foreign('last_updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
