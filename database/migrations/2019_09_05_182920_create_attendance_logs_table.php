<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('biometric_record_id');
            $table->char('biometric_id', 8);
            $table->string('biometric_name', 28);
            $table->dateTime('biometric_timestamp');
            $table->timestamps();

            $table->unique(['biometric_id', 'biometric_timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_logs');
    }
}
