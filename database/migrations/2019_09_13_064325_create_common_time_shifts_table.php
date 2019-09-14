<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonTimeShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_time_shifts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('effectivity_date')->unique()->nullable();
            $table->time('expected_time_in');
            $table->time('expected_time_out');
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
        Schema::dropIfExists('common_time_shifts');
    }
}
