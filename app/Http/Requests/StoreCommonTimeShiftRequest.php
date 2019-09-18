<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
    public function rules()
    {
        return [
            'effectivity_date' => 'required|date|unique:common_time_shifts,effectivity_date|after:now',
            'expected_time_in' => 'required|date_format:H:i|before:expected_time_out',
            'expected_time_out' => 'required|date_format:H:i|after:expected_time_in'
        ];
    }
}
