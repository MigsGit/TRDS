<?php

namespace App\Model\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Model\Hr\HrMemoTraineeCategoryDetails;

class HrMemoTraineeDetails extends Model
{
    // public function systemone_emp_details()
    // {
    //     return $this->hasOne
    //     // return $this->hasOne(HrMemoTraineeCategoryDetails::class, 'trainee_details_id', 'id');
    // }

    public function emp_exam_details()
    {
        return $this->hasMany(HrMemoTraineeCategoryDetails::class, 'trainee_details_id', 'id');
    }
}
