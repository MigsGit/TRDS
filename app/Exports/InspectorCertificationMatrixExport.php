<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class InspectorCertificationMatrixExport implements FromCollection, WithEvents, WithTitle, WithColumnWidths
{
    protected $personnel;
    protected $productLines;
    protected $section;
    
    // Sub-processes under each product line header
    protected $processes = ['IQC', 'IPQC', 'OQC'];

    public function __construct($personnel, $productLines, $section)
    {
        $this->personnel = $personnel;
        
        // Ensure $productLines contains ONLY the user-selected product lines
        $this->productLines = collect($productLines)->values();
        $this->section = $section;
    }

    public function title(): string
    {
        return 'QC INSPECTORS SKILL MATRIX';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No.
            'B' => 22,  // Employee No.
            'C' => 28,  // Name
            'D' => 15,  // Date Hired
            'E' => 26,  // Present Allocation
        ];
    }

    public function collection()
    {
        return collect([]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ----------------------------------------------------
                // 1. TOP TITLE HEADERS
                // ----------------------------------------------------
                $sheet->setCellValue('A1', $this->section . ' QC INSPECTORS SKILL MATRIX');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

                $sheet->setCellValue('A2', 'Updated as of ' . Carbon::now()->format('F Y'));
                $sheet->getStyle('A2')->getFont()->setItalic(true)->setBold(true)->setSize(11);

                // ----------------------------------------------------
                // 2. DYNAMIC HEADER STRUCTURE
                // ----------------------------------------------------
                $fixedColumnsCount = 5; 
                $processCount = count($this->processes);
                
                // Row 3: "PROCESS / SYSTEM SKILLS" Span Header
                $totalProductCols = $this->productLines->count() * $processCount;
                $startColIndex = $fixedColumnsCount + 1; // Column F
                $endColIndex = $fixedColumnsCount + $totalProductCols;

                $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColIndex);
                $endColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endColIndex);

                $sheet->mergeCells("{$startColLetter}3:{$endColLetter}3");
                $sheet->setCellValue("{$startColLetter}3", 'PROCESS / SYSTEM SKILLS');

                // Row 4 & 5: Static Base Headers
                $sheet->setCellValue('A4', 'No.');
                $sheet->setCellValue('B4', 'Employee No.');
                $sheet->setCellValue('C4', 'Name');
                $sheet->setCellValue('D4', 'Date Hired');
                $sheet->setCellValue('E4', "Product type\nPresent Allocation");

                foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
                    $sheet->mergeCells("{$col}4:{$col}5");
                }

                // Render Dynamic Product Lines (Row 4) & Sub-processes (Row 5)
                $currentColIndex = $startColIndex;

                foreach ($this->productLines as $pLine) {
                    $pLineName = is_array($pLine) 
                        ? ($pLine['dropdown_masters_details'] ?? '') 
                        : ($pLine->dropdown_masters_details ?? '');

                    $pStartLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex);
                    $pEndLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex + $processCount - 1);

                    // Merge selected product line header across IQC, IPQC, OQC
                    $sheet->mergeCells("{$pStartLetter}4:{$pEndLetter}4");
                    $sheet->setCellValue("{$pStartLetter}4", $pLineName);

                    // Sub-headers in Row 5
                    foreach ($this->processes as $proc) {
                        $subColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex);
                        $sheet->setCellValue("{$subColLetter}5", $proc);
                        $sheet->getColumnDimension($subColLetter)->setWidth(18);
                        $currentColIndex++;
                    }
                }

                // Append trailing skill headers (QS, TU)
                $qsCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex);
                $sheet->mergeCells("{$qsCol}4:{$qsCol}5");
                $sheet->setCellValue("{$qsCol}4", 'QS');
                $sheet->getColumnDimension($qsCol)->setWidth(12);

                $tuCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex + 1);
                $sheet->mergeCells("{$tuCol}4:{$tuCol}5");
                $sheet->setCellValue("{$tuCol}4", 'TU');
                $sheet->getColumnDimension($tuCol)->setWidth(12);

                // ----------------------------------------------------
                // 3. DATA ROWS POPULATION
                // ----------------------------------------------------
                $currentRow = 6;
                $rowNum = 1;

                foreach ($this->personnel as $empNo => $records) {
                    if (empty($records)) continue;

                    $recordsCollection = collect($records);

                    // Identify the latest record based on appproval_at timestamp
                    $latestRecord = $recordsCollection->sortByDesc(function ($item) {
                        return !empty($item['appproval_at']) ? Carbon::parse($item['appproval_at'])->timestamp : 0;
                    })->first();

                    // Employee Info
                    $empInfo = $latestRecord['qc_slip_employees']['employee_info'] 
                            ?? $latestRecord['qc_slip_employees']['system_one_subcon_emp_info'] 
                            ?? [];
                    
                    $empName = $empInfo['EmpName'] ?? '';
                    $dateHired = !empty($empInfo['DateHired']) 
                        ? Carbon::parse($empInfo['DateHired'])->format('d-M-y') 
                        : '';
                    
                    // Formulate Present Allocation using latest record's primary product line & series
                    $presentAlloc = '';
                    $latestPLineDetail = $latestRecord['product_line_details'][0]['dropdown_masters_details'] ?? null;
                    $latestSeriesName = $latestRecord['series_name'] ?? null;

                    if ($latestPLineDetail && $latestSeriesName) {
                        $presentAlloc = "{$latestPLineDetail} / {$latestSeriesName}";
                    }

                    // Set standard columns
                    $sheet->setCellValue("A{$currentRow}", $rowNum);
                    $sheet->setCellValue("B{$currentRow}", $empNo);
                    $sheet->setCellValue("C{$currentRow}", $empName);
                    $sheet->setCellValue("D{$currentRow}", $dateHired);
                    $sheet->setCellValue("E{$currentRow}", $presentAlloc);

                    // Populate status values for each record and its filtered product lines
                    foreach ($records as $slip) {
                        $series = $slip['series_name'] ?? null;
                        $status = $slip['status'] ?? '';

                        // --------------------------------------------------------
                        // EXTRACT APPROVAL DATE FROM BLQCTC OPER APPROVER
                        // --------------------------------------------------------
                        $opApprovers = collect($slip['op_approvers'] ?? []);
                        
                        $blqctcApprover = $opApprovers->firstWhere('approval_status', 'BLQCTC');

                        $approvalDate = null;

                        if ($blqctcApprover) {
                            $approvalDate = $blqctcApprover['second_date'] 
                                ?? $blqctcApprover['first_date'] 
                                ?? null;
                        }

                        // Fallback to appproval_at if BLQCTC date is missing
                        if (empty($approvalDate) && !empty($slip['appproval_at'])) {
                            $approvalDate = $slip['appproval_at'];
                        }

                        // Format as MM/DD/YYYY
                        $approvalAt = !empty($approvalDate) 
                            ? Carbon::parse($approvalDate)->format('m/d/Y') 
                            : '';
                        
                        $productLineDetails = $slip['product_line_details'] ?? [];

                        // Iterates over filtered product line details attached to the slip
                        foreach ($productLineDetails as $pDetailItem) {
                            $pDetails = is_array($pDetailItem) 
                                ? ($pDetailItem['dropdown_masters_details'] ?? null) 
                                : ($pDetailItem->dropdown_masters_details ?? null);

                            if ($pDetails && $series) {
                                $cellValue = "{$status} - {$approvalAt}";

                                // Search matching column in exported headers
                                $colIdx = $startColIndex;
                                foreach ($this->productLines as $pLine) {
                                    $currentPLineName = is_array($pLine) 
                                        ? ($pLine['dropdown_masters_details'] ?? '') 
                                        : ($pLine->dropdown_masters_details ?? '');

                                    foreach ($this->processes as $proc) {
                                        if ($currentPLineName === $pDetails && strtoupper($proc) === strtoupper($series)) {
                                            $targetLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                                            $sheet->setCellValue("{$targetLetter}{$currentRow}", $cellValue);
                                        }
                                        $colIdx++;
                                    }
                                }
                            }
                        }
                    }

                    $currentRow++;
                    $rowNum++;
                }

                $lastRow = max($currentRow - 1, 6);
                $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex + 1);

                // ----------------------------------------------------
                // 4. STYLING & FORMATTING
                // ----------------------------------------------------
                // Alignments
                $sheet->getStyle("A4:{$lastColumnLetter}{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A4:{$lastColumnLetter}5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A4:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B4:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D4:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E4:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F6:{$lastColumnLetter}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Text wrapping
                $sheet->getStyle("A4:{$lastColumnLetter}5")->getAlignment()->setWrapText(true);

                // Font Styling
                $sheet->getStyle("A3:{$lastColumnLetter}5")->getFont()->setBold(true);

                 // Header Fills
                $sheet->getStyle("{$startColLetter}3:{$endColLetter}3")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ffb8fd'); // Light Pink Header
                $sheet->getStyle("{$startColLetter}3:{$endColLetter}3")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("{$startColLetter}3:{$endColLetter}3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                $sheet->getStyle("{$startColLetter}4:{$lastColumnLetter}4")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('fcff57'); // Yellow Sub-headers

                $sheet->getStyle("{$startColLetter}5:{$lastColumnLetter}5")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('feffb3'); // Yellow Sub-headers


                // Table Borders
                $sheet->getStyle("A4:{$lastColumnLetter}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}