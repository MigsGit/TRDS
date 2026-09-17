<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\GrrSettings;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GrrSettingsController extends Controller
{
    public function viewGrrAttributes(Request $request){
        $grrSettings = GrrSettings::get();

         return DataTables::of($grrSettings)
        ->addColumn('action', function($grrSettings){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-secondary btn-sm btnEdit mr-1' data-id='$grrSettings->id'><i class='fas fa-edit'></i></button>";
            if($grrSettings->status == 0){
                $result .= "<button class='btn btn-danger btn-sm btnDisable mr-1' data-id='$grrSettings->id'><i class='fas fa-ban'></i></button>";
                $result .= "<button class='btn btn-primary btn-sm btnViewQuestionnaire' data-grrno='$grrSettings->grr_no' data-grrsample='$grrSettings->grr_sample' data-id='$grrSettings->id'><i class='fas fa-cog'></i></button>";
            }
            else{
                $result .= "<button class='btn btn-success btn-sm btnEnable' data-id='$grrSettings->id'><i class='fas fa-undo'></i></button>";
            }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('status_label', function($grrSettings){
            $result = "";
            $result .= "<center>";

            if($grrSettings->status == 0){
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

    public function addGrrSample(Request $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->all();
        // return $data;
        $validator = Validator::make($data, [
            'grr_no' => 'required',
            'grr_sample' => 'required',
            'section' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 0, 'message' => 'Validation failed.', 'errors' => $validator->errors()]);
        }
        if($request->id != NULL || $request->id != ''){
            $grrSample = GrrSettings::find($request->input('id'));
            if($grrSample){
                $grrSample->grr_no = $data['grr_no'];
                $grrSample->grr_sample = $data['grr_sample'];
                $grrSample->section = $data['section'];
                $grrSample->save();

                return response()->json(['result' => 1, 'message' => 'Successfully updated!']);
            }
        }else{
            $grrSample = new GrrSettings();
            $grrSample->grr_no = $data['grr_no'];
            $grrSample->grr_sample = $data['grr_sample'];
            $grrSample->section = $data['section'];
            $grrSample->status = 0; // Default to active
            $grrSample->save();
        }
        

        return response()->json(['result' => 0, 'message' => 'Failed to save data.']);
    }

    public function getGrrAttributesById(Request $request){
        $id = $request->input('id');
        $grrAttributes = GrrSettings::find($id);

        if ($grrAttributes) {
            return response()->json($grrAttributes);
        } else {
            return response()->json(['result' => 0, 'message' => 'Record not found.']);
        }
    }

    public function updateGrrStatus(Request $request){
        date_default_timezone_set('Asia/Manila');
        $id = $request->input('id');
        $grrSample = GrrSettings::find($id);

        if ($grrSample) {
            $grrSample->status = $grrSample->status == 0 ? 1 : 0;
            $grrSample->save();
            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }
    }

}
