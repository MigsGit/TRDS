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

    public function getEmployeesForSkillMap(Request $request)
    {
        $productLines = (array) $request->product_line;
        $position = $request->position;
                    // return 'qwe';
        // return $position;
       if ($position === 'Inspector') {

            $employees = QcSlipEmployee::with([
                'system_one_hris_emp_info',
                'system_one_subcon_emp_info',
                'system_one_hris_subcon'
            ])
            ->whereHas('qcSlip', function ($q) use ($productLines, $position) {

                $q->where('status', 'OK')
                ->where('position_category', $position)
                ->where(function ($q) use ($productLines) {

                    foreach ($productLines as $productLine) {

                        $q->orWhereRaw(
                            "FIND_IN_SET(?, REPLACE(REPLACE(product_line, ' ', ''), '|', ',')) > 0",
                            [$productLine]
                        );

                    }

                });

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
        else if($position === 'Operator') {
            $employees = QcSlipEmployee::with([
                'system_one_hris_emp_info',
                'system_one_subcon_emp_info',
                // 'system_one_hris_subcon'
            ])
            ->whereHas('qcSlip', function ($q) use ($productLines, $position) {

                $q->where('status', 'OK')
                ->where('product_line', $productLines)
                ->where(function ($subQuery) use ($position) {
                    $subQuery->where('position_category', 'LIKE', "%{$position}%")
                            ->orWhere('position_category', 'LIKE', '%Partsprep%');
                });

            })
            ->get()
            ->map(function ($employee) {

                $emp = $employee->system_one_hris_emp_info
                    ?? $employee->system_one_subcon_emp_info ?? $employee->system_one_hris_subcon;


                return [
                    'EmpNo'     => optional($emp)->EmpNo,
                    'EmpName'   => optional($emp)->EmpName,
                    'dateHired' => optional($emp)->DateHired,
                    'test' => $emp,
                ];
            })
            ->unique('EmpNo')
            ->values();

            // return $employees;

            return response()->json($employees);
        }else if($position === 'MachineOperator') {
            // return 'asdasd';

            // Custom logic for Machine Operator
             $employees = QcSlipEmployee::with([
                'system_one_hris_emp_info',
                'system_one_subcon_emp_info',
                // 'system_one_hris_subcon'
            ])
            ->whereHas('qcSlip', function ($q) use ($productLines, $position) {

                $q->where('status', 'OK')
                ->whereIn('product_line', $productLines)
                ->where('position_category', $position);

            })
            ->get()
            // return $employees;
            ->map(function ($employee) {

                $emp = $employee->system_one_hris_emp_info
                    ?? $employee->system_one_subcon_emp_info ?? $employee->system_one_hris_subcon;


                return [
                    'EmpNo'     => optional($emp)->EmpNo,
                    'EmpName'   => optional($emp)->EmpName,
                    'dateHired' => optional($emp)->DateHired,
                    'test' => $emp,
                ];
            })
            ->unique('EmpNo')
            ->values();
            // return $productLines;
            return response()->json($employees);
        }else if($position === 'VisualOperator') {
            // return 'asdasd';

            // Custom logic for Machine Operator
             $employees = QcSlipEmployee::with([
                'system_one_hris_emp_info',
                'system_one_subcon_emp_info',
                // 'system_one_hris_subcon'
            ])
            ->whereHas('qcSlip', function ($q) use ($productLines, $position) {

                $q->where('status', 'OK')
                ->whereIn('product_line', $productLines)
                ->where('position_category', $position);

            })
            ->get()
            // return $employees;
            ->map(function ($employee) {

                $emp = $employee->system_one_hris_emp_info
                    ?? $employee->system_one_subcon_emp_info ?? $employee->system_one_hris_subcon;


                return [
                    'EmpNo'     => optional($emp)->EmpNo,
                    'EmpName'   => optional($emp)->EmpName,
                    'dateHired' => optional($emp)->DateHired,
                    'test' => $emp,
                ];
            })
            ->unique('EmpNo')
            ->values();
            // return $productLines;
            return response()->json($employees);
        }else if($position === 'PartsPrep') {
            // return 'asdasd';

            // Custom logic for Machine Operator
             $employees = QcSlipEmployee::with([
                'system_one_hris_emp_info',
                'system_one_subcon_emp_info',
                // 'system_one_hris_subcon'
            ])
            ->whereHas('qcSlip', function ($q) use ($productLines, $position) {

                $q->where('status', 'OK')
                ->whereIn('product_line', $productLines)
                ->where('position_category', $position);

            })
            ->get()
            // return $employees;
            ->map(function ($employee) {

                $emp = $employee->system_one_hris_emp_info
                    ?? $employee->system_one_subcon_emp_info ?? $employee->system_one_hris_subcon;


                return [
                    'EmpNo'     => optional($emp)->EmpNo,
                    'EmpName'   => optional($emp)->EmpName,
                    'dateHired' => optional($emp)->DateHired,
                    'test' => $emp,
                ];
            })
            ->unique('EmpNo')
            ->values();
            // return $productLines;
            return response()->json($employees);
        }
        // else if($position === 'Technician') {
        //     // return 'asdasd';

        //     // Custom logic for Machine Operator
        //      $employees = QcSlipEmployee::with([
        //         'system_one_hris_emp_info',
        //         'system_one_subcon_emp_info',
        //         // 'system_one_hris_subcon'
        //     ])
        //     ->whereHas('qcSlip', function ($q) use ($productLines, $position) {

        //         $q->where('status', 'OK')
        //         ->whereIn('product_line', $productLines)
        //         ->where('position_category', $position);

        //     })
        //     ->get()
        //     // return $employees;
        //     ->map(function ($employee) {

        //         $emp = $employee->system_one_hris_emp_info
        //             ?? $employee->system_one_subcon_emp_info ?? $employee->system_one_hris_subcon;


        //         return [
        //             'EmpNo'     => optional($emp)->EmpNo,
        //             'EmpName'   => optional($emp)->EmpName,
        //             'dateHired' => optional($emp)->DateHired,
        //             'test' => $emp,
        //         ];
        //     })
        //     ->unique('EmpNo')
        //     ->values();
        //     // return $productLines;
        //     return response()->json($employees);
        // }
        // return $employees;
    }

    public function getEmployeesForSkillMatrix(Request $request)
    {
        $productLines = (array) $request->product_line;
        // return $productLines;

        if(empty($productLines)) {
            $employees = QcSlipEmployee::with([
            'system_one_hris_emp_info',
            'system_one_subcon_emp_info',
            'system_one_hris_subcon'
            ])
            ->get()
            ->map(function ($employee) {

                $emp = $employee->system_one_hris_emp_info
                    ?? $employee->system_one_subcon_emp_info;

                return [
                    'EmpNo'     => optional($emp)->EmpNo,
                    'EmpName'   => optional($emp)->EmpName,
                    'dateHired' => optional($emp)->DateHired,
                    'position' => optional($emp)->Position,
                ];
            })
            ->unique('EmpNo')
            ->values();

            return response()->json($employees);
            // return 'empty product lines';
        }else{
            // return 'with product lines';
            $employees = QcSlipEmployee::with([
                'system_one_hris_emp_info',
                'system_one_subcon_emp_info',
                'system_one_hris_subcon'
            ])
            ->whereHas('qcSlip', function ($q) use ($productLines) {
                $q->where('status', 'OK')
                ->whereIn('product_line', $productLines);
            })
            ->get()
            ->map(function ($employee) {

                $emp = $employee->system_one_hris_emp_info
                    ?? $employee->system_one_subcon_emp_info ?? $employee->system_one_hris_subcon;

                return [
                    'EmpNo'     => optional($emp)->EmpNo,
                    'EmpName'   => optional($emp)->EmpName,
                    'dateHired' => optional($emp)->DateHired,
                    'position' => optional($emp)->Position,
                ];
            })
            ->unique('EmpNo')
            ->values();

            return response()->json($employees);
        }

        
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
                    // return 'qwe';
        $productLineId = $request->product_line;
        // return $productLineId;
        $position = strtoupper($request->position);
        // return $position;

        $productLine = DropdownMasterDetail::where('id', $productLineId)
            ->value('dropdown_masters_details');

            // return $productLine;

        $productStation = DropdownMasterDetail::whereHas('dropdown_master', function ($q) use ($position) {
                $q->where('dropdown_masters', 'Stations')
                ->where('category', $position);
                // ->where('category', 'LIKE', "%$position%");
            })
            ->where('dropdown_masters_details', '!=', 'N/A')
            ->select('id', 'dropdown_masters_details')
            ->get();

        // return $position;

        // $employees = collect(json_decode($request->employees, true));
        // return $employees;

       $employees = collect(json_decode($request->employees, true))
        ->unique('empNo')
        ->map(function ($employee) use ($productLineId, $position) { 

            $empNo = $employee['empNo'] ?? '';
            // dd($empNo);

            if ($position === 'INSPECTOR') {
              $records = QcSlipEmployee::where('employee_no', $empNo)
                ->whereHas('qcSlip', function ($q) use ($productLineId) {
                    $q->where('status', 'OK')
                    ->whereRaw(
                        "FIND_IN_SET(?, REPLACE(REPLACE(product_line, ' ', ''), '|', ',')) > 0",
                        [$productLineId]
                    );
                })
                ->get();
                $iqcCount = $records->where('station_to', 241)->count();
                $ipqcCount = $records->where('station_to', 242)->count();
                $oqcCount = $records->where('station_to', 243)->count();
                $packingCount = $records->where('station_to', 244)->count();
                $functionTestCount = $records->where('station_to', 245)->count();
                $auditingCount = $records->where('station_to', 246)->count();

                //  dd($auditingCount);

                return [
                    'empNo'     => $empNo,
                    'empName'   => $employee['empName'] ?? '',
                    'dateHired' => $employee['dateHired'] ?? '',
                    'stations'  => [
                        1 => $this->getSkillLevel($iqcCount),
                        2 => $this->getSkillLevel($ipqcCount),
                        3 => $this->getSkillLevel($oqcCount),
                        4 => $this->getSkillLevel($packingCount),
                        5 => $this->getSkillLevel($functionTestCount),
                        6 => $this->getSkillLevel($auditingCount)
                    ]
                ];

                // dd($records);

            } elseif ($position === 'OPERATOR') {

             $records = QcSlipEmployee::where('employee_no', $empNo)
                ->whereHas('qcSlip', function ($q) use ($productLineId) {
                    $q->where('product_line', $productLineId)
                    ->where('status', 'OK');
                })
                ->get();

                $assemblyCount = $records->where('station_to', 3)->count();
                $visualCount   = $records->where('station_to', 2)->count();
                $partPrepCount = $records->where('station_to', 1)->count();
                $machineCount  = $records->where('station_to', 4)->count();

                return [
                    'empNo'     => $empNo,
                    'empName'   => $employee['empName'] ?? '',
                    'dateHired' => $employee['dateHired'] ?? '',
                    'stations'  => [
                        1 => $this->getSkillLevel($partPrepCount),
                        2 => $this->getSkillLevel($visualCount),
                        3 => $this->getSkillLevel($assemblyCount),
                        4 => $this->getSkillLevel($machineCount),
                    ],
                ];
            }
            elseif ($position === 'MACHINEOPERATOR'){
                // return 'aaa';
                $records = QcSlipEmployee::where('employee_no', $empNo)
                    ->whereHas('qcSlip', function ($q) use ($productLineId) {
                        $q->where('product_line', $productLineId)
                        ->where('status', 'OK');
                    })
                    ->get();

                $machineOperatorcount  = $records->where('station_to', 4)->count();
                $machinePartPrepCount = $records->where('station_to', 1)->count();
                $machineVisualCount   = $records->where('station_to', 2)->count();
                $machineAssemblyCount = $records->where('station_to', 3)->count();

                // return $machineOperatorcount;

                return [
                    'empNo'     => $empNo,
                    'empName'   => $employee['empName'] ?? '',
                    'dateHired' => $employee['dateHired'] ?? '',
                    'stations'  => [
                        1 => $this->getSkillLevel($machinePartPrepCount),
                        2 => $this->getSkillLevel($machineVisualCount),
                        3 => $this->getSkillLevel($machineAssemblyCount),
                        4 => $this->getSkillLevel($machineOperatorcount),
                    ],
                ];
            }elseif ($position === 'VISUALOPERATOR'){
                // return 'aaa';
                $records = QcSlipEmployee::where('employee_no', $empNo)
                    ->whereHas('qcSlip', function ($q) use ($productLineId) {
                        $q->where('product_line', $productLineId)
                        ->where('status', 'OK');
                    })
                    ->get();

                $visualOperatorcount  = $records->where('station_to', 4)->count();
                $visualPartPrepCount = $records->where('station_to', 1)->count();
                $visualVisualCount   = $records->where('station_to', 2)->count();
                $visualAssemblyCount = $records->where('station_to', 3)->count();

                // return $visualOperatorcount;

                return [
                    'empNo'     => $empNo,
                    'empName'   => $employee['empName'] ?? '',
                    'dateHired' => $employee['dateHired'] ?? '',
                    'stations'  => [
                        1 => $this->getSkillLevel($visualPartPrepCount),
                        2 => $this->getSkillLevel($visualVisualCount),
                        3 => $this->getSkillLevel($visualAssemblyCount),
                        4 => $this->getSkillLevel($visualOperatorcount),
                    ],
                ];
            }
            elseif ($position === 'PARTSPREP'){
                // return 'aaa';
                $records = QcSlipEmployee::where('employee_no', $empNo)
                    ->whereHas('qcSlip', function ($q) use ($productLineId) {
                        $q->where('product_line', $productLineId)
                        ->where('status', 'OK');
                    })
                    ->get();

                $partsPrepOperatorcount  = $records->where('station_to', 4)->count();
                $partsPrepPartPrepCount = $records->where('station_to', 1)->count();
                $partsPrepVisualCount   = $records->where('station_to', 2)->count();
                $partsPrepAssemblyCount = $records->where('station_to', 3)->count();

                // return $partsPrepOperatorcount;

                return [
                    'empNo'     => $empNo,
                    'empName'   => $employee['empName'] ?? '',
                    'dateHired' => $employee['dateHired'] ?? '',
                    'stations'  => [
                        1 => $this->getSkillLevel($partsPrepPartPrepCount),
                        2 => $this->getSkillLevel($partsPrepVisualCount),
                        3 => $this->getSkillLevel($partsPrepAssemblyCount),
                        4 => $this->getSkillLevel($partsPrepOperatorcount),
                    ],
                ];
            }
            // elseif ($position === 'TECHNICIAN'){
            //     // return 'aaa';
            //     $records = QcSlipEmployee::where('employee_no', $empNo)
            //         ->whereHas('qcSlip', function ($q) use ($productLineId) {
            //             $q->where('product_line', $productLineId)
            //             ->where('status', 'OK');
            //         })
            //         ->get();

            //     $partsPrepOperatorcount  = $records->where('station_to', 268)->count();
            //     $partsPrepPartPrepCount = $records->where('station_to', 267)->count();
            //     $partsPrepVisualCount   = $records->where('station_to', 266)->count();
            //     $partsPrepAssemblyCount = $records->where('station_to', 265)->count();

            //     // return $partsPrepOperatorcount;

            //     return [
            //         'empNo'     => $empNo,
            //         'empName'   => $employee['empName'] ?? '',
            //         'dateHired' => $employee['dateHired'] ?? '',
            //         'stations'  => [
            //             1 => $this->getSkillLevel($partsPrepPartPrepCount),
            //             2 => $this->getSkillLevel($partsPrepVisualCount),
            //             3 => $this->getSkillLevel($partsPrepAssemblyCount),
            //             4 => $this->getSkillLevel($partsPrepOperatorcount),
            //         ],
            //     ];
            // }
            

            // return null;
        })
        ->filter()
        ->values()
        ->toArray();

        // return $employees;
        // return $data;
        // exit();

        // return $position;
        if($position === 'INSPECTOR') {
            // return 'asd';
            return Excel::download(
                new SkillMapExport(
                    $productLine,
                    $employees,
                    $productStation,
                    $position
                ),
                'skill_map_inspector.xlsx'
            );
        }
        else if($position === 'OPERATOR') {
            // return 'qwe';
            return Excel::download(
                new SkillMapExport(
                    $productLine,
                    $employees,
                    $productStation,
                    $position
                ),
                'skill_map_operator.xlsx'
            );
        }else if($position === 'MACHINEOPERATOR') {
            // return 'qwe';
            return Excel::download(
                new SkillMapExport(
                    $productLine,
                    $employees,
                    $productStation,
                    $position
                ),
                'skill_map_machineoperator.xlsx'
            );
        }else if($position === 'VISUALOPERATOR') {
            // return 'qwe';
            return Excel::download(
                new SkillMapExport(
                    $productLine,
                    $employees,
                    $productStation,
                    $position
                ),
                'skill_map_visualoperator.xlsx'
            );
        }else if($position === 'PARTSPREP') {
            // return 'qwe';
            return Excel::download(
                new SkillMapExport(
                    $productLine,
                    $employees,
                    $productStation,
                    $position
                ),
                'skill_map_partsprep.xlsx'
            );
        }

    }

    private function getSkillLevel($count)
    {
        if ($count <= 0) {
            return 0;
        }

        if ($count == 1) {
            return 1;
        }

        if ($count <= 3) {
            return 2;
        }

        if ($count <= 6) {
            return 3;
        }

        return 4;
    }

    //  public function exportSkillMapExcelPerProductLine(Request $request)
    // {
    //     // return 'asd';
    //     $position = strtoupper($request->position);
    //     $productLineIds = $request->product_line;

    //     if (is_string($productLineIds)) {
    //         $productLineIds = explode(',', $productLineIds);
    //     }

    //     $productLineIds = array_filter($productLineIds);

    //     $productLines = DropdownMasterDetail::whereIn('id', $productLineIds)
    //         ->pluck('dropdown_masters_details')
    //         ->toArray();

    //     $productStation = DropdownMasterDetail::whereHas('dropdown_master', function ($q) use ($position) {
    //         $q->where('dropdown_masters', 'Stations')
    //         // ->where('category', $position);
    //         ->where('category', 'LIKE', "%$position%");
    //     })
    //     ->where('dropdown_masters_details', '!=', 'N/A')
    //     ->select('id', 'dropdown_masters_details')
    //     ->get();

    //     // return $productStation;

    //     $employees = collect(json_decode($request->employees, true))->map(function ($employee) use ($productLineIds) {

    //         $empNo = $employee['empNo'] ?? '';

    //         $productLineTotals = [];

    //         foreach ($productLineIds as $productLineIndex => $productLineId) {
                    
    //             $records = QcSlipEmployee::where('employee_no', $empNo)
    //                 ->whereHas('qcSlip', function ($q) use ($productLineId) {
    //                     $q->where('product_line', $productLineId)
    //                     ->where('status', 'OK');
    //                 })
    //                 ->get();

    //             // Count all 4 stations for this employee + product line
    //             $partPrepCount = $records->where('station_to', 1)->count();
    //             $visualCount   = $records->where('station_to', 2)->count();
    //             $assemblyCount = $records->where('station_to', 3)->count();
    //             $machineCount  = $records->where('station_to', 4)->count();

    //             // Overall total for this product line
    //             $totalCount = $partPrepCount
    //                 + $visualCount
    //                 + $assemblyCount
    //                 + $machineCount;


    //             if ($totalCount == 0) {
    //                 $level = 0;
    //             } elseif ($totalCount <= 8) {
    //                 $level = 1;
    //             } elseif ($totalCount <= 16) {
    //                 $level = 2;
    //             } elseif ($totalCount <= 24) {
    //                 $level = 3;
    //             } elseif ($totalCount <= 35) {
    //                 $level = 4;
    //             } else {
    //                 $level = 4; // 35+ remains Level 4
    //             }

    //             $productLineTotals[$productLineIndex] = $level;
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
    //         // return $records;

    //     // return $employees;

    //     return Excel::download(
    //         new SkillMapExportPerProdLine(
    //             $productLines,
    //             $employees
    //         ),
    //         'skill_map_per_product_line.xlsx'
    //     );
    // }

    public function exportSkillMapExcelPerProductLine(Request $request)
    {
        // return $request->toggle_value;
        $data = $request->all();
        // return $data;
        $productLine = $request->product_line;
        // return $productLine;
        // $prodLine = implode(',', (array) $productLine);
        // 1. Decode the selected employees from the request
        $employeesInput = collect(json_decode($request->employees, true));
        // return $employeesInput;
        // dd($employeesInput);
        $empNos = $employeesInput->pluck('empNo')->filter()->toArray();
        // dd([
        //     'employee_count' => count($empNos),
        //     'employee_numbers' => $empNos,
        //     'employees_input' => $employeesInput->toArray(),
        // ]);
        $toggleValue = collect($employeesInput->pluck('toggleValue'))->first();
        // return $toggleValue;

        if (empty($empNos)) {
            return response()->json(['message' => 'No employees selected.'], 422);
        }

        $collectEmployeePosition = $employeesInput
        ->pluck('position')
        ->unique()
        ->values()
        ->toArray();

        // return $productLine;
        if($toggleValue == 1){
            
           $productLineIds = array_values(array_unique(
                array_filter(
                    array_map('intval', explode(',', $productLine))
                )
            ));
        }else{
             $productLineIds = QcSlipEmployee::whereIn('employee_no', $empNos)
            ->whereHas('qcSlip', function ($q){
                $q->where('status', 'OK');
            })
            ->with('qcSlip')
            ->get()
            ->pluck('qcSlip.product_line')
            ->unique()
            ->filter()
            ->values()
            ->toArray();
        }
        
        // return $productLineIds;

        $productLineDetails = DropdownMasterDetail::whereIn('id', $productLineIds)
        ->get()
        ->keyBy('id');

        // return $productLineDetails;

        $productLines = collect($productLineIds)
        ->map(function ($id) use ($productLineDetails) {
            $detail = $productLineDetails->get($id);

            return $detail ? $detail->dropdown_masters_details : null;
        })
        ->values()
        ->toArray();
        

        $productStation = DropdownMasterDetail::whereHas('dropdown_master', function ($q) use ($collectEmployeePosition) {
            $q->where('dropdown_masters', 'Stations')
            ->whereIn('category', $collectEmployeePosition);
        })
        ->where('dropdown_masters_details', '!=', 'N/A')
        ->select('id', 'dropdown_masters_details')
        ->get();

        // $position = strtoupper(trim($employeesInput->pluck('position')->first() ?? ''));

        $position = $employeesInput
        ->pluck('position')
        ->map(function ($position) {
            return strtoupper(trim($position));
        })
        ->unique()
        ->values()
        ->toArray();

        $employees = $employeesInput->map(function ($employee) use ($productLineIds, $position) {

            $empNo = trim($employee['empNo'] ?? '');
            $position = strtoupper(trim($employee['position'] ?? ''));

            $productLineTotals = [];

            foreach ($productLineIds as $productLineIndex => $productLineId) {

                $records = QcSlipEmployee::where('employee_no', $empNo)
                ->whereHas('qcSlip', function ($q) use ($productLineId) {
                    $q->where('product_line', $productLineId)
                    ->where('status', 'OK');
                })
                ->get();

                // dd($records);

                // Count stations for this employee and product line only
                $stationCounts = $records->countBy('station_to');

                if ($position === 'INSPECTOR') {
                    $allowedStations = [241, 242, 243, 244, 245, 246,7];
                } elseif ($position === 'OPERATOR') { //VisualOperator,PartPrep,Machineo
                    $allowedStations = [1, 2, 3, 4, 441];

                } elseif ($position === 'MATERIAL HANDLER') {
                    // Replace with the actual Material Handler station IDs.
                    $allowedStations = [524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537];
                } elseif (str_contains($position, 'TECHNICIAN')) {
                    $allowedStations = [268, 267, 266, 265];
                } else {
                    $allowedStations = [];
                }

                // Skill level
                $totalCount = $records
                ->whereIn('station_to', $allowedStations)
                ->count();


                $productLineTotals[$productLineIndex] = $totalCount;
            }

            return [
                'empNo'        => trim($employee['empNo'] ?? ''),
                'empName'      => $employee['empName'] ?? '',
                'dateHired'    => $employee['dateHired'] ?? '',
                'position'     => $employee['position'] ?? '',
                'productLines' => $productLineTotals,
                'allowedStations' => $allowedStations ?? [],
            ];


        });
        
        // dd($employees);

        $employees = $employees->values()->toArray();

        // 6. Download Excel Export
        return Excel::download(
            new SkillMapExportPerProdLine(
                $productLines,
                $employees,
                $position
            ),
            'skill_matrix.xlsx'
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
