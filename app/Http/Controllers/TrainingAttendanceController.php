<?php

namespace App\Http\Controllers;
use App\Model\TrainingRequest;
use App\Model\TrainingRequestDetails;
use Illuminate\Http\Request;
use DataTables;


class TrainingAttendanceController extends Controller
{
    public function view_training_attendance(Request $request){
        try {
           $trainingRequests = TrainingRequest::
            where('logdel', 0)
            ->get();
        return DataTables::of($trainingRequests)
        ->addColumn('action', function($row){
            $result = '';
            $result .= '<center><div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">';
                $result .= '<button class="dropdown-item aViewTrainingAttendance" type="button" user-id="' . $row->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalViewTrainingAttendanceRequest" data-keyboard="false">View</button>';
                // $result .= '<button class="dropdown-item aEditModuleAccess" type="button"  rapidx-emp-no= "'.$row->rapidx_emp_no .'"  user-id="' . $row->id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#modalAddUserModuleAccess" data-keyboard="false">View</button>';
                $result .= '</div>
                        </div></center>';
            return $result;
        })
        ->addColumn('status', function($row){
            $result = '';
            return $result;
        })
        ->rawColumns(['action','status'])
        ->make(true);

        } catch (Exception $e) {
            throw $e;
        }
    }
    public function view_training_attendance_request(Request $request){
        try {
        /*
            emp_no
            id
            training_request_id
         */
        $trainingRequests = TrainingRequestDetails::with('emp_no_system_one_hris_emp_info',
        'emp_no_system_one_subcon_emp_info')
        // where('training_request_id',$request->trainingRequestsId)
        ->get();
        return DataTables::of($trainingRequests)
        ->addColumn('action', function($row){
            return $result = '';
            $result .= '<center><div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Action">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">';
                $result .= '<button class="dropdown-item aViewTrainingAttendanceRequest" type="button" training-request-details-id="' . $row->id . '"  training-request-id="' . $row->training_request_id . '" style="padding: 1px 1px; text-align: center;" data-toggle="modal" data-target="#" data-keyboard="false">View</button>';
                $result .= '</div>
                        </div></center>';
            return $result;
        })
        ->addColumn('status', function($row){
            $result = '';
            return $result;
        })
        ->rawColumns(['action','status'])
        ->make(true);

        } catch (Exception $e) {
            throw $e;
        }
    }
}
