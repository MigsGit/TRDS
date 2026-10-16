<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoperoscRequest extends FormRequest
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
            'text_sec1_certified_operator' => ['required'],
            'text_sec1_date_operator' => ['required'],
            'text_sec1_time_operator' => ['required'],
            'text_sel_result1_operator' => ['required'],

        ];
    }
}
