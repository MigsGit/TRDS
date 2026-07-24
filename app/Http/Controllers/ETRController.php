<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneHrisTrainee;
use App\Model\SystemOneSubconEmpInfo;

class ETRController extends Controller
{
    public function viewEmployeeTrainingRecord(Request $request){
        $etr_records = 
            SystemOneHrisTrainee::with([
                'employee_training_record_info' 
            ])
            ->where('fkEmployee', $request->getEmployeeTrainingRecordId)
            ->where('logdel', 0)
            ->whereHas('employee_training_record_info', function ($query) {
                $query->where('logdel', '!=', '1');
            })
            ->get();

        // return $etr_records;
        return DataTables::of($etr_records)
        ->addColumn('action', function($etr_record){
            $result =   '<center>';
            $result .= '<button type="button" class="btn btn-warning btn-sm text-center actionChangeETRStatus" etr-id="' . $etr_record->id . '" status="0" data-toggle="modal" data-target="#modalChangeETRStatus" title="Activate ETR"><i class="fas fa-redo"></i></button>';
            $result .= '</center>';
            return $result;
        })

        ->rawColumns(['action'])
        ->make(true);
    }

    public function getSystemoneEmployeeTrainingDetails(Request $request){
        $search = trim($request->search);

        $hrisEmployees = SystemOneHrisEmpInfo::query()
            ->where('EmpStatus', '!=', 'Resigned')
            ->where(function ($query) use ($search) {
                $query->where('EmpNo', 'LIKE', "%{$search}%")
                    ->orWhere('EmpName', 'LIKE', "%{$search}%");
            });

        $subconEmployees = SystemOneSubconEmpInfo::query()
            ->where(function ($query) use ($search) {
                $query->where('EmpNo', 'LIKE', "%{$search}%")
                    ->orWhere('EmpName', 'LIKE', "%{$search}%");
            });

        $employees = $hrisEmployees
            ->union($subconEmployees)
            ->limit(50)
            ->get();

        return response()->json($employees);
    }

}
