<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Model\WAQuestionnaireDetails;

class WAQuestionnaires extends Model
{
    protected $table = 'w_a_questionnaires';
    protected $connection = 'mysql';

    public function questionnaire_details(){
        return $this->hasMany(WAQuestionnaireDetails::class, 'questionnaire_id', 'id')->where('status', 0)->where('logdel', 0);
    }
}