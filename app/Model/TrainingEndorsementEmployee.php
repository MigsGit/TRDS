<?php

namespace App\Model;

use App\Model\TrainingEndorsement;
use App\Model\TrainingRequestDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEndorsementEmployee extends Model
{
    use HasFactory;

    public function training_request_details_info(){
        return $this->hasOne(TrainingRequestDetails::class, 'id', 'training_request_detail_id');
    }
    public function training_endorsement_training_request_id(){
        return $this->belongsTo(TrainingEndorsement::class, 'training_endorsement_id', 'id')->whereNull('deleted_at');
    }

}
