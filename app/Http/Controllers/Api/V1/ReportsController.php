<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ZKLib\ZKLibrary;
use Dingo\Api\Routing\Helpers;
use Carbon\Carbon;

class ReportsController extends Controller
{
  use Helpers;

  private $zk = null;

  public function __construct()
  {
    $this->zk = new ZKLibrary(env('DEVICE_IP'), env('DEVICE_PORT'));
    $this->zk->connect();
  }

  public function lateUndertime(Request $request)
  {
    if($this->zk) {
      $type = $request->input('type');
      $year = $request->input('year') ?: Carbon::now()->format('Y');
      $month = $request->input('month');
      $period = $request->input('period') ?: 1;
      $biometricId = $type == 'individual' ?
        ($request->input('biometric_id') ? $request->input('biometric_id') : -1) 
        : null;

      $this->zk->disableDevice();

      $users = $this->api->get('biometric/users');
      $users = $users['data'];

      $queryParams = 'year=' . $year . '&month=' . $month;
      $queryParams .= ($type ? '&biometric_id=' . $biometricId : '');

      $attendanceLogs = $this->api->get('biometric/attendance-logs?' . $queryParams);
      $attendanceLogs = $attendanceLogs['data'];

      $this->zk->enableDevice();
      $this->zk->disconnect();

      $report = [];

      $startDate = Carbon::createFromDate($year, $month);
      if($period == 1) {
        $startDate = $startDate->startOfMonth();
        $endDate = (clone $startDate)->addDay(14);
      } else if ($period == 2) {
        $startDate = $startDate->startOfMonth()->addDay(15);
        $endDate = (clone $startDate)->endOfMonth();
      }

      // expected time-in/out (default) todo: create settings
      $expectedTimeIn = Carbon::now()
        ->setHour(7)
        ->setMinutes(30)
        ->setSecond(0);
      $expectedTimeOut = Carbon::now()
        ->setHour(16)
        ->setMinutes(30)
        ->setSecond(0);

      $expectedTimeInMinutes = (((int)$expectedTimeIn->format('H'))*60*60
        + ((int)$expectedTimeIn->format('i')*60)
        + (int)$expectedTimeIn->format('s')) / 60;

      $expectedTimeOutMinutes = (((int)$expectedTimeOut->format('H'))*60*60
        + ((int)$expectedTimeOut->format('i')*60)
        + (int)$expectedTimeOut->format('s')) / 60;

      while($startDate <= $endDate) {
        $date = $startDate->format('Y-m-d');
        foreach($users as $user) {

          $logs = array_filter($attendanceLogs, function($log) use ($date, $user) {
            $logDate = Carbon::createFromFormat('Y-m-d H:i:s', $log['biometric_timestamp'])->format('Y-m-d');
            return $log['biometric_id'] == $user['biometric_id'] && $logDate == $date;
          });

          // only include users with time-in/out
          if(count($logs)>0) {
            $logs = array_values($logs);
            $timeIn = Carbon::createFromFormat('Y-m-d H:i:s', $logs[0]['biometric_timestamp']);
            $timeOut = Carbon::createFromFormat('Y-m-d H:i:s', $logs[count($logs)-1]['biometric_timestamp']);

            $timeInInMinutes = (((int)$timeIn->format('H'))*60*60
              + ((int)$timeIn->format('i')*60)
              + (int)$timeIn->format('s')) / 60;
            $timeOutInMinutes = (((int)$timeOut->format('H'))*60*60
              + ((int)$timeOut->format('i')*60)
              + (int)$timeOut->format('s')) / 60;

            $late = $timeInInMinutes - $expectedTimeInMinutes;
            $late = number_format($late > 0 ? $late : 0, 2);

            $undertime = $expectedTimeOutMinutes - $timeOutInMinutes;
            $undertime = number_format($undertime > 0 ? $undertime : 0, 2);

            $totalLateUndertime = $late + $undertime;

            $tmp = [
              'biometric_id' => $user['biometric_id'],
              'name' => $user['name'],
              'date' => $date,
              'expected_time_in' => $expectedTimeIn->format('H:i:s'),
              'expected_time_out' => $expectedTimeOut->format('H:i:s'),
              'time_in' => $timeIn->format('H:i:s'),
              'time_out' => $timeOut->format('H:i:s'),
              'late_in_minutes' => $late,
              'undertime_in_minutes' => $undertime,
              'total_late_undertime_in_minutes' => $totalLateUndertime
            ];

            $report[] = $tmp;
          }
        }

        $startDate->addDay(1);
      }

      return response()->json(['data' => $report]);
    }

    return null;
  }
}
