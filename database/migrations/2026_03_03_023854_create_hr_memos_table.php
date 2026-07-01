<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_memos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('status')->default(1)->comment('1-Pending, 2-Done');
            $table->string('document_no');
            $table->string('classification')->comment('1-Direct, 2-Subcon');
            $table->string('reason')->nullable();
            $table->string('from')->nullable();

            // sub table
            // $table->string('to')->nullable();
            // $table->string('cc')->nullable();

            $table->string('subject')->nullable();
            $table->date('date_filed')->nullable();
            $table->unsignedBigInteger('prepared_by')->nullable()->comment('db_rapidx.id');
            $table->unsignedBigInteger('noted_by')->nullable()->comment('db_rapidx.id');

            // Define columns first
            $table->unsignedBigInteger('created_by')->nullable()->comment('db_rapidx.id');
            $table->unsignedBigInteger('last_updated_by')->nullable()->comment('db_rapidx.id');

            // Cross-database foreign key constraints
            $table->foreign('prepared_by')
                ->references('id')
                ->on('db_rapidx.users')
                ->onDelete('set null');

            $table->foreign('noted_by')
                ->references('id')
                ->on('db_rapidx.users')
                ->onDelete('set null');

            $table->foreign('created_by')
                ->references('id')
                ->on('db_rapidx.users')
                ->onDelete('set null');

            $table->foreign('last_updated_by')
                ->references('id')
                ->on('db_rapidx.users')
                ->onDelete('set null');

            $table->softDeletes();
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
        Schema::dropIfExists('hr_memos');
    }
}
