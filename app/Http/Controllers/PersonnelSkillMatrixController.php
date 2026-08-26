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
use App\Model\Qc\QcSlipEmployee;
use App\Model\DropdownMasterDetail;
use App\Model\DropdownMaster;
use Barryvdh\DomPDF\Facade;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SkillMapExport;
use App\Exports\SkillMapExportPerProdLine;
use Maatwebsite\Excel\Facades\Excel;

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

    public function getProductLine()
    {
        return QcSlip::with('product_line_details')
            ->where('status', 'OK')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => optional($item->product_line_details)->id,
                    'product_line' => optional($item->product_line_details)->dropdown_masters_details,
                ];
            })
            ->filter(function ($item) {
                return !empty($item['product_line']);
            })
            ->unique('id')
            ->values();
    }

    public function getEmployeePosition(){
         return QcSlip::where('status', 'OK')
        ->select('position_category')
        ->distinct()
        ->get();

    }

    public function getEmployees(Request $request)
    {
        $productLines = (array) $request->product_line;
        $position = $request->position;

        $employees = QcSlipEmployee::with([
            'system_one_hris_emp_info',
            'system_one_subcon_emp_info'
        ])
        ->whereHas('qcSlip', function ($q) use ($productLines, $position) {

            $q->where('status', 'OK')
            ->whereIn('product_line', $productLines)
            ->where('position_category', $position);

        })
        ->get()
        ->map(function ($employee) {

            $emp = $employee->system_one_hris_emp_info
                ?? $employee->system_one_subcon_emp_info;

            return [
                'EmpNo'     => optional($emp)->EmpNo,
                'EmpName'   => optional($emp)->EmpName,
                'dateHired' => optional($emp)->DateHired,
            ];
        })
        ->unique('EmpNo')
        ->values();

        return response()->json($employees);
    }

    // public function exportSkillMapPdf(Request $request)
    // {
    //     $productLineId = $request->product_line;
    //     // dd($request->position);
    //     $position = strtoupper($request->position);

    //     $productLine = DropdownMasterDetail::where('id', $productLineId)
    //     ->value('dropdown_masters_details');

    //     $productStation = DropdownMasterDetail::whereHas('dropdown_master', function ($q) use ($position) {
    //         $q->where('dropdown_masters', 'Stations')
    //         ->where('category', $position);
    //     })
    //     ->where('dropdown_masters_details', '!=', 'N/A')
    //     ->select('id', 'dropdown_masters_details')
    //     ->get();

    //     // return ($productStation);

    //     $employees = json_decode($request->employees, true);

    //     $employees = collect(json_decode($request->employees, true))
    //     ->map(function ($employee) use ($productLineId) {

    //         $details = explode('|', $employee['empNo']);
    //         $empNo = $details[0] ?? '';

    //         $records = QcSlipEmployee::where('employee_no', $empNo)
    //             ->whereHas('qcSlip', function ($q) use ($productLineId) {
    //                 $q->where('product_line', $productLineId)
    //                 ->where('status', 'OK');
    //             })
    //             ->get();

    //         // Count records per station
    //         $assemblyCount = $records->where('station_to', 3)->count(); // Assembly Process
    //         $visualCount   = $records->where('station_to', 2)->count(); // Visual Inspection
    //         $partPrepCount     = $records->where('station_to', 1)->count(); // Parts Prep
    //         $machineCount  = $records->where('station_to', 4)->count(); // Machine Operation


    //         return [
    //             'empNo'      => $empNo,
    //             'empName'    => $details[1] ?? '',
    //             'dateHired'  => $details[2] ?? '',

    //             'stations' => [
    //                 1 => $this->levelImage($partPrepCount),
    //                 2 => $this->levelImage($visualCount),
    //                 3 => $this->levelImage($assemblyCount),
    //                 4 => $this->levelImage($machineCount),
    //             ],


    //         ];
    //     })
    //     ->toArray();

    //     $pdf = Pdf::loadView('pdf.skill_map', [
    //         'productLine' => $productLine,
    //         'employees'   => $employees,
    //         'productStation' => $productStation,
    //     ])->setPaper('a4', 'landscape');

    //     return $pdf->stream('skill_matrix.pdf');
    // }

    public function exportSkillMapExcel(Request $request)
    {
        $data = $request->all();
        // return $data;

        $productLineId = $request->product_line;
        $position = strtoupper($request->position);

        $productLine = DropdownMasterDetail::where('id', $productLineId)
            ->value('dropdown_masters_details');

        $productStation = DropdownMasterDetail::whereHas('dropdown_master', function ($q) use ($position) {
                $q->where('dropdown_masters', 'Stations')
                ->where('category', $position);
            })
            ->where('dropdown_masters_details', '!=', 'N/A')
            ->select('id', 'dropdown_masters_details')
            ->get();

        $employees = collect(json_decode($request->employees, true))
            ->map(function ($employee) use ($productLineId) {

                $empNo = $employee['empNo'] ?? '';

                $records = QcSlipEmployee::where('employee_no', $empNo)
                    ->whereHas('qcSlip', function ($q) use ($productLineId) {
                        $q->where('product_line', $productLineId)
                        ->where('status', 'OK');
                    })
                    ->get();

                $assemblyCount  = $records->where('station_to', 3)->count();
                $visualCount    = $records->where('station_to', 2)->count();
                $partPrepCount  = $records->where('station_to', 1)->count();
                $machineCount   = $records->where('station_to', 4)->count();

                return [
                    'empNo'     => $empNo,
                    'empName'   => $employee['empName'] ?? '',
                    'dateHired' => $employee['dateHired'] ?? '',

                    'stations' => [
                        1 => $partPrepCount,
                        2 => $visualCount,
                        3 => $assemblyCount,
                        4 => $machineCount,
                    ],
                ];
            })
            ->values()
            ->toArray();

        return Excel::download(
            new SkillMapExport(
                $productLine,
                $employees,
                $productStation
            ),
            'skill_matrix.xlsx'
        );
    }

     public function exportSkillMapExcelPerProductLine(Request $request)
    {
        $position = strtoupper($request->position);
        $productLineIds = $request->product_line;

        if (is_string($productLineIds)) {
            $productLineIds = explode(',', $productLineIds);
        }

        $productLineIds = array_filter($productLineIds);

        $productLines = DropdownMasterDetail::whereIn('id', $productLineIds)
            ->pluck('dropdown_masters_details')
            ->toArray();

        $productStation = DropdownMasterDetail::whereHas('dropdown_master', function ($q) use ($position) {
            $q->where('dropdown_masters', 'Stations')
            ->where('category', $position);
        })
        ->where('dropdown_masters_details', '!=', 'N/A')
        ->select('id', 'dropdown_masters_details')
        ->get();

        // return $productStation;

        // $employees = collect(json_decode($request->employees, true))->map(function ($employee) use ($productLineIds) {

        //         $empNo = $employee['empNo'] ?? '';

        //         $productLineSkills = [];

        //         foreach ($productLineIds as $productLineId) {

        //             $records = QcSlipEmployee::where('employee_no', $empNo)
        //                 ->whereHas('qcSlip', function ($q) use ($productLineId) {
        //                     $q->where('product_line', $productLineId)
        //                     ->where('status', 'OK');
        //                 })
        //                 ->get();

        //             // Count all 4 stations
        //             $partPrepCount = $records->where('station_to', 1)->count();
        //             $visualCount   = $records->where('station_to', 2)->count();
        //             $assemblyCount = $records->where('station_to', 3)->count();
        //             $machineCount  = $records->where('station_to', 4)->count();

        //             // Overall total for this employee + product line
        //             $total = $partPrepCount
        //                 + $visualCount
        //                 + $assemblyCount
        //                 + $machineCount;

        //             // Store using product line ID
        //             $productLineTotals[$productLineId] = $total;
        //         }

        //         return [
        //             'empNo'     => $empNo,
        //             'empName'   => $employee['empName'] ?? '',
        //             'dateHired' => $employee['dateHired'] ?? '',

        //             'productLines' => $productLineTotals,

        //         ];
        //     })
        //     ->values()
        //     ->toArray();

        $employees = collect(json_decode($request->employees, true))->map(function ($employee) use ($productLineIds) {

            $empNo = $employee['empNo'] ?? '';

            $productLineTotals = [];

            foreach ($productLineIds as $productLineIndex => $productLineId) {

                $records = QcSlipEmployee::where('employee_no', $empNo)
                    ->whereHas('qcSlip', function ($q) use ($productLineId) {
                        $q->where('product_line', $productLineId)
                        ->where('status', 'OK');
                    })
                    ->get();

                // Count all 4 stations for this employee + product line
                $partPrepCount = $records->where('station_to', 1)->count();
                $visualCount   = $records->where('station_to', 2)->count();
                $assemblyCount = $records->where('station_to', 3)->count();
                $machineCount  = $records->where('station_to', 4)->count();

                // Overall total for this product line
                $totalCount = $partPrepCount
                    + $visualCount
                    + $assemblyCount
                    + $machineCount;


                if ($totalCount == 0) {
                    $level = 0;
                } elseif ($totalCount <= 8) {
                    $level = 1;
                } elseif ($totalCount <= 16) {
                    $level = 2;
                } elseif ($totalCount <= 24) {
                    $level = 3;
                } elseif ($totalCount <= 35) {
                    $level = 4;
                } else {
                    $level = 4; // 35+ remains Level 4
                }

                $productLineTotals[$productLineIndex] = $level;
            }

            return [
                'empNo'     => $empNo,
                'empName'   => $employee['empName'] ?? '',
                'dateHired' => $employee['dateHired'] ?? '',

                // Example:
                // [
                //     0 => 14, // BGA-FP
                //     1 => 8,  // Probe Pin
                // ]
                'productLines' => $productLineTotals,
            ];
        })
        ->values()
        ->toArray();
            // return $records;

        // return $employees;

        return Excel::download(
            new SkillMapExportPerProdLine(
                $productLines,
                $employees
            ),
            'skill_map_per_product_line.xlsx'
        );
    }

    private function levelImage($count)
    {
        if ($count <= 0) return 'level0.png';
        if ($count == 1) return 'level1.png';
        if ($count == 2) return 'level2.png';
        if ($count == 3) return 'level3.png';

        return 'level4.png';
    }
}
