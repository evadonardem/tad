<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreManualAttendanceLogRequest extends FormRequest
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
          'log_date' => ['required', 'date', 'before:today',
              Rule::unique('manual_attendance_logs')->where(function ($query) use ($request) {
                  $query->where('log_date', $request->input('log_date'))
                    ->where('biometric_id', $request->input('biometric_id'));
              })],
          'time_in' => 'required|date_format:H:i|before:time_out',
          'time_out' => 'required|date_format:H:i|after:time_in',
          'reason' => 'required'
        ];
    }
}
