<?php

namespace App\Model;

use App\RapidXUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingRequest extends Model
{
    use HasFactory;

    protected $table = 'training_requests';
    protected $fillable = [
        'ctrl_number',
        'date_filed',
        'department_id',    
        'section_id',
        'job_function',
        'area_allocation',
        'reason',
        'created_by',
        'updated_by',
        'section_head'  
    ];

    public function conformance_user(){
        return $this->hasOne(RapidXUser::class, 'id', 'section_head');
    }

}