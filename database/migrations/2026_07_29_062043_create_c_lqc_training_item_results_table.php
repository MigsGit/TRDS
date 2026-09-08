<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCLqcTrainingItemResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_lqc_training_item_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_slips_id')->constrained('qc_slips')->cascadeOnDelete()->comment('reference from qc_slips_id');
            $table->foreignId('training_item_id')
                  ->constrained('dropdown_master_details')
                  ->cascadeOnDelete()->comment('reference from dropdown_master_details id');
            $table->unsignedTinyInteger('day_number'); // 1, 2, 3, 4, 5 
            $table->longText('result')->nullable(); // Typed input (e.g. "OK", "NG", "Passed")
            $table->longText('item_remark')->nullable(); // Overall row remark
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
        Schema::dropIfExists('c_lqc_training_item_results');
    }
}
