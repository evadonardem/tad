<?php

use Illuminate\Database\Seeder;
use App\User;
use Carbon\Carbon;

class FakeUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class, 10)->create();
        $users->each(function ($user) {
            $user->types()->create([
            'type' => 'FACULTY'
          ]);
        });
    }
}
