<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

use App\Model\Questionnaires;

class QuestionnaireDetails extends Model
{
    protected $table = 'questionnaire_details';
    protected $connection = 'mysql';

    public function questionare_title_info(){
        return $this->hasOne(Questionnaires::class, 'id', 'questionnaire_id')->where('status', 0)->where('logdel', 0);
    }
}
