<?php

namespace App\Model;

use App\Model\UserAccessModule;
use App\RapidXUser;
use Illuminate\Database\Eloquent\Model;

class UserModule extends Model
{
    protected $table = 'user_modules';

    public function rapidx_user_updated_by(){
        return $this->hasOne(RapidXUser::class, 'id', 'updated_by');
    }
    public function user_module_access(){
        return $this->hasOne(UserAccessModule::class, 'id', 'updated_by');
    }
}
