<?php

namespace App\Model\Hr;

use App\Model\Hr\HrMemoExamination;
use App\Model\Hr\HrMemoTraineeDetails;
use Illuminate\Database\Eloquent\Model;

class HrMemoTraineeCategoryDetails extends Model
{
    public function exam_info()
    {
        return $this->hasMany(HrMemoExamination::class, 'id', 'category');
    }

    public function employee_info()
    {
        return $this->hasOne(HrMemoTraineeDetails::class, 'id', 'hr_memo_id');
    }
}
