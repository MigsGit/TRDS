<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Model\ExamResult;
use App\Model\ExamAttempts;

class ExamResultDetails extends Model
{
    protected $table = 'exam_result_details';
    protected $connection = 'mysql';

    public function exam_result_info(){
        return $this->hasOne(ExamResult::class, 'id', 'exam_result_id')->where('status', 0)->where('logdel', 0);
    }

    public function exam_attempts_info(){
        return $this->hasMany(ExamAttempts::class, 'exam_result_id', 'exam_result_id');
    }

    public function qwe(){
        return $this->hasMany(ExamAttempts::class, 'questionnaire_id', 'questionnaire_id');
    }
}
