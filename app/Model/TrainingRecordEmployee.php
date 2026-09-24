<?php

namespace App\Model;

use App\Model\SystemOneHrisSubcon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingRecordEmployee extends Model
{
    use HasFactory;
    use SoftDeletes;
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

    public function training_record(){
        return $this->belongsTo(TrainingRecord::class, 'training_record_id', 'id');
    }
}
