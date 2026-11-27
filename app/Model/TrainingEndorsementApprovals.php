<?php

namespace App\Model;

use App\Model\RapidXUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEndorsementApprovals extends Model
{
    use HasFactory;

    public function approver_details(){
        return $this->hasOne(RapidXUser::class, 'id', 'rapidx_id');
    }
}
