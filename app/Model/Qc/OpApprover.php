<?php

namespace App;

use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpApprover extends Model
{
    use HasFactory;
    public function system_one_subcon_emp_info()
    {
        return $this->hasOne(SystemOneSubconEmpInfo::class, 'EmpNo', 'employee_no');
    }
    public function system_one_hris_emp_info()
    {
        return $this->hasOne(SystemOneHrisEmpInfo::class, 'EmpNo', 'alert_prod_sec');
    }

    /**
     * 2. Dynamic Attribute to get ALL employees when piped
     * Usage: $certification->all_operators
     */
    public function get_all_operators_attribute()
    {
        // If the column is empty, return an empty collection
        if (empty($this->employee_no)) {
            return collect();
        }

        // Split "R131 | R153" into an array: ['R131', 'R153']
        $employeeIds = array_map('trim', explode(' | ', $this->employee_no));

        // Use WHERE IN to pull all employees in a single query
        return SystemOneHrisEmpInfo::whereIn('EmpNo', $employeeIds)->get();
    }
}
