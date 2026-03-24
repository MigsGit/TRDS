<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Model\TrainingRequest;
use App\Model\Questionnaires;
use App\Model\QuestionnaireDetails;
use App\Model\TrainingRequestDetails;


class ExaminationController extends Controller
{
    public function examDashboard(){
        $examCategories = Questionnaires::where('status', 0)
            ->where('logdel', 0)
            ->get();
        return view('theoretical_exam.examination_dashboard', compact('examCategories'));
    }

    public function startExam($id, $revision){
        $exam = Questionnaires::where('id', $id)
            ->where('revision', $revision)
            ->firstOrFail();

        $questions = QuestionnaireDetails::where('questionnaire_id', $id)
            ->where('revision', $revision)
            ->where('status', 0)
            ->where('logdel', 0)
            ->get();

        return view('theoretical_exam.examination', compact('exam', 'questions'));
    }

    public function getExamTrainingRequestControlNo(Request $request){
        date_default_timezone_set('Asia/Manila');
        $get_tr_control_no = TrainingRequest::where('status', '3')
            ->select('ctrl_number','status')
            ->distinct()
            ->orderBy('ctrl_number', 'ASC')
            ->get();

        return response()->json($get_tr_control_no);
    }

    public function getExamTrainingRequestEmployeeNo(Request $request){
        date_default_timezone_set('Asia/Manila');

        $get_tr_employee_no = TrainingRequest::with(['training_request_details'])->where('ctrl_number', $request->controlNo)->where('status', '3')->get();

        return response()->json($get_tr_employee_no);
    }
}