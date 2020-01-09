<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndividualTimeShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individual_time_shifts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('biometric_id', 8);
            $table->date('effectivity_date');
            $table->date('effective_until_date')->nullable();
            $table->time('expected_time_in');
            $table->time('expected_time_out');
            $table->timestamps();
            $table->foreign('biometric_id')
              ->references('biometric_id')
              ->on('users')
              ->onDelete('restrict')
              ->onUpdate('cascade');
            $table->unique(['biometric_id', 'effectivity_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('individual_time_shifts');
    }
}
