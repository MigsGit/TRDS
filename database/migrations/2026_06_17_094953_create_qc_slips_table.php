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
            $table->string('section');
            $table->string('product_line');
            $table->string('destination/station')->comment('dropdown_maintenance_details');
            // $table->foreignId('dropdown_maintenance_details')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->string('series_name');
            $table->longText('reason')->comment('dropdown_maintenance_details');  // Evaluated from your exact form option items
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
