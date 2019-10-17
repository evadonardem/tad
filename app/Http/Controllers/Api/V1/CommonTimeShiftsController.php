<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\CommonTimeShift;
use App\Http\Requests\StoreCommonTimeShiftRequest;

class CommonTimeShiftsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commonTimeShifts = CommonTimeShift::orderBy('effectivity_date', 'desc')
            ->get();

        foreach ($commonTimeShifts as &$timeShift) {
            $count = AttendanceLog::whereDate(
                    'biometric_timestamp',
                    '>=',
                    $timeShift->effectivity_date . ' 00:00:00'
                )->get()->count();

            $timeShift->is_locked = $count > 0;
        }

        return response()->json(['data' => $commonTimeShifts]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommonTimeShiftRequest $request)
    {
        $attributes = $request->only([
            'type',
            'effectivity_date',
            'expected_time_in',
            'expected_time_out'
        ]);

        return CommonTimeShift::create($attributes);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return CommonTimeShift::where('id', '=', $id)->delete();
    }
}
