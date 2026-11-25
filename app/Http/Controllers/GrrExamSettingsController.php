<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\GrrExamSettings;
use App\Model\GrrSettings;
use App\Model\GrrExamEmployees;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneHrisSubcon;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GrrExamSettingsController extends Controller
{
    //
    public function viewGrrExamGroupings()
    {
        // Logic to view GRR exam groupings
        $grrExamSettings = GrrExamSettings::with(['grrSetting', 'examEmployees', 'examEmployees.getEmployeeAttribute'])->get();

        // return $grrExamSettings;

         return DataTables::of($grrExamSettings)
        ->addColumn('action', function($grrExamSettings){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-secondary btn-sm btnEdit mr-1' data-id='$grrExamSettings->id'><i class='fas fa-edit'></i></button>";
            if($grrExamSettings->status == 0){
                $result .= "<button class='btn btn-danger btn-sm btnDisable mr-1' data-id='$grrExamSettings->id'><i class='fas fa-ban'></i></button>";
                // $result .= "<button class='btn btn-primary btn-sm btnViewQuestionnaire' data-grrno='$grrExamSettings->grr_no' data-grrsample='$grrExamSettings->grr_sample' data-id='$grrExamSettings->id'><i class='fas fa-cog'></i></button>";
            }
            else{
                $result .= "<button class='btn btn-success btn-sm btnEnable' data-id='$grrExamSettings->id'><i class='fas fa-undo'></i></button>";
            }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('grr_no_grr_sample', function($grrExamSettings){
            // return $grrExamSettings->grr_no . ' - ' . $grrExamSettings->grr_sample;
            return $grrExamSettings->grrSetting->grr_no . ' - ' . $grrExamSettings->grrSetting->grr_sample;
        })
        ->addColumn('exam_employees', function ($grrExamSettings) {

            $names = $grrExamSettings->examEmployees->map(function ($employee) {
                $empName = $employee->getEmployeeAttribute->empname ?? '';
                return str_replace('Ã±', 'ñ', $empName);
            })->implode('<br>');

            return "<center>{$names}</center>";
        })
        ->addColumn('status_label', function($grrExamSettings){
            $result = "";
            $result .= "<center>";

            if($grrExamSettings->status == 0){
                $result .= "<span class='badge rounded-pill bg-success'>Active</span>";
            }else{
                $result .= "<span class='badge rounded-pill bg-danger'>Inactive</span>";
            }
            $result .= "</center>";

            return $result;
        })
        ->rawColumns(['action', 'grr_no_grr_sample', 'exam_employees', 'status_label'])
        ->make(true);
    }

    public function fetchGrrNoList(Request $request)
    {
        $data = $request->all();
        // return $data;
        $section = $request->section;

        $grrNo = GrrSettings::where('section', $section)
        ->select('id', 'grr_sample', 'grr_no')
        ->where('status', 0)
        ->get();

        return response()->json($grrNo);
    }

    public function fetchEmployeesByGrrNo(Request $request){
        $grrNo = $request->grr_no;

        // Get employees already assigned to the current GRR
        $existingEmployees = GrrSettings::with('GrrExamSettings', 'GrrExamSettings.examEmployees')
            ->where('id', $grrNo)
            ->get();
        
        $existingEmployeeIds = $existingEmployees
            ->pluck('GrrExamSettings')
            ->flatten()
            ->pluck('examEmployees')
            ->flatten()
            ->pluck('emp_id')
            ->filter()
            ->unique();

        $subconUsers = SystemOneHrisSubcon::where('EmpStatus', 1)
            ->where('Section', '!=', 'BOD')
            ->where('Section', '!=', 'HRS')
            ->where('Section', '!=', 'Facility')
            ->where('Section', '!=', 'Accounting')
            ->where('Section', '!=', 'Treasury')
            ->where('Section', '!=', 'QAS')
            ->where('Section', '!=', 'Information Systems Section')
            ->where('Section', '!=', NULL)
            ->where('Position', 'NOT LIKE', '%Manager%')
            ->whereNotIn('EmpNo', $existingEmployeeIds)
            ->get();

        return response()->json($subconUsers);
    }

    public function saveExamGroupings(Request $request){
        date_default_timezone_set('Asia/Manila');
        $section = $request->section;
        $data = $request->except('_token');
        $employee = $data['employee'];

        // return $employee;

        $explodedEmployee = implode(',', $employee);
        // return $explodedEmployee;
        
        // return $data;

        // Get the latest control number for this section
        $lastRecord = GrrExamSettings::where('section', $section)
            ->orderByDesc('id')
            ->first();

        if (!$lastRecord) {
            // No existing record for this section
            $ctrlNumber = $section . '-001';
        } else {
            // Get the numeric part of the last control number
            $lastNumber = (int) substr($lastRecord->ctrl_number, strrpos($lastRecord->ctrl_number, '-') + 1);

            // Increment
            $nextNumber = $lastNumber + 1;

            // Format as 001, 002, 003...
            $ctrlNumber = $section . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        // Save
        $grrExamSettings = new GrrExamSettings();
        $grrExamSettings->section = $data['section'];
        $grrExamSettings->grr_setting_id = $data['exam_grr_no'];
        $grrExamSettings->ctrl_number = $ctrlNumber;
        $grrExamSettings->description = $data['exam_description'];
        $grrExamSettings->purpose = $data['exam_purpose'];
        $grrExamSettings->instructions = $data['exam_instruction'];
        $grrExamSettings->status = 0;
        $grrExamSettings->save();

        // Save exam employees
        foreach ($employee as $empId) {
            $grrExamEmployee = new GrrExamEmployees();
            $grrExamEmployee->exam_grouping_id = $grrExamSettings->id;
            $grrExamEmployee->emp_id = $empId;
            $grrExamEmployee->status = 0;
            $grrExamEmployee->exam_status = 0;
            $grrExamEmployee->save();
        }

        return response()->json(['result' => 1, 'message' => 'Successfully added!']);

    }

    public function getGrrExamGroupingsById(Request $request){
        $id = $request->id;
        $examGrouping = GrrExamSettings::find($id);
        return response()->json($examGrouping);
    }
}
