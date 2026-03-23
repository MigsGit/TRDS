<?php

namespace App\Model;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model\UserAccessModule;
use App\RapidXUser;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'users';

    public function user_level(){
        return $this->hasOne(UserLevel::class, 'id', 'user_level_id');
    }

    public function user_access_module(){
        return $this->hasOne(UserAccessModule::class, 'users_id', 'id');
    }

    public function users(){
        return $this->hasOne(RapidXUser::class, 'id', 'rapidx_emp_id');
    }
    public function rapidx_system_one_hris_emp_info(){
        return $this->hasOne(SystemOneHrisEmpInfo::class, 'EmpNo', 'rapidx_emp_no');
    }
    public function rapidx_system_one_subcon_emp_info(){
        return $this->hasOne(SystemOneSubconEmpInfo::class, 'EmpNo', 'rapidx_emp_no');
    }
}
