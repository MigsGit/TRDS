<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQcReasonCertificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_reason_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')
            ->nullable()
            ->constrained('qc_slips')
            ->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->longText('reason_of_certification')
            ->comment('reference from dropdown_master_details_id');
            $table->longText('transfer_flexibility')->nullable();
            $table->longText('others')->nullable();
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
        Schema::dropIfExists('qc_reason_certifications');
    }
}
