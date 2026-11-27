<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingEndorsementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_endorsements', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('0 - pending, 1 - for Approval, 2 - Approved checker, 3 - approved approver');
            $table->unsignedBigInteger('training_request_id');
            $table->unsignedBigInteger('hr_memo_id');
            $table->string('ctrl_no')->nullable()->unique();
            $table->string('date')->nullable();
            $table->longText('disapprove_remarks')->nullable();
            $table->string('disapprove_by')->nullable();
            $table->string('mail_cc')->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('training_endorsements');
    }
}
