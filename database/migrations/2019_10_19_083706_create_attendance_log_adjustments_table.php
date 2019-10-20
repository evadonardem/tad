<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceLogAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_log_adjustments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('biometric_id', 8);
            $table->date('log_date');
            $table->decimal('adjustment_in_minutes', 8, 2);
            $table->string('reason');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index('biometric_id');
            $table->unique(['biometric_id', 'log_date']);
            $table->foreign('created_by')
              ->references('id')
              ->on('users')
              ->onDelete('restrict')
              ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_log_adjustments');
    }
}
