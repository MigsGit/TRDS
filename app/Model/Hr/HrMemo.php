<?php

namespace App\Model\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Model\Hr\HrMemoEmailRecipients;
use App\Model\Hr\HrMemoTraineeDetails;

class HrMemo extends Model
{
    protected $table = 'hr_memos';

    public function email_recipients(){
        return $this->hasMany(HrMemoEmailRecipients::class, 'hr_memo_id', 'id');
    }

    public function trainee_details(){
        return $this->hasMany(HrMemoTraineeDetails::class, 'hr_memo_id', 'id');
    }
}
