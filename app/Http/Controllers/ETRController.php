<?php

namespace App\Http\Controllers;

use App\Model\ExamResult;
use App\Model\Hr\HrMemoTraineeCategoryDetails;
use App\Model\QcSlip;
use App\Model\SystemOneHrisEmpInfo;
use App\Model\SystemOneHrisTrainee;
use App\Model\SystemOneSubconEmpInfo;
use App\Model\TrainingEndorsement;
use App\Model\TrainingRecordEmployee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

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
                },
                'get_training_endorsement_employees' => function ($query) use($employeeNo) {
                    $query->where('emp_no', $employeeNo)
                    ->whereNull('deleted_at');
                }
            ])
            ->where('id', $getTrainingEndoresementId)
            ->select('id', 'date', 'created_by')
            ->first();
            // return $trainingEndorsement;
        }

        $data = collect();
        $qwe = collect();

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
                        'trainor' => optional($trainingEndorsement->created_by_user_details)->name ?? '',
                        'passingScore' => $examResult->rating ?? '',
                        'result' => 'Passed',
                        'record_type' => 'TrainingEndorsement',
                        'exam_result_id' => $examResult->id ?? null,
                        'attachment'  => null,
                    ];

                    $qwe->push($trainingEndorsementRecord);
                }

                if( !empty($endorsementEmployee->hands_on_filename) ){
                    $handsOnRecord = (object) [
                        'trainingDate'    => $trainingEndorsement->date ?? '',
                        'title'           => 'Mag Plate Measurement',
                        'seriesName'      => $trainingRequest->section ?? 'N/A',
                        'department'      => $trainingRequest->department ?? 'N/A',
                        'station'         => 'N/A',
                        'detailedStation' => 'N/A',
                        'objective'       => 'To evaluate the capability of newly hired personnel in measuring precise dimensions of the Magnification Plate and unit using data processor commands based on set specifications.',
                        'trainor'         => optional(
                            $trainingEndorsement->created_by_user_details
                        )->name ?? '',
                        'passingScore'    => '100',
                        'result'          => 'Passed',
                        'record_type'     => 'TrainingEndorsement',
                        'exam_result_id'  => null,
                        'attachment'      =>    $trainingEndorsement->get_training_endorsement_employees[0]->id . '.' .
                                                $trainingEndorsement->get_training_endorsement_employees[0]->hands_on_filename_ext
                                                ?? null,
                    ];
                    $qwe->push($handsOnRecord);
                }

                $data = $qwe;
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

        $query3 = TrainingRecordEmployee::
        with([
            'employee_details',
            // 'training_record',
            'training_record' => function ($query) {
                $query->whereNull('deleted_at'); // Eager-load the deleted training_record model
            },
            'training_record.venue_details',
            'training_record.type_of_training_details',
            'training_record.result_details'
        ])
        ->where('employee_no', $employeeNo)
        ->whereNull('deleted_at')
        ->whereHas('training_record')
        ->get();

        $query3->each(function ($item) {
            $item->record_type = 'TrainingRecordEmployee';
        });
        $data = $data->merge($query3);

        return DataTables::collection($data)
            ->addColumn('trainingDate', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return $row->trainingDate ?? '';
                }
                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    return $row->training_record->start_date && $row->training_record->end_date
                        ? $row->training_record->start_date . ' - ' . $row->training_record->end_date
                        : '';
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
                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    return $row->training_record->training_title ?? '';
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
                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    return $row->series ?? 'N/A';
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
                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    return $row->station ?? 'N/A';
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
                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
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
                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    return $row->training_record->objective ?? '';
                }

                return $row->objective ?? '';
            })

            ->addColumn('trainor', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return $row->trainor ?? '';
                }
                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    $trainer = "";
                    foreach ($row->training_record->trainer_details as $trainor) {
                        $trainer .= $trainor->FirstName . ' ' . $trainor->LastName . ', ';
                    }
                    return rtrim($trainer, ', ');
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

                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    return $row->training_record->remarks ?? '';
                }

                return $data ?? '';
            })

            ->addColumn('result', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return '<span class="badge badge-success">Passed</span>';
                }

                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    if (strtolower($row->training_record->result_details->dropdown_masters_details) == 'passed') {
                        return '<span class="badge badge-success">Passed</span>';
                    }
                    else if (strtolower($row->training_record->result_details->dropdown_masters_details) == 'failed') {
                        return '<span class="badge badge-danger">Failed</span>';
                    }
                    else{
                        return $row->training_record->result_details->dropdown_masters_details ? '<span class="badge badge-secondary">' . $row->training_record->result_details->dropdown_masters_details . '</span>' : '<span class="badge badge-secondary">N/A</span>';
                    }

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

                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    return $row->training_record->venue_details->dropdown_masters_details ?? 'N/A';
                }

                return $row->training_venue ?? '';
            })

            ->addColumn('typeOfTraining', function ($row) {
                if (($row->record_type ?? null) === 'TrainingEndorsement') {
                    return 'Training Unit';
                }

                if (($row->record_type ?? null) === 'TrainingRecordEmployee') {
                    return $row->training_record->type_of_training_details->dropdown_masters_details ?? 'N/A';
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

            ->addColumn('attachment', function ($row) {
                if ($row->attachment) {
                    return $result = "<a href='storage/app/public/hands_on_attachments/" . ($row->attachment ?? '#') . "' target='_blank'>View Attachment</a>";
                }

                if (isset($row->training_record)) {
                    return $result = "<a href='storage/app/public/training_update/" . ($row->training_record->id) . "/" . ($row->training_record->attachments ?? '#') . "' target='_blank'>View Attachment</a>";
                }
                return '';
            })

            ->rawColumns([
                'result',
                'attachment',
            ])
            ->make(true);
    }

    public function getEmployeeTrainingRecord($employeeId,$employeeNo){
        $hrisEmployees = SystemOneHrisEmpInfo::query()
            ->where('EmpStatus', '!=', 'Resigned')
            ->where(function ($query) use ($employeeNo) {
                $query->where('EmpNo', $employeeNo);
            })
            ->get();

        $subconEmployees = SystemOneSubconEmpInfo::query()
            ->where(function ($query) use ($employeeNo) {
                $query->where('EmpNo', $employeeNo);
            })
            ->get()
            ->map(function ($employee) {
                $employee->pkid = 'SUB' . $employee->pkid;

                return $employee;
            });

        $employees = 
            $hrisEmployees
            ->concat($subconEmployees)
            ->values();
        
        // return $employees;
        $etr_records = SystemOneHrisTrainee::with([
            'employee_training_record_info'
        ])
        ->where('fkEmployee', $employees[0]['pkid'])
        ->where('logdel', 0)
        ->whereHas('employee_training_record_info', function ($query) {
            $query->where('logdel', '!=', '1');
        })
        ->get();

        $pdf = Pdf::loadView('ETR_PDF', [
            'etrRecords' => $etr_records,
            'employeeInfo' => $employees[0],
        ]);

        return $pdf->stream('employee_training_record_' . $employeeNo . '.pdf');
    }

    public function getTRDSSummary($employeeId, $employeeNo){
        $employeeInfo = null;

        $hrisEmployees = SystemOneHrisEmpInfo::query()
            ->where('EmpStatus', '!=', 'Resigned')
            ->where(function ($query) use ($employeeNo) {
                $query->where('EmpNo', $employeeNo);
            })
            ->get();

        $subconEmployees = SystemOneSubconEmpInfo::query()
            ->where(function ($query) use ($employeeNo) {
                $query->where('EmpNo', $employeeNo);
            })
            ->get()
            ->map(function ($employee) {
                $employee->pkid = 'SUB' . $employee->pkid;

                return $employee;
            });

        $employeeInfo = 
            $hrisEmployees
            ->concat($subconEmployees)
            ->values();

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
                'get_training_endorsement_employees.get_training_request_details_info.employee_exam_details.exam_result_details_info'
                    => function ($query) {
                        $query->where('exam_result_status', 1)
                            ->where('remark', 'Passed')
                            ->where('status', 0)
                            ->where('logdel', 0);
                    },

                'get_training_endorsement_employees'
                    => function ($query) use ($employeeNo) {
                        $query->where('emp_no', $employeeNo)
                            ->whereNull('deleted_at');
                    }
            ])
            ->where('id', $getTrainingEndoresementId)
            ->select('id', 'date', 'created_by')
            ->first();
        }

        $data = collect();

        if ($trainingEndorsement) {
            foreach (
                $trainingEndorsement->get_training_endorsement_employees
                as $endorsementEmployee
            ) {
                $trainingRequest =
                    $endorsementEmployee->get_training_request_details_info;

                if (!$trainingRequest) {
                    continue;
                }

                $examDetails =
                    $trainingRequest->employee_exam_details;

                if (!$examDetails) {
                    continue;
                }

                $examResults =
                    $examDetails->exam_result_details_info;

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

                    $data->push((object) [
                        'trainingDate' =>
                            $trainingEndorsement->date ?? '',

                        'title' =>
                            $questionnaire['exam_title'] ?? '',

                        'seriesName' =>
                            $trainingRequest->section ?? 'N/A',

                        'department' =>
                            $trainingRequest->department ?? 'N/A',

                        'station' =>
                            'N/A',

                        'detailedStation' =>
                            'N/A',

                        'objective' =>
                            $questionnaire['purpose'] ?? '',

                        'trainor' =>
                            optional(
                                $trainingEndorsement->created_by_user_details
                            )->name ?? '',

                        'passingScore' =>
                            $examResult->rating ?? '',

                        'result' =>
                            'Passed',

                        'record_type' =>
                            'TrainingEndorsement',

                        'exam_result_id' =>
                            $examResult->id ?? null,

                        'attachment' =>
                            null,

                        'training_remarks' =>
                            ($examResult->rating ?? '') !== ''
                                ? $examResult->rating . '%'
                                : '',

                        'trainingVenue' =>
                            $trainingRequest->department ?? 'N/A',

                        'typeOfTraining' =>
                            'Training Unit',
                    ]);
                }

                if (!empty($endorsementEmployee->hands_on_filename)) {
                    $attachment = null;

                    if (
                        !empty($endorsementEmployee->id) &&
                        !empty($endorsementEmployee->hands_on_filename_ext)
                    ) {
                        $attachment =
                            $endorsementEmployee->id . '.' .
                            $endorsementEmployee->hands_on_filename_ext;
                    }

                    $data->push((object) [
                        'trainingDate' =>
                            $trainingEndorsement->date ?? '',

                        'title' =>
                            'Mag Plate Measurement',

                        'seriesName' =>
                            $trainingRequest->section ?? 'N/A',

                        'department' =>
                            $trainingRequest->department ?? 'N/A',

                        'station' =>
                            'N/A',

                        'detailedStation' =>
                            'N/A',

                        'objective' =>
                            'To evaluate the capability of newly hired personnel in measuring precise dimensions of the Magnification Plate and unit using data processor commands based on set specifications.',

                        'trainor' =>
                            optional(
                                $trainingEndorsement->created_by_user_details
                            )->name ?? '',

                        'passingScore' =>
                            '100',

                        'result' =>
                            'Passed',

                        'record_type' =>
                            'TrainingEndorsement',

                        'exam_result_id' =>
                            null,

                        'attachment' =>
                            $attachment,

                        'training_remarks' =>
                            '100%',

                        'trainingVenue' =>
                            $trainingRequest->department ?? 'N/A',

                        'typeOfTraining' =>
                            'Training Unit',
                    ]);
                }
            }
        }

        $hrMemoData = HrMemoTraineeCategoryDetails::with([
            'exam_info_test',
            'employee_info_tist',
            'rapidx_system_one_hris_emp_info'
        ])
        ->whereHas('employee_info_tist', function ($q) use ($employeeNo) {
            $q->where('employee_no', $employeeNo);
        })
        ->get();

        foreach ($hrMemoData as $row) {
            $data->push((object) [
                'trainingDate' =>
                    ($row->date_start && $row->date_end)
                        ? $row->date_start . ' - ' . $row->date_end
                        : '',

                'title' =>
                    optional($row->exam_info_test)->examination_name ?? '',

                'seriesName' =>
                    'N/A',

                'department' =>
                    'N/A',

                'station' =>
                    'N/A',

                'detailedStation' =>
                    'N/A',

                'objective' =>
                    $row->objective ?? '',

                'trainor' => $row->rapidx_system_one_hris_emp_info
                    ? trim(
                        $row->rapidx_system_one_hris_emp_info->FirstName .
                        ' ' .
                        $row->rapidx_system_one_hris_emp_info->LastName
                    )
                    : '',

                'result' =>
                    $row->result ?? null,

                'record_type' =>
                    'HrMemoTraineeCategoryDetails',

                'training_remarks' =>
                    $row->training_remarks ?? '',

                'trainingVenue' =>
                    $row->training_venue ?? '',

                'typeOfTraining' =>
                    $row->type_of_training ?? '',

                'attachment' =>
                    null,
            ]);
        }

        $qcSlips = QcSlip::with([
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


        foreach ($qcSlips as $row) {
            $employee = $row->qc_slip_employees->first();
            $assessmentResult = null;

            if ($employee) {
                $assessmentResult =
                    $employee->second_take_ins_assessment_result
                    ?: $employee->first_take_ins_assessment_result;
            }

            $result = null;

            switch ($assessmentResult) {
                case 'PASSED':
                    $result = 'Passed';
                    break;

                case 'FAILED':
                    $result = 'Failed';
                    break;

                default:
                    $result = 'N/A';
                    break;
            }

            $data->push((object) [
                'trainingDate' =>
                    $row->created_at
                        ? $row->created_at->format('Y-m-d')
                        : '',

                'title' =>
                    'Qualification and Certification',

                'seriesName' =>
                    optional($row->productLine)
                        ->dropdown_masters_details ?? '',

                'department' =>
                    '',

                'station' =>
                    optional(
                        optional($employee)->get_station_to
                    )->dropdown_masters_details ?? '',

                'detailedStation' =>
                    $employee
                        ? ($employee->remarks ?? '')
                        : '',

                'objective' =>
                    $row->objective ?? '',

                'trainor' =>
                    '',

                'result' =>
                    $result,

                'record_type' =>
                    'QcSlip',

                'training_remarks' =>
                    $row->training_remarks ?? '',

                'trainingVenue' =>
                    $row->training_venue ?? '',

                'typeOfTraining' =>
                    optional(
                        optional(
                            $row->qc_reason_certification
                        )->dropdown_reason
                    )->dropdown_masters_details ?? '',

                'attachment' =>
                    null,
            ]);
        }

        $trainingRecords = TrainingRecordEmployee::with([
            'employee_details',
            'training_record' => function ($query) {
                $query->whereNull('deleted_at');
            },
            'training_record.venue_details',
            'training_record.type_of_training_details',
            'training_record.trainer_details'
        ])
        ->where('employee_no', $employeeNo)
        ->whereNull('deleted_at')
        ->whereHas('training_record')
        ->get();


        foreach ($trainingRecords as $row) {
            $trainingRecord = $row->training_record;
            if (!$trainingRecord) {
                continue;
            }

            $trainer = '';
            if ($trainingRecord->trainer_details) {
                foreach ($trainingRecord->trainer_details as $trainor) {

                    $trainer .=
                        $trainor->FirstName . ' ' .
                        $trainor->LastName . ', ';
                }
                $trainer = rtrim($trainer, ', ');
            }

            $result = 'N/A';

            $data->push((object) [
                'trainingDate' =>
                    ($trainingRecord->start_date &&
                    $trainingRecord->end_date)
                        ? $trainingRecord->start_date .
                        ' - ' .
                        $trainingRecord->end_date
                        : '',

                'title' =>
                    $trainingRecord->training_title ?? '',

                'seriesName' =>
                    $row->series ?? 'N/A',

                'department' =>
                    'N/A',

                'station' =>
                    $row->station ?? 'N/A',

                'detailedStation' =>
                    'N/A',

                'objective' =>
                    $trainingRecord->objective ?? '',

                'trainor' =>
                    $trainer,

                'result' =>
                    $result,

                'record_type' =>
                    'TrainingRecordEmployee',

                'training_remarks' =>
                    $trainingRecord->remarks ?? '',

                'trainingVenue' =>
                    optional(
                        $trainingRecord->venue_details
                    )->dropdown_masters_details ?? 'N/A',

                'typeOfTraining' =>
                    optional(
                        $trainingRecord->type_of_training_details
                    )->dropdown_masters_details ?? 'N/A',

                'attachment' =>
                    !empty($trainingRecord->attachments)
                        ? $trainingRecord->id . '/' .
                        $trainingRecord->attachments
                        : null,
            ]);
        }

        $data = $data->sortBy(function ($row) {
            return $row->trainingDate ?? '';
        })->values();

        $passed = $data->filter(function ($row) {
            return strtolower($row->result ?? '') === 'passed';
        })->count();

        $complied = $data->filter(function ($row) {
            return strtolower($row->result ?? '') === 'complied';
        })->count();

        $failed = $data->filter(function ($row) {
            return strtolower($row->result ?? '') === 'failed';
        })->count();

        $actualHandsOn = $data->filter(function ($row) {
            return
                ($row->title ?? '') === 'Mag Plate Measurement'
                ||
                stripos(
                    $row->typeOfTraining ?? '',
                    'hands on'
                ) !== false
                ||
                stripos(
                    $row->objective ?? '',
                    'hands-on'
                ) !== false
                ||
                stripos(
                    $row->objective ?? '',
                    'hands on'
                ) !== false;
        })->count();

        $total = $data->count();

        $pdf = Pdf::loadView('TRDS_Summary', [
            'employeeInfo'  =>  $employeeInfo[0],
            'records'       =>  $data,
            'passed'        =>  $passed,
            'complied'      =>  $complied,
            'failed'        =>  $failed,
            'actualHandsOn' =>  $actualHandsOn,
            'total'         =>  $total,
        ]);

        return $pdf->stream(
            'trds_summary_record_' . $employeeNo . '.pdf'
        );
    }


}
