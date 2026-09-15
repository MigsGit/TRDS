<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MachineOperatorRequest extends FormRequest
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
            'text_first_result_vpes_oper_2' => ['required'],
            'text_1st_validatedby_vpes_oper_2' => ['required'],
            'text_1st_date_vpes_oper_2' => ['required'],
        ];
    }
}
