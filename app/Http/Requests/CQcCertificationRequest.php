<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CQcCertificationRequest extends FormRequest
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
          'text_oper_approved_confirmed_by' => ['required'],
          'text_obs_first_result_qcs_oper' => ['required'],
          'text_first_sample_qcs_oper' => ['required'],
          'text_first_ok_qcs_oper' => ['required'],
          'text_first_ng_qcs_oper' => ['required'],
        //   'text_1st_certifiedby_qcs_oper' => ['required'],
          'text_1st_date_qcs_oper' => ['required'],
          'text_1st_time_qcs_oper' => ['required'],
          'text_oa_1st_result_qcs_oper' => ['required'],
          'text_1st_disapproval_qcs_oper' => ['required'],
        ];
    }
}
