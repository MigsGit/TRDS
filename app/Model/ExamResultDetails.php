<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model\Questionnaires;
use Illuminate\Database\Eloquent\Model;

use App\Model\ExamResult;

class ExamResultDetails extends Model
{
    protected $table = 'exam_result_details';
    protected $connection = 'mysql';

    public function exam_result_info(){
        return $this->hasOne(ExamResult::class, 'id', 'exam_result_id')->where('status', 0)->where('logdel', 0);
    }

    public function questionnaires_info(){
        return $this->hasOne(Questionnaires::class, 'id', 'questionnaire_id')->where('status', 0)->where('logdel', 0);
    }
}