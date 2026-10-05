<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DropdownItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $u_m_access = explode(',', session('global_user')->user_modules_id);

        if(session('global_user')->user_level_id != 3 && in_array('23', $u_m_access)){ // 23 - Dropdown Maintenance on table user_module
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
        return [
            'dropdown_masters_id'      => 'required|integer',
            'dropdown_masters_details' => 'required|string|max:255',
        ];
    }
}
