<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateP1PersonnelDisqualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p1_personnel_disqualifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('status')->default(1)->comment('1-Pending, 2-Done');
            $table->string('control_no')->nullable();
            $table->date('disqualification_date')->nullable();
            $table->date('recertification_date')->nullable();
            $table->string('disqualified_personnel')->nullable();
            $table->string('station')->nullable();
            $table->string('disqualification_reason')->nullable();
            $table->string('mode_of_defect')->nullable();
            $table->string('series_name')->nullable();
            $table->string('device_name')->nullable();
            $table->string('classification')->nullable();
            $table->string('c1_subclass')->comment('For Classification 1 Only')->nullable();

            // Define columns first
            $table->unsignedBigInteger('created_by')->nullable()->comment('db_rapidx.id');
            $table->unsignedBigInteger('last_updated_by')->nullable()->comment('db_rapidx.id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')
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
        Schema::dropIfExists('p1_personnel_disqualifications');
    }
}
