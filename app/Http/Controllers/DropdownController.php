<?php

namespace App\Http\Controllers;

use App\Http\Requests\DropdownItemRequest;
use App\Http\Requests\DropdownTypeRequest;
use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetail;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Asia/Manila');

class DropdownController extends Controller
{
    public function get_dropdown_types(Request $request){
        return DropdownMaster::whereNull('deleted_at')->get();
    }

    public function save_dropdown_type(DropdownTypeRequest $request){
        DB::beginTransaction();
        try{
            $data = $request->validated();
            $data['created_by'] = $_SESSION["rapidx_user_id"];
            $data['created_at'] = NOW();

            DropdownMaster::create($data);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dropdown type saved successfully.']);
        }catch(\Throwable $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);

        }
    }

    public function dt_get_dropdown_items(Request $request){
        $dropdown_items = DropdownMasterDetail::where('dropdown_masters_id', $request->dropdown_type_id)
        // ->whereNull('deleted_at')
        ->get();
        return DataTables::of($dropdown_items)
        ->addColumn('action', function($row){
            $result = "";
            $result .="<center>";
            $result .= '<button class="btn btn-sm btn-primary btnEditItem" data-id="'.$row->id.'">Edit</button>';
            $result .= '<button class="btn btn-sm btn-danger ml-2 btnDeleteItem" data-id="'.$row->id.'">Delete</button>';
            $result .="</center>";
            return $result;
        })
        ->addColumn('status', function($row){
            $result = "";
            if( is_null($row->deleted_at)) 
                $result = '<span class="badge badge-success">Active</span>';
            else
                $result = '<span class="badge badge-danger">Inactive</span>';

            return $result;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function save_dropdown_items(DropdownItemRequest $request){
        DB::beginTransaction();
        try{
            $data = $request->validated();
            if(isset($request->dropdown_item_id)){ // Update
                unset($data['dropdown_type_id']);
                $data['updated_by'] = $_SESSION["rapidx_user_id"];
                $data['updated_at'] = NOW();

                DropdownMasterDetail::where('id', $request->dropdown_item_id)->update($data);
            }
            else{ // Create
                $data['created_by'] = $_SESSION["rapidx_user_id"];
                $data['created_at'] = NOW();
                $data['status'] = 1;

                DropdownMasterDetail::insert($data);

            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dropdown item saved successfully.']);
        }catch(\Throwable $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function get_dropdown_item_by_id(Request $request){
        return DropdownMasterDetail::with([
            'dropdown_master'
        ])
        ->where('id', $request->id)->whereNull('deleted_at')->first();
    }

    public function delete_dropdown_item(Request $request){
        DB::beginTransaction();
        try{
            DropdownMasterDetail::where('id', $request->id)->update([
                'deleted_at' => NOW(),
                'updated_by' => $_SESSION["rapidx_user_id"]
            ]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dropdown item deleted successfully.']);
        }catch(\Throwable $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
