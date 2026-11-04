<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOpApproverRequest extends FormRequest
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
        // return [
        //     'qc_slips_id'      => 'required|integer|exists:qc_slips,id',
        //     'alert_prod_sec'   => 'required|array|min:1',
        //     'alert_prod_sec.*' => 'required|string|max:50',
        // ];
        return [
            'qc_slips_id'      => 'required',
            'alert_prod_sec'   => 'required',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'qc_slips_id.required'      => 'QC slip is required.',
            'alert_prod_sec.required'   => 'Please select at least one employee.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
