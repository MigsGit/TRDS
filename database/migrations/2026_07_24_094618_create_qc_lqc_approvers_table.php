<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQcLqcApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_lqc_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')
            ->constrained('qc_slips')
            ->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->string('approval_status')->default('PB')->default('APRODTO-A Production Training Orientation | BENGGTQ- B Engineer Training Qualification | CQCC-C Qc Certification | DPPDONLY-D PPD Update | EQCVP-E Qc Validation Process | FQCVVO-F Qc Validation Visual Operator | QCAPP-QC Supervisor Approval | OK-CLOSED');
            $table->string('decision_status')->default('PEN')->comment('PEN-Pending | APP-APPROVED | DIS-DISAPPROVED');
            $table->longText('first_approver')->nullable()->comment('A-Validatedby | B-Qualifiedby | C-Certifiedby | D-Prodn | E-ENG QC Validatedby | F-QC Validated by')->nullable();
            $table->longText('first_approver_2')->nullable();
            $table->date('first_date')->nullable();
            $table->date('first_date_2')->nullable();
            $table->time('first_time')->nullable();
            $table->string('first_status')->default('-')->comment('PEN-Pending | PA-PASSED | FA-FAILED');
            $table->string('first_status_2')->default('-')->comment('EVQC Status | PEN-Pending | PA-PASSED | FA-FAILED')->nullable();
            $table->longText('first_remarks')->nullable();
            $table->longText('first_approver_3')->comment('D- QC Certified')->nullable();
            $table->longText('second_approver')->nullable();
            $table->longText('second_approver_2')->nullable();
            $table->longText('second_date')->nullable();
            $table->longText('second_date_2')->nullable();
            $table->longText('second_time')->nullable();
            $table->string('second_status')->default('-')->comment('PEN-Pending | PA-PASSED | FA-FAILED')->nullable();
            $table->string('second_status_2')->default('-')->comment('EVQC Status | PEN-Pending | PA-PASSED | FA-FAILED')->nullable();
            $table->longText('second_remarks')->nullable();
            $table->longText('second_approver_3')->comment('D- QC Certified')->nullable();
            $table->longText('alert_prod_sec')->nullable();
            $table->longText('alert_prod_cc_sec')->nullable();
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
        Schema::dropIfExists('qc_lqc_approvers');
    }
}
