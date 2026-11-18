<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use DataTables;
use App\Model\TargetRatingSetting;
use App\Model\TargetRatingDetails;
use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TargetRatingSettingController extends Controller
{
    public function viewTargetRatingInfo(Request $request){
        $target_ratings = TargetRatingSetting::with(['rating_details', 'product_line_info', 'product_area_info'])->whereNull('deleted_at')->get();
        $product_lines = DropdownMaster::with('dropdown_master_details')->where('id', 2)->first()->dropdown_master_details->keyBy('id'); //2 = id of Product Lines

        return DataTables::of($target_ratings)
        ->addColumn('action', function($target_rating){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-secondary btn-sm btnEdit mr-1' data-id='$target_rating->id'><i class='fas fa-edit'></i></button>";
            if($target_rating->status == 0){
                $result .= "<button class='btn btn-danger btn-sm btnDisable' data-id='$target_rating->id'><i class='fas fa-ban'></i></button>";
            }else{
                $result .= "<button class='btn btn-success btn-sm btnEnable' data-id='$target_rating->id'><i class='fas fa-undo'></i></button>";
            }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('product_line_label', function($target_rating) use ($product_lines){
            $selected_prod_lines = explode(',', $target_rating->product_line);
            $result = "";
            $result .= "<center>";
                foreach ($selected_prod_lines as $curr_product_line) {
                    if (isset($product_lines[$curr_product_line])) {
                        $result .= "<span class='badge rounded-pill bg-primary'>" . e($product_lines[$curr_product_line]->dropdown_masters_details) . "</span> ";
                    }
                }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('status_label', function($target_rating){
            $result = "";
            $result .= "<center>";

            if($target_rating->status == 1){
                $result .= "<span class='badge rounded-pill bg-success'>Active</span>";
            }else{
                $result .= "<span class='badge rounded-pill bg-danger'>Inactive</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->rawColumns(['action', 'product_line_label', 'status_label'])
        ->make(true);
    }

    public function addTargetRatingInfo(Request $request){
        $validation = array(
            'section' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'product_area' => ['required', 'string', 'max:255'],
            'product_line.*' => 'required',
            'target_name.*' => ['required', 'string', 'max:255'],
            'rating.*' => ['required', 'string', 'max:255']
        );
        
        $data = $request->all();

        $validator = Validator::make($data, $validation);
        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }else{
            DB::beginTransaction();

            try{
            
                $prod_lines = implode(',', $request->product_line);
                $data_array = array(
                    'section' => $request->section,
                    'position' => $request->position,
                    'product_line' => $prod_lines,
                    'product_area' => $request->product_area
                );

                if(isset($request->id)){ // EDIT
                    $target_rating_id = $request->id;
                    TargetRatingSetting::where('id', $target_rating_id)->update($data_array);
                }else{ // ADD
                    $target_rating_id = TargetRatingSetting::insertGetId($data_array);
                }

                // DELETE OLD Target Ratings ON UPDATE
                TargetRatingDetails::where('target_rating_id', $target_rating_id)->delete();

                // SAVE NEW Target Ratings
                if ($request->target_name){
                    foreach ($request->target_name as $i => $value){
                        TargetRatingDetails::insert([
                            'target_rating_id'    => $target_rating_id,
                            'target_name'         => $request->target_name[$i],
                            'rating'              => $request->rating[$i]
                        ]);
                    }
                }

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Transaction Succesful']);
            }catch(Exemption $e){
                DB::rollback();
                return $e;
            }
        }
    }

    public function getProductLineFrMaster(Request $request){
        $product_lines = DropdownMaster::with(['dropdown_master_details'])
                            ->where('status', 1) // active status
                            ->whereNull('deleted_at') // null soft delete
                            ->where('id', 2) // actual ID for Classification
                            ->get();

        return response()->json($product_lines->first()->dropdown_master_details);
    }
    
    public function getProductAreaFrMaster(Request $request){
        $product_areas = DropdownMaster::with(['dropdown_master_details'])
                            ->where('status', 1) // active status
                            ->whereNull('deleted_at') // null soft delete
                            ->where('id', 32) // actual ID for Product Area
                            ->get();

        return response()->json($product_areas->first()->dropdown_master_details);
    }

    public function getTargetRatingById(Request $request){
        return TargetRatingSetting::with('rating_details')->where('id', $request->id)->first();
    }

    public function updateTargetRatingStatus(Request $request){
        DB::beginTransaction();

        try {
            $examination = TargetRatingSetting::findOrFail($request->id);

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
}
