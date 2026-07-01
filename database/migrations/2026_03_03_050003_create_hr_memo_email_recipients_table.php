<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrMemoEmailRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_memo_email_recipients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('hr_memo_id')->comment('hr_memos.id');
            $table->unsignedBigInteger('user_id')->nullable()->comment('db_rapidx.id');
            $table->enum('type', ['to', 'cc']);
            $table->timestamps();

            // Foreign key to main table
            $table->foreign('hr_memo_id')
                ->references('id')
                ->on('hr_memos')
                ->onDelete('cascade');

            // Cross-database foreign key constraints
            $table->foreign('user_id')
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
        Schema::dropIfExists('hr_memo_email_recipients');
    }
}
