<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QcSlipRequest extends FormRequest
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
            // 'select_section' => 'required',
            // 'text_select_position' => 'required',`
            'text_section_operator' => 'required',
            'text_series_operator' => 'required',
            'text_operator_product_line' => 'required',
            'text_certification_operator' => 'required|array',
            'transfer_flexibility' => [
                Rule::requiredIf(in_array(214, (array) $this->input('text_certification_operator'))),
            ],

            
        ];
    }
}
