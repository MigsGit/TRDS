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
    /**
     * actual_ratings[*].rating_source values; SkillCard only applies the series-to-level conversion to series_count.
     */
    const RATING_SOURCE_SERIES_COUNT = 'series_count';
    const RATING_SOURCE_TRAINING_RESULT = 'training_result';

    /**
     * qc_slips.position_category => source of that position's actual rating.
     * Keys are matched trimmed and case-insensitively; positions not listed use DEFAULT_RATING_SOURCE.
     * All training_result positions record their LEVEL in the shared LQC matrix (training item 269).
     */
    const POSITION_RATING_SOURCE = [
        'Inspector'       => self::RATING_SOURCE_TRAINING_RESULT,
        'Technician'      => self::RATING_SOURCE_TRAINING_RESULT,
        'MH'              => self::RATING_SOURCE_TRAINING_RESULT,
        'Supervisor'      => self::RATING_SOURCE_TRAINING_RESULT,
        'Operator'        => self::RATING_SOURCE_SERIES_COUNT,
        'VisualOperator'  => self::RATING_SOURCE_SERIES_COUNT,
        'PartsPrep'       => self::RATING_SOURCE_SERIES_COUNT,
        'MachineOperator' => self::RATING_SOURCE_SERIES_COUNT,
    ];

    const DEFAULT_RATING_SOURCE = self::RATING_SOURCE_SERIES_COUNT;

    protected $employeeNo;
    protected $qcSlipEmployee;
    protected $employeeDetails;
    protected $grouped;

    public function __construct($employeeNo){
        $this->employeeNo = $employeeNo;

        // Step 1: all slips of the employee (no target_rating_settings join, so rows are not multiplied)
        $slips = DB::table('qc_slip_employees')
            ->join('qc_slips', 'qc_slip_employees.qc_slips_id', '=', 'qc_slips.id')
            ->leftJoin('dropdown_master_details as station_dm_details', 'qc_slip_employees.station_to', '=', 'station_dm_details.id')
            ->leftJoin('dropdown_master_details as production_area_dm_details', 'qc_slips.production_area', '=', 'production_area_dm_details.id')
            ->leftJoin('c_lqc_training_item_results as training_result', function ($join) {
                $join->on('qc_slips.id', '=', 'training_result.qc_slips_id')
                    ->where('training_result.training_item_id', '=', 269) //id of the specific training item (LEVEL)
                    ->where('training_result.day_number', '=', 5) //specific day number (DAY 5)
                    ->whereNull('training_result.deleted_at');
            })
            ->where('qc_slip_employees.employee_no', $this->employeeNo)
            ->whereNull('qc_slip_employees.deleted_at')
            ->whereNull('qc_slips.deleted_at')
            ->select(
                'qc_slips.id as qc_slips_id',
                'qc_slips.position_category as position',
                'qc_slips.section_category as section',
                'qc_slips.production_area as production_area',
                'qc_slips.product_line as product_line',
                'qc_slips.series_name',
                'qc_slips.date_of_transfer as date_of_transfer',
                'qc_slip_employees.employee_no',
                'qc_slip_employees.station_to',
                'station_dm_details.dropdown_masters_details as station_to_name',
                'production_area_dm_details.dropdown_masters_details as production_area_name',
                'training_result.qc_slips_id as training_result_qc_slips_id',
                'training_result.result as actual_result'
            )
            ->get();

            // dd($slips);

        if ($slips->isEmpty()) {
            $this->grouped = collect([$this->emptyCard()]);
            return;
        }

        // Step 2: the latest slip (highest qc_slips.id) sets the context and the header fields
        $latest = $slips->sortByDesc('qc_slips_id')->first();
        $contextSection = (string) $latest->section;
        $contextPosition = (string) $latest->position;
        $contextProductionArea = (string) $latest->production_area;

        // Step 3: keep only slips in the same section + position + production area
        $contextSlips = $slips->filter(function ($slip) use ($contextSection, $contextPosition, $contextProductionArea) {
            return (string) $slip->section === $contextSection
                && (string) $slip->position === $contextPosition
                && (string) $slip->production_area === $contextProductionArea;
        });

        // Step 4: match target_rating_settings once, using the context
        $targetSetting = DB::table('target_rating_settings')
            ->where('status', 1)
            ->where('section', $latest->section)
            ->where('position', $latest->position)
            ->where('product_area', $latest->production_area)
            ->orderByDesc('id')
            ->first();

            // dd($targetSetting);

        // Settings product_line is a comma-separated list of ids; parsed the same way as the slip's
        $targetProductLineIds = $targetSetting
            ? self::parseProductLineIds($targetSetting->product_line)
            : [];

        // Step 5: a slip's product_line can hold several ids ("132 | 155"), so expand it into one row per id
        // and keep only the ids listed in the settings ("132 | 999" counts for 132 only)
        $contextSlips = $contextSlips
            ->flatMap(function ($slip) use ($targetProductLineIds) {
                return collect(self::parseProductLineIds($slip->product_line))
                    ->filter(function ($productLineId) use ($targetProductLineIds) {
                        return in_array($productLineId, $targetProductLineIds, true);
                    })
                    ->map(function ($productLineId) use ($slip) {
                        $row = clone $slip;
                        $row->product_line_id = $productLineId;

                        return $row;
                    });
            })
            ->values();

        // Step 6: dedupe after the expansion so "132 | 155" and "132" are duplicates for product line 132.
        // Same product_line_id + station_to + series_name keeps only the rows of the highest slip id
        // (all of that slip's training result rows, none from the older slips).
        $contextSlips = $contextSlips
            ->groupBy(function ($row) {
                return $row->product_line_id . '|' . trim((string) $row->station_to) . '|' . trim((string) $row->series_name);
            })
            ->flatMap(function ($duplicates) {
                $keptSlipId = $duplicates->max('qc_slips_id');

                return $duplicates->where('qc_slips_id', $keptSlipId);
            })
            ->values();

            // dd($contextSlips);

        // Step 7: no settings match or no rows left -> actual vs target cannot be compared
        if (!$targetSetting || $contextSlips->isEmpty()) {
            $this->grouped = collect([$this->emptyCard()]);
            return;
        }

        // Product line names for every id in one query (slip ids are a subset of the settings ids)
        $productLineNames = DB::table('dropdown_master_details')
            ->whereIn('id', $targetProductLineIds)
            ->pluck('dropdown_masters_details', 'id');

        $contextSlips->each(function ($row) use ($productLineNames) {
            $row->product_line_name = $productLineNames->get($row->product_line_id);
        });

        // Target product lines, in the order they are listed in the setting
        $targetRatingProductLines = collect($targetProductLineIds)
            ->map(function ($id) use ($productLineNames) {
                return [
                    'target_product_line' => (int) $id,
                    'target_product_line_name' => $productLineNames->get($id),
                ];
            })
            ->values();

        $targetRatings = DB::table('target_rating_details')
            ->where('target_rating_id', $targetSetting->id)
            ->select('target_name as target_rating_name', 'rating as target_rating_value')
            ->get()
            ->map(function ($detail) {
                return [
                    'target_rating_name' => $detail->target_rating_name,
                    'target_rating_value' => $detail->target_rating_value,
                ];
            })
            ->unique('target_rating_name')
            ->values();

        // Rating descriptions are looked up per station instead of joined onto every slip row
        $targetRatingDescription = $contextSlips
            ->pluck('station_to')
            ->filter()
            ->unique()
            ->flatMap(function ($stationTo) use ($contextPosition) {
                return DB::table('rating_description_settings as rating_descriptions')
                    ->join('rating_details as rating_description_details', 'rating_descriptions.id', '=', 'rating_description_details.rating_description_id')
                    ->where('rating_descriptions.position', $contextPosition)
                    ->where('rating_descriptions.status', 1)
                    ->whereRaw("FIND_IN_SET(?, REPLACE(rating_descriptions.designation, ' ', '')) > 0", [$stationTo])
                    ->select('rating_description_details.level as target_rating_level', 'rating_description_details.description as target_rating_description')
                    ->get();
            })
            ->map(function ($detail) {
                return [
                    'target_rating_level' => $detail->target_rating_level ?: null,
                    'target_rating_description' => $detail->target_rating_description ?: null,
                ];
            })
            ->filter(function ($item) {
                return $item['target_rating_level'] !== null;
            })
            ->sortBy('target_rating_level')
            ->unique('target_rating_level')
            ->values();

        // Step 8: actual ratings per station, then per product line
        $contextSlips->each(function ($slip) {
            $slip->rating_source = self::getRatingSource($slip->position);
        });

        $actualRatings = $contextSlips
            ->groupBy('station_to')
            ->map(function ($stationGroup) {
                return $stationGroup
                    ->groupBy('product_line_id')
                    ->map(function ($productLineGroup) {
                        $record = $productLineGroup->first();

                        // distinct series of THIS product line and station only
                        $seriesCount = $productLineGroup
                            ->where('rating_source', self::RATING_SOURCE_SERIES_COUNT)
                            ->pluck('series_name')
                            ->map(function ($seriesName) {
                                return trim((string) $seriesName);
                            })
                            ->filter()
                            ->unique()
                            ->count();

                        $trainingLevel = $productLineGroup
                            ->where('rating_source', self::RATING_SOURCE_TRAINING_RESULT)
                            ->pluck('actual_result')
                            ->map(function ($result) {
                                return self::normalizeTrainingLevel($result);
                            })
                            ->filter()
                            ->max(); // null when no usable training result

                        // A group mixing both sources uses the training level when one exists, otherwise the series count
                        $ratingSource = $trainingLevel === null && $productLineGroup->contains('rating_source', self::RATING_SOURCE_SERIES_COUNT)
                            ? self::RATING_SOURCE_SERIES_COUNT
                            : self::RATING_SOURCE_TRAINING_RESULT;

                        return [
                            'product_line'               => $record->product_line_id,
                            'product_line_name'          => $record->product_line_name,
                            'functional_competency'      => $record->station_to,
                            'functional_competency_name' => $record->station_to_name,
                            'actual_rating'              => $ratingSource === self::RATING_SOURCE_SERIES_COUNT ? $seriesCount : $trainingLevel,
                            'rating_source'              => $ratingSource,
                        ];
                    })
                    ->values();
            })
            ->flatten(1)
            ->values();

        // Step 9: build the card (single item, same keys as before)
        $employeeRecord = QcSlipEmployee::with([
                'system_one_subcon_emp_info',
                'system_one_hris_emp_info',
            ])
            ->where('employee_no', $this->employeeNo)
            ->first();

        $this->grouped = collect([[
            'employee_name' => $employeeRecord
                ? optional($employeeRecord->employee_info)->EmpName
                : null,

            'date_hired' => $employeeRecord
                ? optional($employeeRecord->employee_info)->DateHired
                : null,

            'position' => $latest->position,
            'section' => $latest->section,
            'date_of_transfer' => $latest->date_of_transfer,
            'validity_period' => $latest->date_of_transfer ? date('Y-m-d', strtotime('+6 months', strtotime($latest->date_of_transfer))) : null,

            'target_rating_product_lines' => $targetRatingProductLines,
            'target_ratings' => $targetRatings,
            'target_rating_description' => $targetRatingDescription,

            'production_area' => $contextSlips
                ->map(function ($record) {
                    return [
                        'production_area' => $record->production_area,
                        'production_area_name' => $record->production_area_name,
                    ];
                })
                ->unique('production_area')
                ->values(),

            // each distinct single product line id, not the raw "132 | 155" string
            'product_line' => $contextSlips
                ->map(function ($record) {
                    return [
                        'product_line' => $record->product_line_id,
                        'product_line_name' => $record->product_line_name,
                    ];
                })
                ->unique('product_line')
                ->values(),

            'stations' => $contextSlips
                ->map(function ($record) {
                    return [
                        'station_to' => $record->station_to,
                        'station_to_name' => $record->station_to_name,
                    ];
                })
                ->unique('station_to')
                ->values(),

            'series_name' => $contextSlips
                ->map(function ($record) {
                    return [
                        'series_name' => $record->series_name,
                    ];
                })
                ->unique('series_name')
                ->values(),

            'actual_ratings' => $actualRatings,

            // training results of the kept slips only
            'training_result' => $contextSlips
                ->map(function ($record) {
                    return [
                        'qc_slip_id' => $record->training_result_qc_slips_id,
                        'actual_result' => $record->actual_result,
                    ];
                })
                ->unique('qc_slip_id')
                ->values(),
        ]]);
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

     /**
     * Placeholder card used when there is no data.
     * Keys match the real structure so SkillCard never hits an undefined index.
     * Employee name / date hired are still looked up directly if possible.
     */
    protected function emptyCard(): array{
        $info = $this->findEmployeeInfo($this->employeeNo);

        return [
            'employee_name'    => $info->EmpName ?? null,
            'date_hired'       => $info->DateHired ?? null,
            'position'         => null,
            'section'          => null,
            'date_of_transfer' => null,
            'validity_period'  => null,

            'target_rating_product_lines' => collect(),
            'target_ratings'              => collect(),
            'target_rating_description'   => collect(),
            'production_area'             => collect(),
            'product_line'                => collect(),
            'stations'                    => collect(),
            'series_name'                 => collect(),
            'actual_ratings'              => collect(),
            'training_result'             => collect(),
        ];
    }

    /**
     * Source of a position's actual rating from POSITION_RATING_SOURCE (trimmed, case-insensitive),
     * or DEFAULT_RATING_SOURCE when the position is not listed. Throws on an invalid configured source.
     */
    public static function getRatingSource($position): string{
        $lookup = [];

        foreach (self::POSITION_RATING_SOURCE as $configuredPosition => $source) {
            self::assertValidRatingSource($source, "position \"{$configuredPosition}\"");
            $lookup[strtolower(trim($configuredPosition))] = $source;
        }

        self::assertValidRatingSource(self::DEFAULT_RATING_SOURCE, 'DEFAULT_RATING_SOURCE');

        return $lookup[strtolower(trim((string) $position))] ?? self::DEFAULT_RATING_SOURCE;
    }

    protected static function assertValidRatingSource($source, string $label): void{
        $validSources = [self::RATING_SOURCE_SERIES_COUNT, self::RATING_SOURCE_TRAINING_RESULT];

        if (!in_array($source, $validSources, true)) {
            throw new \InvalidArgumentException(sprintf(
                'EmployeeSkillCard rating source config: invalid source "%s" for %s. Expected one of: %s.',
                is_scalar($source) ? $source : gettype($source),
                $label,
                implode(', ', $validSources)
            ));
        }
    }

    /**
     * training_result.result is free text, so only values that clearly name a level count:
     * "2", "2.0", "Level 2", "Lvl 2", "L2" => 2. Anything else ("Passed", "", null) => null (ignored).
     */
    protected static function normalizeTrainingLevel($result): ?int{
        if ($result === null) {
            return null;
        }

        if (preg_match('/^(?:level|lvl|l)?\s*-?\s*([1-4])(?:\.0+)?$/i', trim((string) $result), $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Product line ids from a "|"- or ","-separated value: "132 | 155" => ["132", "155"], "132" => ["132"].
     * Used for both qc_slips.product_line and target_rating_settings.product_line so both sides compare equally.
     */
    private static function parseProductLineIds($value): array{
        return collect(preg_split('/[|,]/', (string) $value))
            ->map(function ($id) {
                return trim((string) $id);
            })
            ->filter(function ($id) {
                return $id !== '';
            })
            ->unique()
            ->values()
            ->all();
    }

    protected function findEmployeeInfo($employeeNo){
        foreach (['mysql_systemone', 'mysql_subcon'] as $connection) {
            try {
                $row = DB::connection($connection)
                    ->table('tbl_EmployeeInfo')
                    ->selectRaw("CONCAT_WS(' ', LastName, FirstName) as EmpName, DateHired")
                    ->where('EmpNo', $employeeNo)
                    ->first();

                if ($row) {
                    return $row;
                }
            } catch (\Throwable $e) {
                // ignore and try next connection
            }
        }

        return null;
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
