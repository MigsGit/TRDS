<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingEndorsementRequest extends FormRequest
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
        if(isset($this->endorsement_id)){ // Update Function
            return [
                'endorsement_id' => 'required',
                "training_req_ctrl" => 'required',
                "training_req_id" => 'required',
                "hr_memo_ctrl" => 'required',
                "endorsement_date" => 'required',
                "prepared_by" => 'required',
                "employees" => 'required',
            ];
        }
        else{ // Create Function
            return [
                'hr_memo_id' => 'required',
                "training_req_ctrl" => 'required',
                "training_req_id" => 'required',
                "hr_memo_ctrl" => 'required',
                "endorsement_date" => 'required',
                "prepared_by" => 'required',
                "employees" => 'required',
                "attn" => 'required|array',
                "checked_by" => 'required|array',
                "approved_by" => 'required|array',
            ];
        }
    }
}
