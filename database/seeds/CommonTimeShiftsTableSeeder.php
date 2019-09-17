<?php

use Illuminate\Database\Seeder;
use App\Models\CommonTimeShift;

class CommonTimeShiftsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CommonTimeShift::whereNull('effectivity_date')->delete();
        CommonTimeShift::create([
	        'expected_time_in' => '07:30',
			'expected_time_out' => '16:30'
		]);
    }
}
