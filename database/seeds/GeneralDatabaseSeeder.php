<?php

use Illuminate\Database\Seeder;
use App\User;

class GeneralDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::where('type', '')
          ->update([
              'type' => 'FACULTY'
          ]);
    }
}
