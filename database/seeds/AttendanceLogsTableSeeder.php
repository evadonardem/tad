<?php

use Illuminate\Database\Seeder;
use App\Models\AttendanceLog;

class AttendanceLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AttendanceLog::class, 1000)->create();
    }
}
