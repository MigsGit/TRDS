<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AMhTrainingOrientationRequest extends FormRequest
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
            'text_mh_training_orientation'     => 'required',
            // 'text_mh_training_orientation.*'   => 'required|array',
            'text_mh_first_trained_by'   => 'required',
            // 'text_mh_first_trained_by.*' => 'required|array',
            'text_mh_first_date'                   => 'required|date',
        ];
    }
}
