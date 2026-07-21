<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'rapidx_emp_id' => 'required',
            'rapidx_emp_no'=> 'required',
            'user_level_id'=> 'required',
            'systemone_emp_id'=> 'required',
        ];
    }
    public function messages()
    {
        return [
            'rapidx_emp_id' => 'Rapidx Employee Number is not match! Please contact the ISS to check the RAPIDX User Module!',
        ];
    }
}
