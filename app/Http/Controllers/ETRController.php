<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneHrisTrainee;
use App\Model\SystemOneSubconEmpInfo;
use App\Model\Hr\HrMemoTraineeCategoryDetails;
use App\Model\QcSlip;
use App\Model\ExamResult;
use App\Model\TrainingEndorsement;

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
        ->addColumn('date', function($trds_record){
            $result =  '<center>';
            $result .= $trds_record->employee_training_record_info->PeriodFrom ?? "-";
            $result .= '<br>';
            $result .= $trds_record->employee_training_record_info->PeriodTo ?? "-";
            $result .= '</center>';
            return $result;
        })

        ->rawColumns(['date'])
        ->make(true);
    }

    public function getSystemoneEmployeeTrainingDetails(Request $request){
        $search = trim($request->search);

        $hrisEmployees = SystemOneHrisEmpInfo::query()
        ->where('EmpStatus', '!=', 'Resigned')
        ->where(function ($query) use ($search) {
            $query->where('EmpNo', 'LIKE', "%{$search}%")
                ->orWhere('EmpName', 'LIKE', "%{$search}%");
        })
        ->limit(50)
        ->get();

        $subconEmployees = SystemOneSubconEmpInfo::query()
        ->where(function ($query) use ($search) {
            $query->where('EmpNo', 'LIKE', "%{$search}%")
                ->orWhere('EmpName', 'LIKE', "%{$search}%");
        })
        ->limit(50)
        ->get()
        ->map(function ($employee) {
            $employee->pkid = 'SUB' . $employee->pkid;

            return $employee;
        });

        $employees = $hrisEmployees
            ->concat($subconEmployees)
            ->take(50)
            ->values();

        return response()->json($employees);
    }

    public function viewTRDSSummary(Request $request){
        $employeeNo = $request->getEmployeeNoForTrdsSummary;

        $test = ExamResult::with([
            'training_request_info.training_endorsement_info'
        ])
        ->where('employee_no', $employeeNo)
        ->where('status', 0)
        ->where('logdel', 0)
        ->first();


        $getTrainingEndoresementId = optional(
            optional(
                optional($test)->training_request_info
            )->training_endorsement_info
        )->id;

        $trainingEndorsement = null;

        if ($getTrainingEndoresementId) {
            $trainingEndorsement = TrainingEndorsement::with([
                'created_by_user_details',
                'get_training_endorsement_employees.get_training_request_details_info.employee_exam_details.exam_result_details_info' => function ($query) {
                    $query->where('exam_result_status', 1)
                        ->where('remark', 'Passed')
                        ->where('status', 0)
                        ->where('logdel', 0);
                }
            ])
            ->where('id', $getTrainingEndoresementId)
            ->select('id', 'date', 'created_by')
            ->first();
            // return $trainingEndorsement;
        }

        $data = collect();

        if($trainingEndorsement){
            foreach ($trainingEndorsement->get_training_endorsement_employees as $endorsementEmployee) {
                $trainingRequest = $endorsementEmployee->get_training_request_details_info;
                if (!$trainingRequest) {
                    continue;
                }

                $examDetails = $trainingRequest->employee_exam_details;
                if (!$examDetails) {
                    continue;
                }

                $examResults = $examDetails->exam_result_details_info;
                if (!$examResults) {
                    continue;
                }

                if ($examResults instanceof \Illuminate\Database\Eloquent\Model) {
                    $examResults = collect([$examResults]);
                }

                if (!$examResults instanceof \Illuminate\Support\Collection) {
                    $examResults = collect($examResults);
                }

                foreach ($examResults as $examResult) {
                    $questionnaire = $examResult->questionnaire;
                    if (is_string($questionnaire)) {
                        $questionnaire = json_decode(
                            $questionnaire,
                            true
                        );
                    }

                    if (!is_array($questionnaire)) {
                        $questionnaire = [];
                    }

                    $trainingEndorsementRecord = (object) [
                        'trainingDate' => $trainingEndorsement->date ?? '',
                        'title' => $questionnaire['exam_title'] ?? '',
                        'seriesName' => $trainingRequest->section ?? 'N/A',
                        'department' => $trainingRequest->department ?? 'N/A',
                        'station' => 'N/A',
                        'detailedStation' => 'N/A',
                        'objective' => $questionnaire['purpose'] ?? '',
                        'trainor' => optional(
                            $trainingEndorsement->created_by_user_details
                        )->name ?? '',
                        'passingScore' => $examResult->rating ?? '',
                        'result' => 'Passed',
                        'record_type' => 'TrainingEndorsement',
                        'exam_result_id' => $examResult->id ?? null,
                    ];

                    $data->push($trainingEndorsementRecord);
                }
            }
        }

        $query = HrMemoTraineeCategoryDetails::with([
            'exam_info_test',
            'employee_info_tist',
            'rapidx_system_one_hris_emp_info'
        ])
        ->whereHas('employee_info_tist', function ($q) use ($employeeNo) {
            $q->where('employee_no', $employeeNo);
        });

        $hrMemoData = $query->get();
        $hrMemoData->each(function ($item) {
            $item->record_type = 'HrMemoTraineeCategoryDetails';
        });

        $data = $data->merge($hrMemoData);
        $query2 = QcSlip::with([
            'qc_slip_employees' => function ($q) use ($employeeNo) {
                $q->where('employee_no', $employeeNo);
            },
            'qc_slip_employees.get_station_to',
            'productLine',
            'qc_reason_certification.dropdown_reason'
        ])
        ->whereHas('qc_slip_employees', function ($q) use ($employeeNo) {
            $q->where('employee_no', $employeeNo);
        })
        ->where('status', 'OK')
        ->get();

        $query2->each(function ($item) {
            $item->record_type = 'QcSlip';
        });

        $data = $data->merge($query2);
        return DataTables::collection($data)
            ->addColumn('trainingDate', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return $row->trainingDate ?? '';
                }

                if ($row instanceof \App\Model\QcSlip) {
                    return $row->created_at
                        ? $row->created_at->format('Y-m-d')
                        : '';
                }

                if (!$row->date_start && !$row->date_end) {
                    return '';
                }

                return $row->date_start . ' - ' . $row->date_end;
            })

            ->addColumn('title', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return $row->title ?? '';
                }

                if ($row instanceof \App\Model\QcSlip) {
                    return 'Qualification and Certification';
                }

                return optional(
                    $row->exam_info_test
                )->examination_name;
            })

            ->addColumn('seriesName', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return $row->seriesName ?? 'N/A';
                }

                if ($row instanceof \App\Model\QcSlip) {
                    return optional(
                        $row->productLine
                    )->dropdown_masters_details;
                }

                return 'N/A';
            })

            ->addColumn('station', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return 'N/A';
                }

                if ($row instanceof \App\Model\QcSlip) {
                    return optional(
                        optional(
                            $row->qc_slip_employees->first()
                        )->get_station_to
                    )->dropdown_masters_details ?? '';
                }

                return 'N/A';
            })

            ->addColumn('detailedStation', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return 'N/A';
                }

                if ($row instanceof \App\Model\QcSlip) {
                    $employee = $row->qc_slip_employees->first();
                    return $employee
                        ? ($employee->remarks ?? '')
                        : '';
                }

                return 'N/A';
            })

            ->addColumn('objective', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return $row->objective ?? '';
                }

                return $row->objective ?? '';
            })

            ->addColumn('trainor', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return $row->trainor ?? '';
                }

                if ($row instanceof \App\Model\QcSlip) {
                    return '';
                }

                $trainor = $row->rapidx_system_one_hris_emp_info;
                if (!$trainor) {
                    return '';
                }

                return trim(
                    $trainor->FirstName . ' ' . $trainor->LastName
                );
            })

            ->addColumn('training_remarks', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return $row->passingScore. '%' ?? '';
                }else{
                    $data = $row->training_remarks ?? '';
                }

                return $data ?? '';
            })

            ->addColumn('result', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return '<span class="badge badge-success">Passed</span>';
                }

                if ($row instanceof \App\Model\QcSlip) {
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
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return $row->department ?? 'N/A';
                }

                return $row->training_venue ?? '';
            })

            ->addColumn('typeOfTraining', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return 'Training Unit';
                }

                if ($row instanceof \App\Model\QcSlip) {
                    return optional(
                        optional(
                            $row->qc_reason_certification
                        )->dropdown_reason
                    )->dropdown_masters_details ?? '';
                }

                return $row->type_of_training ?? '';
            })
            ->rawColumns([
                'result'
            ])
            ->make(true);
    }
}
