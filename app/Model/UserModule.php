<?php

namespace App\Model;

use App\RapidXUser;
use Illuminate\Database\Eloquent\Model;

class UserModule extends Model
{
    protected $table = 'user_modules';

    public function rapidx_user_updated_by(){
        return $this->hasOne(RapidXUser::class, 'id', 'updated_by');
    }
}
