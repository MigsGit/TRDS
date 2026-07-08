<?php

namespace App\Model\Qc;


use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcSlipEmployee extends Model
{
    use HasFactory;

    /**
     * Get the user associated with the QcSlipEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function system_one_subcon_emp_info()
    {
        return $this->hasOne(SystemOneSubconEmpInfo::class, 'EmpNo', 'employee_no');
    }
    public function system_one_hris_emp_info()
    {
        return $this->hasOne(SystemOneHrisEmpInfo::class, 'EmpNo', 'employee_no');
    }
}
