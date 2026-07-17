<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FQcValidationRequest extends FormRequest
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
            'refdocno_input_qcvvo_oper' => 'required',
            'refdocno_input_qcvvo_oper_2' => 'required',
            'text_validated1_qcvvo_oper' => 'required',
            'text_date1_qcvvo_oper' => 'required',
            'text_obs_first_result_es_oper' => 'required',
        ];
    }
}
