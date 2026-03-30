<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'training_request_details_id'  => 'required',
            'time_in'  => 'required',
            'time_out' => 'nullable|after:time_in',
        ];
    }
    public function messages()
    {
        return [
            'time_out.after' => 'The Time Out must be later than the Time In.',
        ];
    }
}
