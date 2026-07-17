<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AOperProdTrainingOrientationRequest extends FormRequest
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
           
            'text_training_orientation_ps_oper' => ['required'],
            'defect_escalation' => ['required'],
            'production_abnormality' => ['required'],
            'text_first_trainedby_oper' => ['required'],
            'text_first_mentoredby_oper' => ['required'],
            'text_first_date_oper' => ['required'],
            'text_first_time_oper' => ['required'],
            'text_first_a_prod_result' => ['required'],
        ];
    }
}
