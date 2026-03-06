<?php

namespace App\Http\Controllers\HrMemo;
use App\Http\Controllers\Controller;
use DataTables;
use App\Model\Hr\HrMemo;
use App\Model\Hr\HrMemoEmailRecipients;
use App\Model\Hr\HrMemoTraineeDetails;
use App\Model\Hr\HrMemoTraineeCategoryDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class HrMemoController extends Controller
{
    private function actionButton($class, $icon, $id, $extraClass = ''){
        return "<button class='btn {$class} btn-sm {$extraClass}' data-id='{$id}'>
                    <i class='fa-solid {$icon}'></i>
                </button>";
    }

    public function viewHrMemoInfo(Request $request){
        // $globalUser = session('global_user');
        $hr_memo_details = HrMemo::with(['defects.defect_item', 'situations', 'improvements'])->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        return DataTables::of($hr_memo_details)
        ->addColumn('action', function($hr_memo_details){
            $result = "";
            $result .= "<center>";

            // $canManage  = $globalUser && in_array($globalUser->position, [0,1,2,3]);
            $isActive   = $hr_memo_details->status == 0;
            $isDisabled = $hr_memo_details->status == 1;
            $id = $hr_memo_details->id;

            if ($isActive) {
                // if ($canManage) {
                    $result .= $this->actionButton('btn-secondary btnEdit', 'fa-pen-to-square', $id, 'mr-1');
                    $result .= $this->actionButton('btn-danger btnDisable', 'fa-ban', $id);
                // } else {
                //     $result .= $this->actionButton('btn-info btnView', 'fa-eye', $id, 'mr-1');
                // }
            }

            if ($isDisabled) {
                $result .= $this->actionButton('btn-info btnView', 'fa-eye', $id, 'mr-1');

                // if ($canManage) {
                    $result .= $this->actionButton('btn-success btnEnable', 'fa-rotate-left', $id);
                // }
            }

            $result .= "</center>";
            return $result;
        })
        ->addColumn('status_label', function($pth_details){
            $result = "";
            $result .= "<center>";

            if($pth_details->status == 0){
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

    // public function viewTraineeDetails(Request $request){
    //     $hr_memo_details = HrMemo::with(['defects.defect_item', 'situations', 'improvements'])->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

    //     return DataTables::of($hr_memo_details)
    //     ->addColumn('action', function($hr_memo_details){
    //         $result = "";
    //         $result .= "<center>";

    //         // $canManage  = $globalUser && in_array($globalUser->position, [0,1,2,3]);
    //         $isActive   = $hr_memo_details->status == 0;
    //         $isDisabled = $hr_memo_details->status == 1;
    //         $id = $hr_memo_details->id;

    //         if ($isActive) {
    //             // if ($canManage) {
    //                 $result .= $this->actionButton('btn-secondary btnEdit', 'fa-pen-to-square', $id, 'mr-1');
    //                 $result .= $this->actionButton('btn-danger btnDisable', 'fa-ban', $id);
    //             // } else {
    //             //     $result .= $this->actionButton('btn-info btnView', 'fa-eye', $id, 'mr-1');
    //             // }
    //         }

    //         if ($isDisabled) {
    //             $result .= $this->actionButton('btn-info btnView', 'fa-eye', $id, 'mr-1');

    //             // if ($canManage) {
    //                 $result .= $this->actionButton('btn-success btnEnable', 'fa-rotate-left', $id);
    //             // }
    //         }

    //         $result .= "</center>";
    //         return $result;
    //     })
    //     ->addColumn('status_label', function($pth_details){
    //         $result = "";
    //         $result .= "<center>";

    //         if($pth_details->status == 0){
    //             $result .= "<span class='badge rounded-pill bg-success'>Active</span>";
    //         }else{
    //             $result .= "<span class='badge rounded-pill bg-danger'>Inactive</span>";
    //         }
    //         $result .= "</center>";

    //         return $result;
    //     })
    //     ->rawColumns(['action', 'status_label'])
    //     ->make(true);
    // }

    public function getEmployeeDetails(Request $request){
        $hris = DB::connection('mysql_systemone')->select("
                SELECT 
                    'db_hris' AS source,
                    CONCAT(tbl_EmployeeInfo.FirstName, ' ', tbl_EmployeeInfo.LastName) AS emp_name,
                    tbl_EmployeeInfo.DateHired,
                    CONCAT_WS(' - ', tbl_Training.PeriodFrom, tbl_Training.PeriodTo) AS fromto,
                    CONCAT(tbl_Position.Position, '/', tbl_Department.Department, '/', tbl_Section.Section) AS pos_dept_section,
                    tbl_Training.Venue,
                    vw_Trainee.Remarks,
                    tbl_Training.Title,
                    tbl_Department.Department,
                    tbl_Division.Division
                FROM vw_Trainee
                INNER JOIN tbl_EmployeeInfo ON vw_Trainee.fkEmployee = tbl_EmployeeInfo.pkid
                INNER JOIN tbl_Position ON tbl_EmployeeInfo.fkPosition = tbl_Position.pkid
                INNER JOIN tbl_Section ON tbl_EmployeeInfo.fkSection = tbl_Section.pkid
                INNER JOIN tbl_Department ON tbl_EmployeeInfo.fkDepartment = tbl_Department.pkid
                INNER JOIN tbl_Division ON tbl_EmployeeInfo.fkDivision = tbl_Division.pkid
                INNER JOIN tbl_Training ON vw_Trainee.fkTraining = tbl_Training.pkid
                WHERE tbl_EmployeeInfo.EmpNo = ?
                LIMIT 1
                ", [$request->employee_number]);

        // return $hris;
        if($hris == null || empty($hris)){
            $subcon = DB::connection('mysql_subcon')->select("
                    SELECT 
                        'db_subcon' AS source,
                        CONCAT(tbl_EmployeeInfo.FirstName, ' ', tbl_EmployeeInfo.LastName) AS emp_name,
                        tbl_EmployeeInfo.DateHired,
                        CONCAT_WS(' - ', tbl_Training.PeriodFrom, tbl_Training.PeriodTo) AS fromto,
                        CONCAT(tbl_Position.Position, '/', tbl_Department.Department, '/', tbl_Section.Section) AS pos_dept_section,
                        tbl_Training.Venue,
                        COALESCE(vw_Trainee.Remarks, 'No Record') AS Remarks,
                        tbl_Training.Title,
                        tbl_Department.Department,
                        tbl_Division.Division
                    FROM tbl_EmployeeInfo
                    LEFT JOIN db_hris.vw_Trainee ON vw_Trainee.fkEmployee = tbl_EmployeeInfo.pkid
                    LEFT JOIN db_hris.tbl_Training ON vw_Trainee.fkTraining = tbl_Training.pkid
                    INNER JOIN db_hris.tbl_Position ON tbl_EmployeeInfo.fkPosition = tbl_Position.pkid
                    INNER JOIN db_hris.tbl_Section ON tbl_EmployeeInfo.fkSection = tbl_Section.pkid
                    INNER JOIN db_hris.tbl_Department ON tbl_EmployeeInfo.fkDepartment = tbl_Department.pkid
                    INNER JOIN db_hris.tbl_Division ON tbl_EmployeeInfo.fkDivision = tbl_Division.pkid
                    WHERE tbl_EmployeeInfo.EmpNo = ?
                    LIMIT 1
                    ", [$request->employee_number]);
                    
            $training_details = $subcon;
        }else{
            $training_details = $hris;
        }

        return response()->json($training_details);
    }

    public function addHrMemoInfo(Request $request){
        $validation = array(
            'situation' => 'required',
            'section' => 'required',
            'date_encountered' => 'required',
            'model' => 'required',
            'illustration_of_defect' => ['nullable','file','mimes:jpg,jpeg,png,webp','max:10240'], // 5MB
            'no_of_occurrence' => 'required',
            'defect_id' => 'required',
            // 'root_cause' => 'required',
            'factor.*' => 'required|string',
            'cause.*' => 'required|string',
            'analysis.*' => 'required|string',
            'counter_measure.*' => 'required|string',
            'implementation_date.*' => 'required|string',
            // 'improvement_action.*' => 'required|string',
            // 'improvement_action_remarks.*' => 'required|string'
        );

        $data = $request->all();
        $validator = Validator::make($data, $validation);

        if ($validator->fails()) {
            return response()->json(['result' => '0', 'error' => $validator->messages()]);
        }else{
            DB::beginTransaction();

            try{
                $history_data_array = array(
                    'date_encountered' => $request->date_encountered,
                    'situation' => $request->situation,
                    'section' => $request->section,
                    'model' => $request->model
                );

                if(isset($request->history_id)){ // EDIT
                    $history_id = $request->history_id;

                    PartTroubleHistory::where('id', $request->history_id)
                    ->update($history_data_array);
                }else{ // ADD
                    $history_id = PartTroubleHistory::insertGetId($history_data_array);
                }

                // DELETE OLD PthsDefects ON UPDATE
                PthsDefects::where('history_id', $request->history_id)->delete();

                if ($request->defect_id){

                    if($request->hasFile('illustration_of_defect')){
                        // FILE HANDLING
                        $uploadedFile = $request->file('illustration_of_defect');

                        // Get the original filename parts
                        $filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $file_extension = $uploadedFile->getClientOriginalExtension();

                        // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                        $cleanName = preg_replace('/[^\p{L}\p{N} _-]/u', '', $filename);
                        // 🔹 Replace spaces with underscores for safety
                        $cleanName = str_replace(' ', '_', $cleanName);
                        // 🔹 Add timestamp or unique ID if needed
                        $cleanedFilename = $cleanName . '.' . $file_extension;

                        // use cleanedFilename to be saved to storage
                        $file_attachment = $cleanedFilename;

                        Storage::putFileAs('public/file_attachments', $request->illustration_of_defect, $file_attachment);
                    }else{
                        // use existing filename
                        $file_attachment = $request->illustration_of_defect_filename;
                    }

                    $pths_defects_data = [
                        'history_id'                => $history_id,
                        'defect_id'                 => $request->defect_id,
                        'illustration_of_defect'    => $file_attachment,
                        'no_of_occurrence'          => $request->no_of_occurrence,
                        'root_cause'                => $request->root_cause,
                    ];

                    PthsDefects::insert($pths_defects_data);
                }

                // DELETE OLD Improvement Actions ON UPDATE
                PthsImprovements::where('history_id', $request->history_id)->delete();

                // SAVE NEW Improvement Actions
                if ($request->factor){
                    foreach ($request->factor as $i => $value){
                        PthsImprovements::insert([
                            'history_id'          => $history_id,
                            'factor'              => $request->factor[$i],
                            'cause'               => $request->cause[$i],
                            'analysis'            => $request->analysis[$i],
                            'counter_measure'     => $request->counter_measure[$i],
                            'pic'                 => $request->pic[$i],
                            'implementation_date' => $request->implementation_date[$i]
                            // 'improvement_actions'  => $request->improvement_action[$i],
                            // 'remarks'              => $request->improvement_action_remarks[$i]
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

    public function getHrMemoById(Request $request){
        return PartTroubleHistory::with(['defects.defect_item', 'improvements'])->where('id', $request->id)->first();
    }

    public function updateHrMemoStatus(Request $request){
        DB::beginTransaction();

        try {
            $defect = PartTroubleHistory::findOrFail($request->id);

            $defect->status = $defect->status == 1 ? 0 : 1;
            $defect->save();

            DB::commit(); // ✅ commit here

            return response()->json([
                'success' => true,
                'new_status' => $defect->status,
                'message' => 'Past Trouble History Record status updated successfully.'
            ]);
        } catch (\Throwable $e) { // ✅ catch everything including DB errors
            DB::rollBack(); // ✅ rollback only if it fails

            // log the error so you can see what’s happening
            \Log::error('Past Trouble History Record status update failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Past Trouble History Record status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //====================================== DOWNLOAD FILE ======================================
    public function downloadFile(Request $request, $id){
        $file_name = PartTroubleHistory::with('defects')->where('id', $id)->first();
        // return $file_name->defects->illustration_of_defect;
        $filename = $file_name->defects->illustration_of_defect;
        $filePath =  storage_path() . "/app/public/file_attachments/" . $filename;

        $mimeType = mime_content_type($filePath);
        // return $mimeType;

        if (str_starts_with($mimeType, 'image/')) {
            return response()->file($filePath, [
                'Content-Type'        => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ]);
        }
    }

    private function getMaterialsFrom($connection){
        return DB::connection($connection)
            ->table('tbl_wbs_material_kitting')
            ->select('device_name')
            ->whereNotNull('device_name')
            ->groupBy('device_name')
            ->orderBy('device_name')
            ->pluck('device_name');
    }

    public function getUsers(Request $request){
        $users = User::where('status', 0)->get();
        return response()->json(['users_data' => $users]);
    }

    public function getDeviceName(Request $request){
        $self = $this;

        $section = $request->input('section'); // ts, cn, yf, ppd
        $materials = collect();

        // Only run queries if a section is provided
        if ($section) {

            // TS section
            if ($section == 'TS') {
                $materials = $materials->merge($self->getMaterialsFrom('wbs_ts'));
            }

            // CN section
            if ($section == 'CN') {
                $materials = $materials->merge($self->getMaterialsFrom('wbs_cn'));
            }

            // YF section
            if ($section == 'YF') {
                $materials = $materials->merge($self->getMaterialsFrom('wbs_yf'));
            }

            // PPD section (different DB, only run if selected)
            if ($section == 'PPD') {
                $ppd_results = DB::connection('mysql_rapid')->select("
                    SELECT DeviceName
                    FROM tbl_dieset t1
                    WHERE Rev = (
                        SELECT MAX(NULLIF(Rev, ''))
                        FROM tbl_dieset t2
                        WHERE t2.DeviceName = t1.DeviceName
                    )
                    OR (Rev = '' AND NOT EXISTS (
                        SELECT 1
                        FROM tbl_dieset t3
                        WHERE t3.DeviceName = t1.DeviceName
                            AND t3.Rev <> ''
                    ))
                    ORDER BY DeviceName
                ");

                // Extract only DeviceName and wrap for JSON
                foreach ($ppd_results as $row) {
                    $materials->push($row->DeviceName);
                }
            }

            // Deduplicate, sort, and format for JSON
            $materials = $materials
                ->unique()
                ->sort() // sort alphabetically
                ->values() // reset keys
                ->map(function ($value) {
                    return array('materials' => $value);
                })
                ->values() // reset keys after map
                ->toArray();
        }

        // If no section selected, return empty array
        return response()->json($materials);
    }

    public function getCountOfNoOfOccurrence(Request $request){

        [$year, $month] = explode('-', $request->date_encountered);

        if ($month >= 4) {
            // April to December
            $start = $year . '-04-01';
            $end   = ($year + 1) . '-03-31';
        } else {
            // January to March
            $start = ($year - 1) . '-04-01';
            $end   = $year . '-03-31';
        }

        $count =  PartTroubleHistory::
                where('section', $request->section)
                ->where('model', $request->model)
                ->whereBetween('date_encountered', [$start, $end])
                ->whereHas('defects', function ($query) use ($request){
                    $query->where('defect_id', $request->defect_id)
                            ->whereNull('deleted_at');
                })
                ->whereHas('situations', function ($query) use ($request){
                    $query->where('id', $request->situation)
                            ->where('status', 0);
                })
                ->where('status', 0)
                ->whereNull('deleted_at')
                ->count();

                // +1 because current occurrence is not yet included
                $ordinal = $this->ordinal($count + 1);

                return response()->json([
                    'count'   => $count,
                    'ordinal' => $ordinal
                ]);
    }

    private function ordinal($number){
        if (!in_array($number % 100, [11, 12, 13])) {
            switch ($number % 10) {
                case 1: return $number . 'st';
                case 2: return $number . 'nd';
                case 3: return $number . 'rd';
            }
        }

        return $number . 'th';
    }
}
