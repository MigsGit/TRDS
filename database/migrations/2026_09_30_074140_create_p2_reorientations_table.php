<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateP2ReorientationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p2_reorientations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('p2_id')->comment('p2_counseling_reorientations.id');
            $table->unsignedTinyInteger('status')->default(1)->comment('1-Pending, 2-Done');
            $table->string('training_category')->nullable();
            $table->string('training_scope')->nullable();
            $table->date('training_date')->nullable();
            $table->string('trained_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable()->comment('db_rapidx.id');
            $table->softDeletes();
            $table->timestamps();

            // Foreign key to main table
            $table->foreign('p2_id')
                ->references('id')
                ->on('p2_counseling_reorientations')
                ->onDelete('cascade');

            $table->foreign('last_updated_by')
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
        Schema::dropIfExists('p2_reorientations');
    }
}
