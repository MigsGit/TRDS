<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DPpdCertificationCompletionRequest extends FormRequest
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
            'lot_1st_sample_peqcs_opery' => ['required'],
            '1st_injected_ng_peqcs_opery' => ['required'],
            '1st_detected_ng_peqcs_opery' => ['required'],
        ];
    }
}
