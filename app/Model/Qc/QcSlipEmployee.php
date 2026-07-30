<?php

namespace App\Model\Qc;


use App\Model\DropdownMasterDetail;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneHrisSubcon;
use App\Model\SystemOneSubconEmpInfo;
use App\Model\QcSlip;
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
    public function system_one_hris_subcon()
    {
        return $this->hasOne(SystemOneHrisSubcon::class, 'EmpNo', 'employee_no');
    }

    public function dropdown_master_detail($column)
    {
        return $this->hasOne(DropdownMasterDetail::class, 'id', $column);
    }
    public function get_station_from()
    {
       return $this->dropdown_master_detail('station_from');
    }
    public function get_station_to()
    {
       return $this->dropdown_master_detail('station_to');
    }

    public function qcSlip()
    {
        return $this->belongsTo(QcSlip::class, 'qc_slips_id');
    }


}
