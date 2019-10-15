<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualAttendanceLog extends Model
{
    protected $fillable = [
      'biometric_id',
      'log_date',
      'time_in',
      'time_out',
      'reason',
      'created_by'
    ];
}
