<?php

namespace App\Model;

use App\Model\TrainingRequestDetails;
use App\Model\TrainingEndorsement;
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

    public function section_head_user(){
        return $this->hasOne(RapidXUser::class, 'id', 'section_head');
    }

    public function requestor(){
        return $this->hasOne(RapidXUser::class, 'id', 'created_by');
    }

    public function training_request_details(){
        return $this->hasMany(TrainingRequestDetails::class, 'training_request_id', 'id');
    }

    public function training_endorsement_info(){
        // return $this->hasOne(TrainingEndorsement::class, 'training_request_id', 'id')->select('training_request_id');
        return $this->hasOne(TrainingEndorsement::class, 'training_request_id', 'id');
    }


}
