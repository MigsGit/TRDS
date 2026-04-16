<?php

namespace App\Model;

use App\Model\TrainingRequestDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEndorsementEmployee extends Model
{
    use HasFactory;

    public function training_request_details_info(){
        return $this->hasOne(TrainingRequestDetails::class, 'id', 'training_request_detail_id');
    }
    
}
