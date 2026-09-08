<?php

namespace App\Model\Hr;

use App\Model\Hr\HrMemoExamination;
use App\Model\Hr\HrMemoTraineeDetails;
use Illuminate\Database\Eloquent\Model;
use App\Model\SystemOneHrisEmpInfo;

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

    public function exam_info_test(){
        return $this->belongsTo(HrMemoExamination::class, 'category', 'id');
    }

    public function employee_info_tist(){
        return $this->belongsTo(HrMemoTraineeDetails::class,'trainee_details_id','id');
    }

    public function rapidx_system_one_hris_emp_info(){
        return $this->hasOne(SystemOneHrisEmpInfo::class, 'EmpNo', 'trainor');
    }
}
