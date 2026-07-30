<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneSubconEmpInfo;
use App\Model\SystemoneEmployeeTraining;
use App\Model\Hr\HrMemo;
use App\Model\Hr\HrMemoTraineeDetails;
use App\Model\Hr\HrMemoTraineeCategoryDetails;
use Yajra\DataTables\Facades\DataTables;
use App\Model\QcSlip;
use Barryvdh\DomPDF\Facade;
use Barryvdh\DomPDF\Facade\Pdf;

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
            $result .= '<button type="button" class="btn btn-sm btn-info btnViewSubconEmpInfo ml-1" data-empno="' . $users->EmpNo .'">
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

    public function viewSubconEmployeeInfo(Request $request){
        $employeeDetails = SystemOneSubconEmpInfo::where('EmpNo', $request->id)
        ->first();

        return response()->json($employeeDetails);
    }

    public function getEmployeeTrainings(Request $request)
    {

        $query = HrMemoTraineeCategoryDetails::with([
            'exam_info_test',
            'employee_info_tist',
            'rapidx_system_one_hris_emp_info'
        ])
        ->whereHas('employee_info_tist', function ($q) use ($request) {
            $q->where('employee_no', $request->id);
        });

        $query2 = QcSlip::with([
            'qc_slip_employees' => function ($q) use ($request) {
                $q->where('employee_no', $request->id);
            },
            'qc_slip_employees.get_station_to',
            'productLine',
            'qc_reason_certification.dropdown_reason'
        ])
        ->whereHas('qc_slip_employees', function ($q) use ($request) {
            $q->where('employee_no', $request->id);
        })
        ->where('status', 'OK')
        ->get();


        $data = $query->get()->merge($query2);
        // return $data;


        $passed   = (clone $query)->where('result', 1)->count();
        $complied = (clone $query)->where('result', 2)->count();
        $failed   = (clone $query)->where('result', 3)->count();
        $total    = (clone $query)->count();

        return DataTables::collection($data)

        ->addColumn('trainingDate', function ($row) {

            if ($row instanceof \App\Model\QcSlip) {
                return $row->created_at
                    ? $row->created_at->format('Y-m-d')
                    : '';
            }

            if (!$row->date_start && !$row->date_end) {
                return '';
            }

            return $row->date_start.' - '.$row->date_end;
        })
        ->addColumn('title', function ($row) {

            if ($row instanceof QcSlip) {
                // return optional($row->productLine)->dropdown_masters_details;
                return 'Qualification and Certification';
            }

            return optional($row->exam_info_test)->examination_name;
        })
       ->addColumn('seriesName', function ($row) {
            // if ($row instanceof QcSlip) {
            //     return $row->series_name ?? '';
            // }
            if ($row instanceof QcSlip) {
                return optional($row->productLine)->dropdown_masters_details;
            }

            return 'N/A';
        })

        ->addColumn('station', function ($row) {

            if ($row instanceof QcSlip) {
                return optional(
                    optional($row->qc_slip_employees->first())->get_station_to
                )->dropdown_masters_details ?? '';
            }

            return 'N/A';
        })
         ->addColumn('detailedStation', function ($row) {
                // return '';
            if ($row instanceof QcSlip) {
                $employee = $row->qc_slip_employees->first();

                return $employee->remarks ?? '';
            }
             return 'N/A';
        })
        ->addColumn('objective', function ($row) {
            if(!$row->objective){
                return '';
            }
            return $row->objective;
        })
        ->addColumn('trainor', function ($row) {
            $trainor = $row->rapidx_system_one_hris_emp_info;

            if (!$trainor) {
                return '';
            }

            return trim($trainor->FirstName . ' ' . $trainor->LastName);
        })
        ->addColumn('result', function ($row) {

            if ($row instanceof QcSlip) {

                $employee = $row->qc_slip_employees->first();

                if (!$employee) {
                    return '<span class="badge badge-secondary">N/A</span>';
                }

                $result = $employee->second_take_ins_assessment_result
                    ?: $employee->first_take_ins_assessment_result;

                switch ($result) {
                    case 'PASSED':
                        return '<span class="badge badge-success">Passed</span>';

                    case 'FAILED':
                        return '<span class="badge badge-danger">Failed</span>';

                    default:
                        return '<span class="badge badge-secondary">N/A</span>';
                }
            }
            
            switch ((int) $row->result) {
                case 1:
                    return '<span class="badge badge-success">Passed</span>';

                case 2:
                    return '<span class="badge badge-primary">Complied</span>';

                case 3:
                    return '<span class="badge badge-danger">Failed</span>';

                default:
                    return '<span class="badge badge-secondary">N/A</span>';
            }
        })
        ->addColumn('trainingVenue', function ($row) {
            if(!$row->training_venue){
                return '';
            }
            return $row->training_venue;
        })
        ->addColumn('typeOfTraining', function ($row) {
            if ($row instanceof QcSlip) {
                return optional(
                    optional($row->qc_reason_certification)->dropdown_reason
                )->dropdown_masters_details ?? '';
            }

            return $row->type_of_training;
        })
        ->with([
            'passed'   => $passed,
            'complied' => $complied,
            'failed'   => $failed,
            'total'    => $total,
        ])
        ->rawColumns(['result'])
        ->make(true);
    }

     public function exportSkillMapPdf()
    {
        $pdf = Pdf::loadView('pdf.skill_map')
        ->setPaper('a4', 'landscape');

        return $pdf->stream('skill_matrix.pdf');
    }
}
