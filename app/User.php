<?php

namespace App;

use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;
use App\Model\UserLevel;
use App\RapidXUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $table = 'users';

    public function user_level(){
        return $this->hasOne(UserLevel::class, 'id', 'user_level_id');
    }
    public function rapidx_rapidx_user_no(){
        return $this->hasOne(RapidXUser::class, 'id', 'rapidx_emp_id');
    }
    public function rapidx_system_one_subcon_emp_info()
    {
        return $this->hasOne(SystemOneSubconEmpInfo::class, 'EmpNo', 'rapidx_emp_no');
    }
    public function rapidx_system_one_hris_emp_info()
    {
        return $this->hasOne(SystemOneHrisEmpInfo::class, 'EmpNo', 'rapidx_emp_no');
    }

}
