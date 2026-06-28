<?php

namespace App\Http\Controllers;

use App\Model\DropdownMaster;
use App\Model\DropdownMasterDetail;
use App\Model\SystemHrisViewDivDeptSec;
use Exception;
use Illuminate\Http\Request;

class QualificationCertificationController extends Controller
{
    public function saveQualificationCertificationOper(Request $request){
        // return 'true' ;

        return $request->all();
        try {
            date_default_timezone_set('Asia/Manila');
            DB::beginTransaction();
            DB::commit();
            return response()->json(['is_success' => 'true']);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function getDivDeptSec(Request $request){

        try {
            $section = SystemHrisViewDivDeptSec::where('Division', '!=', '-')
                ->where('Division', '!=', 'Administration')
                ->whereNotNull('Section')
                ->select('Section')
                ->distinct()
                ->orderBy('Section')
                ->pluck('Section');

            return response()->json(['is_success' => 'true', 'section' => $section]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function getDropdownMasterDetailsByFkid(Request $request){

        try {
            $data = DropdownMasterDetail::
            where('dropdown_masters_id', $request->dropdown_masters_id)
            ->where('status',1)
            ->get('dropdown_masters_details');
            $masterDetails = collect($data)->map(function($rowMasterDetails){
                    return $rowMasterDetails['dropdown_masters_details'];
            });
            return response()->json(['is_success' => 'true', 'data' => $masterDetails]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
