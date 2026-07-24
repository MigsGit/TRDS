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
    public function training_endorsement(){
        return $this->hasOne(TrainingEndorsement::class, 'id', 'training_request_id')->whereNull('deleted_at');
    }

}
