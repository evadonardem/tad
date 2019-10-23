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
        $users = $users->filter(function ($user) {
          return $user->types->count() === 0;
        });

        $users->each(function ($user) {
          $type = $user->types()->create([
            'type' => 'FACULTY'
          ]);
          $type->created_at = $type->updated_at = '2019-01-01 00:00:00';
          $type->save();
        });
    }
}
