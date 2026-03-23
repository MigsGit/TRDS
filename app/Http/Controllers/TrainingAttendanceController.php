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
            $result .= '<center>';
            $result .= '</center>';
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
