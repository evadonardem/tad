<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AttendanceLogOverride;
use App\Http\Requests\StoreAttendanceLogOverrideRequest;

class AttendanceLogOverrideController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $overrides = AttendanceLogOverride::orderBy('log_date', 'desc')->get();
        
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

        $roles = $request->input('roles');
        foreach ($roles as $role) {
            $attributes['role_id'] = $role;
            AttendanceLogOverride::create($attributes);
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
