<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
// use DataTables;
// use PDF;

use App\Model\TrainingRequest;
use App\Model\Questionnaires;
use App\Model\QuestionnaireDetails;
use App\Model\TrainingRequestDetails;

use App\Model\ExamResult;
use App\Model\ExamResultDetails;

class ExaminationResultController extends Controller
{
    public function viewExamResult(){
        $exam_results = Questionnaires::where('status', 0)->where('logdel', 0)->get();

        return DataTables::of($exam_results)
        ->addColumn('action', function($exam_result){
            $result =   '<center>';

            // if($exam_result->status == 0){
            //     $result .=  '<div class="btn-group">';
            //     $result .=  '   <button type="button" class="btn btn-dark dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">';
            //     $result .=  '   <i class="fa fa-cog"></i>';
            //     $result .=  '</button>';
            //     $result .=  '<div class="dropdown-menu dropdown-menu-right">';
            //     $result .=  '   <button type="button" class="btn text-center dropdown-item actionQuestionnaireDetailsForExamResult" questionnaire-id="' . $exam_result->id . '" questionnaire-revision="' . $exam_result->revision . '" questionnaire-exam_title="' . $exam_result->exam_title . '" data-toggle="modal" data-target="#modalExamResult" title="Exam Results"><i class="fa fa-list-ul"></i> Details</button>';
            //     $result .=  '   <button type="button" class="btn text-center dropdown-item actionChangeQuestionnaireStatusForExamResult" questionnaire-id="' . $exam_result->id . '" status="1" data-toggle="modal" data-target="#modalChangeQuestionnaireStatus" title="Deactivate Questionnaire"><i class="fa fa-ban"></i> Inactive</button>';
            //     $result .=  '</button>';
            // }else{
            //     $result .= '<button type="button" class="btn btn-warning btn-sm text-center actionChangeQuestionnaireStatusForExamResult" questionnaire-id="' . $exam_result->id . '" status="0" data-toggle="modal" data-target="#modalChangeQuestionnaireStatus" title="Activate Questionnaire"><i class="fas fa-redo"></i></button>';
            // }

            $result .= '<button type="button" class="btn btn-dark btn-sm actionQuestionnaireDetailsForExamResult" questionnaire-id="' . $exam_result->id . '" questionnaire-revision="' . $exam_result->revision . '" questionnaire-exam_title="' . $exam_result->exam_title . '" data-toggle="modal" data-target="#modalExamResult" title="Exam Results"><i class="fa fa-list-ul"></i> Details</button>';
            $result .= '</center>';
            return $result;
        })

        ->addColumn('status', function($exam_result){
            $result = "";
            if($exam_result->status == 0){
                $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
            }else{
                $result .= '<center><span class="badge badge-pill badge-danger">Inactive</span></center>';
            }
            return $result;
        })

        ->rawColumns(['action','status'])
        ->make(true);
    }

    public function viewExamResultDetails(Request $request){
        $rapidx_user_id = $_SESSION['rapidx_user_id'];

        $exam_result_details = ExamResultDetails::with(['exam_result_info'])
            ->whereHas('exam_result_info', function($q) use ($request) {
                $q->where('exam_result_status', $request->examStatus)
                ->where('questionnaire_id', $request->questionnaireId)
                ->where('questionnaire_revision_no', $request->questionnaireRevision)
                ->where('status', 0)
                ->where('logdel', 0);
            })
            ->where('status', 0)
            ->where('logdel', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        return DataTables::of($exam_result_details)
        ->addColumn('action', function($exam_result_detail) use($rapidx_user_id){
            $result =   '<center>';
            if($exam_result_detail->exam_result_status == 0){
                $result .= '<button type="button" class="btn btn-warning btn-sm text-center actionEmployeeExamResult" exam_result_details-id="' . $exam_result_detail->id . '" data-toggle="modal" data-target="#modalEmployeeExamResult" title="Exam Results"><i class="fa fa-list-ul"></i> Details</button>';
            }else{
                $result .= '<a  href="view_pdf_examination_result/' . $exam_result_detail->id . '" target="_blank"
                                class="btn btn-dark btn-sm"
                                target="_blank"
                                title="View Exam Results">
                                <i class="fa fa-eye"></i> View Exam
                            </a>';
            }

            if($rapidx_user_id == '211' || $rapidx_user_id == '965' || $rapidx_user_id == '973' || $rapidx_user_id == '976'|| $rapidx_user_id == '76'){
                $result .= '<br>
                    <button
                        type="button"
                        class="btn btn-info btn-sm text-center mt-2 actionChangeExaminationDate"
                        examinationResultDetails-id="' . $exam_result_detail->id . '"
                        examinationResultDetails-examination_date="' . $exam_result_detail->date_examination . '"
                        data-toggle="modal"
                        data-target="#modalChangeExaminationDate"
                        title="Change Examination Date">
                        <i class="fas fa-calendar"></i>
                    </button>';

                    $result .= '
                        <button
                            type="button"
                            class="btn btn-danger btn-sm text-center mt-2 actionChangeExamResultStatus"
                            examinationResultDetails-id="' . $exam_result_detail->id . '"
                            status="1"
                            data-toggle="modal"
                            data-target="#modalChangeExamResultStatus"
                            title="Delete record">
                            <i class="fas fa-trash"></i>
                        </button>';
            }

            $result .= '</center>';
            return $result;
        })

        ->addColumn('exam_taken', function ($exam_result_details) {
            static $attempts = [];

            $employeeNo = $exam_result_details->exam_result_info->employee_no;

            if (!isset($attempts[$employeeNo])) {
                $attempts[$employeeNo] = 1;
            } else {
                $attempts[$employeeNo]++;
            }

            $badge = $exam_result_details->rating == 100 ? 'success' : 'danger';

            $result = '
                <center>
                    <span class="badge badge-pill badge-' . $badge . '">
                        Attempt ' . $attempts[$employeeNo] . '
                    </span>
                </center>
            ';
            return $result;
        })

        ->addColumn('employee_no', function($exam_result_detail){
            $result = "";
            $result .= '<center>' . $exam_result_detail->exam_result_info->employee_no . '</center>';
            return $result;
        })

        ->addColumn('employee_name', function($exam_result_detail){
            $result = "";
            $result .= '<center>' . $exam_result_detail->exam_result_info->employee_name . '</center>';
            return $result;
        })

        ->rawColumns(['action', 'exam_taken', 'employee_no', 'employee_name'])
        ->make(true);
    }

    public function getEmployeeExamResultById(Request $request){
        date_default_timezone_set('Asia/Manila');
        $exam_result_details = ExamResultDetails::with(['exam_result_info'])
            ->where('id', $request->examResultDetailsId)
            ->first();

        return response()->json($exam_result_details);
    }

    public function updateExamScoreForEmployee(Request $request){
        date_default_timezone_set('Asia/Manila');
        // return $request;
        // $total_score = floatval($request->score) + floatval($request->manual_score);
        $exam_result_details = ExamResultDetails::where('id', $request->exam_result_details_id)
            ->update([
                'identification_essay_score' => $request->manual_score,
                'rating' => $request->rating,
                'remark' => $request->remark,
                'exam_result_status' =>'1',
                'updated_at' => now(),
            ]);

        return response()->json($exam_result_details);
    }

    public function viewPdfExaminationResult($id){
        $exam_result_detail =
            ExamResultDetails::with(['exam_result_info'])
                ->where('id', $id)
                ->where('exam_result_status', 1)
                ->where('status', 0)
                ->where('logdel', 0)
                ->get();
                // return $exam_result_detail;
        $take_exam =  $exam_result_detail->count();

        $questionnaire_info = json_decode($exam_result_detail[0]->questionnaire, true);
        $questions = json_decode($exam_result_detail[0]->exam_result, true);
        // return $questionnaire_info;

        $pdf = Pdf::loadView('theoretical_exam.examination_result_pdf', [
            'examResultDetails' => $exam_result_detail,
            'questionnaireInfo' => $questionnaire_info,
            'questions' => $questions,
            'takeExam' => $take_exam
        ]);

        return $pdf->stream('exam_result_' . $exam_result_detail[0]->id . '.pdf');
    }

    public function updateExaminationDate(Request $request){
        date_default_timezone_set('Asia/Manila');

        DB::beginTransaction();
        try{
            ExamResultDetails::where('id', $request->examination_result_detail_id)
                ->update([
                    'date_examination' => $request->examination_date,
                    'updated_at' => now(),
                ]);
            DB::commit();
            return response()->json(['hasError' => 0]);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['hasError' => 1, 'exceptionError' => $e]);
        }
    }

    public function changeExamResultStatus(Request $request){
        date_default_timezone_set('Asia/Manila');

        DB::beginTransaction();
        try{
            ExamResultDetails::where('id', $request->exam_result_details_id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now(),
                ]);
            DB::commit();
            return response()->json(['hasError' => 0]);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['hasError' => 1, 'exceptionError' => $e]);
        }
    }
}
