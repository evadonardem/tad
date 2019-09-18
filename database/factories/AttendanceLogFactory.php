<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AttendanceLog;
use Faker\Generator as Faker;

$factory->define(AttendanceLog::class, function (Faker $faker) {
    return [
	    'biometric_id' => $faker->unique()->numberBetween(1, 1000000),
		'biometric_name' => $faker->lastName . ', ' . $faker->firstName,
        'biometric_timestamp' => $faker->dateTimeBetween($startDate = '-2 month', $endDate = 'now', $timezone = null)
    ];
});
