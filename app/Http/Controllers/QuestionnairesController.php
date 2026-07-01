<?php
namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Yajra\DataTables\Facades\DataTables;

use App\Model\Questionnaires;
use App\Model\QuestionnaireDetails;
use App\Model\SystemOneHrisSection;
use App\Model\SystemOneHrisPosition;
use App\Model\SystemOneHrisDepartment;

class QuestionnairesController extends Controller
{
    // ===========================================================================================================================================================
    // ====================================================================== Questionnaire ======================================================================
    // ===========================================================================================================================================================
	public function viewQuestionnaire(){
        $questionnaires = Questionnaires::where('logdel', 0)->get();

        return DataTables::of($questionnaires)
        ->addColumn('action', function($questionnaire){
            $result =   '<center>';

            if($questionnaire->status == 0){
                $result .=  '<div class="btn-group">';
                $result .=  '   <button type="button" class="btn btn-dark dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">';
                $result .=  '   <i class="fa fa-cog"></i>';
                $result .=  '</button>';
                $result .=  '<div class="dropdown-menu dropdown-menu-right">';
                if($questionnaire->revision != 0){
                    $result .=  '<button type="button" class="btn text-center dropdown-item actionViewRevisionQuestionnaire" questionnaire-id="' . $questionnaire->id . '" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaire" title="View Questionnaire"><i class="fa fa-eye"></i> Revision</button>';
                }
                $result .=  '   <button type="button" class="btn text-center dropdown-item actionUpdateQuestionnaire" questionnaire-id="' . $questionnaire->id . '" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaire" title="Update Questionnaire"><i class="fa fa-edit"></i> Update</button>';
                $result .=  '   <button type="button" class="btn text-center dropdown-item actionQuestionnaireDetails" questionnaire-id="' . $questionnaire->id . '" questionnaire-revision="' . $questionnaire->revision . '" questionnaire-exam_title="' . $questionnaire->exam_title . '" data-toggle="modal" data-target="#modalQuestionnaireDetails" title="Questionnaire Details"><i class="fa fa-list-ul"></i> Details</button>';
                $result .=  '   <button type="button" class="btn text-center dropdown-item actionChangeQuestionnaireStatus" questionnaire-id="' . $questionnaire->id . '" status="1" data-toggle="modal" data-target="#modalChangeQuestionnaireStatus" title="Deactivate Questionnaire"><i class="fa fa-ban"></i> Inactive</button>';
                $result .=  '</button>';

            }else{
                $result .= '<button type="button" class="btn btn-warning btn-sm text-center actionChangeQuestionnaireStatus" questionnaire-id="' . $questionnaire->id . '" status="0" data-toggle="modal" data-target="#modalChangeQuestionnaireStatus" title="Activate Questionnaire"><i class="fas fa-redo"></i></button>';
            }

            $result .= '</center>';
            return $result;
        })


        ->addColumn('status', function($questionnaire){
            $result = "";
            if($questionnaire->status == 0){
                $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
            }
            else{
                $result .= '<center><span class="badge badge-pill badge-danger">Inactive</span></center>';
            }
            return $result;
        })


        ->rawColumns(['action','status'])
        ->make(true);
    }

    public function getSystemoneHrisDepartment(Request $request){
        date_default_timezone_set('Asia/Manila');

        $hris_department = SystemOneHrisDepartment::where('Department', '!=', 'Board of Directors')->where('isActive', '1')->orderBy('Department', 'ASC')->get();

        return response()->json($hris_department);
    }

    public function getSystemoneHrisPosition(Request $request){
        date_default_timezone_set('Asia/Manila');

        $hris_position = SystemOneHrisPosition::where('level', '<', '3')->orderBy('Position', 'ASC')->get();

        return response()->json($hris_position);
    }

    public function getSystemoneHrisSection(Request $request){
        date_default_timezone_set('Asia/Manila');

        $hris_section =
            SystemOneHrisSection::where('Section', '!=', 'BOD')
                ->where('isActive', '1')
                ->select('Section')
                ->distinct()
                ->orderBy('Section', 'ASC')
                ->get();

        return response()->json($hris_section);
    }

    public function createUpdateQuestionnaire(Request $request){
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

        $questionnaires_record = [
            'category'          => $request->questionnaire_category,
            'exam_title'        => $request->questionnaire_title,
            'purpose'           => $request->questionnaire_purpose,
            'position'          => $request->questionnaire_position,
            'passing_score'     => $request->questionnaire_passing_score,
            'exam_instruction'  => $request->questionnaire_instruction,
            'department'        => $request->questionnaire_department,
            'product_line'      => $request->questionnaire_product_line,
        ];

        if($validator->fails()){
            return response()->json(['validationHasError' => 1, 'error' => $validator->errors()]);
        }else{
            DB::beginTransaction();
            try{
                $count_production_data = Questionnaires::where('id', $request->questionnaire_id)->where('logdel', 0)->exists();
                if($count_production_data == 0){
                    $exists = Questionnaires::where([
                        'id'   => $request->questionnaire_id,
                        'logdel' => 0,
                    ])->exists();

                    if($exists){
                        return response()->json(['result' => 1]);
                    }

                    $questionnaires_record['created_by']    = '';
                    $questionnaires_record['created_at']    = date('Y-m-d H:i:s');

                    Questionnaires::insert($questionnaires_record);
                }else{
                    $data_checking = array_merge($questionnaires_record, [
                        'id'     => $request->questionnaire_id,
                        'logdel' => 0,
                    ]);

                    $exists = Questionnaires::where($data_checking)->exists();

                    if($exists){
                        return response()->json(['result' => 1]);
                    }else{
                        $questionnaires_record['updated_by']    = '';
                        $questionnaires_record['updated_at']    = date('Y-m-d H:i:s');

                        Questionnaires::where('id', $request->questionnaire_id)->where('logdel', 0)->update($questionnaires_record);
                    }
                }

                DB::commit();
                return response()->json(['hasError' => 0]);
            }catch (\Exception $e){
                DB::rollback();
                return response()->json(['hasError' => 1, 'exceptionError' => $e]);
            }
        }
    }

    public function getQuestionnaireById(Request $request){
        date_default_timezone_set('Asia/Manila');

        $questionnaire_info =
            Questionnaires::where('id', $request->questionnaireId)
                ->where('status', '0')
                ->where('logdel', '0')
                ->get();

        return response()->json($questionnaire_info);
    }

    public function changeQuestionnaireStatus(Request $request){
        date_default_timezone_set('Asia/Manila');

        DB::beginTransaction();
        try{
            Questionnaires::where('id', $request->questionnaire_id)->where('logdel', 0)->update(['status' => $request->status]);
            DB::commit();
            return response()->json(['hasError' => 0]);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['hasError' => 1, 'exceptionError' => $e]);
        }
    }

    // ===========================================================================================================================================================
    // ================================================================= Questionnaire Details ===================================================================
    // ===========================================================================================================================================================
	public function viewQuestionnaireDetails(Request $request){
        $questionnaire_details = QuestionnaireDetails::where('questionnaire_id', $request->questionnaireId)->where('revision', $request->questionnaireRevision)->where('status', 0)->where('logdel', 0)->get();
        $passingScore = Questionnaires::where('id', $request->questionnaireId)->where('status', 0)->where('logdel', 0)->value('passing_score');
        $totalPoints = $questionnaire_details->sum('points');

        return DataTables::of($questionnaire_details)
        ->with([
            'totalPoints' => $totalPoints,
            'passingScore' => $passingScore,
        ])
        ->addColumn('action', function($questionnaire_detail){
            $result =   '<center>';

            if($questionnaire_detail->status == 0){
                $result .= '<button type="button" class="btn btn-dark btn-sm text-center actionUpdateQuestionnaireDetails mr-2" questionnaire_detail-id="' . $questionnaire_detail->id . '" questionnaire_detail-revision="' . $questionnaire_detail->revision . '" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaireDetails" title="Update Questionnaire Details"><i class="fas fa-edit"></i></button>';
                $result .= '<button type="button" class="btn btn-danger btn-sm text-center actionChangeQuestionnaireStatus" questionnaire_detail-id="' . $questionnaire_detail->id . '" questionnaire_detail-revision="' . $questionnaire_detail->revision . '" status="1" data-toggle="modal" data-target="#modalChangeQuestionnaireDetailsStatus" title="Deactive Questionnaire"><i class="fas fa-redo"></i></button>';
            }else{
                $result .= '<button type="button" class="btn btn-warning btn-sm text-center actionChangeQuestionnaireStatus" questionnaire_detail-id="' . $questionnaire_detail->id . '" questionnaire_detail-revision="' . $questionnaire_detail->revision . '" status="0" data-toggle="modal" data-target="#modalChangeQuestionnaireDetailsStatus" title="Activate Questionnaire"><i class="fas fa-redo"></i></button>';
            }

            $result .=  '</center>';
            return $result;
        })

        ->addColumn('status', function($questionnaire_detail){
            $result = "";
            if($questionnaire_detail->status == 0){
                $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
            }
            else{
                $result .= '<center><span class="badge badge-pill badge-danger">Inactive</span></center>';
            }
            return $result;
        })

        ->addColumn('image', function($questionnaire_detail){
            $result =   '<center>';
            if($questionnaire_detail->image != '' || $questionnaire_detail->image != NULL){
                $result .=  '<a style="" class="image" href="storage/app/public/questionnaire_attachment/'. $questionnaire_detail->image .'" target="_blank">
                            <img src="storage/app/public/questionnaire_attachment/' . $questionnaire_detail->image . '" style="max-width: 140px; max-height: 115px; width: 130px; height: auto; border: 1px solid #000;"></a>';
            }
            $result .=  '</center>';

            return $result;
        })

        ->addColumn('question', function($questionnaire_detail) {
            $questions = json_decode($questionnaire_detail->answer_choices_question, true);
            if (!is_array($questions)) return '';

            $uniqueQuestions = collect($questions)->pluck('question')->unique();

            return $uniqueQuestions->implode('<br><br>');
        })

        ->addColumn('choices', function($questionnaire_detail) {
            $questions = json_decode($questionnaire_detail->answer_choices_question, true);
            if (!is_array($questions)) return '';

            $uniqueChoices = collect($questions)
                ->pluck('choices')
                ->flatten()
                ->unique();

            return $uniqueChoices->implode('<br>');
        })

        ->addColumn('answer', function($questionnaire_detail) {
            $questions = json_decode($questionnaire_detail->answer_choices_question, true);
            if (!is_array($questions)) return '';

            $uniqueAnswers = collect($questions)
                ->pluck('answer')
                ->map(function ($answer) {
                    return str_replace(',', '<br>', $answer);
                });

            return $uniqueAnswers->implode('<br><br>');
        })

        ->rawColumns(['action','status', 'image', 'question', 'choices', 'answer'])
        ->make(true);
    }

    public function createUpdateQuestionnaireDetails(Request $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->all();
        $type = '';
        switch ($request->questionnaire_category_type) {
            case '0':
                $questions = $request->input('questionnaire_question', '[]');
                $choices   = $request->input('choices', '[]');
                $answers   = $request->input('answer', '[]');

                $questions = is_array($questions) ? $questions : json_decode($questions, true);
                $choices   = is_array($choices) ? $choices : json_decode($choices, true);
                $answers   = is_array($answers) ? $answers : json_decode($answers, true);

                if (!$questions) $questions = [];
                if (!$choices) $choices = [];
                if (!$answers) $answers = [];

                $result = [];
                foreach ($questions as $index => $question) {
                    $result[] = [
                        'question' => $question,
                        'choices'  => $choices,
                        'answer'   => isset($answers[$index]) ? $answers[$index] : null,
                    ];
                }
                break;

            case '1':
                $type       = '1';
                $questions  = $request->input('questionnaire_question', '[]');
                $choices    = $request->input('choices', '[]');
                $answers    = $request->input('identification', '[]');

                $questions  = is_array($questions) ? $questions : json_decode($questions, true);
                $choices    = is_array($choices) ? $choices : json_decode($choices, true);
                $answers    = is_array($answers) ? $answers : json_decode($answers, true);

                if (!$questions) $questions = [];
                if (!$choices) $choices = [];
                if (!$answers) $answers = [];

                $result = [];
                foreach ($questions as $index => $question) {
                    $result[] = [
                        'question' => $question,
                        'choices'  => $choices,
                        'answer'   => isset($answers[$index]) ? $answers[$index] : null,
                    ];
                }
                break;

            case '2':
                $type       = '2';
                $questions  = $request->input('questionnaire_question', '[]');
                $choices    = $request->input('choices', '[]');
                $answers    = $request->input('answer', '[]');

                $questions  = is_array($questions) ? $questions : json_decode($questions, true);
                $choices    = is_array($choices) ? $choices : json_decode($choices, true);
                $answers    = is_array($answers) ? $answers : json_decode($answers, true);

                if (!$questions) $questions = [];
                if (!$choices) $choices = [];
                if (!$answers) $answers = [];

                $result = [];
                foreach ($questions as $index => $question) {
                    $result[] = [
                        'question' => $question,
                        'choices'  => $choices,
                        'answer'   => isset($answers[$index]) ? $answers[$index] : null,
                    ];
                }
                break;

            default:
                return response()->json(['result' => 1]);
                break;
        }
        $validator = Validator::make($data, [
            'questionnaire_category_type' => 'required',
            'questionnaire_points'        => 'required',
        ]);

        $questionnaires_record = [
            'category_type'             => $request->questionnaire_category_type,
            'points'                    => $request->questionnaire_points,
            'answer_choices_question'   => json_encode($result)
        ];

        $map = [
            '1' => 'question_type',
            '2' => 'questionnaire_description',
        ];

        if (isset($map[$type])) {
            $questionnaires_record[$type === '1' ? 'type' : 'description'] = $request->{$map[$type]};
        }

        if ($request->hasFile('upload_image')) {
            $original_filename = $request->file('upload_image')->getClientOriginalName();
            $filename = preg_replace('/[^A-Za-z0-9\.\-]/', '_', $original_filename);
            Storage::putFileAs('public/questionnaire_attachment', $request->file('upload_image'), $filename);
        }else{
            $filename = preg_replace('/[^A-Za-z0-9\.\-]/', '_', $request->upload_image);
        }

        if($validator->fails()){
            return response()->json(['validationHasError' => 1, 'error' => $validator->errors()]);
        }else{
            DB::beginTransaction();
            try{
                $exists = QuestionnaireDetails::where('image', $filename)
                    ->where('logdel', 0)
                    ->exists();

                if ($exists && $request->upload_image != '') {
                    return response()->json(['result' => 1]);
                }

                if($request->questionnaire_details_pkid == ''){

                    $test = QuestionnaireDetails::where('questionnaire_id', $request->questionnaire_details_fkid)
                        ->where('revision', $request->questionnaire_details_revision)
                        ->where('status', 0)
                        ->where('logdel', 0)
                        ->orderBy('exam_no', 'DESC')
                        ->first();

                    $numbering = $test ? intval($test->exam_no) + 1 : 1;

                    $questionnaires_record['exam_no'] = $numbering;
                    $questionnaires_record['questionnaire_id'] = $request->questionnaire_details_fkid;
                    $questionnaires_record['image'] = $filename;
                    $questionnaires_record['created_by'] = '';
                    $questionnaires_record['created_at'] = date('Y-m-d H:i:s');

                    QuestionnaireDetails::insert($questionnaires_record);
                }else{
                    $record = QuestionnaireDetails::where('id', $request->questionnaire_details_pkid)
                        ->where('status', 0)
                        ->where('logdel', 0)
                        ->first();

                    $questionnaires_record['image']             = $request->upload_image;
                    $questionnaires_record['updated_by']        = '';
                    $questionnaires_record['updated_at']        = date('Y-m-d H:i:s');
                    // return $questionnaires_record;
                    if($record->image == $request->upload_image){
                        QuestionnaireDetails::where('id', $request->questionnaire_details_pkid)->where('status', 0)->where('logdel', 0)->update($questionnaires_record);
                        // $record->update($questionnaires_record);
                    }else{
                        if($exists){
                            return response()->json(['result' => 1]);
                        }
                        QuestionnaireDetails::where('id', $request->questionnaire_details_pkid)->where('status', 0)->where('logdel', 0)->update($questionnaires_record);
                        // $record->update($questionnaires_record);
                    }
                }

                DB::commit();
                return response()->json(['hasError' => 0]);
            }catch (\Exception $e){
                DB::rollback();
                return response()->json(['hasError' => 1, 'exceptionError' => $e]);
            }
        }
    }

    public function getQuestionnaireDetailsById(Request $request){
        date_default_timezone_set('Asia/Manila');

        $questionnaire_details =
            QuestionnaireDetails::
                where('id', $request->questionnaireDetailId)
                ->where('revision', $request->questionnaireDetailRevision)
                ->where('status', '0')
                ->where('logdel', '0')
                ->get();

        return response()->json($questionnaire_details);
    }
}
