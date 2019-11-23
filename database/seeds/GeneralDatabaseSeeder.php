<?php

use App\User;
use Illuminate\Database\Seeder;
use App\Models\Role;
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
        $roles = [
          'TEACHING' => '',
          'NON-TEACHING' => '',
          'MAINTENANCE' => '',
          'INTEGRATION FACILITATOR (PHOENIX)' => ''
        ];

        foreach ($roles as $id => $description) {
            $role = Role::where('id', $id)->first();
            if (!$role) {
                Role::create([
                  'id' => $id,
                  'description' => $description
                ]);
            }
        }

        $users = User::all();
        $users->each(function ($user) {
            $user->roles()->sync([
              'TEACHING' => [
                'created_at' => '1970-02-02',
                'updated_at' => '1970-02-02'
              ]
            ]);
        });
    }
}
