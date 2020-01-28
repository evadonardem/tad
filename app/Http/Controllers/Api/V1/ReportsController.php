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
use App\Models\AttendanceLogOverride;
use App\Models\CommonTimeShift;
use App\Models\ManualAttendanceLog;
use App\Models\Role;

class ReportsController extends Controller
{
    use Helpers;

    public function __construct()
    {
        set_time_limit(0);
    }

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

        if ($roleId = $request->input('role_id')) {
            $users = array_filter($users, function ($user) use ($roleId) {
                return $user['role'] == $roleId;
            });
        }

        $report = [];

        if (!empty($users)) {
            $biometricIds = array_column($users, 'biometric_id');
            $biometricIds = implode(',', $biometricIds);

            $queryParams = 'start_date=' . $startDate->format('Y-m-d') . '&end_date=' . $endDate->format('Y-m-d');
            $queryParams .= '&biometric_id=' . $biometricIds;

            $attendanceLogs = $this->api->get('biometric/attendance-logs?' . $queryParams);
            $attendanceLogs = $attendanceLogs['data'];

            while ($startDate <= $endDate) {
                $date = $startDate->format('Y-m-d');
                $dateDisplay = $startDate->format('D d-M-y');

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
                        $userRole = $userObj->roles()
                          ->where('user_roles.created_at', '<=', $timeIn->format('Y-m-d H:i:s'))
                          ->orderBy('user_roles.created_at', 'desc')
                          ->first();
                        $userRole = $userRole->id;

                        $timeInInSeconds = $this->timeInSeconds($timeIn);
                        $timeOutInSeconds = $this->timeInSeconds($timeOut);

                        $lateSeconds = $timeInInSeconds - $expectedTimeInOut[$userRole]['expectedTimeInSeconds'];
                        $lateSeconds = $lateSeconds > 0 ? $lateSeconds : 0;
                        $lateTimeDisplay = $this->formatTimeDisplay($lateSeconds);

                        $undertimeSeconds = $expectedTimeInOut[$userRole]['expectedTimeOutSeconds'] - $timeOutInSeconds;
                        $undertimeSeconds = $undertimeSeconds > 0 ? $undertimeSeconds : 0;
                        $undertimeTimeDisplay = $this->formatTimeDisplay($undertimeSeconds);

                        $adjustmentSeconds = 0;
                        $reason = $expectedTimeInOut[$userRole]['overrideReason'] . ' ';
                        $isAdjusted = false;

                        $attendanceLogAdjustment = AttendanceLogAdjustment::where([
                          'biometric_id' => $user['biometric_id'],
                          'log_date' => $date
                        ])->first();

                        if ($attendanceLogAdjustment) {
                            $adjustmentSeconds = $attendanceLogAdjustment->adjustment_in_seconds;
                            $reason .= $attendanceLogAdjustment->reason;
                            $isAdjusted = true;
                        }

                        $totalLateUndertimeSeconds = $lateSeconds + $undertimeSeconds - $adjustmentSeconds;
                        $adjustmentTimeDisplay = $this->formatTimeDisplay($adjustmentSeconds);
                        $totalLateUndertimeTimeDisplay = $this->formatTimeDisplay($totalLateUndertimeSeconds);

                        $tmp = [
                          'biometric_id' => $user['biometric_id'],
                          'name' => $user['name'],
                          'role_id' => $userRole,
                          'date' => $date,
                          'display_date' => $dateDisplay,
                          'expected_time_in' => $expectedTimeInOut[$userRole]['expectedTimeIn']->format('h:i:s A'),
                          'expected_time_out' => $expectedTimeInOut[$userRole]['expectedTimeOut']->format('h:i:s A'),
                          'time_in' => $timeIn->format('h:i:s A'),
                          'time_out' => $timeOut->format('h:i:s A'),
                          'late' => $lateTimeDisplay,
                          'undertime' => $undertimeTimeDisplay,
                          'adjustment' => $adjustmentTimeDisplay,
                          'total_late_undertime' => $totalLateUndertimeTimeDisplay,
                          'reason' => $reason,
                          'is_adjusted' => $isAdjusted
                      ];

                        $report[] = $tmp;
                    }
                }

                $startDate->addDay(1);
            }
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

        if ($roleId = $request->input('role_id')) {
            $users = array_filter($users, function ($user) use ($roleId) {
                return $user['role'] == $roleId;
            });
        }

        $report = [];

        if (!empty($users)) {
            $biometricIds = array_column($users, 'biometric_id');
            $biometricIds = implode(',', $biometricIds);

            $queryParams = 'start_date=' . $startDate->format('Y-m-d') . '&end_date=' . $endDate->format('Y-m-d');
            $queryParams .= '&biometric_id=' . $biometricIds;

            $attendanceLogs = $this->api->get('biometric/attendance-logs?' . $queryParams);
            $attendanceLogs = $attendanceLogs['data'];

            while ($startDate <= $endDate) {
                if (
                  $startDate >= Carbon::now() ||
                  $startDate->dayOfWeek == Carbon::SUNDAY
                ) {
                    $startDate->addDay(1);
                    continue;
                }

                $date = $startDate->format('Y-m-d');
                $dateDisplay = $startDate->format('D d-M-y');

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
                        $userRole = $userObj->roles()
                          ->where('user_roles.created_at', '<=', $startDate->format('Y-m-d H:i:s'))
                          ->orderBy('user_roles.created_at', 'desc')
                          ->first();

                        if (!$userRole) {
                            $userRole = $userObj->roles()
                            ->orderBy('user_roles.created_at', 'asc')
                            ->first();
                        }

                        $userRole = $userRole->id;

                        // check manual attendance log entry
                        $manualAttendanceLog = ManualAttendanceLog::where([
                        'biometric_id' => $user['biometric_id'],
                        'log_date' => $date
                      ])->first();

                        $timeIn = $timeOut = null;

                        $late = $undertime = $totalLateUndertime = null;

                        if ($manualAttendanceLog) {
                            $timeIn = Carbon::createFromFormat('Y-m-d H:i:s', $manualAttendanceLog->log_date . ' ' . $manualAttendanceLog->time_in);
                            $timeOut = Carbon::createFromFormat('Y-m-d H:i:s', $manualAttendanceLog->log_date . ' ' . $manualAttendanceLog->time_out);

                            $timeInInSeconds = $this->timeInSeconds($timeIn);
                            $timeOutInSeconds = $this->timeInSeconds($timeOut);

                            $lateSeconds = $timeInInSeconds - $expectedTimeInOut[$userRole]['expectedTimeInSeconds'];
                            $lateSeconds = $lateSeconds > 0 ? $lateSeconds : 0;
                            $late = $this->formatTimeDisplay($lateSeconds);

                            $undertimeSeconds = $expectedTimeInOut[$userRole]['expectedTimeOutSeconds'] - $timeOutInSeconds;
                            $undertimeSeconds = $undertimeSeconds > 0 ? $undertimeSeconds : 0;
                            $undertime = $this->formatTimeDisplay($undertimeSeconds);

                            $totalLateUndertimeSeconds = $lateSeconds + $undertimeSeconds;
                            $totalLateUndertime = $this->formatTimeDisplay($totalLateUndertimeSeconds);
                        }

                        $tmp = [
                          'biometric_id' => $user['biometric_id'],
                          'name' => $user['name'],
                          'role_id' => $userRole,
                          'date' => $date,
                          'display_date' => $dateDisplay,
                          'expected_time_in' => $expectedTimeInOut[$userRole]['expectedTimeIn']->format('h:i:s A'),
                          'expected_time_out' => $expectedTimeInOut[$userRole]['expectedTimeOut']->format('h:i:s A'),
                          'time_in' => ($timeIn) ? $timeIn->format('h:i:s A') : null,
                          'time_out' => ($timeOut) ? $timeOut->format('h:i:s A') : null,
                          'late' => $late,
                          'undertime' => $undertime,
                          'total_late_undertime' => $totalLateUndertime,
                          'reason' => ($manualAttendanceLog) ? $manualAttendanceLog->reason : null
                      ];

                        $report[] = $tmp;
                    }
                }

                $startDate->addDay(1);
            }
        }

        return response()->json(['data' => $report]);
    }

    private function expectedTimeInOut($date)
    {
        $roles = Role::all();
        $expectedTimeInOut = [];
        foreach ($roles as $role) {
            // fetch common time shift by date
            $commonTimeShiftModel = resolve(CommonTimeShift::class);
            $commonTimeShift = $commonTimeShiftModel
              ->where('role_id', '=', $role->id)
              ->whereNotNull('effectivity_date')
              ->whereDate('effectivity_date', '<=', $date)
              ->orderBy('effectivity_date', 'desc')
              ->get()
              ->first();

            if (!$commonTimeShift) {
                $commonTimeShiftModel = resolve(CommonTimeShift::class);
                $commonTimeShift = $commonTimeShiftModel
                  ->whereNull('role_id')
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

            // do override, if any
            $attendanceLogOverrideModel = resolve(AttendanceLogOverride::class);
            $attendanceLogOverride = $attendanceLogOverrideModel
              ->whereDate('log_date', $date)
              ->where('role_id', $role->id)
              ->get()
              ->first();
            $overrideReason = null;
            if ($attendanceLogOverride) {
                if ($attendanceLogOverride->expected_time_in) {
                    $expectedTimeIn = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $attendanceLogOverride->log_date . ' ' . $attendanceLogOverride->expected_time_in
                  );
                }
                if ($attendanceLogOverride->expected_time_out) {
                    $expectedTimeOut = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $attendanceLogOverride->log_date . ' ' . $attendanceLogOverride->expected_time_out
                  );
                }
                $overrideReason = $attendanceLogOverride->reason;
            }

            $expectedTimeInSeconds = (((int)$expectedTimeIn->format('H')) * 3600)
              + (((int)$expectedTimeIn->format('i')) * 60)
              + (int)$expectedTimeIn->format('s');
            $expectedTimeOutSeconds = (((int)$expectedTimeOut->format('H')) * 3600)
              + (((int)$expectedTimeOut->format('i')) * 60)
              + (int)$expectedTimeOut->format('s');

            $expectedTimeInOut[$role->id] = [];
            $expectedTimeInOut[$role->id]['expectedTimeIn'] = $expectedTimeIn;
            $expectedTimeInOut[$role->id]['expectedTimeInSeconds'] = $expectedTimeInSeconds;
            $expectedTimeInOut[$role->id]['expectedTimeOut'] = $expectedTimeOut;
            $expectedTimeInOut[$role->id]['expectedTimeOutSeconds'] = $expectedTimeOutSeconds;
            $expectedTimeInOut[$role->id]['overrideReason'] = $overrideReason;
        }

        return $expectedTimeInOut;
    }

    private function formatTimeDisplay($seconds)
    {
        $hours = floor($seconds / 3600) > 0
          ? floor($seconds / 3600)
          : 0;
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60) > 0
          ? floor($seconds / 60)
          : 0;
        $seconds -= $minutes * 60;
        $seconds = $seconds > 0 ? $seconds : 0;

        return str_pad($hours, 2, 0, STR_PAD_LEFT) . ':' .
          str_pad($minutes, 2, 0, STR_PAD_LEFT) . ':' .
          str_pad($seconds, 2, 0, STR_PAD_LEFT);
    }

    private function timeInSeconds($time)
    {
        return (((int)$time->format('H')) * 3600)
          + ((int)$time->format('i') * 60)
          + (int)$time->format('s');
    }
}
