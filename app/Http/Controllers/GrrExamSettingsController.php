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
use Illuminate\Validation\Rule;

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
        $examId = $request->exam_id;

        // Employees already assigned to this GRR (the exam being edited is ignored so its own employees stay available)
        $examIds = GrrExamSettings::where('grr_setting_id', $grrNo)
            ->when($examId, function ($query) use ($examId) {
                $query->where('id', '!=', $examId);
            })
            ->pluck('id');

        $existingEmployeeIds = GrrExamEmployees::whereIn('exam_grouping_id', $examIds)
            ->where('exam_status', '!=', 2)
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

    private function nextControlNumber($section)
    {
        $lastRecord = GrrExamSettings::where('section', $section)
            ->orderByDesc('id')
            ->first();

        if (!$lastRecord) {
            return $section . '-001';
        }

        $lastNumber = (int) substr($lastRecord->ctrl_number, strrpos($lastRecord->ctrl_number, '-') + 1);

        return $section . '-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    public function saveExamGroupings(Request $request){
        date_default_timezone_set('Asia/Manila');

        $validator = Validator::make($request->all(), [
            'id'               => ['nullable', 'integer', 'exists:grr_exam_settings,id'],
            'section'          => ['required', 'string', Rule::in(GrrSettings::SECTIONS)],
            'exam_grr_no'      => ['required', 'integer', 'exists:grr_settings,id'],
            'employee'         => ['required', 'array', 'min:1', 'max:3'],
            'employee.*'       => ['required', 'string', 'distinct'],
            'exam_description' => ['nullable', 'string'],
            'exam_purpose'     => ['nullable', 'string'],
            'exam_instruction' => ['nullable', 'string'],
        ], [
            'employee.required' => 'Please select at least 1 employee.',
            'employee.max'      => 'You can only select a maximum of 3 employees.',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 0, 'message' => $validator->errors()->first()], 422);
        }

        $data = $request->except('_token');
        $employee = array_values($data['employee']);

        if (!empty($data['id'])) {
            $error = DB::transaction(function () use ($data, $employee) {
                $exam = GrrExamSettings::lockForUpdate()->find($data['id']);
                $current = GrrExamEmployees::where('exam_grouping_id', $exam->id)->lockForUpdate()->get();

                // Employees who already started cannot be removed, and the GRR/section they answered cannot change
                $started = $current->filter(function ($row) { return (int) $row->exam_status !== 0; });

                if ($started->isNotEmpty()) {
                    if ((int) $exam->grr_setting_id !== (int) $data['exam_grr_no'] || $exam->section !== $data['section']) {
                        return 'The section and GRR cannot be changed because an examinee has already started this examination.';
                    }

                    $missing = $started->pluck('emp_id')->diff($employee);
                    if ($missing->isNotEmpty()) {
                        return 'Employee ' . $missing->first() . ' has already started the examination and cannot be removed.';
                    }
                }

                if ($exam->section !== $data['section']) {
                    $exam->ctrl_number = $this->nextControlNumber($data['section']);
                }

                $exam->section = $data['section'];
                $exam->grr_setting_id = $data['exam_grr_no'];
                $exam->description = $data['exam_description'] ?? null;
                $exam->purpose = $data['exam_purpose'] ?? null;
                $exam->instructions = $data['exam_instruction'] ?? null;
                $exam->save();

                // Remove examinees who were unselected (only not-started ones can reach here)
                GrrExamEmployees::where('exam_grouping_id', $exam->id)
                    ->whereNotIn('emp_id', $employee)
                    ->delete();

                $existingIds = $current->pluck('emp_id')->all();
                foreach ($employee as $empId) {
                    if (in_array($empId, $existingIds, true)) {
                        continue;
                    }
                    $grrExamEmployee = new GrrExamEmployees();
                    $grrExamEmployee->exam_grouping_id = $exam->id;
                    $grrExamEmployee->emp_id = $empId;
                    $grrExamEmployee->status = 0;
                    $grrExamEmployee->exam_status = 0;
                    $grrExamEmployee->save();
                }

                return null;
            });

            if ($error) {
                return response()->json(['result' => 0, 'message' => $error], 422);
            }

            return response()->json(['result' => 1, 'message' => 'Successfully updated!']);
        }

        DB::transaction(function () use ($data, $employee) {
            $grrExamSettings = new GrrExamSettings();
            $grrExamSettings->section = $data['section'];
            $grrExamSettings->grr_setting_id = $data['exam_grr_no'];
            $grrExamSettings->ctrl_number = $this->nextControlNumber($data['section']);
            $grrExamSettings->description = $data['exam_description'] ?? null;
            $grrExamSettings->purpose = $data['exam_purpose'] ?? null;
            $grrExamSettings->instructions = $data['exam_instruction'] ?? null;
            $grrExamSettings->status = 0;
            $grrExamSettings->save();

            foreach ($employee as $empId) {
                $grrExamEmployee = new GrrExamEmployees();
                $grrExamEmployee->exam_grouping_id = $grrExamSettings->id;
                $grrExamEmployee->emp_id = $empId;
                $grrExamEmployee->status = 0;
                $grrExamEmployee->exam_status = 0;
                $grrExamEmployee->save();
            }
        });

        return response()->json(['result' => 1, 'message' => 'Successfully added!']);
    }

    public function updateGrrExamGroupingsStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'ID is required.']);
        }

        $exam = GrrExamSettings::find($request->id);

        if (!$exam) {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }

        $exam->status = (int) $exam->status === 0 ? 1 : 0;
        $exam->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    public function getGrrExamGroupingsById(Request $request){
        $examGrouping = GrrExamSettings::find($request->id);

        if (!$examGrouping) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        $employees = GrrExamEmployees::with('getEmployeeAttribute')
            ->where('exam_grouping_id', $examGrouping->id)
            ->orderBy('id')
            ->get()
            ->map(function ($row) {
                $name = $row->getEmployeeAttribute
                    ? trim($row->getEmployeeAttribute->FirstName . ' ' . $row->getEmployeeAttribute->LastName)
                    : $row->emp_id;

                return [
                    'emp_id'  => $row->emp_id,
                    'name'    => str_replace('Ã±', 'ñ', $name),
                    'started' => (int) $row->exam_status !== 0,
                ];
            })
            ->values();

        return response()->json([
            'id'             => $examGrouping->id,
            'ctrl_number'    => $examGrouping->ctrl_number,
            'section'        => $examGrouping->section,
            'grr_setting_id' => $examGrouping->grr_setting_id,
            'description'    => $examGrouping->description,
            'purpose'        => $examGrouping->purpose,
            'instructions'   => $examGrouping->instructions,
            'employees'      => $employees,
        ]);
    }
}
