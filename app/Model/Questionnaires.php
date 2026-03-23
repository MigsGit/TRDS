<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use App\Model\QuestionnaireDetails;

class Questionnaires extends Model
{
    protected $table = 'questionnaires';
    protected $connection = 'mysql';

    public function questionnaire_details(){
        return $this->hasMany(QuestionnaireDetails::class, 'questionnaire_id', 'id')->where('status', 0)->where('logdel', 0);
    }
}
