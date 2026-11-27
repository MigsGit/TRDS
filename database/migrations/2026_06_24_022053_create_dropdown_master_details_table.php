<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDropdownMasterDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dropdown_master_details', function (Blueprint $table) {
                $table->id();
            $table->tinyInteger('status')->nullable()->default(1)->comment('1-Active | 2 -Deactivate');
            $table->foreignId('dropdown_masters_id')->references('id')->on('dropdown_masters')->comment ='id from dropdown_masters';
            $table->string('dropdown_masters_details');
            $table->string('remarks')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        // Schema::dropIfExists('dropdown_master_details');
        // Turn off foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('dropdown_master_details');

        // Turn foreign key constraints back on
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
