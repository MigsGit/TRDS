<?php

namespace App\Model\Hr;

use Illuminate\Database\Eloquent\Model;
use App\RapidXUser;

class HrMemoEmailRecipients extends Model
{
    public function rapidx_user()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'user_id');
    }
}
