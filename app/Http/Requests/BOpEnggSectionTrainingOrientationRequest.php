<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BOpEnggSectionTrainingOrientationRequest extends FormRequest
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
            'text_training_orientation_es_oper' => ['required'],
            'text_obs_first_result_es_oper' => ['required'],
            'text_first_sample_es_oper' => ['required','int','min:1'],
            'text_first_ok_es_oper' => ['required','int','min:1'],
            'text_first_ng_es_oper' => ['required','int','min:1'],
        ];
    }
}
