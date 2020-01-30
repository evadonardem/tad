<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLogOverride extends Model
{
    protected $fillable = [
      'log_date',
      'role_id',
      'expected_time_in',
      'expected_time_out',
      'log_time_in',
      'log_time_out',
      'reason'
    ];
}
