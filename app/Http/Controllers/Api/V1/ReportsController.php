<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Requests\GetAbsencesReportRequest;
use App\Http\Requests\GetLateUndertimeReportRequest;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Carbon\Carbon;
use App\User;
use App\Models\AttendanceLogAdjustment;
use App\Models\CommonTimeShift;
use App\Models\ManualAttendanceLog;

class ReportsController extends Controller
{
    use Helpers;

    public function lateUndertime(GetLateUndertimeReportRequest $request)
    {
        $type = $request->input('type');

        $startDate = Carbon::createFromFormat('Y-m-d', $request->input('start_date'))
          ->setTime(0, 0, 0);

        $endDate = Carbon::createFromFormat('Y-m-d', $request->input('end_date'))
          ->setTime(23, 59, 59);

        $biometricId = $type == 'individual'
          ? ($request->input('biometric_id') ? $request->input('biometric_id') : -1)
          : null;

        $users = $this->api->get('biometric/users');
        $users = $users['data'];

        if ($biometricId) {
          $users = array_filter($users, function ($user) use ($biometricId) {
            return $user['biometric_id'] == $biometricId;
          });
        }

        $queryParams = 'start_date=' . $startDate->format('Y-m-d') . '&end_date=' . $endDate->format('Y-m-d');
        $queryParams .= ($biometricId ? '&biometric_id=' . $biometricId : '');

        $attendanceLogs = $this->api->get('biometric/attendance-logs?' . $queryParams);
        $attendanceLogs = $attendanceLogs['data'];

        $report = [];

        while ($startDate <= $endDate) {
            $date = $startDate->format('Y-m-d');

            $expectedTimeInOut = $this->expectedTimeInOut($date);

            foreach ($users as $user) {
                $logs = array_filter($attendanceLogs, function ($log) use ($date, $user) {
                    $logDate = Carbon::createFromFormat('Y-m-d H:i:s', $log['biometric_timestamp'])->format('Y-m-d');
                    return $log['biometric_id'] == $user['biometric_id'] && $logDate == $date;
                });

                // only include users with time-in/out
                if (count($logs)>0) {
                    $logs = array_values($logs);
                    $timeIn = Carbon::createFromFormat('Y-m-d H:i:s', $logs[0]['biometric_timestamp']);

                    // for time-out pick first log in the afternoon
                    $timeOut = null;
                    foreach ($logs as $log) {
                        $logDate = Carbon::createFromFormat('Y-m-d H:i:s', $log['biometric_timestamp']);
                        if ($logDate->format('A') == 'PM') {
                            $timeOut = $logDate;
                            break;
                        }
                    }

                    $timeOut = $timeOut ?: Carbon::createFromFormat('Y-m-d H:i:s', $logs[count($logs)-1]['biometric_timestamp']);

                    // get user type base on time-in log date
                    $userObj = User::find($user['id']);
                    $userType = $userObj->types()
                        ->where('created_at', '<=', $timeIn->format('Y-m-d H:i:s'))
                        ->orderBy('created_at', 'desc')
                        ->first();
                    $userType = $userType->type;

                    $timeInInMinutes = (((int)$timeIn->format('H')) * 60 * 60
                        + ((int)$timeIn->format('i') * 60)
                        + (int)$timeIn->format('s')) / 60;
                    $timeOutInMinutes = (((int)$timeOut->format('H')) * 60 * 60
                        + ((int)$timeOut->format('i') * 60)
                        + (int)$timeOut->format('s')) / 60;

                    $late = $timeInInMinutes - $expectedTimeInOut[$userType]['expectedTimeInMinutes'];
                    $late = number_format($late > 0 ? $late : 0, 2);

                    $undertime = $expectedTimeInOut[$userType]['expectedTimeOutMinutes'] - $timeOutInMinutes;
                    $undertime = number_format($undertime > 0 ? $undertime : 0, 2);

                    $adjustment = 0;
                    $reason = '';
                    $isAdjusted = false;

                    $attendanceLogAdjustment = AttendanceLogAdjustment::where([
                        'biometric_id' => $user['biometric_id'],
                        'log_date' => $date
                    ])->first();

                    if ($attendanceLogAdjustment) {
                        $adjustment = $attendanceLogAdjustment->adjustment_in_minutes;
                        $reason = $attendanceLogAdjustment->reason;
                        $isAdjusted = true;
                    }

                    $totalLateUndertime = number_format($late + $undertime - $adjustment, 2);

                    $tmp = [
                        'biometric_id' => $user['biometric_id'],
                        'name' => $user['name'],
                        'type' => $userType,
                        'date' => $date,
                        'expected_time_in' => $expectedTimeInOut[$userType]['expectedTimeIn']->format('H:i:s'),
                        'expected_time_out' => $expectedTimeInOut[$userType]['expectedTimeOut']->format('H:i:s'),
                        'time_in' => $timeIn->format('H:i:s'),
                        'time_out' => $timeOut->format('H:i:s'),
                        'late_in_minutes' => $late,
                        'undertime_in_minutes' => $undertime,
                        'adjustment_in_minutes' => $adjustment,
                        'total_late_undertime_in_minutes' => $totalLateUndertime,
                        'reason' => $reason,
                        'is_adjusted' => $isAdjusted
                    ];

                    $report[] = $tmp;
                }
            }

            $startDate->addDay(1);
        }

        return response()->json(['data' => $report]);
    }

    public function absences(GetAbsencesReportRequest $request)
    {
        $type = $request->input('type');

        $startDate = Carbon::createFromFormat('Y-m-d', $request->input('start_date'))
          ->setTime(0, 0, 0);

        $endDate = Carbon::createFromFormat('Y-m-d', $request->input('end_date'))
          ->setTime(23, 59, 59);

        $biometricId = $type == 'individual'
          ? ($request->input('biometric_id') ? $request->input('biometric_id') : -1)
          : null;

        $users = $this->api->get('biometric/users');
        $users = $users['data'];

        if ($biometricId) {
          $users = array_filter($users, function ($user) use ($biometricId) {
            return $user['biometric_id'] == $biometricId;
          });
        }

        $queryParams = 'start_date=' . $startDate->format('Y-m-d') . '&end_date=' . $endDate->format('Y-m-d');
        $queryParams .= ($biometricId ? '&biometric_id=' . $biometricId : '');

        $attendanceLogs = $this->api->get('biometric/attendance-logs?' . $queryParams);
        $attendanceLogs = $attendanceLogs['data'];

        $report = [];

        while ($startDate <= $endDate) {

            if (
              $startDate >= Carbon::now() ||
              $startDate->isWeekend()
            ) {
              $startDate->addDay(1);
              continue;
            }

            $date = $startDate->format('Y-m-d');

            $expectedTimeInOut = $this->expectedTimeInOut($date);

            foreach ($users as $user) {
                $logs = array_filter($attendanceLogs, function ($log) use ($date, $user) {
                    $logDate = Carbon::createFromFormat('Y-m-d H:i:s', $log['biometric_timestamp'])->format('Y-m-d');
                    return $log['biometric_id'] == $user['biometric_id'] && $logDate == $date;
                });

                // only include users with time-in/out
                if (count($logs)==0) {
                    $logs = array_values($logs);

                    // get user type base on time-in log date
                    $userObj = User::find($user['id']);
                    $userType = $userObj->types()
                        ->where('created_at', '<=', $startDate->format('Y-m-d H:i:s'))
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if (!$userType) {
                      $userType = $userObj->types()
                          ->orderBy('created_at', 'asc')
                          ->first();
                    }

                    $userType = $userType->type;

                    // check manual attendance log entry
                    $manualAttendanceLog = ManualAttendanceLog::where([
                      'biometric_id' => $user['biometric_id'],
                      'log_date' => $date
                    ])->first();

                    $late = $undertime = $totalLateUndertime = null;

                    if ($manualAttendanceLog) {
                      $timeIn = Carbon::createFromFormat('Y-m-d H:i:s', $manualAttendanceLog->log_date . ' ' . $manualAttendanceLog->time_in);
                      $timeOut = Carbon::createFromFormat('Y-m-d H:i:s', $manualAttendanceLog->log_date . ' ' . $manualAttendanceLog->time_out);

                      $timeInInMinutes = (((int)$timeIn->format('H')) * 60 * 60
                          + ((int)$timeIn->format('i') * 60)
                          + (int)$timeIn->format('s')) / 60;
                      $timeOutInMinutes = (((int)$timeOut->format('H')) * 60 * 60
                          + ((int)$timeOut->format('i') * 60)
                          + (int)$timeOut->format('s')) / 60;

                      $late = $timeInInMinutes - $expectedTimeInOut[$userType]['expectedTimeInMinutes'];
                      $late = number_format($late > 0 ? $late : 0, 2);

                      $undertime = $expectedTimeInOut[$userType]['expectedTimeOutMinutes'] - $timeOutInMinutes;
                      $undertime = number_format($undertime > 0 ? $undertime : 0, 2);

                      $totalLateUndertime = number_format($late + $undertime, 2);
                    }

                    $tmp = [
                        'biometric_id' => $user['biometric_id'],
                        'name' => $user['name'],
                        'type' => $userType,
                        'date' => $date,
                        'expected_time_in' => $expectedTimeInOut[$userType]['expectedTimeIn']->format('H:i:s'),
                        'expected_time_out' => $expectedTimeInOut[$userType]['expectedTimeOut']->format('H:i:s'),
                        'time_in' => ($manualAttendanceLog) ? $manualAttendanceLog->time_in : null,
                        'time_out' => ($manualAttendanceLog) ? $manualAttendanceLog->time_out : null,
                        'late_in_minutes' => $late,
                        'undertime_in_minutes' => $undertime,
                        'total_late_undertime_in_minutes' => $totalLateUndertime,
                        'reason' => ($manualAttendanceLog) ? $manualAttendanceLog->reason : null
                    ];

                    $report[] = $tmp;
                }
            }

            $startDate->addDay(1);
        }

        return response()->json(['data' => $report]);
    }

    private function expectedTimeInOut($date)
    {
        $expectedTimeInOunt = [];
        foreach (['FACULTY', 'ADMIN'] as $type) {
          // fetch common time shift by date
          $commonTimeShiftModel = resolve(CommonTimeShift::class);
          $commonTimeShift = $commonTimeShiftModel
              ->where('type', '=', $type)
              ->whereNotNull('effectivity_date')
              ->whereDate('effectivity_date', '<=', $date)
              ->orderBy('effectivity_date', 'desc')
              ->get()
              ->first();

          if (!$commonTimeShift) {
              $commonTimeShiftModel = resolve(CommonTimeShift::class);
              $commonTimeShift = $commonTimeShiftModel
                  ->where('type', '=', $type)
                  ->whereNull('effectivity_date')
                  ->get()
                  ->first();
          }

          // set expected time-in/out
          $expectedTimeIn = Carbon::createFromFormat(
              'Y-m-d H:i:s',
              ($commonTimeShift->effectivity_date ?: $date) . ' ' . $commonTimeShift->expected_time_in
          );
          $expectedTimeOut = Carbon::createFromFormat(
              'Y-m-d H:i:s',
              ($commonTimeShift->effectivity_date ?: $date) . ' ' . $commonTimeShift->expected_time_out
          );

          $expectedTimeInMinutes = (((int)$expectedTimeIn->format('H')) * 60 * 60
              + ((int)$expectedTimeIn->format('i') * 60)
              + (int)$expectedTimeIn->format('s')) / 60;
          $expectedTimeOutMinutes = (((int)$expectedTimeOut->format('H')) * 60 * 60
              + ((int)$expectedTimeOut->format('i') * 60)
              + (int)$expectedTimeOut->format('s')) / 60;

          $expectedTimeInOut[$type] = [];
          $expectedTimeInOut[$type]['expectedTimeIn'] = $expectedTimeIn;
          $expectedTimeInOut[$type]['expectedTimeInMinutes'] = $expectedTimeInMinutes;
          $expectedTimeInOut[$type]['expectedTimeOut'] = $expectedTimeOut;
          $expectedTimeInOut[$type]['expectedTimeOutMinutes'] = $expectedTimeOutMinutes;
        }

        return $expectedTimeInOut;
    }
}
