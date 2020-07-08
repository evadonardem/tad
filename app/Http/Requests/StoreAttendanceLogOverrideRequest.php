<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreAttendanceLogOverrideRequest extends FormRequest
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
        $rules = [
            'override_date' => 'required',
            'role' => 'required',
            'override_reason' => 'required'
        ];

        if ($request->input('do_override_expected')) {
            $overrideExpectedType = $request->input('override_expected');
            $rules['override_expected'] = 'required';
            if ($overrideExpectedType == 'time_in_and_out') {
                $rules['override_expected_time_in'] = 'required';
                $rules['override_expected_time_out'] = 'required';
            } elseif ($overrideExpectedType == 'time_in_only') {
                $rules['override_expected_time_in'] = 'required';
            } else {
                $rules['override_expected_time_out'] = 'required';
            }
        }

        if ($request->input('do_override_log')) {
            $overrideLogType = $request->input('override_log');
            $rules['override_log'] = 'required';
            if ($overrideLogType == 'time_in_and_out') {
                $rules['override_log_time_in'] = 'required';
                $rules['override_log_time_out'] = 'required';
            } elseif ($overrideLogType == 'time_in_only') {
                $rules['override_log_time_in'] = 'required';
            } else {
                $rules['override_log_time_out'] = 'required';
            }
        }

        return $rules;
    }
}
