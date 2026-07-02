<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;

class PersonnelSkillMatrixController extends Controller
{
    public function getDirectEmployees(Request $request)
    {
        // $users = SystemOneHrisEmpInfo::all();
        $users = SystemOneHrisEmpInfo::where('EmpStatus', 'Active')->get();


        return DataTables()->of($users)
        ->addColumn('action', function($users) use ($request){
           $result = '';
           
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
        $users = SystemOneSubconEmpInfo::where('EmpStatus', 'Active')->get();


        return DataTables()->of($users)
        ->addColumn('action', function($users) use ($request){
           $result = '';
           
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
}
