<?php

namespace App\Model;

use App\Model\ExamResult;
use App\Model\Hr\HrMemo;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;
use App\Model\TrainingAttendance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingRequestDetails extends Model
{
    use HasFactory;
    protected $table = 'training_request_details';

    protected $fillable = [
        'training_request_id',
        'training_memo_doc_id',
        'hr_memo_trainee_details_id',
        'emp_no',
        'date_hired',
        'name',
        'position',
        'department',
        'section',
        'training_title',
        'training_result',
        'remarks',
        'training_venue',
        'training_endorsement_date'
    ];

    public function rapidx_system_one_hris_emp_info(){
        return $this->hasOne(SystemOneHrisEmpInfo::class, 'EmpNo', 'emp_no');
    }
    public function rapidx_system_one_subcon_emp_info(){
        return $this->hasOne(SystemOneSubconEmpInfo::class, 'EmpNo', 'emp_no');
    }
    public function training_attendance(){
        return $this->hasMany(TrainingAttendance::class, 'rapidx_emp_no', 'emp_no');
    }
    public function hr_memo_details(){
        return $this->hasOne(HrMemo::class, 'id', 'training_memo_doc_id');
    }
    public function employee_exam_details(){
        return $this->hasMany(ExamResult::class, 'employee_no','emp_no');
    }
}
