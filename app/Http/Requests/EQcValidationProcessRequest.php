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
            // 'text_application_vpqcs_oper' => ['required'],
             'text_first_result_vpqcs_oper' => ['required'],
             'text_1st_validatedby_vpqcs_oper' => ['required'],
             'text_1st_date_vpqcs_oper' => ['required'],
            //  'text_first_result_vpes_oper_2' => ['required'],
            //  'text_1st_validatedby_vpes_oper_2' => ['required'],
            //  'text_1st_date_vpes_oper_2' => ['required'],

            'text_second_result_vpqcs_oper'  => 'required_if:text_first_result_vpqcs_oper,FAILED',
            'text_2nd_validatedby_vpqcs_oper' => 'required_with:text_second_result_vpqcs_oper',
            'text_2nd_date_vpqcs_oper' => 'required_with:text_second_result_vpqcs_oper',
            'text_second_result_vpes_oper_2'  => 'required_if:text_first_result_vpes_oper_2,FAILED',
            'text_2nd_validatedby_vpes_oper_2' => 'required_with:text_second_result_vpes_oper_2',
            'text_2nd_date_vpes_oper_2' => 'required_with:text_second_result_vpes_oper_2',

        ];
    }
}
