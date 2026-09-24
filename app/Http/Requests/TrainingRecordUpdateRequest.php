<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingRecordUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $u_m_access = explode(',', session('global_user')->user_modules_id);

        if(in_array('21', $u_m_access)){ // 21 - Training Record Update on table user_module
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(isset($this->editing_record_id)){
            return [
                'editing_record_id' => 'required|integer',
                'start_date'        => 'required|date',
                'end_date'          => 'required|date|after_or_equal:start_date',
                'training_title'    => 'required|string|max:255',
                'trainer'           => 'nullable|array|max:255',
                'venue'             => 'nullable|string|max:255',
                'type_of_training'  => 'nullable|string|max:255',
                // 'result'            => 'nullable|string|max:255',
                'objective'         => 'nullable|string|max:255',
                'remarks'           => 'nullable|string|max:255',
                'trainee'           => 'nullable',
            ];
        }
        else{
            return [
                'start_date'       => 'required|date',
                'end_date'         => 'required|date|after_or_equal:start_date',
                'training_title'   => 'required|string|max:255',
                'trainer'          => 'nullable|array|max:255',
                'venue'            => 'nullable|string|max:255',
                'type_of_training' => 'nullable|string|max:255',
                // 'result'           => 'nullable|string|max:255',
                'objective'        => 'nullable|string|max:255',
                'remarks'          => 'nullable|string|max:255',
                'trainee'          => 'nullable',
            ];
        }
    }
}
