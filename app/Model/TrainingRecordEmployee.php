<?php

namespace App\Model;

use App\Model\SystemOneHrisSubcon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingRecordEmployee extends Model
{
    use HasFactory;
    protected $table = 'training_record_employees';
    protected $fillable = [
        'training_record_id',
        'employee_no',
        'station',
        'series',
        'created_by',
        'updated_by'
    ];

    public function employee_details(){
        return $this->belongsTo(SystemOneHrisSubcon::class, 'employee_no', 'EmpNo');
    }
}
