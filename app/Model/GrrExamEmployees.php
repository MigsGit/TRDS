<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrrExamEmployees extends Model
{
    use HasFactory;

    public function getEmployeeAttribute()
    {
        return $this->hasOne(SystemOneHrisSubcon::class, 'EmpNo', 'emp_id');
    }
}
