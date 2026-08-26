<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TrainingAttendanceRequest extends FormRequest
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
            'rapidx_emp_no'  => 'required',
            'status'  => 'required',
            'training_request_details_id'  => 'required',
            'time_in'  => 'required_if:status,PRESENT',
            'time_out' => 'nullable|after:time_in',
            'remarks' => 'required_if:status,ABSENT|string',
        ];
    }
    public function messages()
    {
        return [
            'time_out.after' => 'The Time Out must be later than the Time In.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        // Get the first error message from the collection
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(response()->json([
            'message' => $firstError, // This puts the specific remark error here
            'errors'  => $validator->errors()
        ], 422));
    }
}
