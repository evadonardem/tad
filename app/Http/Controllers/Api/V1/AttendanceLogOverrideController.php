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
        $attributes = [
          'log_date' => $request->input('override_date'),
          'reason' => $request->input('override_reason')
        ];

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

        $exceptUsers = $request->input('override_log_except_users');
        $overrideUsers = collect();
        $role = $request->input('role');
        
        $attributes['role_id'] = $role;
        AttendanceLogOverride::create($attributes);

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

            if ($filteredRole && $filteredRole->id == $role) {
                $overrideUsers->push($user);
            }
        }

        $overrideUsersChunks = $overrideUsers->chunk(100);
        foreach ($overrideUsersChunks as $overrideUsers) {
            foreach ($overrideUsers as $user) {
                if (isset($attributes['log_time_in'])) {
                    AttendanceLog::where([
                      'biometric_id' => $user->biometric_id,
                      'biometric_timestamp' => $attributes['log_date'] . ' ' . $attributes['log_time_in']
                    ])->delete();
                    AttendanceLog::create([
                      'biometric_id' => $user->biometric_id,
                      'biometric_name' => '#OVERRIDE#',
                      'biometric_timestamp' => $attributes['log_date'] . ' ' . $attributes['log_time_in']
                    ]);
                }

                if (isset($attributes['log_time_out'])) {
                    AttendanceLog::where([
                      'biometric_id' => $user->biometric_id,
                      'biometric_timestamp' => $attributes['log_date'] . ' ' . $attributes['log_time_out']
                    ])->delete();
                    AttendanceLog::create([
                      'biometric_id' => $user->biometric_id,
                      'biometric_name' => '#OVERRIDE#',
                      'biometric_timestamp' => $attributes['log_date'] . ' ' . $attributes['log_time_out']
                    ]);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
