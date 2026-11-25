<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GrrExamAnswers extends Model
{
    protected $table = 'grr_exam_answers';

    protected $fillable = [
        'exam_employee_id',
        'questionnaire_id',
        'trial',
        'answer',
    ];

    public function examEmployee()
    {
        return $this->belongsTo(GrrExamEmployees::class, 'exam_employee_id');
    }

    public function questionnaire()
    {
        return $this->belongsTo(GrrQuestionnaireSettings::class, 'questionnaire_id');
    }
}
