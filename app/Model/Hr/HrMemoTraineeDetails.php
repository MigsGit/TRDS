<?php

namespace App\Model\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Model\Hr\HrMemoTraineeCategoryDetails;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;

class HrMemoTraineeDetails extends Model
{
    public function hris_emp_info()
    {
        return $this->hasOne(SystemOneHrisEmpInfo::class, 'pkid', 'hris_id')->where('vw_employeeinfo.EmpStatus', 1);
    }

    public function subcon_emp_info()
    {
        return $this->hasOne(SystemOneSubconEmpInfo::class, 'pkid', 'hris_id')->where('vw_employeeinfo.EmpStatus', 1);
    }

    public function emp_exam_details()
    {
        return $this->hasMany(HrMemoTraineeCategoryDetails::class, 'trainee_details_id', 'id');
    }
}
