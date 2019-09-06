<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
      'biometric_record_id',
      'biometric_id',
      'biometric_name',
      'biometric_timestamp'
    ];
}
