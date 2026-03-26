<?php

namespace App\Model;

use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_request_details_id',
        'rapidx_emp_no',
        'date',
        'time_in',
        'time_out',
        'status',
        'remarks'
    ];
    public function rapidx_system_one_hris_emp_info(){
        return $this->hasOne(SystemOneHrisEmpInfo::class, 'EmpNo', 'rapidx_emp_no');
    }
    public function rapidx_system_one_subcon_emp_info(){
        return $this->hasOne(SystemOneSubconEmpInfo::class, 'EmpNo', 'rapidx_emp_no');
    }
}
