<?php

namespace App\Model;

use App\Model\Hr\HrMemo;
use App\Model\RapidXUser;
use App\Model\TrainingEndorsementApprovals;
use App\Model\TrainingRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEndorsement extends Model
{
    use HasFactory;

    public function training_endorsement_employees(){
        return $this->hasMany(TrainingEndorsementEmployee::class, 'training_endorsement_id', 'id');
    }
    public function training_request_details(){
        return $this->hasOne(TrainingRequest::class, 'id', 'training_request_id');
    }

    public function hr_memo_details(){
        return $this->hasOne(HrMemo::class, 'id', 'hr_memo_id');
    }

    public function created_by_user_details(){
        return $this->hasOne(RapidXUser::class, 'id', 'created_by');
    }

    public function te_approval_details(){
        return $this->hasMany(TrainingEndorsementApprovals::class, 'training_endorsement_id', 'id');
    }
    public function te_approval_details_pending(){
        return $this->hasMany(TrainingEndorsementApprovals::class, 'training_endorsement_id', 'id')->whereNull('updated_at')->whereNull('deleted_at');
    }

}
