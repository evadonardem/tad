<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonTimeShift extends Model
{
    protected $fillable = [
	    'effectivity_date',
		'expected_time_in',
		'expected_time_out'
	];
}
