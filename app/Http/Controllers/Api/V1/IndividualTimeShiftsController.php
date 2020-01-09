<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\IndividualTimeShift;
use App\Http\Requests\StoreIndividualTimeShiftRequest;
use Carbon\Carbon;

class IndividualTimeShiftsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $individualTimeShifts = IndividualTimeShift::orderBy(
          'effectivity_date',
          'desc'
        )
        ->whereNull('effective_until_date')
        ->get();

        return response()->json(['data' => $individualTimeShifts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIndividualTimeShiftRequest $request)
    {
        $attributes = $request->only([
            'biometric_id',
            'effectivity_date',
            'expected_time_in',
            'expected_time_out'
        ]);

        IndividualTimeShift::where([
            'biometric_id' => $attributes['biometric_id'],
          ])
          ->whereNull('effective_until_date')
          ->update([
          'effective_until_date' => Carbon::createFromFormat(
              'Y-m-d',
              $attributes['effectivity_date']
            )
            ->subDays(1)
            ->format('Y-m-d')
        ]);

        return IndividualTimeShift::create($attributes);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
