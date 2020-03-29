<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Models\AttendanceLog;

class FakeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AttendanceLog::whereNotNull('id')->delete();
        User::whereNotNull('id')->delete();
        $this->call(FakeUsersTableSeeder::class);
        $this->call(FakeAttendanceLogsTableSeeder::class);
    }
}
