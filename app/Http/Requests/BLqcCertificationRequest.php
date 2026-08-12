<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BLqcCertificationRequest extends FormRequest
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
            'text_result_input1_inspector'=> ['required'],
            'text_hands_on_inspector'=> ['required'],
            'text_sec2_result_inspector'=> ['required'],
            'text_sec2_certified_inspector'=> ['required'],
            'text_sec2_date_inspector'=> ['required'],
            'text_sec2_time_inspector'=> ['required'],
        ];
    }
}
