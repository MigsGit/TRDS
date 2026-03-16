<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Model\UserLevel;
use App\Model\OQCStamp;

class User extends Authenticatable
{

    protected $table = 'users';

    public function user_level(){
        return $this->hasOne(UserLevel::class, 'id', 'user_level_id');
    }

    // public function oqc_stamps(){
    //     return $this->hasMany(OQCStamp::class, 'user_id', 'id');
    // }
}
