<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapidXUser extends Model
{
    protected $table = 'users';
    protected $connection = 'rapidx';

    // public function department(){
    //     return $this->hasOne(RapidXDepartment::class, 'department_id', 'department_id');
    // }

    public function employee_info(){
        return $this->belongsTo(SystemOneHrisEmpInfo::class, 'employee_number', 'EmpNo');
    }
}
