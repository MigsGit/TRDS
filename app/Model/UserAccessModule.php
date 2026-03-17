<?php

namespace App\Model;

use App\Model\UserModule;
// use App\Model\User;
use Illuminate\Database\Eloquent\Model;

class UserAccessModule extends Model
{
    protected $table = 'user_access_modules';

    public function user_module()
    {
        return $this->hasMany(UserModule::class, 'id', 'user_modules_id');
    }
}
