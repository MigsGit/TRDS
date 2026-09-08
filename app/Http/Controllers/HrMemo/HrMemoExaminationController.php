<?php

namespace App\Http\Controllers\HrMemo;
use App\Http\Controllers\Controller;

use DataTables;
use App\Model\Hr\HrMemoExamination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HrMemoExaminationController extends Controller
{
    public function viewExaminationsInfo(Request $request){
        $examination_details = HrMemoExamination::get();

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

    public function addExaminationsInfo(Request $request){
        $validation = array(
            'exam_name' => ['required', 'string', 'max:255']
        );

        $data = $request->all();
        $validator = Validator::make($data, $validation);
        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }else{
            DB::beginTransaction();

            try{
                $process_array = array(
                    'examination_name' => $request->exam_name,
                    'objective' => $request->objective
                );

                if(isset($request->id)){ // EDIT
                    HrMemoExamination::where('id', $request->id)
                    ->update($process_array);
                }else{ // ADD
                    HrMemoExamination::insert($process_array);
                }

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Transaction Succesful']);
            }catch(Exemption $e){
                DB::rollback();
                return $e;
            }
        }
    }

    public function getExaminationsById(Request $request){
        return HrMemoExamination::where('id', $request->id)->first();
    }

    public function getExaminations(Request $request){
        return HrMemoExamination::where('status', 0)->get();
    }

    public function updateExaminationsStatus(Request $request){
        DB::beginTransaction();

        try {
            $examination = HrMemoExamination::findOrFail($request->id);

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
