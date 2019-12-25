<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreCommonTimeShiftRequest extends FormRequest
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
            'effectivity_date' => ['required', 'date', 'after:now',
                Rule::unique('common_time_shifts')->where(function ($query) use ($request) {
                    $query->where('effectivity_date', $request->input('effectivity_date'))
                      ->where('role_id', $request->input('role_id'));
                })],
            'expected_time_in' => 'required|date_format:H:i|before:expected_time_out',
            'expected_time_out' => 'required|date_format:H:i|after:expected_time_in',
            'role_id' => 'required'
        ];
    }
}
