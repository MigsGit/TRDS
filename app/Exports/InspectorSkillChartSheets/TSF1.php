<?php

namespace App\Exports\InspectorSkillChartSheets;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// use TSF1Layout;
use App\Exports\InspectorSkillChartSheets\TSF1Layout;

/**
 * One sheet of the Inspector Skill Chart export.
 *
 * All data is written cell-by-cell in the AfterSheet event because the template
 * is a hand-drawn matrix (merged header blocks, per-process sub-columns, a
 * trailing summary block) rather than a simple tabular dump. array() therefore
 * intentionally returns an empty dataset; see {@see TSF1Layout} for every fixed
 * layout fact (rows, columns, colors, labels, formula flags).
 *
 * PHP 7.3 target: no arrow functions, typed properties, ??=, match, named args,
 * or constructor promotion anywhere in this file.
 *
 * Assumptions / decisions (see chat for full rationale - confirm if wrong):
 * - A station matches a process by exact station_to_name === process['name'].
 * - categories[].processes[].product_lines is read if present, else
 *   sub_stations is used as a fallback - the parent export and the settings
 *   controller disagree on this key name (see resolveProductLines()).
 * - A sub-station result is written when its name matches a product line; when
 *   a process has no product lines, the station-level result is used, falling
 *   back to the first sub-station result if the station itself has none.
 * - Results are written verbatim (legend values are plain "1".."4").
 * - "Present Allocation" (column G) is filled from sections['main']['section'].
 * - Employees' other sections (sections except 'main') are not rendered yet -
 *   see renderOtherSectionsHook(), gated by TSF1Layout::OTHER_SECTIONS_DATA_ENABLED.
 */
class TSF1 implements FromArray, WithTitle, WithEvents, WithCustomStartCell
{
    /** Single switch for every formula in this file (row totals + footer COUNTIF). */
    const ENABLE_FORMULAS = true;

    /** Shared COUNTIF template for every formula in this file; {range}/{level} are substituted in. */
    const FORMULA_TEMPLATE = '=COUNTIF({range},"{level}")';

    /** AG:AO (other-process matrix) stays blank with no data - flip to zero-fill it like H:Z. */
    const ZERO_DEFAULT_OTHER = false;

    /**
     * One entry per named region, applied as PhpSpreadsheet style arrays via
     * applyTemplateStyles(). Fills for process-based regions are NOT here -
     * see self::PROCESS_FILLS, so a fill follows the process name even when
     * columns shift.
     */
    const STYLE = [
        'title' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => ['rgb' => '000000']],
        ],
        'fixedHeader' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'categoryBand' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'processHeader' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'productLineHeader' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => false, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'levelHeader' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => false, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFCC']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'sectionHeader' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => false, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'employeeData' => [
            'font' => ['name' => 'Calibri', 'size' => 11, 'bold' => false, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'columnAMediumLeft' => [
            'borders' => ['left' => ['borderStyle' => Border::BORDER_MEDIUM]],
        ],
        'serviceWhiteFill' => [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
        ],
        'dateHired' => [
            'numberFormat' => ['formatCode' => 'd-mmm-yy'],
        ],
        'skillData' => [
            'font' => ['name' => 'Calibri', 'size' => 11, 'bold' => false, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'otherProcessData' => [
            'font' => ['name' => 'Arial', 'size' => 12, 'bold' => false, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'rowTotal' => [
            'font' => ['name' => 'Consolas', 'size' => 11, 'bold' => false, 'color' => ['rgb' => '242424']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'footerLabel' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'footerLevelNumber' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => false, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'footerCount' => [
            'font' => ['name' => 'Consolas', 'size' => 11, 'bold' => false, 'color' => ['rgb' => '242424']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'legendTitle' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => ['rgb' => '000000']],
        ],
        'legendNumber' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
        'legendText' => [
            'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => false, 'color' => ['rgb' => '000000']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ],
    ];

    /**
     * header = row 4 fill, subheader = row 5 fill, data = employee-row fill.
     * QS/TU data fill is deliberately different from their header fill (per
     * template). A process absent here gets no fill anywhere.
     */
    const PROCESS_FILLS = [
        'IQC' => ['header' => 'AEAAAA', 'subheader' => 'EDEDED', 'data' => 'AEAAAA'],
        'IPQC' => ['header' => 'F4B084', 'subheader' => 'FCE4D6', 'data' => 'F4B084'],
        'OQC' => ['header' => 'A9D08E', 'subheader' => 'E2EFDA', 'data' => 'A9D08E'],
        'QS' => ['header' => '9BC2E6', 'subheader' => null, 'data' => '8EA9DB'],
        'TU' => ['header' => '9BC2E6', 'subheader' => null, 'data' => '8EA9DB'],
    ];

    /** @var Collection */
    protected $categories;

    /** @var string */
    protected $group;

    /** @var string */
    protected $sheetName;

    /** @var Collection */
    protected $employees;

    /** @var array<string,array> Prebuilt PhpSpreadsheet style arrays, keyed by name used in applyStyles(). */
    protected $styles;

    public function __construct($categories, $group, $sheet, $employees)
    {
        $this->categories = collect($categories);
        $this->group = $group;
        $this->sheetName = $sheet;
        $this->employees = collect($employees);
        $this->styles = $this->buildStyles();
    }

    public function array(): array
    {
        return [];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function registerEvents(): array
    {
        $self = $this;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($self) {
                $self->render($event->sheet->getDelegate());
            },
        ];
    }

    /**
     * Single orchestration method; each step is a self-contained render* method
     * so a template change only ever touches one of them.
     */
    private function render(Worksheet $sheet): void
    {
        $sheet->freezePane('A' . TSF1Layout::EMPLOYEE_START_ROW);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A3);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        $columns = $this->buildDynamicColumns();

        $lastEmployeeRow = $this->renderEmployeeRows($sheet, $columns);

        $nextColumn = $this->renderSkillHeaders($sheet, $columns, $lastEmployeeRow);
        $legendStartColumn = $nextColumn + 1; // same expression already passed to renderLegendHeader() below
        $nextColumn = $this->renderLegendHeader($sheet, $legendStartColumn, $lastEmployeeRow);
        $otherStartColumn = $nextColumn + 1; // same expression already passed to renderOtherProcessHeader() below
        $nextColumn = $this->renderOtherProcessHeader($sheet, $otherStartColumn, $lastEmployeeRow);
        $otherEndColumn = $nextColumn - 1; // renderOtherProcessHeader() returns endColumn + 1
        $otherLegendStartColumn = $nextColumn + 1; // same expression already passed to renderOtherLegendHeader() below
        $lastColumn = $this->renderOtherLegendHeader($sheet, $otherLegendStartColumn, $lastEmployeeRow) - 1;

        $rows = $this->summaryRows($lastEmployeeRow);
        $this->renderSummaryBlock($sheet, $columns, $rows);
        $this->emitDeferredFormulas($sheet, $columns, $lastEmployeeRow, $rows);

        $this->renderSkillRowTotals($sheet, $columns, $legendStartColumn, $lastEmployeeRow);
        $this->renderOtherRowTotals($sheet, $otherStartColumn, $otherEndColumn, $otherLegendStartColumn, $lastEmployeeRow);
        $this->applyFormulaFontStyles($sheet, $columns, $legendStartColumn, $otherLegendStartColumn, $rows, $lastEmployeeRow);

        $this->renderStaticLabels($sheet, $rows, $lastColumn);
        $this->applyFixedHeaderStyles($sheet, $lastColumn);
        $this->setColumnWidths($sheet, $lastColumn);
        $this->setRowHeights($sheet, $rows, $lastEmployeeRow);

        // Runs last so it is the authoritative appearance regardless of the styling above.
        $this->applyTemplateStyles($sheet, $columns, $legendStartColumn, $otherStartColumn, $otherEndColumn, $otherLegendStartColumn, $rows, $lastColumn, $lastEmployeeRow);
    }

    // -----------------------------------------------------------------
    // Dynamic column layout
    // -----------------------------------------------------------------

    /**
     * Flattens categories/processes/product lines into one ordered list of
     * columns. Both the header and the result-writing step iterate this same
     * list, so they can never disagree about which column is which.
     *
     * @return array<int,array{column:int,category:string,process:string,product_line:?string}>
     */
    private function buildDynamicColumns(): array
    {
        $columns = [];
        $column = TSF1Layout::DYNAMIC_START_COLUMN;

        foreach ($this->categories as $category) {
            foreach ($category['processes'] as $process) {
                $lines = $this->resolveProductLines($process);

                if (empty($lines)) {
                    $lines = [null];
                }

                foreach ($lines as $line) {
                    $columns[] = [
                        'column' => $column,
                        'category' => $category['name'],
                        'process' => $process['name'],
                        'product_line' => $line,
                    ];
                    $column++;
                }
            }
        }

        return $columns;
    }

    /**
     * The settings table stores this as a comma list in "sub_station" (or the
     * literal "N/A"); the parent export currently parses it into a
     * `sub_stations` array, while the data contract in this file's docblock
     * says `product_lines`. Accept either until the two are reconciled upstream.
     */
    private function resolveProductLines(array $process): array
    {
        $lines = [];

        if (!empty($process['product_lines'])) {
            $lines = $process['product_lines'];
        } elseif (!empty($process['sub_stations'])) {
            $lines = $process['sub_stations'];
        }

        return array_values(array_filter($lines, function ($line) {
            return $line !== null && strtoupper(trim($line)) !== 'N/A';
        }));
    }

    // -----------------------------------------------------------------
    // Employee rows + results
    // -----------------------------------------------------------------

    /**
     * @param array<int,array> $columns
     */
    private function renderEmployeeRows(Worksheet $sheet, array $columns): int
    {
        $employees = $this->employees->isEmpty() ? collect([$this->blankEmployee()]) : $this->employees;

        $row = TSF1Layout::EMPLOYEE_START_ROW;
        $no = 1;

        foreach ($employees as $employee) {
            $this->renderEmployeeCard($sheet, $row, $no, $employee);
            $this->renderResults($sheet, $row, $employee, $columns);
            $this->renderOtherSectionsHook($sheet, $row, $employee);
            $row++;
            $no++;
        }

        return $row - 1;
    }

    /**
     * Keeps the template structure intact (headers + one card) when there is
     * no data for this group at all.
     */
    private function blankEmployee(): array
    {
        return [
            'employee_no' => '',
            'employee_name' => null,
            'date_hired' => null,
            'length_of_service_year' => null,
            'length_of_service_months' => null,
            'sections' => collect([
                'main' => ['section' => $this->group, 'stations' => collect()],
            ]),
        ];
    }

    private function renderEmployeeCard(Worksheet $sheet, int $row, int $no, array $employee): void
    {
        $mainSection = $this->mainSection($employee);

        $sheet->setCellValue($this->cell(TSF1Layout::COL_NO, $row), $no);
        $sheet->setCellValue($this->cell(TSF1Layout::COL_EMPLOYEE_NO, $row), $employee['employee_no'] ?? '');
        $sheet->setCellValue($this->cell(TSF1Layout::COL_NAME, $row), $employee['employee_name'] ?? '');
        $sheet->setCellValue($this->cell(TSF1Layout::COL_DATE_HIRED, $row), $this->formatDateHired($employee['date_hired'] ?? null));
        $sheet->setCellValue($this->cell(TSF1Layout::COL_SERVICE_YEARS, $row), $employee['length_of_service_year'] ?? '');
        $sheet->setCellValue($this->cell(TSF1Layout::COL_SERVICE_MONTHS, $row), $employee['length_of_service_months'] ?? '');
        $sheet->setCellValue($this->cell(TSF1Layout::COL_PRESENT_ALLOCATION, $row), $mainSection['stations'][0]['station_to_name'] ?? '');
    }

    /**
     * @return array{section:?string,stations:iterable}
     */
    private function mainSection(array $employee): array
    {
        if (empty($employee['sections']) || !isset($employee['sections']['main'])) {
            return ['section' => null, 'stations' => []];
        }

        $main = $employee['sections']['main'];

        return [
            'section' => $main['section'] ?? null,
            'stations' => $main['stations'] ?? [],
        ];
    }

    /**
     * @param array<int,array> $columns
     */
    private function renderResults(Worksheet $sheet, int $row, array $employee, array $columns): void
    {
        $lookup = $this->buildEmployeeLookup($employee);

        foreach ($columns as $col) {
            $station = $lookup['stations'][$col['process']] ?? null;
            $result = $station !== null ? $this->resolveResult($lookup, $col, $station) : null;

            // Every H:Z cell gets a value - 0 when untrained - so the column never has a blank gap.
            $sheet->setCellValue($this->cell($col['column'], $row), $this->normalizeResult($result));
        }
    }

    /**
     * 0 for null/empty/whitespace; numeric strings become real ints so
     * COUNTIF keeps matching "1"."4" either way. A non-empty, non-numeric
     * value is returned unchanged rather than forced to 0.
     *
     * @return int|mixed
     */
    private function normalizeResult($value)
    {
        if ($value === null) {
            return 0;
        }

        $trimmed = is_string($value) ? trim($value) : $value;

        if ($trimmed === '') {
            return 0;
        }

        return is_numeric($trimmed) ? (int) $trimmed : $value;
    }

    /**
     * @param array{stations:array,sub_stations:array} $lookup
     */
    private function resolveResult(array $lookup, array $col, array $station)
    {
        if ($col['product_line'] !== null) {
            $key = $col['process'] . '|' . $col['product_line'];

            return $lookup['sub_stations'][$key] ?? null;
        }

        if (($station['training_result'] ?? null) !== null) {
            return $station['training_result'];
        }

        // No station-level result and no product line column to match against:
        // fall back to the first sub-station result so the cell isn't blank
        // just because the station happens to track sub-stations.
        $subStations = $station['sub_stations'] ?? [];
        $first = is_array($subStations) ? ($subStations[0] ?? null) : null;

        return $first['training_result'] ?? null;
    }

    /**
     * station_to_name => station, and "station_to_name|sub_station_name" => result,
     * built once per employee so renderResults() is a plain array lookup per
     * cell instead of scanning stations/sub-stations per column.
     *
     * @return array{stations:array<string,array>,sub_stations:array<string,mixed>}
     */
    private function buildEmployeeLookup(array $employee): array
    {
        $stations = [];
        $subStations = [];

        foreach ($this->mainSection($employee)['stations'] as $station) {
            $name = $station['station_to_name'] ?? null;

            if ($name === null) {
                continue;
            }

            $stations[$name] = $station;

            foreach ($station['sub_stations'] ?? [] as $sub) {
                if (!isset($sub['name'])) {
                    continue;
                }

                $subStations[$name . '|' . $sub['name']] = $sub['training_result'] ?? null;
            }
        }

        return ['stations' => $stations, 'sub_stations' => $subStations];
    }

    /**
     * Disabled hook for the employee's non-"main" sections (sections[] except
     * 'main'). No column exists for this in the template yet - flip
     * TSF1Layout::OTHER_SECTIONS_DATA_ENABLED once one is designed.
     */
    private function renderOtherSectionsHook(Worksheet $sheet, int $row, array $employee): void
    {
        if (!TSF1Layout::OTHER_SECTIONS_DATA_ENABLED) {
            return;
        }

        // TODO(enable): map $employee['sections'] (all keys except 'main') to
        // the OTHER PROCESS SKILLS columns rendered by renderOtherProcessHeader().
    }

    private function formatDateHired(?string $dateHired): string
    {
        if (empty($dateHired)) {
            return '';
        }

        return Carbon::parse($dateHired)->format(TSF1Layout::DATE_FORMAT);
    }

    // -----------------------------------------------------------------
    // Headers
    // -----------------------------------------------------------------

    /**
     * @param array<int,array> $columns
     * @return int the next unused column index
     */
    private function renderSkillHeaders(Worksheet $sheet, array $columns, int $lastEmployeeRow): int
    {
        if (empty($columns)) {
            return TSF1Layout::DYNAMIC_START_COLUMN;
        }

        foreach (collect($columns)->groupBy('category') as $categoryName => $categoryColumns) {
            $this->renderCategoryHeader($sheet, $categoryName, $categoryColumns, $lastEmployeeRow);
        }

        return collect($columns)->last()['column'] + 1;
    }

    private function renderCategoryHeader(Worksheet $sheet, string $categoryName, Collection $categoryColumns, int $lastEmployeeRow): void
    {
        $start = $categoryColumns->first()['column'];
        $end = $categoryColumns->last()['column'];

        $this->mergeAndSet($sheet, $start, TSF1Layout::HEADER_ROW_1, $end, TSF1Layout::HEADER_ROW_1, $categoryName);
        $this->applyStyles($sheet, $this->range($start, TSF1Layout::HEADER_ROW_1, $end, TSF1Layout::HEADER_ROW_1), ['yellowFill']);
        $this->applyStyles($sheet, $this->range($start, TSF1Layout::HEADER_ROW_1, $end, TSF1Layout::HEADER_ROW_3), ['borderThin']);
        $this->applyStyles($sheet, $this->range($start, TSF1Layout::EMPLOYEE_START_ROW, $end, $lastEmployeeRow), ['borderThin', 'alignCenter']);

        $colors = TSF1Layout::CATEGORY_COLOR_MAP[$categoryName] ?? null;
        $header2Index = 0;
        $header3Index = 0;

        foreach ($categoryColumns->groupBy('process') as $processName => $processColumns) {
            $this->renderProcessHeader($sheet, $processName, $processColumns, $colors, $header2Index, $header3Index);

            $header2Index++;
            if ($processColumns->first()['product_line'] !== null) {
                $header3Index++;
            }
        }
    }

    private function renderProcessHeader(Worksheet $sheet, string $processName, Collection $processColumns, ?array $colors, int $header2Index, int $header3Index): void
    {
        $start = $processColumns->first()['column'];
        $end = $processColumns->last()['column'];
        $hasProductLines = $processColumns->first()['product_line'] !== null;

        if (!$hasProductLines) {
            $this->mergeAndSet($sheet, $start, TSF1Layout::HEADER_ROW_2, $end, TSF1Layout::HEADER_ROW_3, $processName);

            if ($colors) {
                $this->applyStyles($sheet, $this->cell($start, TSF1Layout::HEADER_ROW_2), [$colors['header2'][$header2Index % count($colors['header2'])]]);
            }

            return;
        }

        $this->mergeAndSet($sheet, $start, TSF1Layout::HEADER_ROW_2, $end, TSF1Layout::HEADER_ROW_2, $processName);

        if ($colors) {
            $this->applyStyles($sheet, $this->range($start, TSF1Layout::HEADER_ROW_2, $end, TSF1Layout::HEADER_ROW_2), [$colors['header2'][$header2Index % count($colors['header2'])]]);
        }

        foreach ($processColumns as $col) {
            $sheet->setCellValue($this->cell($col['column'], TSF1Layout::HEADER_ROW_3), $col['product_line']);

            if ($colors) {
                $this->applyStyles($sheet, $this->cell($col['column'], TSF1Layout::HEADER_ROW_3), [$colors['header3'][$header3Index % count($colors['header3'])], 'fontSmall', 'alignCenter']);
            }
        }
    }

    private function renderLegendHeader(Worksheet $sheet, int $startColumn, int $lastEmployeeRow): int
    {
        $endColumn = $startColumn + 3;

        $this->mergeAndSet($sheet, $startColumn, TSF1Layout::HEADER_ROW_1, $endColumn, TSF1Layout::HEADER_ROW_2, 'Total number of skills of QC Inspectors (in terms of skill legend)');
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::HEADER_ROW_1, $endColumn, TSF1Layout::HEADER_ROW_2), ['yellowFill']);
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::HEADER_ROW_3, $endColumn, TSF1Layout::HEADER_ROW_3), ['lightYellowFill']);
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::HEADER_ROW_1, $endColumn, TSF1Layout::HEADER_ROW_3), ['borderThin']);
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::EMPLOYEE_START_ROW, $endColumn, $lastEmployeeRow), ['borderThin', 'alignCenter']);

        foreach (array_keys(TSF1Layout::LEGEND) as $level) {
            $sheet->setCellValue($this->cell($startColumn, TSF1Layout::HEADER_ROW_3), (string) $level);
            $startColumn++;
        }

        return $endColumn + 1;
    }

    private function renderOtherProcessHeader(Worksheet $sheet, int $startColumn, int $lastEmployeeRow): int
    {
        $otherSections = array_values(array_diff(TSF1Layout::OTHER_SECTIONS, [$this->group]));
        $repeats = ['IQC', 'IPQC', 'OQC'];
        $endColumn = $startColumn + (count($otherSections) * count($repeats)) - 1;

        $this->mergeAndSet($sheet, $startColumn, TSF1Layout::HEADER_ROW_1, $endColumn, TSF1Layout::HEADER_ROW_1, 'OTHER PROCESS / SYSTEM SKILLS');
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::HEADER_ROW_1, $endColumn, TSF1Layout::HEADER_ROW_1), ['yellowFill']);
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::HEADER_ROW_1, $endColumn, TSF1Layout::HEADER_ROW_3), ['borderThin']);
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::EMPLOYEE_START_ROW, $endColumn, $lastEmployeeRow), ['borderThin', 'alignCenter']);

        $column = $startColumn;
        foreach ($repeats as $label) {
            $groupEnd = $column + count($otherSections) - 1;
            $this->mergeAndSet($sheet, $column, TSF1Layout::HEADER_ROW_2, $groupEnd, TSF1Layout::HEADER_ROW_2, $label);

            foreach ($otherSections as $section) {
                $sheet->setCellValue($this->cell($column, TSF1Layout::HEADER_ROW_3), $section);
                $column++;
            }
        }

        return $endColumn + 1;
    }

    private function renderOtherLegendHeader(Worksheet $sheet, int $startColumn, int $lastEmployeeRow): int
    {
        $endColumn = $startColumn + 3;

        $this->mergeAndSet($sheet, $startColumn, TSF1Layout::HEADER_ROW_1, $endColumn, TSF1Layout::HEADER_ROW_2, 'Total number of skills of QC Inspectors on other process (in terms of skill legend)');
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::HEADER_ROW_1, $endColumn, TSF1Layout::HEADER_ROW_2), ['yellowFill']);
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::HEADER_ROW_3, $endColumn, TSF1Layout::HEADER_ROW_3), ['lightYellowFill']);
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::HEADER_ROW_1, $endColumn, TSF1Layout::HEADER_ROW_3), ['borderThin']);
        $this->applyStyles($sheet, $this->range($startColumn, TSF1Layout::EMPLOYEE_START_ROW, $endColumn, $lastEmployeeRow), ['borderThin', 'alignCenter']);

        foreach (array_keys(TSF1Layout::LEGEND) as $level) {
            $sheet->setCellValue($this->cell($startColumn, TSF1Layout::HEADER_ROW_3), (string) $level);
            $startColumn++;
        }

        return $endColumn + 1;
    }

    // -----------------------------------------------------------------
    // Summary block (repeats the category/process headers below the data)
    // -----------------------------------------------------------------

    /**
     * @param array<int,array> $columns
     * @param array<string,int> $rows
     */
    private function renderSummaryBlock(Worksheet $sheet, array $columns, array $rows): void
    {
        foreach (collect($columns)->groupBy('category') as $categoryName => $categoryColumns) {
            $start = $categoryColumns->first()['column'];
            $end = $categoryColumns->last()['column'];

            $this->mergeAndSet($sheet, $start, $rows['title_row_1'], $end, $rows['title_row_1'], $categoryName);
            $this->applyStyles($sheet, $this->range($start, $rows['title_row_1'], $end, $rows['title_row_1']), ['yellowFill', 'borderThin']);
            $this->applyStyles($sheet, $this->range($start, $rows['title_row_1'], $end, $rows['title_row_2']), ['fontSmallBold', 'alignCenter', 'borderThin']);
            $this->applyStyles($sheet, $this->range($start, $rows['title_row_3'], $end, $rows['title_row_3']), ['fontSmall', 'borderThin', 'alignCenter']);
            $this->applyStyles($sheet, $this->range($start, $rows['total_row_1'], $end, $rows['total_row_4']), ['borderThin', 'alignCenter']);

            $colors = TSF1Layout::CATEGORY_COLOR_MAP[$categoryName] ?? null;
            $header2Index = 0;

            foreach ($categoryColumns->groupBy('process') as $processName => $processColumns) {
                $pStart = $processColumns->first()['column'];
                $pEnd = $processColumns->last()['column'];
                $hasProductLines = $processColumns->first()['product_line'] !== null;

                if (!$hasProductLines) {
                    $this->mergeAndSet($sheet, $pStart, $rows['title_row_2'], $pEnd, $rows['title_row_3'], $processName);
                } else {
                    $this->mergeAndSet($sheet, $pStart, $rows['title_row_2'], $pEnd, $rows['title_row_2'], $processName);
                    foreach ($processColumns as $col) {
                        $sheet->setCellValue($this->cell($col['column'], $rows['title_row_3']), $col['product_line']);
                    }
                }

                if ($colors) {
                    $endRowForColor = $hasProductLines ? $rows['title_row_2'] : $rows['title_row_3'];
                    $this->applyStyles($sheet, $this->range($pStart, $rows['title_row_2'], $pEnd, $endRowForColor), [$colors['header2'][$header2Index % count($colors['header2'])]]);
                }

                $header2Index++;
            }
        }
    }

    // -----------------------------------------------------------------
    // Deferred formulas - see TSF1Layout::FORMULA_FLAGS / FORMULAS
    // -----------------------------------------------------------------

    /**
     * @param array<int,array> $columns
     * @param array<string,int> $rows
     */
    private function emitDeferredFormulas(Worksheet $sheet, array $columns, int $lastEmployeeRow, array $rows): void
    {
        $totalRowsByLevel = [$rows['total_row_1'], $rows['total_row_2'], $rows['total_row_3'], $rows['total_row_4']];
        $enabled = self::ENABLE_FORMULAS; // unified with the row-total formulas (see renderSkillRowTotals())

        foreach ($columns as $col) {
            $range = $this->range($col['column'], TSF1Layout::EMPLOYEE_START_ROW, $col['column'], $lastEmployeeRow);

            foreach (array_keys(TSF1Layout::LEGEND) as $level) {
                $target = $this->cell($col['column'], $totalRowsByLevel[$level - 1]);

                // TODO(enable): flip TSF1Layout::FORMULA_FLAGS['skill_level_count'] to true
                // to replace this static placeholder with a live COUNTIF over $range -
                // the count of employees who scored $level in this specific skill column.
                if ($enabled) {
                    $sheet->setCellValue($target, "=COUNTIF({$range},\"{$level}\")");
                } else {
                    $sheet->setCellValue($target, (string) $level);
                }
            }
        }
    }

    /**
     * Row-level legend totals for the skill area (template AB:AE): how many
     * of this employee's own skill cells are at each legend level. Per spec,
     * no formulas are written at all when there are no employees.
     *
     * @param array<int,array> $columns
     */
    private function renderSkillRowTotals(Worksheet $sheet, array $columns, int $legendStartColumn, int $lastEmployeeRow): void
    {
        if (!self::ENABLE_FORMULAS || $this->employees->isEmpty() || empty($columns)) {
            return;
        }

        $firstColumn = $columns[0]['column'];
        $lastColumn = end($columns)['column'];

        for ($row = TSF1Layout::EMPLOYEE_START_ROW; $row <= $lastEmployeeRow; $row++) {
            $this->writeRowTotalFormulas($sheet, $legendStartColumn, $row, $this->range($firstColumn, $row, $lastColumn, $row));
        }
    }

    /**
     * Row-level legend totals for the "OTHER PROCESS / SYSTEM SKILLS" matrix
     * (template AQ:AT) - same mechanics as renderSkillRowTotals().
     */
    private function renderOtherRowTotals(Worksheet $sheet, int $otherStartColumn, int $otherEndColumn, int $otherLegendStartColumn, int $lastEmployeeRow): void
    {
        if (!self::ENABLE_FORMULAS || $this->employees->isEmpty()) {
            return;
        }

        for ($row = TSF1Layout::EMPLOYEE_START_ROW; $row <= $lastEmployeeRow; $row++) {
            $this->writeRowTotalFormulas($sheet, $otherLegendStartColumn, $row, $this->range($otherStartColumn, $row, $otherEndColumn, $row));
        }
    }

    /** One COUNTIF per legend level, written into the 4 columns starting at $legendStartColumn. */
    private function writeRowTotalFormulas(Worksheet $sheet, int $legendStartColumn, int $row, string $range): void
    {
        $column = $legendStartColumn;

        foreach (array_keys(TSF1Layout::LEGEND) as $level) {
            $formula = str_replace(['{range}', '{level}'], [$range, $level], self::FORMULA_TEMPLATE);
            $sheet->setCellValue($this->cell($column, $row), $formula);
            $column++;
        }
    }

    /**
     * Consolas 11 / #242424 font for every formula cell (row totals + footer
     * totals) - the only styling these cells were missing; border/alignment
     * are already applied by the header methods that own those ranges.
     *
     * @param array<int,array> $columns
     * @param array<string,int> $rows
     */
    private function applyFormulaFontStyles(Worksheet $sheet, array $columns, int $legendStartColumn, int $otherLegendStartColumn, array $rows, int $lastEmployeeRow): void
    {
        $legendWidth = count(TSF1Layout::LEGEND) - 1;

        $this->applyStyles($sheet, $this->range($legendStartColumn, TSF1Layout::EMPLOYEE_START_ROW, $legendStartColumn + $legendWidth, $lastEmployeeRow), ['fontFormula']);
        $this->applyStyles($sheet, $this->range($otherLegendStartColumn, TSF1Layout::EMPLOYEE_START_ROW, $otherLegendStartColumn + $legendWidth, $lastEmployeeRow), ['fontFormula']);

        foreach ($columns as $col) {
            $this->applyStyles($sheet, $this->range($col['column'], $rows['total_row_1'], $col['column'], $rows['total_row_4']), ['fontFormula']);
        }
    }

    // -----------------------------------------------------------------
    // Template style pass (self::STYLE / self::PROCESS_FILLS) - runs last, see render()
    // -----------------------------------------------------------------

    /**
     * @param array<int,array> $columns
     * @param array<string,int> $rows
     */
    private function applyTemplateStyles(Worksheet $sheet, array $columns, int $legendStartColumn, int $otherStartColumn, int $otherEndColumn, int $otherLegendStartColumn, array $rows, int $lastColumn, int $lastEmployeeRow): void
    {
        $legendEnd = $legendStartColumn + count(TSF1Layout::LEGEND) - 1;
        $otherLegendEnd = $otherLegendStartColumn + count(TSF1Layout::LEGEND) - 1;

        $sheet->getStyle('A1')->applyFromArray(self::STYLE['title']);
        $sheet->getStyle('A2')->applyFromArray(self::STYLE['title']);
        $sheet->getStyle('A3:G5')->applyFromArray(self::STYLE['fixedHeader']);

        $this->applyCategoryProcessStyles($sheet, $columns, TSF1Layout::HEADER_ROW_1, TSF1Layout::HEADER_ROW_2, TSF1Layout::HEADER_ROW_3);

        // The 3 fixed blocks (legend / other-process / other-legend) get the same category-band look.
        $sheet->getStyle($this->range($legendStartColumn, TSF1Layout::HEADER_ROW_1, $legendEnd, TSF1Layout::HEADER_ROW_2))->applyFromArray(self::STYLE['categoryBand']);
        $sheet->getStyle($this->range($otherStartColumn, TSF1Layout::HEADER_ROW_1, $otherEndColumn, TSF1Layout::HEADER_ROW_1))->applyFromArray(self::STYLE['categoryBand']);
        $sheet->getStyle($this->range($otherLegendStartColumn, TSF1Layout::HEADER_ROW_1, $otherLegendEnd, TSF1Layout::HEADER_ROW_2))->applyFromArray(self::STYLE['categoryBand']);

        // Template exception: the IQC/IPQC/OQC labels here get no fill, unlike the main table's.
        $sheet->getStyle($this->range($otherStartColumn, TSF1Layout::HEADER_ROW_2, $otherEndColumn, TSF1Layout::HEADER_ROW_2))->applyFromArray(self::STYLE['processHeader']);
        $sheet->getStyle($this->range($otherStartColumn, TSF1Layout::HEADER_ROW_3, $otherEndColumn, TSF1Layout::HEADER_ROW_3))->applyFromArray(self::STYLE['sectionHeader']);

        $sheet->getStyle($this->range($legendStartColumn, TSF1Layout::HEADER_ROW_3, $legendEnd, TSF1Layout::HEADER_ROW_3))->applyFromArray(self::STYLE['levelHeader']);
        $sheet->getStyle($this->range($otherLegendStartColumn, TSF1Layout::HEADER_ROW_3, $otherLegendEnd, TSF1Layout::HEADER_ROW_3))->applyFromArray(self::STYLE['levelHeader']);

        if ($lastEmployeeRow >= TSF1Layout::EMPLOYEE_START_ROW) {
            $this->applyEmployeeDataStyles($sheet, $columns, $legendStartColumn, $legendEnd, $otherStartColumn, $otherEndColumn, $otherLegendStartColumn, $otherLegendEnd, $lastEmployeeRow);
        }

        $this->applyFooterStyles($sheet, $columns, $rows);

        $sheet->getStyle("E{$rows['legend_row_1']}")->applyFromArray(self::STYLE['legendTitle']);

        foreach (TSF1Layout::LEGEND as $level => $description) {
            $legendRow = $rows['legend_row_' . ($level + 1)];
            $sheet->getStyle("E{$legendRow}")->applyFromArray(self::STYLE['legendNumber']);
            $sheet->getStyle("F{$legendRow}:I{$legendRow}")->applyFromArray(self::STYLE['legendText']);
        }
    }

    /**
     * Category band (row/title 1) + process row (2) + product-line row (3),
     * shared by the main header and the footer repeat of the same header.
     *
     * @param array<int,array> $columns
     */
    private function applyCategoryProcessStyles(Worksheet $sheet, array $columns, int $categoryRow, int $processRow, int $productLineRow): void
    {
        foreach (collect($columns)->groupBy('category') as $categoryColumns) {
            $start = $categoryColumns->first()['column'];
            $end = $categoryColumns->last()['column'];
            $sheet->getStyle($this->range($start, $categoryRow, $end, $categoryRow))->applyFromArray(self::STYLE['categoryBand']);
        }

        foreach (collect($columns)->groupBy('process') as $processName => $processColumns) {
            $start = $processColumns->first()['column'];
            $end = $processColumns->last()['column'];
            $hasProductLines = $processColumns->first()['product_line'] !== null;
            $endRow = $hasProductLines ? $processRow : $productLineRow;

            $style = self::STYLE['processHeader'];
            $fill = self::PROCESS_FILLS[$processName]['header'] ?? null;
            if ($fill !== null) {
                $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fill]];
            }
            $sheet->getStyle($this->range($start, $processRow, $end, $endRow))->applyFromArray($style);

            if ($hasProductLines) {
                $subStyle = self::STYLE['productLineHeader'];
                $subFill = self::PROCESS_FILLS[$processName]['subheader'] ?? null;
                if ($subFill !== null) {
                    $subStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $subFill]];
                }
                $sheet->getStyle($this->range($start, $productLineRow, $end, $productLineRow))->applyFromArray($subStyle);
            }
        }
    }

    /**
     * @param array<int,array> $columns
     */
    private function applyEmployeeDataStyles(Worksheet $sheet, array $columns, int $legendStartColumn, int $legendEnd, int $otherStartColumn, int $otherEndColumn, int $otherLegendStartColumn, int $otherLegendEnd, int $lastEmployeeRow): void
    {
        $firstRow = TSF1Layout::EMPLOYEE_START_ROW;

        $sheet->getStyle($this->range(TSF1Layout::COL_NO, $firstRow, TSF1Layout::COL_PRESENT_ALLOCATION, $lastEmployeeRow))->applyFromArray(self::STYLE['employeeData']);
        $sheet->getStyle($this->range(TSF1Layout::COL_NO, $firstRow, TSF1Layout::COL_NO, $lastEmployeeRow))->applyFromArray(self::STYLE['columnAMediumLeft']);
        $sheet->getStyle($this->range(TSF1Layout::COL_SERVICE_YEARS, $firstRow, TSF1Layout::COL_SERVICE_MONTHS, $lastEmployeeRow))->applyFromArray(self::STYLE['serviceWhiteFill']);
        $sheet->getStyle($this->range(TSF1Layout::COL_DATE_HIRED, $firstRow, TSF1Layout::COL_DATE_HIRED, $lastEmployeeRow))->applyFromArray(self::STYLE['dateHired']);

        foreach (collect($columns)->groupBy('process') as $processName => $processColumns) {
            $start = $processColumns->first()['column'];
            $end = $processColumns->last()['column'];

            $style = self::STYLE['skillData'];
            $fill = self::PROCESS_FILLS[$processName]['data'] ?? null;
            if ($fill !== null) {
                $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fill]];
            }
            $sheet->getStyle($this->range($start, $firstRow, $end, $lastEmployeeRow))->applyFromArray($style);
        }

        $sheet->getStyle($this->range($otherStartColumn, $firstRow, $otherEndColumn, $lastEmployeeRow))->applyFromArray(self::STYLE['otherProcessData']);
        $sheet->getStyle($this->range($legendStartColumn, $firstRow, $legendEnd, $lastEmployeeRow))->applyFromArray(self::STYLE['rowTotal']);
        $sheet->getStyle($this->range($otherLegendStartColumn, $firstRow, $otherLegendEnd, $lastEmployeeRow))->applyFromArray(self::STYLE['rowTotal']);
    }

    /**
     * @param array<int,array> $columns
     * @param array<string,int> $rows
     */
    private function applyFooterStyles(Worksheet $sheet, array $columns, array $rows): void
    {
        $this->applyCategoryProcessStyles($sheet, $columns, $rows['title_row_1'], $rows['title_row_2'], $rows['title_row_3']);

        $sheet->getStyle("E{$rows['total_row_1']}:F{$rows['total_row_4']}")->applyFromArray(self::STYLE['footerLabel']);
        $sheet->getStyle("G{$rows['total_row_1']}:G{$rows['total_row_4']}")->applyFromArray(self::STYLE['footerLevelNumber']);

        foreach ($columns as $col) {
            $sheet->getStyle($this->range($col['column'], $rows['total_row_1'], $col['column'], $rows['total_row_4']))->applyFromArray(self::STYLE['footerCount']);
        }
    }

    // -----------------------------------------------------------------
    // Static (non-dynamic) labels, titles and legend text
    // -----------------------------------------------------------------

    /**
     * @param array<string,int> $rows
     */
    private function renderStaticLabels(Worksheet $sheet, array $rows, int $lastColumn): void
    {
        $lastColumnLetter = Coordinate::stringFromColumnIndex($lastColumn);

        $sheet->mergeCells("A1:{$lastColumnLetter}1");
        $sheet->setCellValue('A1', strtoupper($this->sheetName) . ' QC INSPECTORS SKILL CHART');

        $sheet->mergeCells("A2:{$lastColumnLetter}2");
        $sheet->setCellValue('A2', 'Updated as of ' . Carbon::now()->format(TSF1Layout::REPORT_SUBTITLE_DATE_FORMAT));

        $sheet->mergeCells('A3:A5');
        $sheet->setCellValue('A3', 'No.');

        $sheet->mergeCells('B3:B5');
        $sheet->setCellValue('B3', 'Employee No.');

        $sheet->mergeCells('C3:C5');
        $sheet->setCellValue('C3', 'Name');

        $sheet->mergeCells('D3:D5');
        $sheet->setCellValue('D3', 'Date Hired');

        $sheet->mergeCells('E3:F4');
        $sheet->setCellValue('E3', 'Length of Service');
        $sheet->setCellValue('E5', 'Years');
        $sheet->setCellValue('F5', 'Months');

        $sheet->mergeCells('G3:G5');
        $sheet->setCellValue('G3', 'Present Allocation');

        $sheet->mergeCells("E{$rows['total_row_1']}:F{$rows['total_row_4']}");
        $sheet->setCellValue("E{$rows['total_row_1']}", 'Total number of certified QC Inspectors per skill (in terms of skill legend)');

        foreach (array_keys(TSF1Layout::LEGEND) as $i => $level) {
            $totalRow = $rows['total_row_' . ($i + 1)];
            $sheet->setCellValue("G{$totalRow}", (string) $level);
        }

        $sheet->setCellValue("E{$rows['legend_row_1']}", 'LEGEND');

        foreach (TSF1Layout::LEGEND as $level => $description) {
            $legendRow = $rows['legend_row_' . ($level + 1)];
            $sheet->setCellValue("E{$legendRow}", (string) $level);
            $sheet->mergeCells("F{$legendRow}:I{$legendRow}");
            $sheet->setCellValue("F{$legendRow}", $description);
        }
    }

    private function applyFixedHeaderStyles(Worksheet $sheet, int $lastColumn): void
    {
        $lastColumnLetter = Coordinate::stringFromColumnIndex($lastColumn);

        $this->applyStyles($sheet, 'A1:A2', ['fontSmallBold']);
        $this->applyStyles($sheet, "A3:{$lastColumnLetter}3", ['fontSmallBold', 'alignCenter']);
        $this->applyStyles($sheet, 'A3:G5', ['fontSmallBold', 'alignCenter', 'borderThin']);
        $this->applyStyles($sheet, "H3:{$lastColumnLetter}4", ['fontSmallBold', 'alignCenter']);
        $this->applyStyles($sheet, "H5:{$lastColumnLetter}5", ['fontSmall', 'alignCenter']);
    }

    // -----------------------------------------------------------------
    // Row/column math - kept out of the render methods so offsets live in one place
    // -----------------------------------------------------------------

    /**
     * @return array<string,int>
     */
    private function summaryRows(int $lastEmployeeRow): array
    {
        $blankRow = $lastEmployeeRow + 1;
        $titleRow1 = $blankRow + 1;
        $titleRow2 = $blankRow + 2;
        $titleRow3 = $blankRow + 3;
        $totalRow1 = $titleRow3 + 1;
        $totalRow4 = $titleRow3 + 4;
        $legendRow1 = $totalRow4 + 3;

        return [
            'title_row_1' => $titleRow1,
            'title_row_2' => $titleRow2,
            'title_row_3' => $titleRow3,
            'total_row_1' => $totalRow1,
            'total_row_2' => $titleRow3 + 2,
            'total_row_3' => $titleRow3 + 3,
            'total_row_4' => $totalRow4,
            'legend_row_1' => $legendRow1,
            'legend_row_2' => $legendRow1 + 1,
            'legend_row_3' => $legendRow1 + 2,
            'legend_row_4' => $legendRow1 + 3,
            'legend_row_5' => $legendRow1 + 4,
        ];
    }

    private function setRowHeights(Worksheet $sheet, array $rows, int $lastEmployeeRow): void
    {
        $sheet->getRowDimension(1)->setRowHeight(24.95);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20.1);
        $sheet->getRowDimension(4)->setRowHeight(20.1);
        $sheet->getRowDimension(5)->setRowHeight(24.95);

        for ($row = TSF1Layout::EMPLOYEE_START_ROW; $row <= $lastEmployeeRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(24.95);
        }

        $sheet->getRowDimension($rows['title_row_1'])->setRowHeight(20.1);
        $sheet->getRowDimension($rows['title_row_2'])->setRowHeight(20.1);
        $sheet->getRowDimension($rows['title_row_3'])->setRowHeight(25.5);
    }

    private function setColumnWidths(Worksheet $sheet, int $lastColumn): void
    {
        foreach (TSF1Layout::COLUMN_WIDTHS as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        for ($column = TSF1Layout::DYNAMIC_START_COLUMN; $column <= $lastColumn; $column++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setWidth(TSF1Layout::DYNAMIC_COLUMN_WIDTH);
        }
    }

    // -----------------------------------------------------------------
    // Low-level cell/style helpers
    // -----------------------------------------------------------------

    private function cell(int $column, int $row): string
    {
        return Coordinate::stringFromColumnIndex($column) . $row;
    }

    private function range(int $startColumn, int $startRow, int $endColumn, int $endRow): string
    {
        return $this->cell($startColumn, $startRow) . ':' . $this->cell($endColumn, $endRow);
    }

    private function mergeAndSet(Worksheet $sheet, int $startColumn, int $startRow, int $endColumn, int $endRow, string $value): void
    {
        if ($startColumn !== $endColumn || $startRow !== $endRow) {
            $sheet->mergeCells($this->range($startColumn, $startRow, $endColumn, $endRow));
        }

        $sheet->setCellValue($this->cell($startColumn, $startRow), $value);
    }

    private function applyStyles(Worksheet $sheet, string $range, array $styleNames): void
    {
        $style = [];

        foreach ($styleNames as $name) {
            $style = array_replace_recursive($style, $this->styles[$name]);
        }

        $sheet->getStyle($range)->applyFromArray($style);
    }

    /**
     * @return array<string,array>
     */
    private function buildStyles(): array
    {
        $colors = TSF1Layout::COLORS;

        $styles = [];
        foreach ($colors as $name => $hex) {
            $styles[$name . 'Fill'] = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $hex],
                ],
            ];
        }

        $styles['fontSmall'] = ['font' => ['bold' => false, 'size' => 10]];
        $styles['fontSmallBold'] = ['font' => ['bold' => true, 'size' => 10]];
        $styles['alignCenter'] = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
        $styles['borderThin'] = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $styles['fontFormula'] = [
            'font' => [
                'name' => 'Consolas',
                'size' => 11,
                'color' => ['rgb' => '242424'],
            ],
        ];

        return $styles;
    }
}
