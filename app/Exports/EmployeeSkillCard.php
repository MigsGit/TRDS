<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Exports\EmployeeSkillCardSheets\SkillCard;

// use App\Model\InspectorSkillChart\InspectorSkillChartSetting;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Model\Qc\QcSlipEmployee;
use App\Model\QcSlip;

use Carbon\Carbon;

class EmployeeSkillCard implements WithMultipleSheets
{
    protected $employeeNo;
    protected $qcSlipEmployee;
    protected $employeeDetails;
    protected $grouped;

    public function __construct($employeeNo){
        $this->employeeNo = $employeeNo;

        // Get certification records
        $certEmpDetails = DB::table('qc_slip_employees')
            ->join(
                'qc_slips',
                'qc_slip_employees.qc_slips_id',
                '=',
                'qc_slips.id'
            )
            ->leftJoin(
                'dropdown_master_details as station_dm_details',
                'qc_slip_employees.station_to',
                '=',
                'station_dm_details.id'
            )
            ->leftJoin(
                'dropdown_master_details as product_line_dm_details',
                'qc_slips.product_line',
                '=',
                'product_line_dm_details.id'
            )
            ->leftJoin(
                'dropdown_master_details as production_area_dm_details',
                'qc_slips.production_area',
                '=',
                'production_area_dm_details.id'
            )
            //connectiont to target rating settings
            ->join('target_rating_settings as target_ratings', function ($join) {
                $join->on('qc_slips.section_category', '=', 'target_ratings.section')
                    ->on('qc_slips.position_category', '=', 'target_ratings.position')
                    ->on('qc_slips.production_area', '=', 'target_ratings.product_area')
                    ->where('target_ratings.status', '=', 1)
                    ->whereRaw(
                        "CONCAT(',', REPLACE(target_ratings.product_line, ' ', ''), ',')
                        REGEXP CONCAT(',(', REPLACE(qc_slips.product_line, ' ', ''), '),')"
                    );
                    // ->whereRaw(
                    //     "FIND_IN_SET(qc_slips.product_line, REPLACE(target_ratings.product_line, ' ', '')) > 0"
                    // );
            })
            ->join('dropdown_master_details as target_product_line_dm_details', function ($join){
                $join->whereRaw(
                        "FIND_IN_SET(
                            target_product_line_dm_details.id,
                            REPLACE(target_ratings.product_line, ' ', '')
                        ) > 0"
                    );
                // ->on('target_ratings.status', '=', DB::raw('1'))
                //         ->whereRaw(
                //             "FIND_IN_SET(target_product_line_dm_details.dropdown_masters_id, REPLACE(target_ratings.product_line, ' ', '')) > 0"
                //         );
            })
            ->join('target_rating_details as target_details', 'target_ratings.id', '=', 'target_details.target_rating_id')

            //connectiont to rating description settings
            ->join('rating_description_settings as rating_descriptions', function ($join) {
                $join->on('qc_slips.position_category', '=', 'rating_descriptions.position')
                    ->where('rating_descriptions.status', '=', 1)
                    ->whereRaw(
                        "FIND_IN_SET(qc_slip_employees.station_to, REPLACE(rating_descriptions.designation, ' ', '')) > 0"
                    );
            })
            ->join('rating_details as rating_description_details', 'rating_descriptions.id', '=', 'rating_description_details.rating_description_id')

            ->where('qc_slip_employees.employee_no', $this->employeeNo) //for specific employee number
            ->whereNull('qc_slip_employees.deleted_at')
            ->whereNull('qc_slips.deleted_at')
            ->select(
                'qc_slips.position_category as position',
                'qc_slips.section_category as section',
                'qc_slips.production_area as production_area',
                'qc_slips.product_line as product_line',
                'qc_slips.date_of_transfer as date_of_transfer',
                'qc_slip_employees.employee_no',
                'qc_slip_employees.station_to',
                'qc_slips.series_name',
                'station_dm_details.dropdown_masters_details as station_to_name',
                'product_line_dm_details.dropdown_masters_details as product_line_name',
                'production_area_dm_details.dropdown_masters_details as production_area_name',
                'target_product_line_dm_details.id as target_product_line_id',
                'target_product_line_dm_details.dropdown_masters_details as target_product_line_name',
                'target_ratings.product_line as target_product_line',
                'target_details.target_name as target_rating_name',
                'target_details.rating as target_rating_value',
                'rating_description_details.level as target_rating_level',
                'rating_description_details.description as target_rating_description',
            )
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

        // Group certification records by employee
        $this->grouped = $certEmpDetails
            ->groupBy('employee_no')
            ->map(function ($records, $employeeNo) use ($employeeDetails) {
                $first = $records->first();

                $employeeRecord = $employeeDetails->get($employeeNo);

                return [
                    // 'employee_no' => $employeeNo,

                    'employee_name' => $employeeRecord
                        ? optional($employeeRecord->employee_info)->EmpName
                        : null,

                    'date_hired' => $employeeRecord
                        ? optional($employeeRecord->employee_info)->DateHired
                        : null,

                    'position' => $first->position,
                    'section' => $first->section,
                    'date_of_transfer' => $first->date_of_transfer,
                    'validity_period' => $first->date_of_transfer ? date('Y-m-d', strtotime('+6 months', strtotime($first->date_of_transfer))) : null,

                    'target_rating_product_lines' => $records
                    ->map(function ($record) {
                        return [
                            'target_product_line' => $record->target_product_line_id,
                            'target_product_line_name' => $record->target_product_line_name,
                        ];
                    })
                    ->unique('target_product_line')
                    ->values(),

                    'target_ratings' => $records
                        ->map(function ($record) {
                            return [
                                'target_rating_name' => $record->target_rating_name,
                                'target_rating_value' => $record->target_rating_value,
                            ];
                        })
                        ->unique('target_rating_name')
                        ->values(),

                    'target_rating_description' => $records
                        ->map(function ($record) {
                            return [
                                'target_rating_level' => $record->target_rating_level ? $record->target_rating_level : null,
                                'target_rating_description' => $record->target_rating_description ? $record->target_rating_description : null,
                            ];
                        })
                        ->filter(function ($item) {
                            return $item['target_rating_level'] !== null;
                        })
                        ->sortBy('target_rating_level')
                        ->unique('target_rating_level')
                        ->values(),

                    'production_area' => $records
                        ->map(function ($record) {
                            return [
                                'production_area' => $record->production_area,
                                'production_area_name' => $record->production_area_name,
                            ];
                        })
                        ->unique('production_area')
                        ->values(),

                    'product_line' => $records
                        ->map(function ($record) {
                            return [
                                'product_line' => $record->product_line,
                                'product_line_name' => $record->product_line_name,
                            ];
                        })
                        ->unique('product_line')
                        ->values(),

                    'stations' => $records
                        ->map(function ($record) {
                            return [
                                'station_to' => $record->station_to,
                                'station_to_name' => $record->station_to_name,
                            ];
                        })
                        ->unique('station_to')
                        ->values(),

                    'series_name' => $records
                        ->map(function ($record) {
                            return [
                                'series_name' => $record->series_name,
                            ];
                        })
                        ->unique('series_name')
                        ->values(),

                    'actual_ratings' => $records
                    ->groupBy('station_to')
                    ->map(function ($stationGroup) {
                        $first = $stationGroup->first();

                        // distinct series for this station across ALL product lines
                        $seriesCount = $stationGroup->pluck('series_name')
                            ->filter()
                            ->unique()
                            ->count(); // 2

                        // one row per product line, each carrying the station's series count
                        return $stationGroup
                            ->unique('product_line')
                            ->map(function ($record) use ($first, $seriesCount) {
                                return [
                                    'product_line'               => $record->product_line,
                                    'product_line_name'          => $record->product_line_name,
                                    'functional_competency'      => $first->station_to,
                                    'functional_competency_name' => $first->station_to_name,
                                    'actual_rating'              => $seriesCount,
                                ];
                            });
                    })
                    ->flatten(1)
                    ->values(),
                ];
            })
            ->values();
    }

    public function collection(){
        return $this->grouped;
    }

    public function map($row): array{
        return [
            // $row['employee_no'],
            $row['employee_name'],
            $row['date_hired'],
            $row['position'],
            $row['section'],
            $row['production_area'],
            $row['product_line'],
            $row['date_of_transfer'],
            $row['validity_period'],

            $row['target_ratings'],
            $row['target_rating_description'],

            // Combine multiple stations into one Excel cell
            collect($row['stations'])
                ->pluck('station_to_name')
                ->filter()
                ->unique()
                ->implode(', '),

            // Combine multiple series names into one Excel cell
            collect($row['series_name'])
                ->pluck('series_name')
                ->filter()
                ->unique()
                ->implode(', '),
        ];
    }

    public function sheets(): array{
        // dd($this->grouped);

        $sheets = [];

        $sheets[] = new SkillCard(
            $this->grouped
        );

        return $sheets;
    }
}
