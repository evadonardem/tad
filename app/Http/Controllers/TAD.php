<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class TAD extends Controller
{
    public function login()
    {
        return view('layouts.login');
    }

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

    public function reports()
    {
      return view('reports.index');
    }

    public function reportsLateUndertimeGroup()
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

      return view('reports.late-undertime-group', [
        'months' => $months,
        'currentMonth' => $currentMonth,
        'currentYear' => $currentYear
      ]);
    }

    public function reportsLateUndertimeIndividual()
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

        return view('reports.late-undertime-individual', [
          'months' => $months,
          'currentMonth' => $currentMonth,
          'currentYear' => $currentYear
    	  ]);
    }

    public function reportsAbsencesGroup()
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

        return view('reports.absences-group', [
          'months' => $months,
          'currentMonth' => $currentMonth,
          'currentYear' => $currentYear
        ]);
    }

    public function settings()
    {
    	return view('settings.index');
    }
}
