<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateP1EmailRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p1_email_recipients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('p1_id')->comment('p1_personnel_disqualifications.id');
            $table->unsignedBigInteger('rapidx_id')->nullable()->comment('db_rapidx.id');
            $table->enum('type', ['to', 'cc']);
            $table->timestamps();

            // Foreign key to main table
            $table->foreign('p1_id')
                ->references('id')
                ->on('p1_personnel_disqualifications')
                ->onDelete('cascade');

            // Cross-database foreign key constraints
            $table->foreign('rapidx_id')
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
        Schema::dropIfExists('p1_email_recipients');
    }
}
