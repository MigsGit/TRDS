<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use DataTables;
use App\Model\InspectorSkillChart\InspectorSkillChartSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InspSkillChartSettingController extends Controller
{
    public function viewProcessStationsInfo(Request $request){
        $examination_details = InspectorSkillChartSetting::get();

        return DataTables::of($examination_details)
        ->addColumn('action', function($examination_details){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-secondary btn-sm btnEdit mr-1' data-id='$examination_details->id'><i class='fas fa-edit'></i></button>";
            if($examination_details->status == 0){
                $result .= "<button class='btn btn-danger btn-sm btnDisable' data-id='$examination_details->id'><i class='fas fa-ban'></i></button>";
            }
            else{
                $result .= "<button class='btn btn-success btn-sm btnEnable' data-id='$examination_details->id'><i class='fas fa-undo'></i></button>";
            }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('status_label', function($examination_details){
            $result = "";
            $result .= "<center>";

            if($examination_details->status == 0){
                $result .= "<span class='badge rounded-pill bg-success'>Active</span>";
            }else{
                $result .= "<span class='badge rounded-pill bg-danger'>Inactive</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->rawColumns(['action', 'status_label'])
        ->make(true);
    }

    public function addProcessStationInfo(Request $request){
        $validation = array(
            'section' => ['required', 'string', 'max:255'],
            'process_station' => ['required', 'string', 'max:255']
        );

        $data = $request->all();
        $validator = Validator::make($data, $validation);
        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }else{
            DB::beginTransaction();
            
            if($request->product_line != 'N/A'){
                $product_lines = implode(',', $request->product_line);
            }else{
                $product_lines = $request->product_line;
            }
                  
            try{
                $process_array = array(
                    'section' => $request->section,
                    'skill_category' => $request->skill_category,
                    'process_station' => $request->process_station,
                    'process_order' => $request->process_order,
                    'product_line' => $product_lines
                );

                if(isset($request->id)){ // EDIT
                    InspectorSkillChartSetting::where('id', $request->id)->update($process_array);
                }else{ // ADD
                    InspectorSkillChartSetting::insert($process_array);
                }

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Transaction Succesful']);
            }catch(Exemption $e){
                DB::rollback();
                return $e;
            }
        }
    }

    public function getProcessStationById(Request $request){
        return InspectorSkillChartSetting::where('id', $request->id)->first();
    }

    public function getProcessStations(Request $request){
        return InspectorSkillChartSetting::where('status', 0)->get();
    }

    public function updateProcessStationStatus(Request $request){
        DB::beginTransaction();

        try {
            $examination = InspectorSkillChartSetting::findOrFail($request->id);

            $examination->status = $examination->status == 1 ? 0 : 1;
            $examination->save();

            DB::commit(); // ✅ commit here

            return response()->json([
                'success' => true,
                'new_status' => $examination->status,
                'message' => 'Examination status updated successfully.'
            ]);
        } catch (\Throwable $e) { // ✅ catch everything including DB errors
            DB::rollBack(); // ✅ rollback only if it fails

            // log the error so you can see what’s happening
            \Log::error('Examination status update failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update examination status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProcessCountPerCategory(Request $request){
        $count =  InspectorSkillChartSetting::where('section', $request->section)->where('skill_category', $request->skill_category)
                    ->where('status', 0)
                    ->count();

        return response()->json(['count' => $count ]);
    }
}
