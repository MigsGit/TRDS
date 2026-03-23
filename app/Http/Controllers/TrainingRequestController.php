<?php

namespace App\Http\Controllers;

use App\Model\SystemOneHrisDepartment;
use App\Model\SystemOneHrisDivision;
use App\Model\SystemOneHrisSection;
use App\Model\TrainingRequest;
use App\Model\TrainingRequestDetails;
use App\Model\Hr\HrMemo;
use App\Model\Hr\HrMemoTraineeCategoryDetails;
use App\Model\User;
use App\RapidXUser;
use Illuminate\Http\Request;

class TrainingRequestController extends Controller
{

    public function getTrainingRequests(Request $request){
       
        $trainingRequests = TrainingRequest::with(['conformance_user'])
        ->where('logdel', 0)
        ->get();

        if($request->filter != 4){
            $trainingRequests = $trainingRequests->where('status', $request->filter);
        }

        return DataTables()->of($trainingRequests)
        ->addColumn('action', function($trainingRequest){
            $result = '';
            $result .= '<center>';
            $result .= '<button class="btn btn-sm btn-primary btnViewTrainingRequest" data-id="' . $trainingRequest->id . '"><i class="fas fa-eye"></i></button>';
            $result .= '</center>';
            return $result;
        })
        ->addColumn('status', function($trainingRequest){
            $result = '';
            if($trainingRequest->status == 0){
                $result = '<span class="badge badge-warning">For Conformance</span>';
            }

            return $result;
        })

        ->addColumn('conformance_user', function($trainingRequest){

            $result = '<div class="text-center">';
            $conformanceUser = $trainingRequest->conformance_user;

            if ($trainingRequest->status == 0) {
                $name = $conformanceUser ? $conformanceUser->name : '';

                $date = date('M d, Y', strtotime($trainingRequest->created_at));
                $time = date('h:i:s A', strtotime($trainingRequest->created_at));

                $result .= "<strong>$name</strong>";
                $result .= '<br><span class="badge badge-secondary">Pending</span>';
                $result .= "<br><small class='text-muted'>$date $time</small>";
            }

            $result .= '</div>';

            return $result;
        })

        ->addColumn('receiving', function($trainingRequest){

            $result = '<div class="text-center">';

            $date = date('M d, Y', strtotime($trainingRequest->created_at));
            $time = date('h:i:s A', strtotime($trainingRequest->created_at));

            if ($trainingRequest->status == 1) {
                $result .= '<span class="badge badge-success">Received</span>';
                $result .= "<br><small class='text-muted'>$date $time</small>";
            } 
            else if ($trainingRequest->status == 0) {
                $result .= '<span class="badge badge-secondary">Pending</span>';
                $result .= "<br><small class='text-muted'>$date $time</small>";
            }

            $result .= '</div>';

            return $result;
        })

        ->addColumn('tu_head_approval', function($trainingRequest){

            $result = '<div class="text-center">';

            $date = date('M d, Y', strtotime($trainingRequest->created_at));
            $time = date('h:i:s A', strtotime($trainingRequest->created_at));

            if ($trainingRequest->status == 1) {
                $result .= '<span class="badge badge-success">Approved</span>';
                $result .= "<br><small class='text-muted'>$date $time</small>";
            } 
            else if ($trainingRequest->status == 0) {
                $result .= '<span class="badge badge-secondary">Pending</span>';
                $result .= "<br><small class='text-muted'>$date $time</small>";
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
        ->rawColumns(['action', 'status', 'conformance_user','receiving', 'tu_head_approval','date_filed'])
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
        ->where('isActive', 0)
        ->get();
        return response()->json($systemOneSection);
    }

    public function getHRISSectionByDepartment(Request $request){
        $systemOneDepartment = SystemOneHrisDepartment::where('isActive', 1)
        ->get();
        return $systemOneDepartment;
        return response()->json($systemOneDepartment);
    }

    public function addTrainingRequest(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        $data = $request->all();

        $ctrlNumber = TrainingRequest::max('id') + 1;
        $ctrlNumber = str_pad($ctrlNumber, 4, '0', STR_PAD_LEFT);
        $date = date('ym');
    

        $ctrlNumber = 'TR-' . $date . '-' . $ctrlNumber;

        // return $ctrlNumber;

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
        if($trainingRequest){
            return response()->json(['result' => 1, 'message' => 'Training request added successfully']);
        } else {
            return response()->json(['result' => 0, 'message' => 'Failed to add training request']);
        }

    }

    public function getUserConformance(Request $request){
        // return 'asd';
        $trainingRequest = User::whereHas('user_access_module', function ($query) {
            $query->where('user_modules_id', 6); // Assuming '1' is the ID for the specific module
        })
        ->with(['users'])
        ->get();
        
        return response()->json($trainingRequest);
    }

    public function getRequestor(Request $request){
        session_start();
        $requestor_id = $_SESSION['rapidx_user_id'];
        $requestorName = RapidXUser::where('id', $requestor_id)->first();
        
        $requestorName = $requestorName ? $requestorName->name : 'Unknown User';


        // return $requestorName;
        
        return response()->json(['result' => 1, 'requestor_name' => $requestorName]);
    }

    public function getTrainingRequestDetails(Request $request){
        $trainingRequestDetails = TrainingRequest::where('id', $request->id)
        ->first();

        return response()->json($trainingRequestDetails);
    }

    public function getMemoDocs(Request $request){
        session_start();
        // $requestor_id = $_SESSION['rapidx_user_id'];
        $memoDocs = HrMemo::where('deleted_at', NULL)
        ->get();

        return response()->json($memoDocs);
    }

    public function getMemoDocsDetails(Request $request){
        $memoDocDetails = HrMemoTraineeCategoryDetails::where('hr_memo_id', $request->id)
        ->first();

        return response()->json($memoDocDetails);
    }

    public function getRequestedEmployeeDetails(Request $request){
        $trainingRequestDetails = TrainingRequestDetails::all();

         return DataTables()->of($trainingRequestDetails)

        // ->rawColumns(['action', 'status', 'conformance_user','receiving', 'tu_head_approval','date_filed'])
        ->make(true);
    }

    public function getMemoDocEmployeeDetails(Request $request){
        $trainingRequestDetails = TrainingRequestDetails::all();

         return DataTables()->of($trainingRequestDetails)

        // ->rawColumns(['action', 'status', 'conformance_user','receiving', 'tu_head_approval','date_filed'])
        ->make(true);
    }
    
}
