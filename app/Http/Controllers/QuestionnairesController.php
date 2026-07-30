<?php
namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

use Yajra\DataTables\Facades\DataTables;

use App\Model\ExamTitle;
use App\Model\Questionnaires;
use App\Model\ExamResultDetails;
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
                $result .=  '       <i class="fa fa-cog"></i>';
                $result .=  '   </button>';
                $result .=  '   <div class="dropdown-menu dropdown-menu-right">';

                if($questionnaire->revision != 0){
                    $result .=  '   <button type="button" class="btn text-center dropdown-item actionViewRevisionQuestionnaire" questionnaire-id="' . $questionnaire->id . '" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaire" title="View Questionnaire"><i class="fa fa-eye"></i> Revision</button>';
                }

                $result .=  '       <button type="button" class="btn text-center dropdown-item actionUpdateQuestionnaire" questionnaire-id="' . $questionnaire->id . '" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaire" title="Update Questionnaire"><i class="fa fa-edit"></i> Update</button>';
                $result .=  '       <button type="button" class="btn text-center dropdown-item actionQuestionnaireDetails" questionnaire-id="' . $questionnaire->id . '" questionnaire-revision="' . $questionnaire->revision . '" questionnaire-exam_title="' . $questionnaire->exam_title . '" questionnaire-description="' . $questionnaire->description . '" data-toggle="modal" data-target="#modalQuestionnaireDetails" title="Questionnaire Details"><i class="fa fa-list-ul"></i> Details</button>';
                $result .=  '       <button type="button" class="btn text-center dropdown-item actionChangeQuestionnaireStatus" questionnaire-id="' . $questionnaire->id . '" status="1" data-toggle="modal" data-target="#modalChangeQuestionnaireStatus" title="Deactivate Questionnaire"><i class="fa fa-ban"></i> Inactive</button>';
                $result .=  '   </div>';
                $result .=  '</div>';

            }else{
                $result .= '<button type="button" class="btn btn-warning btn-sm text-center actionChangeQuestionnaireStatus" questionnaire-id="' . $questionnaire->id . '" status="0" data-toggle="modal" data-target="#modalChangeQuestionnaireStatus" title="Activate Questionnaire"><i class="fas fa-redo"></i></button>';
            }

            $result .= '<a  href="view_pdf_questionnaire/' . $questionnaire->id . '" target="_blank"
                                class="btn btn-warning btn-sm mt-3 w-100"
                                target="_blank"
                                title="View PDF">
                                <i class="fa fa-eye"></i>
                            </a>';
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

    public function getExamTitle(Request $request){
        date_default_timezone_set('Asia/Manila');

        $exam_title = ExamTitle::where('status', 0)->where('logdel', 0)->orderBy('exam_title', 'ASC')->get();

        return response()->json($exam_title);
    }

    public function createUpdateQuestionnaire(Request $request){
        date_default_timezone_set('Asia/Manila');

        $data = $request->all();
        $validator = Validator::make($data, [
            'questionnaire_category'        => 'required',
            'questionnaire_title'           => 'required',
            'questionnaire_description'     => 'required',
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
            'description'       => $request->questionnaire_description,
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
                $check_data = Questionnaires::where('id', $request->questionnaire_id)->where('logdel', 0)->exists();
                if($check_data == 0){
                    $exists = Questionnaires::where([
                        'id'   => $request->questionnaire_id,
                        'logdel' => 0,
                    ])->exists();

                    if($exists){
                        return response()->json(['result' => 1]);
                    }

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
                        $questionnaires_record['updated_at']    = date('Y-m-d H:i:s');

                        Questionnaires::where('id', $request->questionnaire_id)->where('logdel', 0)->update($questionnaires_record);

                        $questionnaires_record['id'] = $request->questionnaire_id;
                        ExamResultDetails::where('questionnaire_id', $request->questionnaire_id)
                            ->where('logdel', 0)
                            ->where('status', 0)
                            ->update([
                                'questionnaire' => json_encode($questionnaires_record),
                            ]);
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

    public function viewPdfQuestionnaire($id){
        $questionnaire_detail = Questionnaires::with([
                'questionnaire_details' => function ($query) {
                    $query->where('status', 0)
                        ->where('logdel', 0)
                        ->orderBy('exam_no', 'asc');
                }
            ])
            ->where('id', $id)
            ->where('status', 0)
            ->where('logdel', 0)
            ->firstOrFail();

        // $pdf = Pdf::loadView('theoretical_exam.questionnaire_pdf_1x', [
        $pdf = Pdf::loadView('theoretical_exam.questionnaire_pdf', [
            'questionnaire' => $questionnaire_detail
        ]);


        return $pdf->stream(
            'questionnaire_' . $questionnaire_detail->id . '.pdf'
        );
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
                $result .= '<button type="button" class="btn btn-danger btn-sm text-center actionChangeQuestionnaireDetailsStatus" questionnaire_detail-id="' . $questionnaire_detail->id . '" questionnaire_detail-revision="' . $questionnaire_detail->revision . '" status="1" data-toggle="modal" data-target="#modalChangeQuestionnaireDetailsStatus" title="Deactive Questionnaire"><i class="fas fa-redo"></i></button>';
            }else{
                $result .= '<button type="button" class="btn btn-warning btn-sm text-center actionChangeQuestionnaireDetailsStatus" questionnaire_detail-id="' . $questionnaire_detail->id . '" questionnaire_detail-revision="' . $questionnaire_detail->revision . '" status="0" data-toggle="modal" data-target="#modalChangeQuestionnaireDetailsStatus" title="Activate Questionnaire"><i class="fas fa-redo"></i></button>';
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
                    return str_replace(' || ', '<br>', $answer);
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
        switch($request->questionnaire_category_type){
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

        if(isset($map[$type])){
            $questionnaires_record[$type === '1' ? 'type' : 'description'] = $request->{$map[$type]};
        }

        if($request->hasFile('upload_image')){
            $original_filename = $request->file('upload_image')->getClientOriginalName();
            $filename = preg_replace('/[^A-Za-z0-9\.\-]/', '_', $original_filename);

            Storage::putFileAs(
                'public/questionnaire_attachment',
                $request->file('upload_image'),
                $filename
            );
        }else{
            $filename = !empty($request->upload_image) ? preg_replace('/[^A-Za-z0-9\.\-]/', '_', $request->upload_image) : null;
        }

        if($validator->fails()){
            return response()->json(['validationHasError' => 1, 'error' => $validator->errors()]);
        }else{
            // DB::beginTransaction();
            // try{
                if(!empty($filename)){
                    $duplicate = QuestionnaireDetails::where('image', $filename)
                        ->where('logdel', 0);

                    if(!empty($request->questionnaire_details_pkid)){
                        $duplicate->where('id', '!=', $request->questionnaire_details_pkid);
                    }

                    if($duplicate->exists()){
                        return response()->json(['result' => 1]);
                    }
                }

                $test =
                    QuestionnaireDetails::where('questionnaire_id', $request->questionnaire_details_fkid)
                        ->where('revision', $request->questionnaire_details_revision)
                        ->where('status', 0)
                        ->where('logdel', 0)
                        ->get();


                $tist = Questionnaires::where('id', $request->questionnaire_details_fkid)
                    ->where('status', 0)
                    ->where('logdel', 0)
                    ->first();

                $total_points = $test->sum('points');
                $new_total_points = $total_points + $request->questionnaire_points;
                $passing_score = optional($tist)->passing_score ?? 0;
                // $passing_score = optional($test->first()->questionare_title_info)->passing_score ?? 0;

                if($request->questionnaire_details_pkid == ''){
                    if ($new_total_points > $passing_score) {
                        return response()->json([
                            'result'  => 0,
                            'message' => 'Cannot insert. Total questionnaire points exceed the passing score.'
                        ]);
                    }

                    $existing_no = $test->pluck('exam_no')
                        ->sort()
                        ->values()
                        ->toArray();

                    $next_package_count = 1;

                    foreach ($existing_no as $count) {
                        if ($count == $next_package_count) {
                            $next_package_count++;
                        } else {
                            break;
                        }
                    }

                    $questionnaires_record['exam_no'] = $next_package_count;
                    $questionnaires_record['questionnaire_id'] = $request->questionnaire_details_fkid;
                    $questionnaires_record['points'] = $request->questionnaire_points;
                    $questionnaires_record['image'] = $filename;
                    $questionnaires_record['created_by'] = '';
                    $questionnaires_record['created_at'] = now();

                    QuestionnaireDetails::insert($questionnaires_record);
                }else{
                    $record = QuestionnaireDetails::where('id', $request->questionnaire_details_pkid)
                        ->where('status', 0)
                        ->where('logdel', 0)
                        ->first();

                        $for_update_total_points = $total_points - $record->points + $request->questionnaire_points;
                    if ($for_update_total_points > $passing_score) {
                        return response()->json([
                            'result'  => 0,
                            'message' => 'Cannot update. Total questionnaire points exceed the passing score.'
                        ]);
                    }

                    if(!$record){
                        return response()->json(['hasError' => 1, 'message' => 'Record not found.']);
                    }

                    if(!empty($filename)){
                        $questionnaires_record['image'] = $filename;
                    }

                    $questionnaires_record['updated_by'] = '';
                    $questionnaires_record['updated_at'] = date('Y-m-d H:i:s');

                    QuestionnaireDetails::where('id', $request->questionnaire_details_pkid)
                        ->where('status', 0)
                        ->where('logdel', 0)
                        ->update($questionnaires_record);
                }
                // DB::commit();
                return response()->json(['hasError' => 0]);
            // }catch (\Exception $e){
            //     DB::rollback();
            //     return response()->json(['hasError' => 1, 'exceptionError' => $e]);
            // }
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

    public function changeQuestionnaireDetailsStatus(Request $request){
        date_default_timezone_set('Asia/Manila');

        DB::beginTransaction();
        try{
            QuestionnaireDetails::where('id', $request->questionnaire_id)->where('logdel', 0)->update(['status' => $request->status]);
            DB::commit();
            return response()->json(['hasError' => 0]);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['hasError' => 1, 'exceptionError' => $e]);
        }
    }

    // public function reorder(Request $request){
    //     DB::transaction(function () use ($request) {
    //         foreach($request->rows as $row){
    //             QuestionnaireDetails::where('id',$row['id'])->where('status', 0)->where('logdel', 0)
    //                 ->update([
    //                     'exam_no'=>$row['exam_no']
    //                 ]);
    //         }
    //     });

    //     return response()->json([ 'success'=> true ]);
    // }

    public function reorder(Request $request){
        DB::transaction(function () use ($request) {
            foreach ($request->rows as $row) {
                QuestionnaireDetails::query()
                    ->where('id', $row['id'])
                    ->where('status', 0)
                    ->where('logdel', 0)
                    ->update([
                        'exam_no' => $row['exam_no']
                    ]);
            }

            $details = QuestionnaireDetails::query()
                ->where('questionnaire_id', $request->rows[0]['questionnaire_id'])
                ->where('status', 0)
                ->where('logdel', 0)
                ->orderBy('exam_no')
                ->get();

            $currentNumbers = $details->pluck('exam_no')->map(function ($value) {
                return (int) $value;
            })->values()->toArray();

            $expectedNumbers = range(1, $details->count());

            if ($currentNumbers !== $expectedNumbers) {
                foreach ($details as $index => $detail) {
                    $detail->update([
                        'exam_no' => $index + 1,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Questionnaire reordered successfully.'
        ]);
    }
}
