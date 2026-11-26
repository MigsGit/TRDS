<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model\WAQuestionnaires;
use Illuminate\Database\Eloquent\Model;

use App\Model\WAExamResult;

class WAExamResultDetails extends Model
{
    protected $table = 'w_a_exam_result_details';
    protected $connection = 'mysql';

    public function exam_result_info(){
        return $this->hasOne(WAExamResult::class, 'id', 'exam_result_id')->where('status', 0)->where('logdel', 0);
    }

    public function questionnaires_info(){
        return $this->hasOne(WAQuestionnaires::class, 'id', 'questionnaire_id')->where('status', 0)->where('logdel', 0);
    }
}