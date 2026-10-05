<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\GrrQuestionnaireSettings;
use App\Model\GrrSettings;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GrrQuestionnaireSettingsController extends Controller
{
    public function viewGrrQuestionnaire(Request $request){
        $data = $request->all();
        $GrrQuestionnaireSettings = GrrQuestionnaireSettings::with(['grrSample'])
        ->where('grr_setting_id', $request->grr_setting_id)
        ->get();

        // return $GrrQuestionnaireSettings;

         return DataTables::of($GrrQuestionnaireSettings)
        ->addColumn('action', function($GrrQuestionnaireSettings){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-secondary btn-sm btnEdit mr-1' data-id='$GrrQuestionnaireSettings->id'><i class='fas fa-edit'></i></button>";
            if($GrrQuestionnaireSettings->status == 0){
                $result .= "<button class='btn btn-danger btn-sm btnDisable' data-id='$GrrQuestionnaireSettings->id'><i class='fas fa-ban'></i></button>";
            }
            else{
                $result .= "<button class='btn btn-success btn-sm btnEnable' data-id='$GrrQuestionnaireSettings->id'><i class='fas fa-undo'></i></button>";
            }
            $result .= "</center>";
            return $result;
        })
         ->addColumn('grr_no', function($GrrQuestionnaireSettings){
            $result = "";
            $result .= "<center>";

            $result .= $GrrQuestionnaireSettings->grrSample->pluck('grr_no')->implode(', ');
            $result .= "</center>";

            return $result;
        })
         ->addColumn('grr_sample', function($GrrQuestionnaireSettings){
            $result = "";
            $result .= "<center>";

            $result .= $GrrQuestionnaireSettings->grrSample->pluck('grr_sample')->implode(', ');
            $result .= "</center>";

            return $result;
        })
        ->addColumn('status_label', function($GrrQuestionnaireSettings){
            $result = "";
            $result .= "<center>";

            if($GrrQuestionnaireSettings->status == 0){
                $result .= "<span class='badge rounded-pill bg-success'>Active</span>";
            }else{
                $result .= "<span class='badge rounded-pill bg-danger'>Inactive</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->rawColumns(['action', 'grr_no', 'grr_sample', 'status_label'])
        ->make(true);
    }


    public function addGrrQuestionnaire(Request $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->all();
        // return $data;
        Validator::make($data, [
            'q_sample_id' => 'required',
            'reference' => 'required|string',
            'defect' => 'required|string',
            'location' => 'required|string',
        ])->validate();

        if($request->questionnaire_id != NULL || $request->questionnaire_id != ''){
            $grrQuestionnaire = GrrQuestionnaireSettings::find($request->questionnaire_id);
            $grrQuestionnaire->grr_setting_id = $data['q_sample_id'];
            $grrQuestionnaire->reference = $data['reference'];
            $grrQuestionnaire->defect = $data['defect'];
            $grrQuestionnaire->location = $data['location'];
            $grrQuestionnaire->save();
            return response()->json(['result' => 1, 'message' => 'Successfully updated!']);
        }else{
            $grrQuestionnaire = new GrrQuestionnaireSettings();
            $grrQuestionnaire->grr_setting_id = $data['q_sample_id'];
            $grrQuestionnaire->reference = $data['reference'];
            $grrQuestionnaire->defect = $data['defect'];
            $grrQuestionnaire->location = $data['location'];
            $grrQuestionnaire->status = 0;
            $grrQuestionnaire->save();
            return response()->json(['result' => 1, 'message' => 'Successfully added!']);
        }
        

        return response()->json(['result' => 0, 'message' => 'Failed to save data.']);
    }
    
    public function getGrrQuestionnaireById(Request $request){
        $id = $request->id;

        if (!$id) {
            return response()->json([]);
        }

        $grrQuestionnaire = GrrQuestionnaireSettings::with('grrSample')->find($id);

        return response()->json($grrQuestionnaire);
    }

    public function updateGrrQuestionnaireStatus(Request $request){
        date_default_timezone_set('Asia/Manila');
        $id = $request->id;

        if (!$id) {
            return response()->json(['success' => false, 'message' => 'ID is required.']);
        }

        $grrQuestionnaire = GrrQuestionnaireSettings::find($id);
        if (!$grrQuestionnaire) {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }

        $grrQuestionnaire->status = $grrQuestionnaire->status == 0 ? 1 : 0;
        $grrQuestionnaire->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);  
    }
}
