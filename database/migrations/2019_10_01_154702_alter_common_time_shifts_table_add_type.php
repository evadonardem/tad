<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCommonTimeShiftsTableAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('common_time_shifts', function (Blueprint $table) {
            $table->dropUnique('common_time_shifts_effectivity_date_unique');
            $table->string('type')->after('id');
            $table->unique(['type', 'effectivity_date']);
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
            $table->dropUnique('common_time_shifts_type_effectivity_date_unique');
            $table->dropColumn('type');
            $table->unique('effectivity_date');
        });
    }
}
