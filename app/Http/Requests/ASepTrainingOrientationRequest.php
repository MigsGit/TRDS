<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ASepTrainingOrientationRequest extends FormRequest
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
            'text_sep_training_orientation'     => 'required|array',
            'text_sep_training_orientation.*'   => 'required|string',
            'text_a_sep_trained_certified_by'   => 'required|array',
            'text_a_sep_trained_certified_by.*' => 'required|string',
            'text_a_sep_date'                   => 'required|date',

            // B. CERTIFICATION
            'text_sep_theoretical_result'      => 'required|in:PASSED,FAILED',
            'text_sep_handson_result'          => 'required|in:PASSED,FAILED',
            'text_sep_trained_certified_by'    => 'required|array',
            'text_sep_trained_certified_by.*'  => 'required|string',
            'text_sep_date'                     => 'required|date',
        ];
    }
}
