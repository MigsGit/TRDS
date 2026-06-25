<?php

namespace App\Http\Controllers;

use App\Model\DropdownMaster;
use App\Model\SystemHrisViewDivDeptSec;
use Exception;
use Illuminate\Http\Request;

class QualificationCertificationController extends Controller
{
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
    public function getDropdownMaster(Request $request){
     
        try {
            $section = DropdownMaster::with('dropdown_master_details')
                ->where('Division', '!=', '-')
                ->where('status',1);

            return response()->json(['is_success' => 'true', 'section' => $section]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
