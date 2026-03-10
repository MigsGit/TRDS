<?php
namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use DataTables;

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
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
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

        return DataTables::of($questionnaire_details)
        ->addColumn('action', function($questionnaire_detail){
            $result =   '<center>';
            
            // if($questionnaire_detail->status == 0){
            //     $result .=  '<div class="btn-group">';
            //     $result .=  '   <button type="button" class="btn btn-dark dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">';
            //     $result .=  '   <i class="fa fa-cog"></i>'; 
            //     $result .=  '</button>';
            //     $result .=  '<div class="dropdown-menu dropdown-menu-right">';
            //     if($questionnaire_detail->revision != 0){
            //         $result .=  '<button type="button" class="btn text-center dropdown-item actionViewRevisionQuestionnaire" questionnaire_detail-id="' . $questionnaire_detail->id . '" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaire" title="View Questionnaire"><i class="fa fa-eye"></i> Revision</button>';
            //     }
            //     $result .=  '   <button type="button" class="btn text-center dropdown-item actionUpdateQuestionnaire" questionnaire_detail-id="' . $questionnaire_detail->id . '" data-toggle="modal" data-target="#modalCreateUpdateQuestionnaire" title="Update Questionnaire"><i class="fa fa-edit"></i> Update</button>';
            //     $result .=  '   <button type="button" class="btn text-center dropdown-item actionQuestionnaireDetails" questionnaire_detail-id="' . $questionnaire_detail->id . '" data-toggle="modal" data-target="#modalQuestionnaireDetails" title="Questionnaire Details"><i class="fa fa-list-ul"></i> Details</button>';
            //     $result .=  '   <button type="button" class="btn text-center dropdown-item actionChangeQuestionnaireStatus" questionnaire_detail-id="' . $questionnaire_detail->id . '" status="1" data-toggle="modal" data-target="#modalChangeQuestionnaireStatus" title="Deactivate Questionnaire"><i class="fa fa-ban"></i> Inactive</button>';
            //     $result .=  '</button>';
            // }else{
            //     $result .= '<button type="button" class="btn btn-warning btn-sm text-center actionChangeQuestionnaireStatus" questionnaire_detail-id="' . $questionnaire_detail->id . '" status="0" data-toggle="modal" data-target="#modalChangeQuestionnaireStatus" title="Activate Questionnaire"><i class="fas fa-redo"></i></button>';
            // }
            
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
                            <img src="storage/app/public/questionnaire_attachment/' . $questionnaire_detail->image . '" style="max-width: 170px; max-height: 125px; width: 150px; height: auto; border: 1px solid #000;"></a>';            
            }
            $result .=  '</center>';

            return $result;
        })

        ->addColumn('choices', function($questionnaire_detail) {
            $choices = json_decode($questionnaire_detail->choices, true);
            $result = collect($choices)->pluck('choice')->implode("<br><br>");

            return $result;
        })
        
        ->addColumn('answer', function($questionnaire_detail) {
            $result =   '';
            if($questionnaire_detail->type == 'Identification'){
                $result .= $questionnaire_detail->answer;
            }else{
                $choices = json_decode($questionnaire_detail->choices, true);
                $result .= collect($choices)
                    ->filter(function($item) {
                        return $item['answer'] == 1;
                    })
                    ->pluck('choice')
                    ->implode("<br><br>");
            }

            return $result;
        })
        ->rawColumns(['action','status', 'image', 'choices', 'answer'])
        ->make(true);
    }

    public function createUpdateQuestionnaireDetails(Request $request){
        date_default_timezone_set('Asia/Manila');

        $data = $request->all();

        switch ($request->questionnaire_category_type) {
            case '0':
                $validator = Validator::make($data, [
                    'questionnaire_category_type'   => 'required',
                    'questionnaire_points'          => 'required',
                    'questionnaire_question'        => 'required',
                    'choices'                       => 'required',
                    'answer'                        => 'required',
                ]);

                $choices = $request->choices;
                $answers = $request->answer;
                
                $options = [];
                
                foreach ($choices as $i => $choice) {
                    $options[] = [
                        'choice' => $choice,
                        'answer' => (int)$answers[$i]
                    ];
                }
                
                $questionnaires_record = [
                    'category_type' => $request->questionnaire_category_type,
                    'points'        => $request->questionnaire_points,
                    'question'      => $request->questionnaire_question,
                    'choices'       => json_encode($options)
                ];

                break;
            
            case '1':
                $validator = Validator::make($data, [
                    'questionnaire_category_type'   => 'required',
                    'questionnaire_points'          => 'required',
                    'questionnaire_question'        => 'required',
                    'question_type'                 => 'required',
                ]);
                $questionnaires_record = [
                    'category_type' => $request->questionnaire_category_type,
                    'points'        => $request->questionnaire_points,
                    'question'      => $request->questionnaire_question,
                    'type'          => $request->question_type,
                ];

                if($request->question_type == 'Identification'){
                    $questionnaires_record['answer']  = $request->identification;
                }
                break;

            case '2':
                // Get raw inputs (JSON strings from hidden inputs)
                $questions = $request->input('questionnaire_question', '[]');
                $choices   = $request->input('choices', '[]');
                $answers   = $request->input('answer', '[]');

                // Decode JSON strings into arrays
                $questions = is_array($questions) ? $questions : json_decode($questions, true);
                $choices   = is_array($choices) ? $choices : json_decode($choices, true);
                $answers   = is_array($answers) ? $answers : json_decode($answers, true);

                // Make sure they are arrays
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

                // return response()->json($result);

                $validator = Validator::make($data, [
                    'questionnaire_category_type'   => 'required',
                    'questionnaire_points'          => 'required',
                ]);
                $questionnaires_record = [
                    'category_type' => $request->questionnaire_category_type,
                    'points'        => $request->questionnaire_points,
                    'question_choices_answer'       => json_encode($result)
                ];
                break;

            default:
                return response()->json(['result' => 1]);
                break;
        }
        // return $request;
        if ($request->hasFile('upload_image')) {
            $original_filename = $request->file('upload_image')->getClientOriginalName();
            $filename = preg_replace('/[^A-Za-z0-9\.\-]/', '_', $original_filename);
            Storage::putFileAs('public/questionnaire_attachment', $request->file('upload_image'), $filename);
        }else{
            $filename = preg_replace('/[^A-Za-z0-9\.\-]/', '_', $request->upload_image);
        }

        if($validator->fails()){
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }else{
            // DB::beginTransaction();
            // try{
                // $count_production_data = QuestionnaireDetails::where('questionnaire_id', $request->questionnaire_details_id)->where('logdel', 0)->exists();
                // if($count_production_data == 0){
                //     $exists = QuestionnaireDetails::where([
                //         'id'   => $request->questionnaire_details_id,
                //         'logdel' => 0,
                //     ])->exists();

                //     if($exists){
                //         return response()->json(['result' => 1]);
                //     }

                    $questionnaires_record['questionnaire_id']  = $request->questionnaire_details_id;
                    $questionnaires_record['image']             = $filename;
                    $questionnaires_record['created_by']        = '';
                    $questionnaires_record['created_at']        = date('Y-m-d H:i:s');

                    QuestionnaireDetails::insert($questionnaires_record);
                // }else{
                //     $data_checking = array_merge($questionnaires_record, [
                //         'id'     => $request->questionnaire_details_id,
                //         'logdel' => 0,
                //     ]);

                //     $exists = QuestionnaireDetails::where($data_checking)->exists();

                //     if($exists){
                //         return response()->json(['result' => 1]);
                //     }else{
                //         $questionnaires_record['updated_by']    = '';
                //         $questionnaires_record['updated_at']    = date('Y-m-d H:i:s');

                //         QuestionnaireDetails::where('id', $request->questionnaire_details_id)->where('logdel', 0)->update($questionnaires_record);
                //     }
                // }

                // DB::commit();
                return response()->json(['hasError' => 0]);
            // }catch (\Exception $e){
            //     DB::rollback();
            //     return response()->json(['hasError' => 1, 'exceptionError' => $e]);
            // }
        }
    }

}
