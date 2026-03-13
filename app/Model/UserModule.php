<?php

namespace App\Model;

use App\RapidXUser;
use Illuminate\Database\Eloquent\Model;

class UserModule extends Model
{
    /**
     * Get the user associated with the UserModule
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rapidx_user_updated_by()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'rapidx_emp_id');
    }

}
