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

use App\Model\ExamResult;
use App\Model\ExamResultDetails;


class ExaminationController extends Controller
{
    public function examDashboard(Request $request){
        $allCategories = Questionnaires::where('status', 0)
            ->where('logdel', 0)
            ->get();

        $examCategories = Questionnaires::where('status', 0)
            ->where('logdel', 0);

        if($request->department){
            $examCategories->where('department', $request->department);
        }else{
            $examCategories->whereRaw('1 = 0');
        }

        $examCategories = $examCategories->get();

        return view('theoretical_exam.examination_dashboard', compact('examCategories', 'allCategories'));
    }

    public function startExam($id, $revision){
        $exam = Questionnaires::where('id', $id)
            ->where('revision', $revision)
            ->firstOrFail();

        $questions = QuestionnaireDetails::where('questionnaire_id', $id)
            ->where('revision', $revision)
            ->where('status', 0)
            ->where('logdel', 0)
            ->orderBy('exam_no', 'ASC')
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

    public function countExamTrainingRequestExaminationTake(Request $request){
        $detailFilter = function ($q) use ($request) {
            $q->where('questionnaire_id', $request->examTitleId)
            ->where('status', 0)
            ->where('logdel', 0);
        };

        $examResults = ExamResult::with([
                'exam_result_details_info' => $detailFilter
            ])
            ->where('training_request_ctrl_no', $request->controlNo)
            ->where('employee_no', $request->employeeNo)
            ->where('status', 0)
            ->where('logdel', 0)
            ->withCount([
                'exam_result_details_info' => $detailFilter
            ])
            ->get();

        $count = $examResults->sum('exam_result_details_info_count');

        $attemptNumber = $count + 1;

        if ($attemptNumber > 3) {
            return response()->json([
                'status' => false,
                'message' => 'Maximum attempt reached.'
            ]);
        }

        return response()->json([
            'status' => true,
            'examResults' => $examResults,
            'attempt' => $attemptNumber
        ]);
    }

    public function getExamTrainingRequestDetailsByRevisionId(Request $request){
        date_default_timezone_set('Asia/Manila');

        $getQuestionnaireDetails = Questionnaires::with(['questionnaire_details'])->where('id', $request->idRevision[0])
            ->where('revision', $request->idRevision[1])
            ->where('status', 0)
            ->where('logdel', 0)
            ->firstOrFail();

            return response()->json(['getQuestionnaireDetails' => $getQuestionnaireDetails]);
    }


    public function examSubmission(Request $request){
        date_default_timezone_set('Asia/Manila');

        $data = $request->all();
        // return $request;
        $validator = Validator::make($data, [
            'questionnaire_category'        => 'required',
            'questionnaire_title'           => 'required',
            'questionnaire_purpose'         => 'required',
            'questionnaire_position'        => 'required',
            'questionnaire_passing_score'   => 'required',
            'questionnaire_instruction'     => 'required',
            'questionnaire_department'      => 'required',
            'questionnaire_product_line'    => 'required'
        ]);

        $request_examination_user_info = json_decode($request->examination_user_info);
        $request_examination_questionnaire = json_decode($request->examination_questionnaire);
        $request_employee_examination_result = json_decode($request->employee_examination_result);
        $summary = $request_employee_examination_result->summary;

        $examResult = ExamResult::where('training_request_ctrl_no', $request_examination_user_info->training_request_ctrl_no)
            ->where('employee_no', $request_examination_user_info->employee_no)
            ->where('status', '0')
            ->where('logdel', 0)
            ->first();

            $examination_user_info = [
            'training_request_ctrl_no'  => $request_examination_user_info->training_request_ctrl_no,
            'employee_no'               => $request_examination_user_info->employee_no,
            'employee_name'             => $request_examination_user_info->employee_name,
            'date_hired'                => $request_examination_user_info->date_hired,
            'created_at'                => date('Y-m-d H:i:s')
        ];

        if($examResult){
            $ExamResultId = $examResult->id;
        }else{
            $examination_user_info['created_at'] = date('Y-m-d H:i:s');
            $ExamResultId = ExamResult::insertGetId($examination_user_info);
        }

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
            'attempt'                   => $request->attempts,
        ];

        $examination_user_details['created_at'] = date('Y-m-d H:i:s');
        ExamResultDetails::insert([
            $examination_user_details
        ]);
        return response()->json($request);
    }
}
