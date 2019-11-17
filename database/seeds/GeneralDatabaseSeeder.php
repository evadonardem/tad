<?php

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
          'ADMIN' => '',
          'FACULTY' => ''
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
    }
}
