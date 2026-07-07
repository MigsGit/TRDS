<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EQcValidationProcessRequest extends FormRequest
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
            'text_vpqcs_oper' => ['required'],
            'text_application_vpqcs_oper' => ['required'],
        ];
    }
}
