<?php

namespace App\Model;

use App\Model\ExamResultDetails;
use App\Model\TrainingRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $table = 'exam_results';
    protected $connection = 'mysql';

    public function exam_result_details_info(){
        return $this->hasMany(ExamResultDetails::class, 'exam_result_id', 'id');
    }

    public function training_request_info(){
        // return $this->hasOne(TrainingRequest::class, 'ctrl_number', 'training_request_ctrl_no')->select('id','ctrl_number');
        return $this->hasOne(TrainingRequest::class, 'ctrl_number', 'training_request_ctrl_no');
    }

}
