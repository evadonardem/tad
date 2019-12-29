<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLogAdjustment extends Model
{
    protected $fillable = [
      'biometric_id',
      'log_date',
      'adjustment_in_seconds',
      'reason',
      'created_by'
    ];
}
