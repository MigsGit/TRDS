<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQcSlipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_slips', function (Blueprint $table) {

            $table->id();
            $table->string('control_no')->unique()->index();
            $table->string('status')->default('PB')->comment('1 PB-Preparedby | ');
            $table->string('approval_status')->default('PB')->comment('1 PB-Preparedby | ');
            $table->string('section_category');
            $table->string('position_category');
            $table->string('section')->comment('systemone HRIS');
            // $table->string('product_line')->comment('dropdown_maintenance_details');
            $table->foreignId('product_line')
            ->constrained('dropdown_master_details')
            ->cascadeOnDelete()->comment('reference from dropdown_maintenance_details_id');
            $table->string('series_name');
            $table->string('oper_approved_confirmed_by')->nullable()->comment('Last Approver | systemone HRIS');
            $table->dateTime('appproval_at')->nullable();
            $table->string('created_by')->comment('systemone HRIS');
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
        Schema::dropIfExists('qc_slips');
    }
}
