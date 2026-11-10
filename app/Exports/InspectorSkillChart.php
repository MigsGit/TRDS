<?php

namespace App\Exports;

use App\Model\InspectorSkillChart\InspectorSkillChartSetting;
// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\InspectorSkillChartSheets\TSF1;
use App\Exports\InspectorSkillChartSheets\TSF1Layout;
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
    /**
     * Sheet (= section_category) => group. Shared by the constructor and sheets().
     * The group is used only for the OTHER PROCESS / SYSTEM SKILLS matrix (AG:AO).
     */
    const SHEET_GROUPS = [
        'TSF1'  => 'TS',
        'TSF3'  => 'TS',
        'CN'    => 'CN',
        'CNF3'  => 'CN',
        'PPDCN' => 'PPD',
        'PPDTS' => 'PPD',
        'PPDF3' => 'PPD',
        'YF'    => 'YF',
    ];

    protected $selectedSheets;
    protected $processStationDetails;

    public function __construct(array $selectedSheets){
        $this->selectedSheets = $selectedSheets;
        $this->processStationDetails = InspectorSkillChartSetting::where('status', 0)->orderBy('process_order')->get();

        // Selected sections (sheet name = section_category); unknown sheet names are skipped
        $selectedSections = array_values(array_filter($selectedSheets, function ($sheet) {
            return is_string($sheet) && isset(self::SHEET_GROUPS[$sheet]);
        }));

        // Current slip per employee = highest Inspector slip id, from the slip tables
        // (not from the joined result below, whose inner join can drop the real latest slip)
        $latestSlips = DB::table('qc_slip_employees as le')
            ->join('qc_slips as ls', 'ls.id', '=', 'le.qc_slips_id')
            ->where('ls.position_category', 'Inspector')
            ->whereNull('ls.deleted_at')
            ->whereNull('le.deleted_at')
            ->groupBy('le.employee_no')
            ->select('le.employee_no', DB::raw('MAX(ls.id) AS current_slip_id'));

        // Keep only employees whose current section is one of the selected sheets
        $currentSlips = DB::table('qc_slips as cs')
            ->joinSub($latestSlips, 'latest', 'cs.id', '=', 'latest.current_slip_id')
            ->whereIn('cs.section_category', $selectedSections)
            ->select(
                'latest.employee_no',
                'cs.id as current_slip_id',
                'cs.section_category as current_section'
            )
            ->get()
            ->keyBy('employee_no');

        $keptNos = $currentSlips->pluck('employee_no')->all();

        if (empty($keptNos)) {
            $this->grouped = collect();

            return;
        }

        // Every slip of the kept employees, in every section (older sections feed AG:AO)
        $slipIds = DB::table('qc_slip_employees as se')
            ->join('qc_slips as ss', 'ss.id', '=', 'se.qc_slips_id')
            ->where('ss.position_category', 'Inspector')
            ->whereNull('ss.deleted_at')
            ->whereNull('se.deleted_at')
            ->whereIn('se.employee_no', $keptNos)
            ->distinct()
            ->pluck('se.qc_slips_id')
            ->all();

        // Current station = the employee's newest qc_slip_employees row (rn = 1) in the current slip
        $currentStations = DB::table('qc_slip_employees as ce')
            ->leftJoin('dropdown_master_details as cdm', 'ce.station_to', '=', 'cdm.id')
            ->whereIn('ce.qc_slips_id', $currentSlips->pluck('current_slip_id')->all())
            ->whereIn('ce.employee_no', $keptNos)
            ->whereNull('ce.deleted_at')
            ->select(
                'ce.qc_slips_id',
                'ce.employee_no',
                'ce.station_to',
                'cdm.dropdown_masters_details as station_to_name'
            )
            ->orderBy('ce.created_at', 'desc')
            ->orderBy('ce.id', 'desc')
            ->get()
            ->filter(function ($row) use ($currentSlips) {
                $current = $currentSlips->get($row->employee_no);

                return $current && (int) $current->current_slip_id === (int) $row->qc_slips_id;
            })
            ->unique('employee_no')   // first row per employee = newest
            ->keyBy('employee_no');

        // Stations ranked newest -> oldest per slip (no window function)
        $employees = DB::table('qc_slip_employees as e')
            ->whereNull('e.deleted_at')
            ->whereIn('e.qc_slips_id', $slipIds)
            ->whereIn('e.employee_no', $keptNos)
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
            ->whereIn('r.qc_slips_id', $slipIds)
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
            ->map(function ($records, $employeeNo) use ($employeeDetails, $currentSlips, $currentStations) {
                $info    = optional($employeeDetails->get($employeeNo))->employee_info;
                $current = $currentSlips->get($employeeNo);
                $currentStation = $currentStations->get($employeeNo);
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
                    'current_section'          => $current ? $current->current_section : null,
                    'current_station'          => $currentStation ? [
                        'station_to'      => $currentStation->station_to,
                        'station_to_name' => $currentStation->station_to_name,
                    ] : null,
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
     * Returns employees with the section whose key equals $sheet renamed to 'main',
     * but only when $sheet is also the employee's current_section, and adds their
     * 'other_sections' for the sheet's group. Other sections keep their original names.
     *
     * @param string $sheet sheet name, a key of self::SHEET_GROUPS
     * @return \Illuminate\Support\Collection
     */
    private function withMainSection($sheet)
    {
        $sheetGroup = self::SHEET_GROUPS[$sheet];

        return $this->grouped->map(function ($employee) use ($sheet, $sheetGroup) {
            $sections = $employee['sections'];
            $currentSection = isset($employee['current_section']) ? $employee['current_section'] : null;

            if ($currentSection === $sheet && $sections->has($sheet)) {
                $employee['sections'] = collect(
                    ['main' => $sections->get($sheet)] + $sections->except($sheet)->all()
                );
                $employee['other_sections'] = $this->buildOtherSections($employee, $sheetGroup);
            }

            return $employee;
        });
    }

    /**
     * Data for the OTHER PROCESS / SYSTEM SKILLS matrix (AG:AO). The employee's
     * non-main sections are rolled up by group via self::SHEET_GROUPS (CNF3 goes
     * under CN), the sheet's own group is excluded, and unknown section names are
     * skipped. Stations are merged per group and de-duplicated by station_to,
     * keeping the newest (highest slip_id). Only stations whose trimmed
     * station_to_name is listed in TSF1Layout::OTHER_PROCESSES are used.
     *
     * @param array  $employee   employee with 'main' already set in 'sections'
     * @param string $sheetGroup group of the sheet being built (TS, CN, PPD, YF)
     * @return \Illuminate\Support\Collection other group (TSF1Layout::OTHER_SECTIONS order,
     *         own group removed) => [process => ['total' => int, 'stations' => array, 'sub_stations' => array]]
     */
    private function buildOtherSections($employee, $sheetGroup)
    {
        $stationsByGroup = [];

        foreach ($employee['sections'] as $sectionName => $section) {
            if ($sectionName === 'main' || !is_string($sectionName) || !isset(self::SHEET_GROUPS[$sectionName])) {
                continue;
            }

            $group = self::SHEET_GROUPS[$sectionName];

            if ($group === $sheetGroup) {
                continue;
            }

            foreach ($section['stations'] as $station) {
                $stationsByGroup[$group][] = $station;
            }
        }

        $otherSections = collect();

        foreach (array_diff(TSF1Layout::OTHER_SECTIONS, [$sheetGroup]) as $group) {
            $stations = collect(isset($stationsByGroup[$group]) ? $stationsByGroup[$group] : [])
                ->sortByDesc('slip_id')
                ->unique('station_to')   // newest per station across the group's sections
                ->values();

            $processes = [];

            foreach (TSF1Layout::OTHER_PROCESSES as $process => $names) {
                $matched = $stations->filter(function ($station) use ($names) {
                    return in_array(trim((string) $station['station_to_name']), $names, true);
                })->values()->all();

                $subStations = [];
                foreach ($matched as $station) {
                    foreach (isset($station['sub_stations']) ? $station['sub_stations'] : [] as $sub) {
                        $subStations[] = $sub;
                    }
                }

                $processes[$process] = [
                    'total'        => $this->otherProcessTotal($matched),
                    'stations'     => $matched,
                    'sub_stations' => $subStations,
                ];
            }

            $otherSections->put($group, $processes);
        }

        return $otherSections;
    }

    /**
     * Integer total of one other-process cell, by TSF1Layout::OTHER_CELL_MODE.
     * Per station: 'sum' = its sub-station ratings summed, or its own rating when it
     * has none (never both); 'max' = highest of those; 'station' = its own rating only.
     * Several matched stations are added together ('max' keeps the highest).
     *
     * @param array $stations de-duplicated stations of one process in one group
     * @return int
     */
    private function otherProcessTotal(array $stations)
    {
        $mode = TSF1Layout::OTHER_CELL_MODE;
        $total = 0;

        foreach ($stations as $station) {
            $own = $this->ratingToInt(isset($station['training_result']) ? $station['training_result'] : null);
            $subs = array_map(function ($sub) {
                return $this->ratingToInt(isset($sub['training_result']) ? $sub['training_result'] : null);
            }, isset($station['sub_stations']) ? $station['sub_stations'] : []);

            if ($mode === 'station') {
                $value = $own;
            } elseif ($mode === 'max') {
                $value = max(array_merge([$own], $subs));
            } else {
                $value = empty($subs) ? $own : array_sum($subs);
            }

            $total = $mode === 'max' ? max($total, $value) : $total + $value;
        }

        return $total;
    }

    /**
     * Null, empty, whitespace and non-numeric become 0; numbers become int.
     *
     * @param mixed $value
     * @return int
     */
    private function ratingToInt($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        return is_numeric($value) ? (int) $value : 0;
    }

    public function sheets(): array{
        // dd($this->grouped);
        $grouped_employee_details = $this->grouped;

        $sheets = [];

        foreach ($this->selectedSheets as $sheet) {
            if (!is_string($sheet) || !isset(self::SHEET_GROUPS[$sheet])) {
                continue;   // unknown sheet name
            }

            $group = self::SHEET_GROUPS[$sheet];

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

            // Employees whose current section is this sheet; that section renamed to 'main'
            $employees = $this->withMainSection($sheet)
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
