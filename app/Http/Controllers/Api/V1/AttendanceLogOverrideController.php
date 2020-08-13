<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\AttendanceLogOverride;
use App\User;
use App\Http\Requests\StoreAttendanceLogOverrideRequest;
use Carbon\Carbon;

class AttendanceLogOverrideController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $overrides = AttendanceLogOverride::orderBy('log_date', 'desc')
          ->orderBy('role_id', 'asc')
          ->get();

        foreach ($overrides as $override) {
            foreach (['expected_time_in', 'expected_time_out', 'log_time_in', 'log_time_out'] as $type) {
                if (is_null($override->{$type})) {
                    $override->{$type} = 'N/A';
                } else {
                    $override->{$type} = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $override->log_date . ' ' . $override->{$type}
                      )->format('h:i:s A');
                }
            }
        }

        return response()->json(['data' => $overrides]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAttendanceLogOverrideRequest $request)
    {
        $attributes = $this->prepareAttributes($request);
        AttendanceLogOverride::create($attributes);
        $this->overrideLogs($request, $attributes);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreAttendanceLogOverrideRequest $request, $id)
    {
        $attendanceLogOverride = AttendanceLogOverride::find($id);
        $this->revokeOverridenLogs($attendanceLogOverride);

        // reset before update
        $attendanceLogOverride->expected_time_in = null;
        $attendanceLogOverride->expected_time_out = null;

        $attendanceLogOverride->log_time_in = null;
        $attendanceLogOverride->log_time_out = null;

        $attendanceLogOverride->save();
        
        // update
        $attributes = $this->prepareAttributes($request, true, $attendanceLogOverride->log_date, $attendanceLogOverride->role_id);
        AttendanceLogOverride::where('id', $id)->update($attributes);
        $this->overrideLogs($request, $attributes);
        
        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attendanceLogOverride = AttendanceLogOverride::find($id);
        $this->revokeOverridenLogs($attendanceLogOverride);
        $attendanceLogOverride->delete();

        return response()->noContent();
    }
    
    private function prepareAttributes($request, $isEdit = false, $logDate = null, $roleId = null)
    {
        $attributes = [];

        if (!$isEdit) {
            $attributes['role_id'] = $request->input('role');
            $attributes['log_date'] =  $request->input('override_date');
        } else {
            $attributes['log_date'] = $logDate;
            $attributes['role_id'] = $roleId;
        }

        if ($request->input('do_override_expected')) {
            $overrideExpectedType = $request->input('override_expected');
            if ($overrideExpectedType == 'time_in_and_out') {
                $attributes['expected_time_in'] = $request->input('override_expected_time_in');
                $attributes['expected_time_out'] = $request->input('override_expected_time_out');
            } elseif ($overrideExpectedType == 'time_in_only') {
                $attributes['expected_time_in'] = $request->input('override_expected_time_in');
            } else {
                $attributes['expected_time_out'] = $request->input('override_expected_time_out');
            }
        }

        if ($request->input('do_override_log')) {
            $overrideLogType = $request->input('override_log');
            if ($overrideLogType == 'time_in_and_out') {
                $attributes['log_time_in'] = $request->input('override_log_time_in');
                $attributes['log_time_out'] = $request->input('override_log_time_out');
            } elseif ($overrideLogType == 'time_in_only') {
                $attributes['log_time_in'] = $request->input('override_log_time_in');
            } else {
                $attributes['log_time_out'] = $request->input('override_log_time_out');
            }
        }

        $attributes['reason'] = $request->input('override_reason');

        return $attributes;
    }

    private function overrideLogs($request, $attributes)
    {
        $exceptUsers = $request->input('override_log_except_users');
        $overrideUsers = collect();

        $users = User::with('roles');
        if ($exceptUsers) {
            $users->whereNotIn('biometric_id', $exceptUsers);
        }
        $users = $users->get();

        foreach ($users as $user) {
            $filteredRole = null;
            if (isset($attributes['log_time_in'])) {
                $filteredRole = $user->roles()
                    ->where(
                    'user_roles.created_at',
                    '<=',
                    $attributes['log_date'] . ' ' . $attributes['log_time_in']
                    )
                    ->orderBy('user_roles.created_at', 'desc')
                    ->first();
            } elseif (isset($attributes['log_time_out'])) {
                $filteredRole = $user->roles()
                    ->where(
                    'user_roles.created_at',
                    '<=',
                    $attributes['log_date'] . ' ' . $attributes['log_time_out']
                    )
                    ->orderBy('user_roles.created_at', 'desc')
                    ->first();
            }

            if ($filteredRole && $filteredRole->id == $attributes['role_id']) {
                $overrideUsers->push($user);
            }
        }

        $overrideUsersChunks = $overrideUsers->chunk(100);
        foreach ($overrideUsersChunks as $overrideUsers) {
            foreach ($overrideUsers as $user) {
                if (isset($attributes['log_time_in'])) {
                    $similarAttendanceLog = AttendanceLog::where([
                      'biometric_id' => $user->biometric_id,
                      'biometric_timestamp' => $attributes['log_date'] . ' ' . $attributes['log_time_in']
                    ])->first();
                    if (!$similarAttendanceLog) {
                        AttendanceLog::create([
                            'biometric_id' => $user->biometric_id,
                            'biometric_name' => '#OVERRIDE#',
                            'biometric_timestamp' => $attributes['log_date'] . ' ' . $attributes['log_time_in']
                        ]);
                    }
                }

                if (isset($attributes['log_time_out'])) {
                    $similarAttendanceLog = AttendanceLog::where([
                      'biometric_id' => $user->biometric_id,
                      'biometric_timestamp' => $attributes['log_date'] . ' ' . $attributes['log_time_out']
                    ])->first();
                    if (!$similarAttendanceLog) {
                        AttendanceLog::create([
                            'biometric_id' => $user->biometric_id,
                            'biometric_name' => '#OVERRIDE#',
                            'biometric_timestamp' => $attributes['log_date'] . ' ' . $attributes['log_time_out']
                        ]);
                    }
                }
            }
        }
    }

    private function revokeOverridenLogs($attendanceLogOverride) {
        $overrideUsers = collect();
        $role = $attendanceLogOverride->role_id;
        $users = User::with('roles')->get();

        foreach ($users as $user) {
            $filteredRole = null;
            if (isset($attendanceLogOverride->log_time_in)) {
                $filteredRole = $user->roles()
                    ->where(
                        'user_roles.created_at',
                        '<=',
                        $attendanceLogOverride->log_date . ' ' . $attendanceLogOverride->log_time_in
                    )
                    ->orderBy('user_roles.created_at', 'desc')
                    ->first();
            } elseif (isset($attendanceLogOverride->log_time_out)) {
                $filteredRole = $user->roles()
                    ->where(
                        'user_roles.created_at',
                        '<=',
                        $attendanceLogOverride->log_date . ' ' . $attendanceLogOverride->log_time_out
                    )
                    ->orderBy('user_roles.created_at', 'desc')
                    ->first();
            }

            if ($filteredRole && $filteredRole->id == $role) {
                $overrideUsers->push($user);
            }
        }

        $overrideUsersChunks = $overrideUsers->chunk(100);
        foreach ($overrideUsersChunks as $overrideUsers) {
            foreach ($overrideUsers as $user) {
                if (isset($attendanceLogOverride->log_time_in)) {
                    AttendanceLog::where([
                      'biometric_id' => $user->biometric_id,
                      'biometric_timestamp' => $attendanceLogOverride->log_date . ' ' . $attendanceLogOverride->log_time_in,
                      'biometric_name' => '#OVERRIDE#',
                    ])->delete();
                }
                if (isset($attendanceLogOverride->log_time_out)) {
                    AttendanceLog::where([
                        'biometric_id' => $user->biometric_id,
                        'biometric_timestamp' => $attendanceLogOverride->log_date . ' ' . $attendanceLogOverride->log_time_out,
                        'biometric_name' => '#OVERRIDE#',
                    ])->delete();
                }
            }
        }
    }
}
