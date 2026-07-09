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
    // public function examDashboard(){
    //     $examCategories = Questionnaires::where('status', 0)
    //         ->where('logdel', 0)
    //         ->get();
    //     return view('theoretical_exam.examination_dashboard', compact('examCategories'));
    // }

    public function examDashboard(Request $request){
        $allCategories = Questionnaires::where('status', 0)
            ->where('logdel', 0)
            ->get();

        $examCategories = Questionnaires::where('status', 0)
            ->where('logdel', 0);

        if ($request->department) {
            $examCategories->where('department', $request->department);
        } else {
            // show nothing initially (optional)
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
        // return $get_tr_employee_no;
        return response()->json($get_tr_employee_no);
    }

    public function countExamTrainingRequestExaminationTake(Request $request){
        date_default_timezone_set('Asia/Manila');

        $examResults = ExamResult::with('exam_result_details_info')
            ->where('training_request_ctrl_no', $request->controlNo)
            ->where('employee_no', $request->employeeNo)
            ->where('status', '0')
            ->where('logdel', 0)
            ->whereHas('exam_result_details_info.qwe', function ($query) use ($request) {
                $query->where('questionnaire_id', $request->examTitleId);
            })
            ->withCount([
                'exam_result_details_info as qwe_count' => function ($query) use ($request) {
                    $query->whereHas('qwe', function ($q) use ($request) {
                        $q->where('questionnaire_id', $request->examTitleId);
                    });
                }
            ])
            ->get();

        // Count after getting the data
        $count = $examResults->sum('qwe_count');


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
    // public function countExamTrainingRequestExaminationTake(Request $request){
    //     date_default_timezone_set('Asia/Manila');

    //     $count = ExamResult::where('training_request_ctrl_no', $request->controlNo)
    //         ->where('employee_no', $request->employeeNo)
    //         ->where('status', '0')
    //         ->where('logdel', 0)
    //         ->whereHas('exam_result_details_info.qwe', function ($query) use ($request) {
    //             $query->where('questionnaire_id', $request->examTitleId);
    //         })
    //         ->withCount([
    //             'exam_result_details_info as qwe_count' => function ($query) use ($request) {
    //                 $query->whereHas('qwe', function ($q) use ($request) {
    //                     $q->where('questionnaire_id', $request->examTitleId);
    //                 });
    //             }
    //         ])
    //         ->get()
    //         ->sum('qwe_count');

    //     $attemptNumber = $count + 1;

    //     if ($attemptNumber > 3) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Maximum attempt reached.'
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'attempt' => $attemptNumber
    //     ]);
    // }


    // public function countExamTrainingRequestExaminationTake(Request $request){
    //     date_default_timezone_set('Asia/Manila');

    //     $get_employee_exam_details = ExamResult::with(['exam_result_details_info.qwe'])->where('training_request_ctrl_no', $request->controlNo)->where('employee_no', $request->employeeNo)->where('status', '0')->where('logdel', 0)->get();
    //     $count =  optional(data_get($get_employee_exam_details, '0.exam_result_details_info.qwe'))->count() ?? 0;
    //     return $get_employee_exam_details;
    //     return response()->json($count);
    // }

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

        $data = $request->all();
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
            ->whereHas('exam_result_details_info', function ($q) use ($request_examination_questionnaire) {
                $q->where('questionnaire_id', $request_examination_questionnaire->id);
            })
            ->first();

        $examination_user_info = [
            'training_request_ctrl_no'  => $request_examination_user_info->training_request_ctrl_no,
            'employee_no'               => $request_examination_user_info->employee_no,
            'employee_name'             => $request_examination_user_info->employee_name,
            'date_hired'                => $request_examination_user_info->date_hired,
            'created_at'                => date('Y-m-d H:i:s')
        ];

        // $ExamResultId = ExamResult::insertGetId($examination_user_info);
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
