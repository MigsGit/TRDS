<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommonController;
use App\Http\Requests\TrainingEndorsementRequest;
use App\Jobs\SendEmailTrainingEndorsementApprovalJob;
use App\Model\RapidXUser;
use App\Model\TrainingEndorsement;
use App\Model\TrainingEndorsementApprovals;
use App\Model\TrainingEndorsementEmployee;
use App\Model\TrainingRequest;
use App\Model\TrainingRequestDetails;
use App\Model\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



class TrainingEndorsementController extends Controller
{
    protected $CommonController;
    
    public function __construct( CommonController $CommonController) {
        $this->CommonController = $CommonController;
    }

     /**
     * Insert approval record for endorsement
     *
     * @param int $training_endorsement_id
     * @param int $rapidx_id
     * @param string $approval_type ('approved_by' or 'checked_by')
     * @return void
     */
    protected function insertApproval($training_endorsement_id, $rapidx_id, $approval_type)
    {
        TrainingEndorsementApprovals::insert([
            'training_endorsement_id' => $training_endorsement_id,
            'rapidx_id' => $rapidx_id,
            'approval_type' => $approval_type,
            'created_by' => $_SESSION['rapidx_user_id'] ?? 'system',
            'created_at' => now(),
        ]);
    }

    public function getTrainingEndorsements(Request $request)
    {
        
        $data = TrainingEndorsement::with([
            'training_request_details',
            'hr_memo_details',
            'te_approval_details',
            'te_approval_details.approver_details',
            'created_by_user_details'
        ])
        ->whereNull('deleted_at')
        ->get();
        
        // return $_SESSION['rapidx_user_id'];
        $user_access = User::with(['user_access_module'])
        ->where('rapidx_emp_id', $_SESSION['rapidx_user_id'])->first();

        // $exploded_u_access = 17 for approver
        $exploded_u_access = explode(',', $user_access->user_access_module->user_modules_id);
        
        if($request->status != ''){
           $data = $data->where('status', $request->status);
        }

        return DataTables::of($data)
            ->addColumn('action', function ($row) use($exploded_u_access) {

                $approver_array = $row->te_approval_details->where('approval_type', 'approved_by')->whereNull('updated_at')->pluck('rapidx_id')->toArray();
                $checker_array = $row->te_approval_details->where('approval_type', 'checked_by')->whereNull('updated_at')->pluck('rapidx_id')->toArray();
                $result = "";
                $result .= '<center>';
                $result .= '<button class="btn btn-sm mr-1 btn-info btnViewEndorsement" data-id="' . $row->id . '" data-tr-ctrl-no="'.$row->training_request_details->ctrl_number.'" title="View Endorsement"><i class="fa fa-eye"></i></button>';
                    
                if($row->created_by == $_SESSION['rapidx_user_id'] && $row->status != 3){
                    $result .= '<button class="btn btn-sm mr-1 btn-danger btnDeleteEndorsement" data-id="' . $row->id . '" title="Delete Endorsement"><i class="fa fa-trash"></i></button>';
                    $result .= '<button class="btn btn-sm mr-1 btn-warning btnAddNotEndorsement" data-id="' . $row->id . '" data-tr-id="'.$row->training_request_id.'" title="Add Not Endorsed Employee"><i class="fa fa-plus"></i></button>';
                }

                if($row->status == 0 && $row->created_by == $_SESSION['rapidx_user_id']){
                    $result .= '<button class="btn btn-sm mr-1 btn-secondary btnEditEndorsement" data-id="' . $row->id . '" data-tr-ctrl-no="'.$row->training_request_details->ctrl_number.'" title="View Endorsement"><i class="fa fa-edit"></i></button>';
                    $result .= '<button class="btn btn-sm mr-1 btn-success btnProceedApprovalEndorsement" data-id="' . $row->id . '" data-tr-ctrl-no="'.$row->training_request_details->ctrl_number.'" title="Proceed Approval"><i class="fa fa-paper-plane"></i></button>';
                }
                else if ($row->status == 1 && in_array(17, $exploded_u_access) && in_array($_SESSION['rapidx_user_id'], $checker_array) ) {
                    $result .= '<button class="btn btn-sm mr-1 btn-success btnApproveEndorsement" data-approval-type="checker" data-id="' . $row->id . '" data-tr-ctrl-no="'.$row->training_request_details->ctrl_number.'" title="Approve Endorsement"><i class="fa fa-check"></i></button>';
                    $result .= '<button class="btn btn-sm mr-1 btn-danger btnRejectEndorsement" data-approval-type="checker" data-id="' . $row->id . '" data-tr-ctrl-no="'.$row->training_request_details->ctrl_number.'" title="Reject Endorsement"><i class="fa fa-times"></i></button>';
                }
                else if ($row->status == 2 && in_array(17, $exploded_u_access) && in_array($_SESSION['rapidx_user_id'], $approver_array)) {
                    $result .= '<button class="btn btn-sm mr-1 btn-success btnApproveEndorsement" data-approval-type="approver" data-id="' . $row->id . '" data-tr-ctrl-no="'.$row->training_request_details->ctrl_number.'" title="Approve Endorsement"><i class="fa fa-check"></i></button>';
                    $result .= '<button class="btn btn-sm mr-1 btn-danger btnRejectEndorsement" data-approval-type="approver" data-id="' . $row->id . '" data-tr-ctrl-no="'.$row->training_request_details->ctrl_number.'" title="Reject Endorsement"><i class="fa fa-times"></i></button>';
                }
                $result .= '</center>';

                // return '
                // <center>
                //     <button class="btn btn-sm btn-info btnViewEndorsement" data-id="' . $row->id . '" data-tr-ctrl-no="'.$row->training_request_details->ctrl_number.'" title="View Endorsement"><i class="fa fa-eye"></i></button>
                //     <button class="btn btn-sm btn-danger btnDeleteEndorsement" data-id="' . $row->id . '" title="Delete Endorsement"><i class="fa fa-trash"></i></button>
                //     <button class="btn btn-sm btn-warning btnAddNotEndorsement" data-id="' . $row->id . '" data-tr-id="'.$row->training_request_id.'" title="Add Not Endorsed Employee"><i class="fa fa-plus"></i></button>
                // </center>
                // ';
                return $result;
            })
            ->addColumn('raw_status', function($row){
                $result = "";
                $result .= '<center>';
                if($row->status == 0){
                    if(!is_null($row->disapprove_remarks) && !is_null($row->disapprove_by)){
                        $result .= '<span class="badge badge-danger mt-1">Disapproved</span>';
                        $result .= '<br><span class="mt-1"><strong>Remarks:</strong> '.$row->disapprove_remarks.'</span>';
                    }
                    else{
                        $result .= '<span class="badge badge-warning">Pending</span>';
                    }
                } elseif($row->status == 1){
                    $result .= '<span class="badge badge-info">For Endorsement Checker</span>';
                } elseif($row->status == 2){
                    $result .= '<span class="badge badge-primary">For Endorsement Approver</span>';
                } elseif($row->status == 3){
                    $result .= '<span class="badge badge-success">Approved</span>';
                }
                $result .= '</center>';
                return $result;
            })
            ->addColumn('date_created', function ($row) {
                return $row->created_at ?? '';
            })
            ->addColumn('prepared_by', function ($row){
                $result = "";
                $result .= "<center>";
                $result .= "<span class='badge badge-info mt-1'>{$row->created_by_user_details->name}</span><br>";
                // $result .= $row->created_by_user_details->name ?? '';
                $result .= "<em >{$row->created_at}</em>";
                $result .= "</center>";
                return $result;
            })
            ->addColumn('raw_checker', function($row){
                $checker = $row->te_approval_details->where('approval_type', 'checked_by')->flatten(1)->toArray();

                $result = "";
                $result .= "<center>";
                if($checker){
                    foreach($checker as $checker){
                        $format_updated_at = $checker['updated_at'] ? Carbon::parse($checker['updated_at'])->format('Y-m-d H:i:s') : null;
                        if($checker['updated_at'] != null){
                            $result .= "<span class='badge badge-success mt-1'>{$checker['approver_details']['name']}</span><br>";
                            $result .= "<em >{$format_updated_at}</em><br>";
                        }
                        else{
                            $result .= "<span class='badge badge-warning mt-1'>{$checker['approver_details']['name']}</span><br>";
                            $result .= "<em >N/A</em><br>";
                        }
                    }
                }
                else{
                    $result .= "<span class='badge badge-warning mt-1'>Not Assigned</span>";
                }
                $result .= "</center>";

                return $result;
            })
            ->addColumn('raw_approver', function($row){
                $approver = $row->te_approval_details->where('approval_type', 'approved_by')->flatten(1)->toArray();

                $result = "";
                $result .= "<center>";
                if($approver){
                    foreach($approver as $approver){
                        $format_updated_at = $approver['updated_at'] ? Carbon::parse($approver['updated_at'])->format('Y-m-d H:i:s') : null;
                        if($approver['updated_at'] != null){
                            $result .= "<span class='badge badge-success mt-1'>{$approver['approver_details']['name']}</span><br>";
                            $result .= "<em >{$format_updated_at}</em><br>";
                        }
                        else{
                            $result .= "<span class='badge badge-warning mt-1'>{$approver['approver_details']['name']}</span><br>";
                            $result .= "<em >N/A</em><br>";
                        }
                    }
                }
                else{
                    $result .= "<span class='badge badge-warning mt-1'>Not Assigned</span>";
                }
                $result .= "</center>";

                return $result;
            })
            ->rawColumns(['action', 'raw_status', 'prepared_by', 'raw_checker', 'raw_approver'])
            ->make(true);
    }

    public function getTrainingEndorsementById(Request $request)
    {
        $tr_ctrl_no = $request->tr_ctrl_no;

        $data = TrainingEndorsement::with([
            'created_by_user_details',
            'te_approval_details',
            'training_request_details',
            'hr_memo_details',
            'training_endorsement_employees' => function($query) {
                $query->whereNull('deleted_at');
            },
            'training_endorsement_employees.training_request_details_info',
            'training_endorsement_employees.training_request_details_info.employee_exam_details' => function($query) use ($tr_ctrl_no) {
                $query->where('training_request_ctrl_no', $tr_ctrl_no);
            },
            'training_endorsement_employees.training_request_details_info.employee_exam_details.exam_result_details_info'
        ])
        ->where('id', $request->id)
        ->first();

        // Group te_approval_details by approval_type for frontend assignment
        $checked_by = [];
        $approved_by = [];
        if ($data && $data->te_approval_details) {
            foreach ($data->te_approval_details as $approval) {
                if ($approval->approval_type === 'checked_by') {
                    $checked_by[] = $approval->rapidx_id;
                } elseif ($approval->approval_type === 'approved_by') {
                    $approved_by[] = $approval->rapidx_id;
                }
            }
        }

        $data->checked_by = $checked_by;
        $data->approved_by = $approved_by;

        return response()->json(['result' => true, 'data' => $data]);
    }

    public function saveTrainingEndorsement(TrainingEndorsementRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        $inserted_te_id = null;
        try{
            $list_of_employee = json_decode($data['employees'], true);
            // Validate that there are employees in the list
            if(count($list_of_employee) == 0){
                return response()->json([
                    'result' => false,
                    'message' => 'No employees added for endorsement.'
                ]);
            }
            // Check if any employee has not passed the exam or has no exam result
            // return $list_of_employee;
            if (
                collect($list_of_employee)->contains(function ($employee) {
                    return (empty($employee['hasExam']) || empty($employee['hasPassed'])) && !$employee['will_not_endorse'];
                })
            ) {
                return response()->json([
                    'result' => false,
                    'message' => 'All employees must have passed the exam.'
                ]);
            }

            if(isset($data['endorsement_id'])){ // Update
               $inserted_te_id = $data['endorsement_id'];
                TrainingEndorsement::where('id', $data['endorsement_id'])->update([
                    'disapprove_remarks' => null,
                    'disapprove_by'      => null,
                    'mail_cc'            => implode(',', $data['attn']),
                    'updated_by'         => $_SESSION['rapidx_user_id'] ?? 'system',
                    'updated_at'         => now(),
                ]);

                // 1. Fetch the OLD IDs and image details BEFORE deleting the records
                // This creates a nested array keyed by emp_no containing id, filename, and extension
                $oldRecords = TrainingEndorsementEmployee::where('training_endorsement_id', $data['endorsement_id'])
                    ->get()
                    ->keyBy('emp_no')
                    ->toArray();

                // 2. Clear old data
                TrainingEndorsementEmployee::where('training_endorsement_id', $data['endorsement_id'])->delete();
                TrainingEndorsementApprovals::where('training_endorsement_id', $data['endorsement_id'])->delete();
                foreach($list_of_employee as $employee){
                    $empNo = $employee['emp_no'];
                    $filename = "";
                    
                    $array_endorsement_employee = [
                        'training_endorsement_id'    => $data['endorsement_id'],
                        'training_request_detail_id' => $employee['tr_details_id'],
                        'emp_no'                     => $empNo,
                        'will_endorse'               => $employee['will_not_endorse'],
                        'will_not_endorse_remarks'   => $employee['remarks'],
                        'created_by'                 => $_SESSION['rapidx_user_id'] ?? 'system',
                        'created_at'                 => now(),
                    ];

                    // This is your new ID (e.g., 12)
                    $te_emp_id = TrainingEndorsementEmployee::insertGetId($array_endorsement_employee);

                    // 3. Process new image
                    if (isset($employee['hands_on_image']) && !empty($employee['hands_on_image'])) {
                        $filename = $employee['hands_on_file_name'] ?? '';
                        $extension = 'png';
                        if (!empty($filename) && str_contains($filename, '.')) {
                            $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        }
                        $storageFilename = $te_emp_id . '.' . $extension;

                        $imageData = $employee['hands_on_image'];
                        if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $imageData)) {
                            $imageData = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $imageData);
                            $imageData = base64_decode($imageData);
                        }
                        Storage::put('public/hands_on_attachments/' . $storageFilename, $imageData);

                        TrainingEndorsementEmployee::where('id', $te_emp_id)->update([
                            'hands_on_filename'     => $filename,
                            'hands_on_filename_ext' => $extension
                        ]);
                        
                    // 4. Retain old image (e.g., renaming 10.png to 12.png)
                    } elseif (isset($oldRecords[$empNo]) && !empty($oldRecords[$empNo]['hands_on_filename'])) {
                        
                        $oldId        = $oldRecords[$empNo]['id'];                 // This is 10
                        $oldExtension = $oldRecords[$empNo]['hands_on_filename_ext'] ?? 'png';
                        $oldFilename  = $oldRecords[$empNo]['hands_on_filename'];

                        $oldStoragePath = 'public/hands_on_attachments/' . $oldId . '.' . $oldExtension;       // public/hands_on_attachments/10.png
                        $newStoragePath = 'public/hands_on_attachments/' . $te_emp_id . '.' . $oldExtension;   // public/hands_on_attachments/12.png

                        // Check if the physical file 10.png actually exists before trying to rename it
                        if (Storage::exists($oldStoragePath)) {
                            // Rename/move the file from 10.png to 12.png
                            Storage::move($oldStoragePath, $newStoragePath);
                        }

                        // Save the original filename metadata into your new row (ID 12)
                        TrainingEndorsementEmployee::where('id', $te_emp_id)->update([
                            'hands_on_filename'     => $oldFilename,
                            'hands_on_filename_ext' => $oldExtension
                        ]);
                    }
                }
            }
            else{ // Create

                $ctrl_no = $this->generateControlNumber();
                $endorsementData = [
                    'training_request_id' => $data['training_req_id'],
                    'hr_memo_id'          => $data['hr_memo_id'],
                    'date'                => $data['endorsement_date'],
                    'ctrl_no'             => $ctrl_no,
                    'mail_cc'             => implode(',', $data['attn']),
                    'created_by'          => $_SESSION['rapidx_user_id'] ?? 'system',
                    'created_at'          => now(),
                ];
                $inserted_te_id = TrainingEndorsement::insertGetId($endorsementData);
                foreach($list_of_employee as $employee){
                    $filename = "";
                    $array_endorsement_employee = [
                        'training_endorsement_id'    => $inserted_te_id,
                        'training_request_detail_id' => $employee['tr_details_id'],
                        'emp_no'                     => $employee['emp_no'],
                        'will_endorse'               => $employee['will_not_endorse'],
                        'will_not_endorse_remarks'   => $employee['remarks'],
                        'created_by'                 => $_SESSION['rapidx_user_id'] ?? 'system',
                        'created_at'                 => now(),
                    ];

                    $te_emp_id = TrainingEndorsementEmployee::insertGetId($array_endorsement_employee);

                    if (isset($employee['hands_on_image']) && !empty($employee['hands_on_image'])) {
                        $filename = $employee['hands_on_file_name'] ?? '';
                        // Get extension from filename or default to png
                        $extension = 'png';
                        if (!empty($filename) && str_contains($filename, '.')) {
                            $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        }
                        $storageFilename = $te_emp_id . '.' . $extension;

                        // Decode base64 image if needed
                        $imageData = $employee['hands_on_image'];
                        if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $imageData)) {
                            $imageData = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $imageData);
                            $imageData = base64_decode($imageData);
                        }
                        Storage::put('public/hands_on_attachments/' . $storageFilename, $imageData);

                        $total_rating = "{$employee['hands_on_rating']}/{$employee['hands_on_total_rating']}";
                        TrainingEndorsementEmployee::where('id', $te_emp_id)
                        ->update([
                            'hands_on_filename'     => $filename,
                            'hands_on_filename_ext' => $extension,
                            'hands_on_rating'       => $total_rating,
                            'hands_on_remarks'      => $employee['hands_on_remarks'] ?? null
                        ]);
                    }
                }
            }
            // Insert checked_by approvals
            if (!empty($data['checked_by']) && is_array($data['checked_by'])) {
                foreach ($data['checked_by'] as $rapidx_id) {
                    $this->insertApproval($inserted_te_id, $rapidx_id, 'checked_by');
                }
            }
            // Insert approved_by approvals
            if (!empty($data['approved_by']) && is_array($data['approved_by'])) {
                foreach ($data['approved_by'] as $rapidx_id) {
                    $this->insertApproval($inserted_te_id, $rapidx_id, 'approved_by');
                }
            }
            DB::commit();


            return response()->json([
                'result' => true,
                'message' => 'Training endorsement saved successfully.',
                'endorsement_ctrl_no' => $ctrl_no ?? null
            ]);
        }catch(\Throwable $e){
            DB::rollback();
            return $e->getMessage();
        }
        
    }

    public function deleteTrainingEndorsement(Request $request)
    {
        DB::beginTransaction();
        try{
            TrainingEndorsement::where('id', $request->id)
            ->update([
                'deleted_at' => now(),
                'updated_by' => $_SESSION['rapidx_user_id'] ?? 'system',
                'updated_at' => now()
            ]);
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Training endorsement deleted successfully.'
            ]);
        }catch(\Throwable $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function getEndorsementUsers(Request $request)
    {

        $users = User::with(['users'])->whereNull('deleted_at')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->users->name,
                'email' => $user->users->email,
                'rapidx_id' => $user->rapidx_emp_id
            ];
        });

        return response()->json($users);
    }

    public function getCurrentUser(Request $request)
    {
        $user = session('global_user');
        $name = '';
        if ($user) {
            $name = $user->name;
        }

        return response()->json(['name' => trim($name)]);
    }

    public function getAllEmail(Request $request){
        $users = RapidXUser::where('user_stat', 1)->get();

        return response()->json($users);
    }

    public function getTrainingRequestControls(Request $request){
        $trainingReqCtrls = TrainingRequest::select('ctrl_number')
        ->where('logdel', 0)
        ->where('ctrl_number', 'like', '%' . $request->training_req_ctrl . '%')
        ->distinct()
        ->get();
        return response()->json($trainingReqCtrls);
    }

    public function getTrainingRequestDetails(Request $request){
        $ctrl_number = $request->training_req_ctrl;

        // // Get training_request_detail_ids already in training_endorsement_employees
       
            // 'training_request_details' => function($query) {
            //     $query->whereNotIn('id', function($sub) {
            //         $sub->select('training_request_detail_id')
            //             ->from('training_endorsement_employees')
            //             ->whereNull('deleted_at');
            //     });
            // },

        $trainingRequest = TrainingRequest::with([
            'training_request_details',
            'training_request_details.hr_memo_details',
            'training_request_details.employee_exam_details' => function($query) use ($ctrl_number) {
                $query->where('training_request_ctrl_no', $ctrl_number);
            },
            'training_request_details.employee_exam_details.exam_result_details_info' => function($query) {
                $query->where('exam_result_status', 1);
            }
        ])
        ->where('ctrl_number', $request->training_req_ctrl)
        ->where('status', 3) // Training Request Approved
        ->first();

        if(!$trainingRequest){
            return response()->json([
                'result' => false,
                'message' => 'Training Request Control Number not found or not approved.'
            ]);
        }

        $endorsement = TrainingEndorsement::with([
            'training_endorsement_employees:id,training_endorsement_id,training_request_detail_id'
        ])
        ->whereNull('deleted_at')
        ->where('training_request_id', $trainingRequest->id)
        ->get();

        // Flatten all training_endorsement_employee IDs into a single array
        // Get all training_request_detail_id from endorsement employees
        $endorsement_employee_detail_ids = [];
        foreach ($endorsement as $endorse) {
            foreach ($endorse->training_endorsement_employees as $emp) {
                $endorsement_employee_detail_ids[] = $emp->training_request_detail_id;
            }
        }
        // Remove training_request_details whose id is in endorsement_employee_detail_ids
        $filtered_details = collect($trainingRequest->training_request_details)->reject(function($detail) use ($endorsement_employee_detail_ids) {
            return in_array($detail->id, $endorsement_employee_detail_ids);
        })->values();

        // Properly replace the original details with the filtered collection using setRelation
        $trainingRequest->setRelation('training_request_details', $filtered_details);

        
        if(!$trainingRequest){
            return response()->json([
                'result' => false,
                'message' => 'Training Request Control Number not found.'
            ]);
        }

        // Get only the first hr_memo_details.document_no from training_request_details
        $hr_memo_document_no = null;
        $hr_memo_id = null;
        foreach ($trainingRequest->training_request_details as $detail) {
            $hr_memo_id = $detail->training_memo_doc_id;

            if ($detail->hr_memo_details && isset($detail->hr_memo_details->document_no)) {
                $hr_memo_document_no = $detail->hr_memo_details->document_no;
                break;
            }
        }

        return response()->json([
            'result'              => true,
            'training_request'    => $trainingRequest,
            'hr_memo_document_no' => $hr_memo_document_no,
            'hr_memo_id'     => $hr_memo_id
        ]);
    }

    public function generateControlNumber()
    {
        $prefix = 'TUE';
        $year = date('y'); // last two digits of year
        $month = date('m');  // month, two digits

        // Count existing endorsements for today
        $count = TrainingEndorsement::count() + 1;
        $countPadded = str_pad($count, 4, '0', STR_PAD_LEFT);

        $controlNumber = "{$prefix}-{$year}{$month}-{$countPadded}";
        return $controlNumber;
    }

    public function getEmployeesForNotEndorsed(Request $request)
    {
        $te_emp_details = TrainingEndorsementEmployee::where('training_endorsement_id', $request->training_endorsement_id)->get('emp_no')->pluck('emp_no')->toArray();

        $tr_details = TrainingRequestDetails::where('training_request_id', $request->trId)->whereNotIn('emp_no', $te_emp_details)->get();

        return DataTables::of($tr_details)
        ->addColumn('action', function ($row) use ($request) {
            $result = '';            
            $result .= '<center>';            
            $result .= '<button class="btn btn-sm btn-danger btnAddEmployeeForNotEndorsed" 
                        data-emp-no="' . $row->emp_no . '"  
                        data-te-id="' . $request->training_endorsement_id . '"  
                        data-tr-id="' . $row->id . '"  
                        title="Add Employee for Not Endorsed"><i class="fa fa-plus"></i></button>';
            $result .= '</center>';
            return $result;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    
    public function addNotEndorsedEmp(Request $request){
        DB::beginTransaction();
        try{
            TrainingEndorsementEmployee::insert([
                'training_endorsement_id'    => $request->training_endorsement_id,
                'training_request_detail_id' => $request->training_request_id,
                'emp_no'                     => $request->emp_no,
                'will_endorse'               => 1,
                'will_not_endorse_remarks'   => $request->remarks,
                'created_by'                 => $_SESSION['rapidx_user_id'] ?? 'system',
                'updated_by'                 => $_SESSION['rapidx_user_id'] ?? 'system',
                'created_at'                 => now(),
            ]);
            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Employee added successfully for not endorsed.'
            ]);
        }catch(\Throwable $e){
            DB::rollback();
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function exportEndorsementPdf(Request $request)
    {
        $tr_ctrl_no = $request->tr_ctrl_no;

        $data = TrainingEndorsement::with([
            'created_by_user_details',
            'training_request_details',
            'hr_memo_details',
            'te_approval_details',
            'te_approval_details.approver_details',
            'training_endorsement_employees' => function($query) {
                $query->whereNull('deleted_at');
            },
            'training_endorsement_employees.training_request_details_info',
            'training_endorsement_employees.training_request_details_info.employee_exam_details' => function($query) use ($tr_ctrl_no) {
                $query->where('training_request_ctrl_no', $tr_ctrl_no);
            },
            'training_endorsement_employees.training_request_details_info.employee_exam_details.exam_result_details_info'
        ])
        ->where('id', $request->id)
        ->first();

        if (!$data) {
            abort(404, 'Endorsement not found.');
        }

        // return $data;
        // Build employees array for the PDF
        $employees = [];
        $employees_will_not_endorse = [];

        foreach ($data->training_endorsement_employees as $emp) {
            $detail = $emp->training_request_details_info;
            if (!$detail) continue;

            $exams = [];
            if ($detail->employee_exam_details && $detail->employee_exam_details->count() > 0) {
                foreach ($detail->employee_exam_details as $examResult) {
                    $examDetail = $examResult->exam_result_details_info;
                    $examTitle = '';
                    $score = '';
                    $rating = '';
                    $remark = '';

                    if ($examDetail) {
                        // Parse questionnaire JSON for exam_title
                        if ($examDetail->questionnaire) {
                            $questionnaire = is_string($examDetail->questionnaire)
                                ? json_decode($examDetail->questionnaire, true)
                                : $examDetail->questionnaire;
                        $examTitle = $questionnaire['exam_title'] ?? '';
                        }

                        $totalScore = ($examDetail->score ?? 0) + ($examDetail->identification_essay_score ?? 0);
                        $totalItems = $questionnaire['total_items'] ?? $questionnaire['total_points'] ?? '';
                        $score = $totalItems ? $totalScore . '/' . $totalItems : $totalScore;
                        $rating = $examDetail->rating ?? '';
                        $remark = $examDetail->remark ?? '';
                    }

                    $exams[] = [
                        'title'  => $examTitle,
                        'score'  => $score,
                        'rating' => $rating,
                        'remark' => $remark,
                    ];
                }
            }

            $posDeptSec = implode(' / ', array_filter([
                $detail->position ?? '',
                $detail->department ?? '',
                $detail->section ?? '',
            ]));

            if(!is_null($emp->hands_on_filename) && !is_null($emp->hands_on_rating)){
                list($score, $total) = explode('/', $emp->hands_on_rating);

                // 2. Prevent division by zero error just in case
                if ($total > 0) {
                    $percentage = ($score / $total) * 100;
                } else {
                    $percentage = 0;
                }

                // Output: 100%
                $rating = round($percentage) . '%';

                $exams[] = [
                    'title'  => "Hands On Exam",
                    'score'  => $emp->hands_on_rating ?? '',
                    'rating' => $rating ?? '0%',
                    'remark' => $emp->hands_on_remarks ?? '',
                ];
            }
            
            if($emp->will_endorse == 1){
                $employees_will_not_endorse[] = [
                    'date_hired'          => $detail->date_hired ?? '',
                    'emp_no'              => $detail->emp_no ?? '',
                    'name'                => $detail->name ?? '',
                    'position'            => $posDeptSec,
                    'exams'               => $exams,
                    'immediate_superior'  => $data->training_request_details->section_head_user->name ?? '',
                    'remarks'             => $emp->will_not_endorse_remarks ?? '',
                ];
            }
            else{
                $ext = $emp->hands_on_filename_ext ?? '';
                $employees[] = [
                    'date_hired'          => $detail->date_hired ?? '',
                    'emp_no'              => $detail->emp_no ?? '',
                    'name'                => $detail->name ?? '',
                    'position'            => $posDeptSec,
                    'exams'               => $exams,
                    'immediate_superior'  => $data->training_request_details->section_head_user->name ?? '',
                    'attachment'          => $emp->hands_on_filename ? asset('public/storage/hands_on_attachments/' . $emp->id . '.' . $emp->hands_on_filename_ext) : '',
                ];
            }
           
        }

        $attnEmails = $data->mail_cc ?? '';
        $endorsementDate = $data->date ? Carbon::parse($data->date)->format('F j, Y') : '';

        $pdf = Pdf::loadView('pdf.training_endorsement', [
            'endorsement'                   => $data,
            'to'                            => $attnEmails,
            'attn'                          => $attnEmails,
            'hr_memo_no'                    => $data->hr_memo_details->document_no ?? '',
            'training_request_ctrl'         => $data->training_request_details->ctrl_number ?? '',
            'endorsement_date'              => $endorsementDate,
            'hr_endorsement_date'           => '',
            'training_date'                 => '',
            'endorsement_to_requestor_date' => $endorsementDate,
            'employees'                     => $employees,
            'employees_will_not_endorse'    => $employees_will_not_endorse,
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('endorsement_' . ($data->ctrl_no ?? 'unknown') . '.pdf');
    }

    public function proceedEndorsementApproval(Request $request){
        DB::beginTransaction();
        try{
            TrainingEndorsement::where('id', $request->id)
            ->update([
                'status' => 1,
                'updated_by' => $_SESSION['rapidx_user_id'],
                'updated_at' => now(),
            ]);

            $details = TrainingEndorsement::with([
                'te_approval_details',
                'te_approval_details.approver_details',
                'created_by_user_details'
            ])->where('id', $request->id)->first();

            $this->CommonController->sendEmailTrainingEndorsement($details, 1);
            
            DB::commit();
            
            return response()->json([
                'result' => true,
                'message' => 'Endorsement approval proceeded successfully.'
            ]);
        }catch(\Throwable $e){
            DB::rollback();
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while processing your request.'
            ]);
        }
    }

    public function approveEndorsement(Request $request){
        DB::beginTransaction();
        try{
            $approval_type = $request->approval_type; // 'checker' or 'approver'
            TrainingEndorsementApprovals::where('training_endorsement_id', $request->id)
            ->where('rapidx_id', $_SESSION['rapidx_user_id'])
            ->where('approval_type', $approval_type === 'checker' ? 'checked_by' : 'approved_by')
            ->update([
                'updated_by' => $_SESSION['rapidx_user_id'],
                'updated_at' => now(),
            ]);

            $approver_left = TrainingEndorsementApprovals::where('training_endorsement_id', $request->id)
            ->where('approval_type', $approval_type === 'checker' ? 'checked_by' : 'approved_by')
            ->whereNull('updated_at')
            ->get();

            $new_status = 1;
            if($approver_left->count() == 0){
                $new_status = ($approval_type === 'checker') ? 2 : 3;
                TrainingEndorsement::where('id', $request->id)
                ->update([
                    'status' => $new_status,
                    'updated_by' => $_SESSION['rapidx_user_id'],
                    'updated_at' => now(),
                ]);
            }

            $details = TrainingEndorsement::with([
                'te_approval_details',
                'te_approval_details.approver_details',
                'created_by_user_details'
            ])->where('id', $request->id)->first();
            if($approval_type == 'checker' && $new_status == 1){
                $this->CommonController->sendEmailTrainingEndorsement($details, 1);

            }
            else{
                if($new_status == 3){
                    $this->CommonController->sendEmailTrainingEndorsement($details, 3);
                }
                else{
                    $this->CommonController->sendEmailTrainingEndorsement($details, 2);
                }

            }
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Endorsement approved successfully.'
            ]);
        }catch(Throwable $e){
            DB::rollback();
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while processing your request.'
            ]);
        }
    }

    public function disapproveEndorsement(Request $request){
        DB::beginTransaction();
        try{
            
            $approval_type = $request->approval_type; // 'checker' or 'approver'
            TrainingEndorsement::where('id', $request->id)
            ->update([
                'status' => 0,
                'disapprove_remarks' => $request->dis_remarks ?? '',
                'disapprove_by' => $_SESSION['rapidx_user_id'],
                'updated_by' => $_SESSION['rapidx_user_id'],
                'updated_at' => now(),
            ]);
            TrainingEndorsementApprovals::where('training_endorsement_id', $request->id)
            ->where('rapidx_id', $_SESSION['rapidx_user_id'])
            ->where('approval_type', $approval_type === 'checker' ? 'checked_by' : 'approved_by')
            ->update([
                'updated_by' => $_SESSION['rapidx_user_id'],
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Endorsement disapproved successfully.'
            ]);
        }catch(Throwable $e){
            DB::rollback();
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while processing your request.'
            ]);
        }
    }
}
