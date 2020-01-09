<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAttendanceLogAdjustments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_log_adjustments', function (Blueprint $table) {
            $table->dropColumn('adjustment_in_minutes');
            $table->unsignedInteger('adjustment_in_seconds')->after('log_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_log_adjustments', function (Blueprint $table) {
            $table->dropColumn('adjustment_in_seconds');
            $table->decimal('adjustment_in_minutes', 8, 2)->after('log_date');
        });
    }
}
