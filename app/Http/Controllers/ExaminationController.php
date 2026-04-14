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

use App\Model\ExamResult;
use App\Model\ExamResultDetails;
use App\Model\ExamAttempts;


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
        // return $get_tr_employee_no;
        return response()->json($get_tr_employee_no);
    }

    public function countExamTrainingRequestExaminationTake(Request $request){
        date_default_timezone_set('Asia/Manila');

        $get_employee_exam_details = ExamResult::with(['exam_result_details_info.qwe'])->where('training_request_ctrl_no', $request->controlNo)->where('employee_no', $request->employeeNo)->where('status', '0')->where('logdel', 0)->get();
        $count =  optional(data_get($get_employee_exam_details, '0.exam_result_details_info.qwe'))->count() ?? 0;        
        return response()->json($count);
    }

    public function getExamTrainingRequestDetailsByIdRevision(Request $request){
        date_default_timezone_set('Asia/Manila');

        $qwe = Questionnaires::with(['questionnaire_details'])->where('id', $request->idRevision[0])
            ->where('revision', $request->idRevision[1])
            ->where('status', 0)
            ->where('logdel', 0)
            ->firstOrFail();
        // return $qwe;
        return response()->json(['qwe' => $qwe]);
    }

    public function examSubmission(Request $request){
        date_default_timezone_set('Asia/Manila');

        $request_examination_user_info = json_decode($request->examination_user_info);
        $examination_user_info = [
            'training_request_ctrl_no'  => $request_examination_user_info->training_request_ctrl_no,
            'employee_no'               => $request_examination_user_info->employee_no,
            'employee_name'             => $request_examination_user_info->employee_name,
            'date_hired'                => $request_examination_user_info->date_hired,
            'created_at'                => date('Y-m-d H:i:s')
        ];
        $examination_user_info['created_at'] = date('Y-m-d H:i:s');
        $ExamResultId = ExamResult::insertGetId($examination_user_info);
        
        $request_examination_questionnaire = json_decode($request->examination_questionnaire);
        $request_employee_examination_result = json_decode($request->employee_examination_result);
        $summary = $request_employee_examination_result->summary;

        $examination_user_details = [
            'exam_result_id'            => $ExamResultId,
            'questionnaire_id'          => $request_examination_questionnaire->id,
            'questionnaire_revision_no' => $request_examination_questionnaire->revision,
            'questionnaire'             => $request->examination_questionnaire,
            'questionnaire_details'     => $request->examination_questionnaire_details,
            'exam_result'               => $request->employee_examination_result,
            'score'                     => $summary->total_score,
            'rating'                    => $summary->percentage,
            'remark'                    => $summary->result,
            'date_examination'          => $request_examination_user_info->date_examination,
        ];

        $examination_user_details['created_at'] = date('Y-m-d H:i:s');
        ExamResultDetails::insert([
            $examination_user_details
        ]);

        ExamAttempts::insert([
            'exam_result_id'    => $ExamResultId,
            'questionnaire_id'  => $request_examination_questionnaire->id,
            'attempt'           => 1,
            'created_at'        => date('Y-m-d H:i:s')
        ]);
        return response()->json($request);
    }
}