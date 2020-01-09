<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndividualTimeShift extends Model
{
    protected $fillable = [
      'biometric_id',
      'effectivity_date',
      'expected_time_in',
      'expected_time_out'
    ];
}
