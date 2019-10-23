<?php

use Illuminate\Database\Seeder;
use App\User;
use Carbon\Carbon;

class GeneralDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        /*$users = $users->filter(function ($user) {
          return $user->types->count() === 0;
        });*/

        $users->each(function ($user) {
          /*$type = $user->types()->create([
            'type' => 'FACULTY'
          ]);*/
          $type = $user->types()->first();
          /*$firstLog = $user->attendanceLogs()
            ->orderBy('biometric_timestamp', 'asc')
            ->first();
          if($firstLog) {
            $type->created_at = $type->updated_at = Carbon::parse($firstLog->biometric_timestamp)
              ->format('Y-m-d H:i:s');
            $type->save();
          }*/
          $type->created_at = $type->updated_at = '2019-01-01 00:00:00';
          $type->save();
        });
    }
}
