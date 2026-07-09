<?php

namespace App\Http\Controllers;
use App\Model\Hr\HrMemo;
use App\Model\Hr\HrMemoTraineeCategoryDetails;
use App\Model\SystemOneHrisDepartment;
use App\Model\SystemOneHrisDivision;
use App\Model\SystemOneHrisSection;
use App\Model\TrainingRequest;
use App\Model\TrainingRequestDetails;
use App\Model\User;
use App\RapidXUser;
use App\Model\RapidXDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class TrainingRequestController extends Controller
{

    public function getTrainingRequests(Request $request){
        $trainingRequests = TrainingRequest::with(['section_head_user'])
        ->where('logdel', 0)
        ->get();

        // return $trainingRequests;

        $receiverUsers = User::with(['user_access_module'])
        ->whereHas('user_access_module', function ($query) {
            $query->whereRaw("FIND_IN_SET(?, user_modules_id)", [5]);
        })
        ->whereNull('deleted_at') // optional: exclude deleted users
        ->get();

        $tuHeadApproverUser = User::where('rapidx_emp_id', session('global_user')->rapidx_emp_id)
            ->whereHas('user_access_module', function ($query) {
                $query->whereRaw("FIND_IN_SET(?, user_modules_id)", [6]);
            })
            ->first();

        if(!$tuHeadApproverUser){
            $tuHeadApproverUser = null;
        }else{
            $tuHeadApproverUser->pluck('rapidx_emp_id');
        }

        $receiverUsers->pluck('rapidx_emp_id');

        $filter = $request->filter;

        if ($filter == 0) {
            $trainingRequests = $trainingRequests->where('status', 0); // For Conformance
        } else if ($filter == 1) {
            $trainingRequests = $trainingRequests->where('status', 1); // For Receive
        } else if ($filter == 2) {
            $trainingRequests = $trainingRequests->where('status', 2); // For TU Head Approval
        }else{
            $trainingRequests = $trainingRequests->whereIn('status', [0, 1, 2,3]); // All
        }


        return DataTables()->of($trainingRequests)
        ->addColumn('action', function($trainingRequest) use ($receiverUsers, $tuHeadApproverUser){
            $sectionHeadDeptId = $trainingRequest->section_head_user->department_id;
            $trainingRequestDept = RapidXDepartment::where('department_id', $sectionHeadDeptId)->first();
            $dept = $trainingRequestDept->department_name;
            // return $dept;
            // return $trainingRequest->department_id;
            $result = '';
            $result .= '<center>';

            $result .= '<button class="btn btn-sm btn-primary btnViewTrainingRequest me-5" data-id="' . $trainingRequest->id . '"><i class="fas fa-eye"></i></button>&nbsp;';

            if($trainingRequest->status == 0) {
                if($trainingRequest->section_head_user && $trainingRequest->section_head_user->id == $_SESSION['rapidx_user_id']){
                    $result .= '<button class="btn btn-sm btn-success btnConformTrainingRequest"
                        data-id="' . $trainingRequest->id . '"
                        data-ctrl="' . $trainingRequest->ctrl_number . '"
                        data-dept="' . $dept . '">
                        <i class="fas fa-check"></i>
                    </button>';                }
            }else if($trainingRequest->status == 1){
                if($receiverUsers->contains('rapidx_emp_id', $_SESSION['rapidx_user_id'])){
                    // $result .= '<button class="btn btn-sm btn-success btnReceiveTrainingRequest" data-id="' . $trainingRequest->id . '"><i class="fas fa-check"></i></button>';
                    $result .= '<button class="btn btn-sm btn-success btnReceiveTrainingRequest"
                        data-id="' . $trainingRequest->id . '"
                        data-ctrl="' . $trainingRequest->ctrl_number . '"
                        data-dept="' . $dept . '">
                        <i class="fas fa-check"></i>
                    </button>';
                    }
            }else if($trainingRequest->status == 2){
                if($tuHeadApproverUser){
                    // $result .= '<button class="btn btn-sm btn-success btnApproveTrainingRequest" data-id="' . $trainingRequest->id . '"><i class="fas fa-check"></i></button>';
                    $result .= '<button class="btn btn-sm btn-success btnApproveTrainingRequest"
                        data-id="' . $trainingRequest->id . '"
                        data-ctrl="' . $trainingRequest->ctrl_number . '"
                        data-dept="' . $dept . '">
                        <i class="fas fa-check"></i>
                    </button>';
                }
            }

            $result .= '</center>';
            return $result;
        })
        ->addColumn('status', function($trainingRequest){
            $result = '';
            if($trainingRequest->status == 0){
                $result = '<span class="badge badge-warning">For Conformance</span>';
            }else if($trainingRequest->status == 1){
                $result = '<span class="badge badge-warning">For Receive</span>';
            }else if($trainingRequest->status == 2){
                $result = '<span class="badge badge-warning">For TU Head Approval</span>';
            }else if($trainingRequest->status == 3){
                $result = '<span class="badge badge-success">Approved</span>';
            }

            return $result;
        })

        ->addColumn('section_head_user', function($trainingRequest){

            $result = '<div class="text-center">';
            $conformanceUser = $trainingRequest->section_head_user;

            if ($trainingRequest->status == 0) {
                $name = $conformanceUser ? $conformanceUser->name : '';

                $date = date('M d, Y', strtotime($trainingRequest->created_at));
                $time = date('h:i:s A', strtotime($trainingRequest->created_at));

                $result .= "<strong>$name</strong>";
                $result .= '<br><span class="badge badge-secondary">Pending</span>';
                $result .= "<br><small class='text-muted'>$date $time</small>";
            }else if($trainingRequest->status == 1 || $trainingRequest->status == 2 || $trainingRequest->status == 3){
                    $name = $conformanceUser ? $conformanceUser->name : '';

                    $conformDate = date('M d, Y', strtotime($trainingRequest->section_head_date));
                    $conformTime = date('h:i:s A', strtotime($trainingRequest->section_head_date));

                    $result .= "<strong>$name</strong>";
                    $result .= '<br><span class="badge badge-success">Conformed</span>';
                    $result .= "<br><small class='text-muted'>$conformDate $conformTime</small>";
            }

            $result .= '</div>';

            return $result;
        })

        ->addColumn('receiving', function($trainingRequest){

            $result = '<div class="text-center">';

            $date = date('M d, Y', strtotime($trainingRequest->created_at));
            $time = date('h:i:s A', strtotime($trainingRequest->created_at));

            if ($trainingRequest->status == 0) {
                $result .= '<span class="badge badge-secondary">Pending</span>';
                $result .= "<br><small class='text-muted'>$date $time</small>";
            }
            else if ($trainingRequest->status == 1) {
                // if(!$trainingRequest->section_head_date){
                    $forReceiveDate = date('h:i:s A', strtotime($trainingRequest->section_head_date));
                    $forReceiveTime = date('h:i:s A', strtotime($trainingRequest->section_head_date));
                    //  $result .= '<span class="badge badge-secondary">Pending</span>';
                    $result .= '<span class="badge badge-secondary">Pending</span>';
                    $result .= "<br><small class='text-muted'>$forReceiveDate $forReceiveTime</small>";
                // }
            }else if($trainingRequest->status == 2){
                $receiverName = $trainingRequest->received_by ? RapidXUser::where('id', $trainingRequest->received_by)->first()->name : 'Unknown User';
                $receivedDate = date('M d, Y', strtotime($trainingRequest->received_date));
                $receivedTime = date('h:i:s A', strtotime($trainingRequest->received_date));

                $result .= "<strong>$receiverName</strong>";
                $result .= '<br><span class="badge badge-success">Received</span>';
                $result .= "<br><small class='text-muted'>$receivedDate $receivedTime</small>";
            }else if($trainingRequest->status == 3){
                $receiverName = $trainingRequest->received_by ? RapidXUser::where('id', $trainingRequest->received_by)->first()->name : 'Unknown User';
                $receivedDate = date('M d, Y', strtotime($trainingRequest->received_date));
                $receivedTime = date('h:i:s A', strtotime($trainingRequest->received_date));

                $result .= "<strong>$receiverName</strong>";
                $result .= '<br><span class="badge badge-success">Received</span>';
                $result .= "<br><small class='text-muted'>$receivedDate $receivedTime</small>";
            }

            $result .= '</div>';

            return $result;
        })

        ->addColumn('tu_head_approval', function($trainingRequest){

            $result = '<div class="text-center">';

            $date = date('M d, Y', strtotime($trainingRequest->created_at));
            $time = date('h:i:s A', strtotime($trainingRequest->created_at));

            if ($trainingRequest->status == 0) {
                 $result .= '<span class="badge badge-secondary">Pending</span>';
                $result .= "<br><small class='text-muted'>$date $time</small>";
            }
            else if ($trainingRequest->status == 1) {
                // $result .= '<span class="badge badge-secondary">Pending</span>';
                // $result .= "<br><small class='text-muted'>$date $time</small>";
                $forReceiveDate = date('h:i:s A', strtotime($trainingRequest->section_head_date));
                $forReceiveTime = date('h:i:s A', strtotime($trainingRequest->section_head_date));
                //  $result .= '<span class="badge badge-secondary">Pending</span>';
                $result .= '<span class="badge badge-secondary">Pending</span>';
                $result .= "<br><small class='text-muted'>$forReceiveDate $forReceiveTime</small>";

            }else if($trainingRequest->status == 2 || $trainingRequest->status == 3){
                if(!$trainingRequest->tu_head_approver && $trainingRequest->received_by){
                    $receivedDate = date('M d, Y', strtotime($trainingRequest->received_date));
                    $receivedTime = date('h:i:s A', strtotime($trainingRequest->received_date));

                    // $result .= "<strong>$receiverName</strong>";
                    $result .= '<span class="badge badge-secondary">Pending</span>';
                    $result .= "<br><small class='text-muted'>$receivedDate $receivedTime</small>";
                }else{
                    $tuHeadApproverName = $trainingRequest->tu_head_approver ? RapidXUser::where('id', $trainingRequest->tu_head_approver)->first()->name : 'Unknown User';
                    $tuHeadApprovalDate = date('M d, Y', strtotime($trainingRequest->tu_head_approve_date));
                    $tuHeadApprovalTime = date('h:i:s A', strtotime($trainingRequest->tu_head_approve_date));

                    $result .= "<strong>$tuHeadApproverName</strong>";
                    $result .= '<br><span class="badge badge-success">Approved</span>';
                    $result .= "<br><small class='text-muted'>$tuHeadApprovalDate $tuHeadApprovalTime</small>";
                }

            }

            $result .= '</div>';

            return $result;
        })
        ->addColumn('date_filed', function($trainingRequest){
            $date = date('M d, Y', strtotime($trainingRequest->created_at));
            $time = date('h:i:s A', strtotime($trainingRequest->created_at));

            return '<div>
                        <strong>'.$date.'</strong><br>
                        <small class="text-muted">'.$time.'</small>
                    </div>';
        })
        ->rawColumns(['action', 'status', 'section_head_user','receiving', 'tu_head_approval','date_filed'])
        ->make(true);
    }

    public function getHRISDivisions(Request $request){
        // return 'asd';
        $systemOneDivision = SystemOneHrisDivision::where('logdel', 0)
        ->get();
        return response()->json($systemOneDivision);
    }

    public function getHRISSections(Request $request){
        // return 'asd';
       $systemOneSection = SystemOneHrisSection::with(['department'])
    ->where('isActive', 1)
    // ->select('section', 'department_id')
    ->distinct()
    ->get();

    // return $systemOneSection;
        // $systemOneSection = SystemOneHrisSection::where('isActive', 1)
        // ->get();
        return response()->json($systemOneSection);
    }

    public function getHRISSectionByDepartment(Request $request){
        $systemOneDepartment = SystemOneHrisDepartment::where('isActive', 1)
        ->get();
        // return $systemOneDepartment;
        return response()->json($systemOneDepartment);
    }

    public function addTrainingRequest(Request $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->all();

        $ctrlNumber = TrainingRequest::max('id') + 1;
        $ctrlNumber = str_pad($ctrlNumber, 4, '0', STR_PAD_LEFT);
        $date = date('ym');


        $ctrlNumber = 'TR-' . $date . '-' . $ctrlNumber;

        $trainingRequest = new TrainingRequest();
        $trainingRequest->ctrl_number = $ctrlNumber;
        $trainingRequest->date_filed = $data['date_filed'];
        $trainingRequest->department_id = $data['department'];
        $trainingRequest->section_id = $data['section'];
        $trainingRequest->job_function = $data['job_function'];
        $trainingRequest->area_allocation = $data['area_line'];
        $trainingRequest->reason = $data['reason'];
        $trainingRequest->section_head = $data['section_head'];
        $trainingRequest->created_by = $_SESSION["rapidx_user_id"] ?? 0;

        $trainingRequest->save();
        $result = $trainingRequest->save();

        // dd([
        //     'saved' => $result,
        //     'id' => $trainingRequest->id,
        //     'attributes' => $trainingRequest->getAttributes(),
        // ]);

        $trainingRequestId = $trainingRequest->id;

        $employees = $request->employees;
        $memoDocId = $request->memo_doc_id;


        if (!$employees) {
            return response()->json([
                'message' => 'No employees to save'
            ]);
        }

        // dd($employees);


        foreach($employees as $emp){
            // dd($emp);
            $empPosDeptSec = $emp['position'];
            $hrmemoDocsId = $emp['id'];
            $trainingTitle = $emp['training_title'];
            $remarks = $emp['remarks'];
            $result = strip_tags($emp['training_result'], '<br>');


            $explodedEmpPosDeptSec = explode('/', $empPosDeptSec);
            $explodedtrainingTitle = explode(',<br>', $trainingTitle);
            $explodedRemarks = explode(',<br>', $remarks);
            $explodedResult = explode('<br>', $result);

            $implodedTrainingTitle = implode(',', $explodedtrainingTitle);
            $implodedRemarks = implode(',', $explodedRemarks);
            $implodedResult = implode(',', $explodedResult);
            $position   = isset($explodedEmpPosDeptSec[0]) ? trim($explodedEmpPosDeptSec[0]) : '';
            $department = isset($explodedEmpPosDeptSec[1]) ? trim($explodedEmpPosDeptSec[1]) : '';
            $section    = isset($explodedEmpPosDeptSec[2]) ? trim($explodedEmpPosDeptSec[2]) : '';

            // dd($trainingRequestId);
            try {

                $detail = TrainingRequestDetails::updateOrCreate(
                    [
                        'training_request_id' => $trainingRequestId,
                        'training_memo_doc_id' => $memoDocId,
                        'hr_memo_trainee_details_id' => $hrmemoDocsId,
                        'emp_no' => $emp['emp_no']
                    ],
                    [
                        'date_hired' => $emp['date_hired'],
                        'name' => $emp['name'],
                        'position' => $position,
                        'department' => $department,
                        'section' => $section,
                        'training_title' => $implodedTrainingTitle,
                        'training_result' => $implodedResult,
                        'remarks' => $implodedRemarks,
                        'training_venue' => $emp['training_venue'],
                        'training_endorsement_date' => $emp['training_endorsement_date']
                    ]
                );

            } catch (\Exception $e) {
                dd($e->getMessage(), $e->getTraceAsString());
            }

            $savedEmployees[] = [
                'name' => $emp['name'],
                'emp_no' => $emp['emp_no'],
                'training_result' => implode(',', $explodedResult),
            ];
        }


        // email notification
        $recipient = RapidXUser::where('id', $trainingRequest->section_head)->first();
        $bcc = RapidXUser::find($trainingRequest->created_by);
        $hrMemoDoc = HrMemo::where('id', $memoDocId)->get();

        if ($recipient) {
            $recipientEmail = $recipient->email;
            $recipientName = $recipient->name;
            $bccEmail = $bcc ? $bcc->email : null;
            $documentNo = $hrMemoDoc[0]->document_no;

            // return $documentNo;

            $data = [
                'ctrl_number' => $ctrlNumber,
                'documentNo' => $documentNo,
                'dateFiled' => $data['date_filed'],
                'recipientName' => $recipientName,
            ];


            Mail::send('mail.new_training_request_notification', $data, function ($message) use ($recipientEmail, $recipientName, $bccEmail) {
                $message->to($recipientEmail, $recipientName);
                $message->cc(['kcalcantara@pricon.ph, cnpoblete@pricon.ph']);
                    if ($bccEmail) {
                        $message->bcc($bccEmail);
                    }
                $message->subject('New Training Request Notification');
            });

        }


        if($trainingRequest){
            return response()->json(['result' => 1, 'message' => 'Training request added successfully']);
        } else {
            return response()->json(['result' => 0, 'message' => 'Failed to add training request']);
        }

    }

    public function getUserConformance(Request $request){
        // return 'asd';
       $modules = [3, 5, 6];

        $trainingRequest = User::whereHas('user_access_module', function ($query) use ($modules) {
            $query->where(function ($q) use ($modules) {
                foreach ($modules as $module) {
                    $q->orWhereRaw("FIND_IN_SET(?, user_modules_id)", [$module]);
                }
            });
        })
        ->with('users')
        ->get();

        // return $trainingRequest;
        return response()->json($trainingRequest);
    }

    public function getRequestor(Request $request){
        $requestor_id = $_SESSION['rapidx_user_id'];
        $requestorName = RapidXUser::where('id', $requestor_id)->first();

        $requestorName = $requestorName ? $requestorName->name : 'Unknown User';

        return response()->json(['result' => 1, 'requestor_name' => $requestorName]);
    }

    public function getTrainingRequestDetails(Request $request){
        $trainingRequestDetails = TrainingRequest::with(['section_head_user', 'training_request_details','requestor'])
        ->where('id', $request->id)
        ->first();

        return response()->json($trainingRequestDetails);

    }

    public function getMemoDocs(Request $request){
        $selectedMemoId = $request->selectedMemoId;

        $memo = HrMemo::with([
                'trainee_details',
                'trainee_details.hris_emp_info',
                'trainee_details.emp_exam_details',
                'trainee_details.emp_exam_details.exam_info'
            ])
            ->where(function ($query) use ($selectedMemoId) {

                $query->whereHas('trainee_details', function ($q) {
                    $q->whereNotIn('id', function ($subquery) {
                        $subquery->select('hr_memo_trainee_details_id')
                            ->from('training_request_details')
                            ->whereNotNull('hr_memo_trainee_details_id');
                    });
                });

                if ($selectedMemoId) {
                    $query->orWhere('id', $selectedMemoId); // ✅ FIX: use HrMemo.id
                }

            })
            ->where('status', 6)
            ->get();

        return response()->json($memo);
    }

    public function getMemoDocsDetails(Request $request){
        $memoDocDetails = HrMemoTraineeCategoryDetails::where('hr_memo_id', $request->id)
        ->first();

        return response()->json($memoDocDetails);
    }


    public function getEmployeeListByMemoDoc(Request $request){
        $memoDocId = $request->memo_doc_id;

        $memo = HrMemo::with([
            'trainee_details',
            'trainee_details.hris_emp_info',
            'trainee_details.emp_exam_details',
            'trainee_details.emp_exam_details.exam_info'
        ])
        ->where('id', $memoDocId)
        ->where('status', 6)
        ->first();

        // return $memo;

        // return $memo;



        // $traineeDetails = $memo ? $memo->trainee_details : collect();
        $traineeDetails = collect();

        if ($memo) {
            // Get already used trainee IDs
            $existingIds = \DB::table('training_request_details')
                ->pluck('hr_memo_trainee_details_id')
                ->toArray();

            // Filter only NOT yet added
            $traineeDetails = $memo->trainee_details
                ->whereNotIn('id', $existingIds)
                ->values();
        }

        foreach($traineeDetails as $td){

            if ($td->employment_type == 1) {
                $td->load(['hris_emp_info' => function ($q) {
                    // $q->join('vw_Trainee', 'vw_employeeinfo.pkid', '=', 'vw_Trainee.fkEmployee')
                    // ->join('tbl_Training', 'vw_Trainee.fkTraining', '=', 'tbl_Training.pkid')
                    $q->select('vw_employeeinfo.*');
                }]);
            } else {
                $td->load(['subcon_emp_info' => function ($q) {
                    // $q->join('vw_Trainee', 'vw_subcon_employeeinfo.pkid', '=', 'vw_Trainee.fkSubconEmployee')
                    // ->join('tbl_Training', 'vw_Trainee.fkTraining', '=', 'tbl_Training.pkid')
                    // $q->select('vw_subcon_employeeinfo.*');
                }]);
            }
        }

        // return $td;


        return DataTables()->of($traineeDetails)
        ->addColumn('id', function($td){
            return $td->id;
        })
        ->addColumn('action', function($td){
            $result = '';
            $result .= '<center>';

            $result .= '<button class="btn btn-sm btn-danger btnRemoveEmployeeFromMemoDoc" data-id="' . $td->id . '">
            <i class="fa fa-trash"></i>
            </button>';

            $result .= ' </center>';
            return $result;
        })

        ->addColumn('date_hired', function($td){
            if ($td->employment_type == 1) {
                return $td->hris_emp_info->DateHired ?? '';
            } else {
                return $td->subcon_emp_info->DateHired ?? '';
            }
        })

        ->addColumn('emp_no', function($td){
            if ($td->employment_type == 1) {
                return $td->hris_emp_info->EmpNo ?? '';
            } else {
                return $td->subcon_emp_info->EmpNo ?? '';
            }
        })

        ->addColumn('name', function($td){
            if($td->employment_type == 1){
                return $td->hris_emp_info->EmpName ?? '';
            }else{
                return $td->subcon_emp_info->EmpName ?? '';
            }
        })

        ->addColumn('training_venue', function($td){
            if ($td->employment_type == 1) {
                return $td->hris_emp_info->Venue ?? '';
            } else {
                return $td->subcon_emp_info->Venue ?? '';
            }
        })

        ->addColumn('training_endorsement_date', function($td){
            return $td->endorsement_date;
        })

        ->addColumn('position', function($td){
            if($td->employment_type == 1){
                return ($td->hris_emp_info->Position ?? '') . ' / ' .
                ($td->hris_emp_info->Department ?? '') . ' / ' .
                ($td->hris_emp_info->Section ?? '');
            }else{
                return ($td->subcon_emp_info->Position ?? '') . ' / ' .
                ($td->subcon_emp_info->Department ?? '') . ' / ' .
                ($td->subcon_emp_info->Section ?? '');

            }

        })

        ->addColumn('training_title', function($td){
            $titles = [];
            foreach($td->emp_exam_details as $ed){
                foreach($ed->exam_info as $ei){
                    $titles[] = $ei->examination_name;
                }
            }
            return implode(',<br>', $titles);
        })

        ->addColumn('training_result', function($td){
            $trainingResult = [];
            foreach($td->emp_exam_details as $ed){
                $trainingResult[] = $ed->result == 1 ? '<span class="badge badge-success">Passed</span>' :
                                    ($ed->result == 2 ? '<span class="badge badge-danger">Failed</span>' :
                                    '<span class="badge badge-warning">Complied</span>');
            }
            return implode('<br>', $trainingResult);
        })

        ->addColumn('remarks', function($td){
            $remarks = [];
            foreach($td->emp_exam_details as $ed){
                $remarks[] = $ed->training_remarks;
            }
            return implode(',<br>', $remarks);
        })

        ->rawColumns(['action','training_result','remarks','training_title'])
        ->make(true);
    }

     public function confirmTrainingRequest(Request $request){ // email
        date_default_timezone_set('Asia/Manila');

        $trainingRequest = TrainingRequest::find($request->id);
        // $trainingRequestDept = RapidXDepartment::where('department_id', $trainingRequest->department_id)->first();
        $systemOneDepartment = SystemOneHrisDepartment::where('pkid',  $trainingRequest->department_id)
        ->where('isActive', 1)
        ->get();
        $trainingRequestMemoDocId = TrainingRequest::with('training_request_details')
            ->find($request->id);

        $documentNoId = $trainingRequestMemoDocId->training_request_details->first()->training_memo_doc_id;

        $hrMemoDoc = HrMemo::where('id', $documentNoId)->get();

        $documentNo = $hrMemoDoc[0]->document_no;

        $receivingRecipients = [];
        if($trainingRequest){
            $trainingRequest->status = 1; // Update status to "Conformed"
            $trainingRequest->section_head_date = date('Y-m-d H:i:s');
            $trainingRequest->save();
            $departmentName = strtoupper($systemOneDepartment[0]->Department);

            if (str_contains($departmentName, 'F3')) {
                // F3 recipient
                $receivingRecipients = RapidXUser::whereIn('id', [82,976, 965])->pluck('email')
                ->toArray();

            } elseif ( str_contains($departmentName, 'TS-F1') || str_contains($departmentName, 'TS - PRODUCTION') || str_contains($departmentName, 'YF')){
                // F1/Regular TS recipient
                $receivingRecipients = RapidXUser::whereIn('id', [102, 965])->pluck('email')
                ->toArray();

            } elseif ( str_contains($departmentName, 'CN')) {

                // CN/PPS recipient
                $receivingRecipients = RapidXUser::whereIn('id', [76, 75, 965])->pluck('email')
                ->toArray();

            }elseif(str_contains($departmentName, 'PPD')){
                $receivingRecipients = RapidXUser::whereIn('id', [102, 76, 965])->pluck('email')
                ->toArray();
            }

            $emailData = [
                'ctrlNumber'  => $trainingRequest->ctrl_number,
                'documentNo'  => $documentNo,
                'dateFiled'   => $trainingRequest->date_filed,
                'receivingRecipients' => $receivingRecipients
            ];

            Mail::send(
                'mail.for_receive_training_request_notification',
                $emailData,
                function ($message) use ($receivingRecipients) {

                    $message->to($receivingRecipients)
                            ->subject('Training Request for Receiving');
                }
            );

            return response()->json(['result' => 1, 'message' => 'Training request conformed successfully']);
        } else {
            return response()->json(['result' => 0, 'message' => 'Training request not found']);
        }
    }

    public function receiveTrainingRequest(Request $request){ // email notification
        date_default_timezone_set('Asia/Manila');

        $trainingRequest = TrainingRequest::find($request->id);
        $trainingRequestMemoDocId = TrainingRequest::with('training_request_details')
        ->find($request->id);

        $documentNoId = $trainingRequestMemoDocId->training_request_details->first()->training_memo_doc_id;

        $hrMemoDoc = HrMemo::where('id', $documentNoId)->get();

        $documentNo = $hrMemoDoc[0]->document_no;

        if($trainingRequest){
            $trainingRequest->status = 2; // Update status to "Received"
            $trainingRequest->received_by = $_SESSION['rapidx_user_id'];
            $trainingRequest->received_date = date('Y-m-d H:i:s');
            $trainingRequest->save();

            $tuHeadRecipient = RapidXUser::where('id', 74)
                ->get();

            $emailData = [
                'ctrlNumber'  => $trainingRequest->ctrl_number,
                'documentNo'  => $documentNo,
                'dateFiled'   => $trainingRequest->date_filed,
                'tuHeadRecipient' => $tuHeadRecipient[0]->name
            ];

            Mail::send(
                'mail.tu_head_approval_request_notification',
                $emailData,
                function ($message) use ($tuHeadRecipient) {

                    $message->to($tuHeadRecipient[0]->email)
                            ->cc(['kcalcantara@pricon.ph','havasquez@pricon.ph'])
                            ->subject('Training Request for TU Head Approval');
                }
            );

            return response()->json(['result' => 1, 'message' => 'Training request received successfully']);
        } else {
            return response()->json(['result' => 0, 'message' => 'Training request not found']);
        }
    }

    public function approveTrainingRequest(Request $request){ // email notification to TU Head Approver
        date_default_timezone_set('Asia/Manila');

        $trainingRequest = TrainingRequest::find($request->id);
        if($trainingRequest){
            $trainingRequest->status = 3; // Update status to "Approved"
            $trainingRequest->tu_head_approver = $_SESSION['rapidx_user_id'];
            $trainingRequest->tu_head_approve_date = date('Y-m-d H:i:s');
            $trainingRequest->save();
            return response()->json(['result' => 1, 'message' => 'Training request approved successfully']);
        } else {
            return response()->json(['result' => 0, 'message' => 'Training request not found']);
        }
    }

    public function index(){
        $globalUser = session('global_user');

        $tuHeadApprover = User::where('rapidx_emp_id', $globalUser->rapidx_emp_id)
            ->whereHas('user_access_module', function ($query) {
                $query->whereRaw("FIND_IN_SET(?, user_modules_id)", [6]);
            })
            ->exists();

        $defaultFilter = $tuHeadApprover ? 2 : 3;

        return view('training_request', [
            'defaultFilter' => $defaultFilter,
            'isTuHeadApprover' => $tuHeadApprover,
        ]);
    }


}
