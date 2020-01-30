<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceLogOverridesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_log_overrides', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('log_date');
            $table->string('role_id');
            $table->time('expected_time_in')->nullable()->default(null);
            $table->time('expected_time_out')->nullable()->default(null);
            $table->time('log_time_in')->nullable()->default(null);
            $table->time('log_time_out')->nullable()->default(null);
            $table->string('reason');
            $table->timestamps();
            $table->index(['log_date', 'role_id']);
            $table->unique(['log_date', 'role_id']);
            $table->foreign('role_id')
              ->references('id')
              ->on('roles')
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
        Schema::dropIfExists('attendance_log_overrides');
    }
}
