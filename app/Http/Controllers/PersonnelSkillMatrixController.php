<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;
use App\Model\SystemoneEmployeeTraining;
use Yajra\DataTables\Facades\DataTables;

class PersonnelSkillMatrixController extends Controller
{
    public function getDirectEmployees(Request $request)
    {
        // $users = SystemOneHrisEmpInfo::all();
        $users = SystemOneHrisEmpInfo::where('EmpStatus', 'Active')
        ->where('Section', '!=', 'BOD')
        ->where('Position', 'NOT LIKE', '%Manager%')
        ->orderBy('DateHired', 'desc')
        ->get();


        return DataTables()->of($users)
        ->addColumn('action', function($users) use ($request){
            $result = '';
            $result = '<center>';
            $result .= '<button type="button" class="btn btn-sm btn-primary btnUpdateDirectEmpInfo" data-empno="' . $users->EmpNo .'">
                            <i class="fas fa-edit fa-md me-2"></i>
                        </button>';
            $result .= '<button type="button" class="btn btn-sm btn-info btnViewDirectEmpInfo ml-1" data-empno="' . $users->EmpNo .'">
                            <i class="fas fa-eye fa-md me-2"></i>
                        </button>';
            $result .= '</center>';

            return $result;
        })

        ->addColumn('EmpName', function($users){
           $result = '';

            $middle = !empty($users->MiddleName)
                ? ' ' . strtoupper(substr($users->MiddleName, 0, 1)) . '.'
                : '';

            $result = $users->FirstName . $middle . ' ' . $users->LastName;

            return $result;

        })

        ->rawColumns(['action', 'EmpName'])
        ->make(true);
    }

    public function getSubconEmployees(Request $request)
    {
        // $users = SystemOneSubconEmpInfo::all();
        $users = SystemOneSubconEmpInfo::where('EmpStatus', 'Active')
        ->orderBy('DateHired', 'desc')
        ->get();


        return DataTables()->of($users)
        ->addColumn('action', function($users) use ($request){
            $result = '';
            $result = '<center>';
            $result .= '<button type="button" class="btn btn-sm btn-primary btnUpdateSubconEmpInfo" data-empno="' . $users->EmpNo .'">
                            <i class="fas fa-edit fa-md me-2"></i>
                        </button>';
            $result .= '<button type="button" class="btn btn-sm btn-info btnViewEmployeeSkillMatrix ml-1" data-empno="' . $users->EmpNo .'">
                            <i class="fas fa-eye fa-md me-2"></i>
                        </button>';
            $result .= '</center>';

            return $result;
        })

        ->addColumn('EmpName', function($users){
            $result = '';

            $middle = !empty($users->MiddleName)
                ? ' ' . strtoupper(substr($users->MiddleName, 0, 1)) . '.'
                : '';

            $result = $users->FirstName . $middle . ' ' . $users->LastName;


            return $result;

        })

        ->rawColumns(['action', 'EmpName'])
        ->make(true);
    }

    public function getDirectEmployeeInfo(Request $request){
        $employeeDetails = SystemOneHrisEmpInfo::where('EmpNo', $request->id)
        ->first();

        return response()->json($employeeDetails);

    }

    public function viewDirectEmployeeInfo(Request $request){
        $employeeDetails = SystemOneHrisEmpInfo::where('EmpNo', $request->id)
        ->first();

        return response()->json($employeeDetails);

    }

    public function getSubconEmployeeInfo(Request $request){
        $employeeDetails = SystemOneSubconEmpInfo::where('EmpNo', $request->id)
        ->first();

        return response()->json($employeeDetails);

    }

        public function getEmployeeTrainings(Request $request){
        // Query for DataTables
        $query = SystemoneEmployeeTraining::where('EmpNo', $request->id)
            ->select([
                'EmpNo',
                'PeriodFrom',
                'PeriodTo',
                'Title',
                'Objective',
                'Trainor',
                'Result',
                'Venue',
                'Mechanics',
                'TypeTraining',
                'Remarks'
            ]);

        // Single query to get all summary counts
        $summary = SystemoneEmployeeTraining::selectRaw("
                COUNT(*) AS total,
                SUM(CASE WHEN Result = 'Passed' THEN 1 ELSE 0 END) AS passed,
                SUM(CASE WHEN Result = 'Complied' THEN 1 ELSE 0 END) AS complied,
                SUM(CASE WHEN Result = 'Failed' THEN 1 ELSE 0 END) AS failed,
                SUM(CASE WHEN Result = 'Actual Hands on' THEN 1 ELSE 0 END) AS handsOn
            ")
            ->where('EmpNo', $request->id)
            ->first();

        return DataTables::eloquent($query)
            ->addColumn('trainingDate', function ($row) {
                return $row->PeriodFrom . ' - ' . $row->PeriodTo;
            })
            ->with([
                'passed'   => (int) $summary->passed,
                'complied' => (int) $summary->complied,
                'failed'   => (int) $summary->failed,
                'handsOn'  => (int) $summary->handsOn,
                'total'    => (int) $summary->total,
            ])
            ->rawColumns(['trainingDate'])
            ->toJson();
    }
}
