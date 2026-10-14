<?php

namespace App\Model\Disqualification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\RapidXUser;

class P1EmailRecipients extends Model
{
    use HasFactory;

    protected $fillable = ['p1_id', 'rapidx_id', 'type'];

    public function rapidx_user(){
        return $this->hasOne(RapidXUser::class, 'id', 'rapidx_id');
    }
}
