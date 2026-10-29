<?php

namespace App\Exports;

use App\Model\InspectorSkillChart\InspectorSkillChartSetting;
// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\InspectorSkillChartSheets\TSF1;
use App\Exports\InspectorSkillChartSheets\TSF3;
use App\Exports\InspectorSkillChartSheets\CN;
use App\Exports\InspectorSkillChartSheets\CNF3;
use App\Exports\InspectorSkillChartSheets\PPDCN;
use App\Exports\InspectorSkillChartSheets\PPDTS;
use App\Exports\InspectorSkillChartSheets\PPDF3;
use App\Exports\InspectorSkillChartSheets\YF;

use App\Model\Qc\QcSlipEmployee;
use App\Model\QcSlip;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class InspectorSkillChart implements WithMultipleSheets{
    protected $selectedSheets;
    protected $processStationDetails;

    public function __construct(array $selectedSheets){
        $this->selectedSheets = $selectedSheets;
        $this->processStationDetails = InspectorSkillChartSetting::where('status', 0)->orderBy('process_order')->get();

        // Stations ranked newest -> oldest per slip (no window function)
        $employees = DB::table('qc_slip_employees as e')
            ->whereNull('e.deleted_at')
            ->select(
                'e.qc_slips_id',
                'e.employee_no',
                'e.station_to',
                'e.remarks',
                'e.created_at',
                DB::raw('(
                    SELECT COUNT(*) FROM qc_slip_employees e2
                    WHERE e2.qc_slips_id = e.qc_slips_id
                    AND e2.deleted_at IS NULL
                    AND (e2.created_at > e.created_at
                        OR (e2.created_at = e.created_at AND e2.id > e.id))
                ) + 1 AS rn')
            );

        // Results ranked newest -> oldest per slip
        $results = DB::table('c_lqc_training_item_results as r')
            ->where('r.training_item_id', 269)   // Specific Training = Level
            ->where('r.day_number', 5) //Specific day number for the training item
            ->whereNull('r.deleted_at')
            ->select(
                'r.qc_slips_id',
                'r.result',
                DB::raw('(
                    SELECT COUNT(*) FROM c_lqc_training_item_results r2
                    WHERE r2.qc_slips_id = r.qc_slips_id
                    AND r2.training_item_id = 269
                    AND r2.day_number = 5
                    AND r2.deleted_at IS NULL
                    AND (r2.created_at > r.created_at
                        OR (r2.created_at = r.created_at AND r2.id > r.id))
                ) + 1 AS rn')
            );

        $certEmpDetails = DB::table('qc_slips')
            ->joinSub($employees, 'emp', 'qc_slips.id', '=', 'emp.qc_slips_id')
            ->leftJoin(
                'dropdown_master_details as station_dm_details',
                'emp.station_to',
                '=',
                'station_dm_details.id'
            )
            ->joinSub($results, 'training_item_result', function ($join) {
                $join->on('qc_slips.id', '=', 'training_item_result.qc_slips_id')
                    ->on('emp.rn', '=', 'training_item_result.rn');
            })
            ->where('qc_slips.position_category', 'Inspector')
            ->whereNull('qc_slips.deleted_at')
            ->select(
                'qc_slips.id as slip_id',
                'emp.employee_no',
                'qc_slips.section_category as section',
                'emp.station_to',
                'emp.remarks as sub_station',
                'station_dm_details.dropdown_masters_details as station_to_name',
                'training_item_result.result as training_result'
            )
            ->orderBy('qc_slips.id', 'desc')
            ->orderBy('emp.rn')
            ->get();

        // Get unique employee numbers
        $employeeNos = $certEmpDetails
            ->pluck('employee_no')
            ->unique()
            ->values()
            ->toArray();

        // Retrieve employee information from System One
        $employeeDetails = QcSlipEmployee::with([
                'system_one_subcon_emp_info',
                'system_one_hris_emp_info',
            ])
            ->whereIn('employee_no', $employeeNos)
            ->get()
            ->keyBy('employee_no');

        $this->grouped = $certEmpDetails
            ->groupBy('employee_no')
            ->map(function ($records, $employeeNo) use ($employeeDetails) {
                $info    = optional($employeeDetails->get($employeeNo))->employee_info;
                $hired   = isset($info->DateHired) ? $info->DateHired : null;
                $service = $hired ? (new \DateTime($hired))->diff(new \DateTime()) : null;

                // Split this employee's records per section (CN, TS, ...)
                $sections = $records
                    ->groupBy(function ($record) {
                        return $record->section ?: 'UNASSIGNED';
                    })
                    ->map(function ($sectionRecords, $sectionName) {
                        return [
                            'section'  => $sectionName,
                            'stations' => $sectionRecords
                                ->unique('station_to')   // newest per station, within this section only
                                ->map(function ($record) {
                                    return $this->buildStation($record);
                                })
                                ->values(),
                        ];
                    });

                return [
                    'employee_no'              => $employeeNo,
                    'employee_name'            => isset($info->EmpName) ? $info->EmpName : null,
                    'date_hired'               => $hired,
                    'length_of_service_year'   => $service ? $service->y : null,
                    'length_of_service_months' => $service ? $service->m : null,
                    'sections'                 => $sections,   // keyed by section name
                ];
            })
            ->values();
    }

    private function buildStation($record){
        $subStations = $this->explodeSubStations($record->sub_station, $record->training_result);

        return [
            'slip_id'         => $record->slip_id,
            'station_to'      => $record->station_to,
            'station_to_name' => $record->station_to_name,
            'training_result' => empty($subStations) ? $record->training_result : null,
            'sub_stations'    => $subStations,
        ];
    }

    private function explodeSubStations($raw, $result){
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        return collect(explode(',', $raw))
            ->map(function ($name) {
                return trim($name);
            })
            ->filter()
            ->unique()
            ->map(function ($name) use ($result) {
                return ['name' => $name, 'training_result' => $result];
            })
            ->values()
            ->all();
    }

    public function collection(){
        return $this->grouped;
    }

    public function map($row): array{
        return [
            $row['slip_id'],
            $row['employee_no'],
            $row['employee_name'],
            $row['date_hired'],
            $row['section'],
            $row['stations'],
        ];
    }

    /**
     * Returns employees with the section matching $group renamed to 'main'.
     * Other sections are kept under their original names.
     */

    private function withMainSection(array $names)
    {
        return $this->grouped->map(function ($employee) use ($names) {
            $sections = $employee['sections'];

            // first section key that equals one of the accepted names
            $key = $sections->keys()->first(function ($sectionName) use ($names) {
                return in_array($sectionName, $names, true);
            });

            if ($key !== null) {
                $employee['sections'] = collect(
                    ['main' => $sections->get($key)] + $sections->except($key)->all()
                );
            }

            return $employee;
        });
    }

    public function sheets(): array{
        // dd($this->grouped);
        $grouped_employee_details = $this->grouped;

        $sheets = [];

        foreach ($this->selectedSheets as $sheet) {
            switch ($sheet) {
                case 'TSF1':
                case 'TSF3':
                    $group = 'TS';
                    break;

                case 'CN':
                case 'CNF3':
                    $group = 'CN';
                    break;

                case 'PPDCN':
                case 'PPDTS':
                case 'PPDF3':
                    $group = 'PPD';
                    break;

                case 'YF':
                    $group = 'YF';
                    break;

                default:
                    $group = null;
            }

            $categories = $this->processStationDetails
                ->where('section', $sheet)
                ->groupBy('skill_category')
                ->map(function ($processes, $category) {
                    return [
                        'name' => $category,
                        'processes' => $processes->map(function ($process) {
                            return [
                                'name' => $process->process_station,
                                'order' => $process->process_order,
                                'sub_stations' => strtoupper($process->sub_station) == 'N/A'
                                    ? []
                                    : array_map('trim', explode(',', $process->sub_station)),
                            ];
                        })->values()->toArray(),
                    ];
                })
                ->values();

            // Employees with data for this group; that section renamed to 'main'
            $employees = $this->withMainSection([$group, $sheet])
            ->filter(function ($employee) {
                return $employee['sections']->has('main');
            })
            ->values();

            // dd($employees);

            $sheets[] = new TSF1($categories, $group, $sheet, $employees);
        }

        return $sheets;
    }
}
