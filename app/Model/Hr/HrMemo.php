<?php

namespace App\Model\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Model\Hr\HrMemoEmailRecipients;
use App\Model\Hr\HrMemoTraineeDetails;
use App\RapidXUser;

class HrMemo extends Model
{
    protected $table = 'hr_memos';

    public function email_recipients(){
        return $this->hasMany(HrMemoEmailRecipients::class, 'hr_memo_id', 'id');
    }

    public function trainee_details(){
        return $this->hasMany(HrMemoTraineeDetails::class, 'hr_memo_id', 'id');
    }

    public function prepared_by_info(){
        return $this->hasOne(RapidXUser::class, 'id', 'prepared_by');
    }

    public function noted_by_info(){
        return $this->hasOne(RapidXUser::class, 'id', 'noted_by');
    }
}
