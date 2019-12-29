<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdjustmentLateUndertimeRequest;
use App\Models\AttendanceLogAdjustment;
use Auth;

class AdjustmentLateUndertimeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdjustmentLateUndertimeRequest $request)
    {
        $fields = $request->only([
          'biometric_id',
          'log_date',
          'adjustment',
          'reason'
        ]);

        $adjustmentTime = explode(':', $fields['adjustment']);
        $adjustmentSeconds = $adjustmentTime[0] * 3600
          + $adjustmentTime[1] * 60
          + $adjustmentTime[2];
        $fields['adjustment_in_seconds'] = $adjustmentSeconds;
        $fields['created_by'] = Auth::user()->id;

        return AttendanceLogAdjustment::create($fields);
    }
}
