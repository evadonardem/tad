<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreAdjustmentLateUndertimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
          'biometric_id' => 'required',
          'log_date' => ['required', 'date', 'before:now',
              Rule::unique('manual_attendance_logs')->where(function ($query) use ($request) {
                  $query->where('log_date', $request->input('log_date'))
                    ->where('biometric_id', $request->input('biometric_id'));
              })],
          'adjustment_in_minutes' => 'required|numeric|gte:0',
          'total_late_undertime_in_minutes' => 'required|numeric|gte:0',
          'reason' => 'required'
        ];
    }
}
