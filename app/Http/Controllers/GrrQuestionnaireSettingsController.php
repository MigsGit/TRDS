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
        $validator = Validator::make($request->all(), [
            'grr_setting_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => []]);
        }

        // Active items first in sequence order; inactive items (not sequenced) after them
        $questionnaires = GrrQuestionnaireSettings::where('grr_setting_id', $request->grr_setting_id)
            ->orderBy('status')
            ->ordered()
            ->get(['id', 'reference', 'defect', 'location', 'status']);

        return response()->json(['data' => $questionnaires]);
    }

    public function updateGrrQuestionnaireOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'grr_setting_id' => 'required|integer|exists:grr_settings,id',
            'ordered_ids' => 'required|array|min:1',
            'ordered_ids.*' => 'required|integer|distinct',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid questionnaire order.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $settingId = (int) $request->grr_setting_id;
        $orderedIds = array_map('intval', $request->ordered_ids);

        $saved = DB::transaction(function () use ($settingId, $orderedIds) {
            // Only active items are part of the sequence
            $currentIds = GrrQuestionnaireSettings::where('grr_setting_id', $settingId)
                ->where('status', 0)
                ->ordered()
                ->lockForUpdate()
                ->pluck('id')
                ->map(function ($id) { return (int) $id; })
                ->all();

            // Every submitted ID must be an active item of this GRR sample.
            if (!empty(array_diff($orderedIds, $currentIds))) {
                return false;
            }

            // Items added by someone else after the page loaded keep their relative order at the end.
            $finalIds = array_merge($orderedIds, array_values(array_diff($currentIds, $orderedIds)));

            foreach ($finalIds as $index => $id) {
                GrrQuestionnaireSettings::where('id', $id)
                    ->where('grr_setting_id', $settingId)
                    ->update(['sort_order' => $index + 1]);
            }

            return true;
        });

        if (!$saved) {
            return response()->json([
                'success' => false,
                'message' => 'One or more questionnaire items are inactive or do not belong to this GRR sample. Please refresh the list.',
            ], 422);
        }

        return response()->json(['success' => true, 'message' => 'Questionnaire order updated.']);
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
            DB::transaction(function () use ($data) {
                $lastOrder = GrrQuestionnaireSettings::where('grr_setting_id', $data['q_sample_id'])
                    ->lockForUpdate()
                    ->max('sort_order');

                $grrQuestionnaire = new GrrQuestionnaireSettings();
                $grrQuestionnaire->grr_setting_id = $data['q_sample_id'];
                $grrQuestionnaire->reference = $data['reference'];
                $grrQuestionnaire->defect = $data['defect'];
                $grrQuestionnaire->location = $data['location'];
                $grrQuestionnaire->status = 0;
                $grrQuestionnaire->sort_order = ((int) $lastOrder) + 1;
                $grrQuestionnaire->save();
            });
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

        DB::transaction(function () use ($grrQuestionnaire) {
            $settingId = $grrQuestionnaire->grr_setting_id;

            // Lock the whole sample so concurrent toggles/reorders can't interleave
            GrrQuestionnaireSettings::where('grr_setting_id', $settingId)->lockForUpdate()->get(['id']);

            if ($grrQuestionnaire->status == 0) {
                // Disabling: leave the sequence and close the gap
                $grrQuestionnaire->status = 1;
                $grrQuestionnaire->sort_order = 0;
                $grrQuestionnaire->save();

                $this->resequenceActive($settingId);
            } else {
                // Enabling: rejoin the sequence at the end
                $lastOrder = GrrQuestionnaireSettings::where('grr_setting_id', $settingId)
                    ->where('status', 0)
                    ->max('sort_order');

                $grrQuestionnaire->status = 0;
                $grrQuestionnaire->sort_order = ((int) $lastOrder) + 1;
                $grrQuestionnaire->save();
            }
        });

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);  
    }

    private function resequenceActive($settingId){
        $activeIds = GrrQuestionnaireSettings::where('grr_setting_id', $settingId)
            ->where('status', 0)
            ->ordered()
            ->pluck('id');

        foreach ($activeIds as $index => $activeId) {
            GrrQuestionnaireSettings::where('id', $activeId)->update(['sort_order' => $index + 1]);
        }
    }
}
