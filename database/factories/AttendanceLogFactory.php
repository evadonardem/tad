<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AttendanceLog;
use Faker\Generator as Faker;

$factory->define(AttendanceLog::class, function (Faker $faker) {
    return [
      'biometric_timestamp' => $faker->unique()
          ->dateTimeBetween(
              $startDate = '-12 month',
              $endDate = 'now',
              $timezone = null
          )
    ];
});
