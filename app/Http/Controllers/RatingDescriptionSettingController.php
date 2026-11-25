<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use DataTables;
use App\Model\RatingDescriptionSetting;
use App\Model\RatingDetails;
use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RatingDescriptionSettingController extends Controller
{
    public function viewRatingDescriptionInfo(Request $request){
        $rating_description = RatingDescriptionSetting::with(['rating_description_details'])->whereNull('deleted_at')->get();
        
        $designation = DropdownMaster::with('dropdown_master_details')
                        ->whereIn('id', [1, 7, 9])
                        ->get()
                        ->flatMap(function ($item) {
                            return $item->dropdown_master_details;
                        })
                        ->keyBy('id');
                        // return $designation;

        return DataTables::of($rating_description)
        ->addColumn('action', function($rating_desc){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-secondary btn-sm btnEdit mr-1' data-id='$rating_desc->id'><i class='fas fa-edit'></i></button>";
            if($rating_desc->status == 0){
                $result .= "<button class='btn btn-danger btn-sm btnDisable' data-id='$rating_desc->id'><i class='fas fa-ban'></i></button>";
            }else{
                $result .= "<button class='btn btn-success btn-sm btnEnable' data-id='$rating_desc->id'><i class='fas fa-undo'></i></button>";
            }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('designation_label', function($rating_desc) use ($designation){
            $selected_designations = explode(',', $rating_desc->designation);
            $result = "";
            $result .= "<center>";
                foreach ($selected_designations as $curr_designation) {
                    if (isset($designation[$curr_designation])) {
                        $result .= "<span class='badge rounded-pill bg-primary'>" . e($designation[$curr_designation]->dropdown_masters_details) . "</span> ";
                        $result .= "<br>";
                    }
                }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('status_label', function($rating_desc){
            $result = "";
            $result .= "<center>";

            if($rating_desc->status == 1){
                $result .= "<span class='badge rounded-pill bg-success'>Active</span>";
            }else{
                $result .= "<span class='badge rounded-pill bg-danger'>Inactive</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->rawColumns(['action', 'designation_label', 'status_label'])
        ->make(true);
    }

    public function addRatingDescriptionInfo(Request $request){
        $validation = array(
            'position' => ['required', 'string', 'max:255'],
            'designation' => ['required'],
            'description.*' => ['required', 'string', 'max:255'],
            'level.*' => ['required', 'string', 'max:255']
        );
        
        $data = $request->all();

        $validator = Validator::make($data, $validation);
        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }else{
            DB::beginTransaction();

            try{
                $designation = implode(',', $request->designation);
                $data_array = array(
                    'position' => $request->position,
                    'designation' => $designation
                );

                if(isset($request->id)){ // EDIT
                    $rating_description_id = $request->id;
                    RatingDescriptionSetting::where('id', $rating_description_id)->update($data_array);
                }else{ // ADD
                    $rating_description_id = RatingDescriptionSetting::insertGetId($data_array);
                }

                // DELETE OLD Target Ratings ON UPDATE
                RatingDetails::where('rating_description_id', $rating_description_id)->delete();

                // SAVE NEW Target Ratings
                if ($request->description){
                    foreach ($request->description as $i => $value){
                        RatingDetails::insert([
                            'rating_description_id'     => $rating_description_id,
                            'description'               => $request->description[$i],
                            'level'                     => $request->level[$i]
                        ]);
                    }
                }

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Transaction Succesful']);
            }catch(Exception $e){
                DB::rollback();
                return $e;
            }
        }
    }

    public function getRatingDescriptionById(Request $request){
        return RatingDescriptionSetting::with('rating_description_details')->where('id', $request->id)->first();
    }

    public function updateRatingDescriptionStatus(Request $request){
        DB::beginTransaction();

        try {
            $rating_description = RatingDescriptionSetting::findOrFail($request->id);

            $rating_description->status = $rating_description->status == 1 ? 0 : 1;
            $rating_description->save();

            DB::commit(); // ✅ commit here

            return response()->json([
                'success' => true,
                'new_status' => $rating_description->status,
                'message' => 'Rating description status updated successfully.'
            ]);
        } catch (\Throwable $e) { // ✅ catch everything including DB errors
            DB::rollBack(); // ✅ rollback only if it fails

            return response()->json([
                'success' => false,
                'message' => 'Failed to update rating description status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDesignationFrMaster(Request $request){
        $designations = DropdownMaster::with(['dropdown_master_details'])
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->whereIn('id', [1, 7, 9])
                    ->get()
                    ->flatMap(function ($item) {
                        return $item->dropdown_master_details;
                    })
                    ->values();

        return response()->json($designations);

        // $designations = DropdownMaster::with(['dropdown_master_details'])
        //                     ->where('status', 1) // active status
        //                     ->whereNull('deleted_at') // null soft delete
        //                     ->whereIn('id', [1,7,9]) // actual ID for Designation
        //                     ->get();

        // return response()->json($designations->first()->dropdown_master_details);
    }
}