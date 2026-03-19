<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Questionnaires;
use App\Model\QuestionnaireDetails;

class ExaminationController extends Controller
{
    // List all exam categories
    public function exam_dashboard()
    {
        $examCategories = Questionnaires::all(); 
        return view('theoretical_exam.examination_dashboard', compact('examCategories'));
    }

    // Start exam
    public function start_exam($id, $revision)
    {
        $exam = Questionnaires::where('id', $id)
            ->where('revision', $revision)
            ->firstOrFail();

        // FETCH QUESTIONS
        $questions = QuestionnaireDetails::where('questionnaire_id', $id)
            ->where('revision', $revision)
            ->get();

        return view('theoretical_exam.examination', compact('exam', 'questions'));
    }
}