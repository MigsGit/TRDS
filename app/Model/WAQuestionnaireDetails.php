<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Model\WAQuestionnaires;

class WAQuestionnaireDetails extends Model
{
    protected $table = 'w_a_questionnaire_details';
    protected $connection = 'mysql';
    protected $fillable = ['exam_no'];

    public function questionare_title_info(){
        return $this->hasOne(WAQuestionnaires::class, 'id', 'questionnaire_id')->where('status', 0)->where('logdel', 0);
    }
}