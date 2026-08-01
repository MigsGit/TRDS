<?php

namespace App;

use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpApprover extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_slips_id',
        'approval_status',
        'decision_status',
        'alert_prod_sec',
        'alert_prod_cc_sec',
        'first_approver',
        'first_approver_2',
        'first_approver_3',
        'first_date',
        'first_time',
        'first_status',
        'first_status_2',
        'first_remarks',
        'first_date_2',
        'second_approver',
        'second_approver_2',
        'second_approver_3',
        'second_date',
        'second_time',
        'second_status',
        'second_status_2',
        'second_remarks',
        'second_date_2',
        'oper_approved_confirmed_by',
    ];
    public function system_one_subcon_emp_info()
    {
        return $this->hasOne(SystemOneSubconEmpInfo::class, 'EmpNo', 'employee_no');
    }
    public function system_one_hris_emp_info()
    {
        return $this->hasOne(SystemOneHrisEmpInfo::class, 'EmpNo', 'alert_prod_sec');
    }
    public function system_one_hris_subcon_alert_prod_sec()
    {
       return $this->system_one_hris_subcon_emp_info('alert_prod_sec');
    }

    public function system_one_hris_subcon_emp_info($column)
    {
        return $this->hasOne(SystemOneHrisSubcon::class, 'EmpNo', $column);
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
