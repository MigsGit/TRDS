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
            // Relational Parent Links (Many-to-One Setup)
            // $table->foreignId('section_id')->constrained('section_id')->onDelete('restrict')->onUpdate('cascade');
            // $table->foreignId('product_line_id')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->string('section')->comment('systemone HRIS');
            $table->string('product_line')->comment('dropdown_maintenance_details');
            $table->string('series_name');
            $table->string('series_name');
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
