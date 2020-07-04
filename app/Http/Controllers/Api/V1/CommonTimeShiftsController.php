<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\CommonTimeShift;
use App\Http\Requests\StoreCommonTimeShiftRequest;
use Carbon\Carbon;

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
                    ($timeShift->effectivity_date ?: Carbon::now()->format('Y-m-d')) . ' 00:00:00'
                )->get()->count();

            $timeShift->is_locked = $count > 0;
            $effectivityDate = $timeShift->effectivity_date ?: Carbon::now()->format('Y-m-d');

            $timeShift->expected_time_in = Carbon::createFromFormat(
                  'Y-m-d H:i:s',
                  $effectivityDate . ' ' . $timeShift->expected_time_in
                )->format('h:i:s A');
            $timeShift->expected_time_out = Carbon::createFromFormat(
                  'Y-m-d H:i:s',
                  $effectivityDate . ' ' . $timeShift->expected_time_out
                )->format('h:i:s A');
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
            'role_id',
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
