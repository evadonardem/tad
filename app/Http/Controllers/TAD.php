<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TAD extends Controller
{
    public function users()
    {
      return view('users.list');
    }

    public function attendanceLogs()
    {
      return view('attendance.logs');
    }
}
