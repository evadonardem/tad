<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class TAD extends Controller
{
    public function dashboard()
    {
      return view('dashboard.index');
    }

    public function users()
    {
      return view('users.list');
    }

    public function attendanceLogs()
    {
      $months = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Aug',
        9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dec',
      ];
      $now = Carbon::now();
      $currentMonth = $now->format('n');
      $currentYear = $now->format('Y');

      return view('attendance.logs', [
        'months' => $months,
        'currentMonth' => $currentMonth,
        'currentYear' => $currentYear
      ]);
    }
}
