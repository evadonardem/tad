<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCommonTimeShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('common_time_shifts', function (Blueprint $table) {
            $table->dropUnique('common_time_shifts_type_effectivity_date_unique');
            $table->dropColumn('type');
            $table->string('role_id')
              ->nullable()
              ->after('id');
            $table->foreign('role_id')
              ->references('id')
              ->on('roles')
              ->onDelete('restrict')
              ->onUpdate('cascade');
            $table->unique(['role_id', 'effectivity_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('common_time_shifts', function (Blueprint $table) {
            $table->dropForeign('common_time_shifts_role_id_foreign');
            $table->dropUnique('common_time_shifts_role_id_effectivity_date_unique');
            $table->dropColumn('role_id');
            $table->string('type')->after('id');
            $table->unique(['type', 'effectivity_date']);
        });
    }
}
