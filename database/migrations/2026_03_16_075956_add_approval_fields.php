<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_requests', function (Blueprint $table) {
            $table->timestamp('section_head_date')->nullable()->after('section_head');

            $table->string('received_by')->nullable()->after('section_head_date');
            $table->timestamp('received_date')->nullable()->after('received_by');

            $table->string('tu_head_approver')->nullable()->after('received_date');
            $table->timestamp('tu_head_approve_date')->nullable()->after('tu_head_approver');
        });
    }

    public function down()
    {
        Schema::table('training_requests', function (Blueprint $table) {
            $table->dropColumn([
                'section_head_date',
                'received_by',
                'received_date',
                'tu_head_approver',
                'tu_head_approve_date'
            ]);
        });
    }
}
