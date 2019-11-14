<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Models\AttendanceLog;

class FakeAttendanceLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $users->each(function ($user) {
            factory(AttendanceLog::class, 100)->create([
                'biometric_id' => $user->biometric_id,
                'biometric_name' => $user->name
            ]);
        });
    }
}
