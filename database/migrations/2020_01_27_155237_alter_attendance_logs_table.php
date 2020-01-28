<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAttendanceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->foreign('biometric_id')
              ->references('biometric_id')
              ->on('users')
              ->onDelete('restrict')
              ->onUpdate('cascade');
            $table->index(['biometric_id', 'biometric_timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign(['biometric_id']);
            $table->dropIndex(['biometric_id', 'biometric_timestamp']);
        });
    }
}
