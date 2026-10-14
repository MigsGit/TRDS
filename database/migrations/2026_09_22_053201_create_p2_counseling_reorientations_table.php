<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateP2CounselingReorientationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p2_counseling_reorientations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('p1_id')->comment('p1_personnel_disqualifications.id');
            $table->unsignedTinyInteger('status')->default(1)->comment('1-Pending, 2-Done');
            $table->unsignedTinyInteger('training_category')->default(1)->comment('1-Operator & MH, 2-QC Inspector, 3-Technician');
            $table->date('counselling_date')->nullable();
            $table->string('conducted_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable()->comment('db_rapidx.id');
            $table->softDeletes();
            $table->timestamps();

            // Foreign key to main table
            $table->foreign('p1_id')
                ->references('id')
                ->on('p1_personnel_disqualifications')
                ->onDelete('cascade');

            $table->foreign('conducted_by')
                ->references('id')
                ->on('db_rapidx.users')
                ->onDelete('set null');

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
        Schema::dropIfExists('p2_counseling_reorientations');
    }
}
