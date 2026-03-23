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

    protected $table = 'users';

    public function user_level(){
        return $this->hasOne(UserLevel::class, 'id', 'user_level_id');
    }
}
