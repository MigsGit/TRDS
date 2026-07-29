<?php

namespace App\Exports\InspectorSkillChartSheets;

use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class TSF1 implements FromCollection, WithTitle, WithEvents, WithCustomStartCell
{
    private $styles = [];
    protected $processStationDetails;
    
    public function __construct($processStationDetails, $group){
        $this->styles = [
            'yellowFill'      => $this->getFillBuilder('FFFF00'),
            'lightYellowFill' => $this->getFillBuilder('FFFFCC'),
            'grayFill'        => $this->getFillBuilder('AEAAAA'),
            'lightGrayFill'   => $this->getFillBuilder('EDEDED'),
            'orangeFill'      => $this->getFillBuilder('F4B084'),
            'lightOrangeFill' => $this->getFillBuilder('FCE4D6'),
            'greenFill'       => $this->getFillBuilder('A9D08E'),
            'lightGreenFill'  => $this->getFillBuilder('E2EFDA'),
            'blueFill'        => $this->getFillBuilder('9BC2E6'),
            'fontSmall' => [
                'font' => [
                        'bold' => false,
                        'size' => 10,
                ],
            ],
            'fontSmallBold' => [
                'font' => [
                        'bold' => true,
                        'size' => 10,
                ],
            ],
            'alignCenter' => [
                'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                ],
            ],
            'borderThin' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]
        ];

        $this->processStationDetails = $processStationDetails;
        $this->sectionGroup = $group;
        // dd($this->sectionGroup);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        // dd($this->processStationDetails);
        return $this->processStationDetails;
    }

    public function startCell(): string{
        return 'A1';
    }

    public function title(): string{
        return 'TS-F1';
    }

    private function getFillBuilder($color){
        return [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => $color,
                    ],
                ],
        ];
    }

    private function applyStyles($sheet, $range, array $styleNames){
        $applied_style = [];

        foreach ($styleNames as $name) {
            $applied_style = array_replace_recursive(
                $applied_style,
                $this->styles[$name]
            );
        }

        $sheet->getStyle($range)->applyFromArray($applied_style);
    }

    private function generateSkillCategory($sheet, $currentColumn, $category, $lastEmployeeRow){
        $totalColumns = 0;

        $totalProductLines = array_sum(
            array_map(function ($process) {
                $count = count($process['product_lines']);
                return $count > 0 ? $count : 1;
            }, $category['processes'])
        );

        $totalColumns += $totalProductLines;

        $endColumn = $currentColumn + $totalColumns - 1;

        //SKILL CATEGORY
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($currentColumn) . '3:'. Coordinate::stringFromColumnIndex($endColumn) . '3');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '3', $category['name']);

        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($currentColumn) .'3:'. Coordinate::stringFromColumnIndex($endColumn) .'3', [ 'yellowFill', ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($currentColumn) .'3:'. Coordinate::stringFromColumnIndex($endColumn) .'5', [ 'borderThin', ]);
        $this->applyStyles($sheet, "A6:".Coordinate::stringFromColumnIndex($endColumn).$lastEmployeeRow, ['borderThin', 'alignCenter']); //borders for skill category data

        $header2ColorFillIndex = 0;
        $header3ColorFillIndex = 0;

        $header2ColorFill = ['grayFill','orangeFill','greenFill','blueFill','blueFill'];
        $header3ColorFill = ['lightGrayFill','lightOrangeFill','lightGreenFill'];

        foreach ($category['processes'] as $process_per_cat){

            $count = count($process_per_cat['product_lines']);

            // No product lines
            if ($count === 0) {
                $column = Coordinate::stringFromColumnIndex($currentColumn);

                $sheet->mergeCells($column . '4:' . $column . '5');
                $sheet->setCellValue($column . '4', $process_per_cat['name']);

                if($category['name'] == 'PROCESS / SYSTEM SKILLS'){
                    $this->applyStyles($sheet, $column.'4', [$header2ColorFill[$header2ColorFillIndex], ]);
                }

                $currentColumn++;
                continue;
            }
            
            // Has product lines
            $start = $currentColumn;
            $end = $currentColumn + $count - 1;

            //FILL PROCESSES
            $sheet->mergeCells(Coordinate::stringFromColumnIndex($start) . '4:' . Coordinate::stringFromColumnIndex($end) . '4');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($start) . '4', $process_per_cat['name']);
            
            if($category['name'] == 'PROCESS / SYSTEM SKILLS'){
                $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($start).'4:'.Coordinate::stringFromColumnIndex($end).'4', [$header2ColorFill[$header2ColorFillIndex], ]);
            }

            // $this->applyStyles($sheet, 'A1:A2', [ 'fontSmallBold', ]);

            //FILL PRODUCT LINES (sub column of process stations)
            foreach ($process_per_cat['product_lines'] as $productLine) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', $productLine);

                if($category['name'] == 'PROCESS / SYSTEM SKILLS'){
                    $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($currentColumn).'5', [ $header3ColorFill[$header3ColorFillIndex], 'fontSmall', 'alignCenter' ]);
                }
                
                $currentColumn++;
            }
            
            $header2ColorFillIndex++;
            $header3ColorFillIndex++;
        }

        return $currentColumn;
    }

    private function generateSkillLegend($sheet, $currentColumn, $lastEmployeeRow){
        //FILL SKILL LEGEND
        $headerStartColumn = $currentColumn;
        $headerEndColumn = $headerStartColumn + 3;
        // $header2StartColumn = $currentColumn;

        // Header Row 1
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($headerStartColumn) .'3:'.Coordinate::stringFromColumnIndex($headerEndColumn) .'4');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($headerStartColumn) . '3', 'Total number of skills of QC Inspectors (in terms of skill legend)');
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($headerStartColumn) .'3:'.Coordinate::stringFromColumnIndex($headerEndColumn) .'4', [ 'yellowFill', ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($headerStartColumn) .'5:'.Coordinate::stringFromColumnIndex($headerEndColumn) .'5', [ 'lightYellowFill', ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($headerStartColumn) .'3:'.Coordinate::stringFromColumnIndex($headerEndColumn) .'5', [ 'borderThin',]);

        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($headerStartColumn).'6:'.Coordinate::stringFromColumnIndex($headerEndColumn).$lastEmployeeRow, ['borderThin', 'alignCenter']); //borders for skill category data

        // Header Row 2
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', '1');
        
        $currentColumn++;

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', '2');
        $currentColumn++;

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', '3');
        $currentColumn++;

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', '4');
        $currentColumn++;

        // $column = Coordinate::stringFromColumnIndex($currentColumn);
        
        $totalSkillsQcColumns = [
            'AB','AC','AD','AE',
            // 'AQ','AR','AS','AT'
        ];

        for ($i = 1; $i <= 4; $i++) {
            $column = Coordinate::stringFromColumnIndex($currentColumn);
            // Header Row 2
            $sheet->setCellValue(($column + $i) . '5', '1');

            
            $currentColumn++;
            
            $sheet->setCellValue("{$column}{$row}", "=COUNTIF(H{$row}:Z{$row},\"{$count}\")");

            $count++;

            if ($count > 4) {
                $count = 1;
            }
        }

        return $currentColumn;
    }
    
    private function generateOtherProcessSkills($sheet, $currentColumn, $sectionGroup, $lastEmployeeRow){
        //STATIC SECTIONS
        $sections = [
            'TS', 
            'CN',
            'PPD',
            'YF'
        ];

        //FILL OTHER PROCESS SKILLS
        $header1StartColumn = $currentColumn;
        $header1EndColumn = $header1StartColumn + 8;
        $header2StartColumn1 = $currentColumn;
        $header2EndColumn1 = $header2StartColumn1 + 2;
        $header2StartColumn2 = $header2EndColumn1 + 1;
        $header2EndColumn2 = $header2StartColumn2 + 2;
        $header2StartColumn3 = $header2EndColumn2 + 1;
        $header2EndColumn3 = $header2StartColumn3 + 2;

        // Header Row 1
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($header1StartColumn) .'3:'.Coordinate::stringFromColumnIndex($header1EndColumn) .'3');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($header1StartColumn) . '3', 'OTHER PROCESS / SYSTEM SKILLS');
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($header1StartColumn) .'3:'.Coordinate::stringFromColumnIndex($header1EndColumn) .'3', [ 'yellowFill', ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($header1StartColumn) .'3:'.Coordinate::stringFromColumnIndex($header1EndColumn) .'5', [ 'borderThin', ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($header1StartColumn).'6:'.Coordinate::stringFromColumnIndex($header1EndColumn).$lastEmployeeRow, ['borderThin', 'alignCenter']); //borders for skill category data

        // Header Row 2
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($header2StartColumn1) .'4:'.Coordinate::stringFromColumnIndex($header2EndColumn1) .'4');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($header2StartColumn1) . '4', 'IQC');

        $sheet->mergeCells(Coordinate::stringFromColumnIndex($header2StartColumn2) .'4:'.Coordinate::stringFromColumnIndex($header2EndColumn2) .'4');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($header2StartColumn2) . '4', 'IPQC');

        $sheet->mergeCells(Coordinate::stringFromColumnIndex($header2StartColumn3) .'4:'.Coordinate::stringFromColumnIndex($header2EndColumn3) .'4');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($header2StartColumn3) . '4', 'OQC');

        // Header Row 3
        for ($i = 0; $i < 3; $i++) { 
            foreach ($sections as $section) {
                if ($section === $sectionGroup) {
                    continue;
                }

                $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', $section);
                $currentColumn++;
            }
        }

        return $currentColumn;
    }
    
    private function generateOtherSkillLegend($sheet, $currentColumn, $lastEmployeeRow){
        //FILL SKILL LEGEND
        $headerStartColumn = $currentColumn;
        $headerEndColumn = $headerStartColumn + 3;

        // Header Row 1
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($headerStartColumn) .'3:'.Coordinate::stringFromColumnIndex($headerEndColumn) .'4');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($headerStartColumn) . '3', 'Total number of skills of QC Inspectors on other process (in terms of skill legend)');

        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($headerStartColumn) .'3:'.Coordinate::stringFromColumnIndex($headerEndColumn) .'4', [ 'yellowFill', ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($headerStartColumn) .'5:'.Coordinate::stringFromColumnIndex($headerEndColumn) .'5', [ 'lightYellowFill', ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($headerStartColumn) .'3:'.Coordinate::stringFromColumnIndex($headerEndColumn) .'5', [ 'borderThin', ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($headerStartColumn).'6:'.Coordinate::stringFromColumnIndex($headerEndColumn).$lastEmployeeRow, ['borderThin', 'alignCenter']); //borders for skill category data

        // Header Row 2
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', '1');
        $currentColumn++;

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', '2');
        $currentColumn++;

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', '3');
        $currentColumn++;

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . '5', '4');
        $currentColumn++;

        return $currentColumn;
    }

    private function generateSkillCategorySummary($sheet, $currentColumn, $lastEmployeeRow, $category){
        $currentRow = $lastEmployeeRow + 1;

        $headerRow1 = $currentRow + 1;
        $headerRow2 = $currentRow + 2;
        $headerRow3 = $currentRow + 3;

        $totalColumns = 0;

        $totalProductLines = array_sum(
            array_map(function ($process) {
                $count = count($process['product_lines']);
                return $count > 0 ? $count : 1;
            }, $category['processes'])
        );

        $totalColumns += $totalProductLines;

        $endColumn = $currentColumn + $totalColumns - 1;

        //SKILL CATEGORY
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($currentColumn) . $headerRow1 .':'. Coordinate::stringFromColumnIndex($endColumn) . $headerRow1);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . $headerRow1, $category['name']);

        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($currentColumn) . $headerRow1 .':'. Coordinate::stringFromColumnIndex($endColumn) . $headerRow1, [ 'yellowFill', 'borderThin' ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($currentColumn) . $headerRow1 .':'. Coordinate::stringFromColumnIndex($endColumn) . $headerRow2, [ 'fontSmallBold', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($currentColumn) . $headerRow3 .':'. Coordinate::stringFromColumnIndex($endColumn) . $headerRow3, [ 'fontSmall', 'borderThin', 'alignCenter' ]);
        $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($currentColumn) . ($headerRow3 + 1) .':'.Coordinate::stringFromColumnIndex($endColumn) . ($headerRow3 + 4), ['borderThin', 'alignCenter']); //borders for skill category summary


        $header2ColorFillIndex = 0;
        $header3ColorFillIndex = 0;
        
        $header2ColorFill = ['grayFill','orangeFill','greenFill','blueFill','blueFill'];
        $header3ColorFill = ['lightGrayFill','lightOrangeFill','lightGreenFill'];

        foreach ($category['processes'] as $process_per_cat) {
            $count = count($process_per_cat['product_lines']);

            // No product lines
            if ($count === 0) {
                $column = Coordinate::stringFromColumnIndex($currentColumn);

                $sheet->mergeCells($column . $headerRow2 .':'. $column . $headerRow3);
                $sheet->setCellValue($column . $headerRow2, $process_per_cat['name']);

                if($category['name'] == 'PROCESS / SYSTEM SKILLS'){
                    $this->applyStyles($sheet, $column.$headerRow2, [ $header2ColorFill[$header2ColorFillIndex] ]);
                }

                $currentColumn++;
                continue;
            }
            
            // Has product lines
            $start = $currentColumn;
            $end = $currentColumn + $count - 1;

            //FILL PROCESSES
            $sheet->mergeCells(Coordinate::stringFromColumnIndex($start) . $headerRow2 .':' . Coordinate::stringFromColumnIndex($end) . $headerRow2);
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($start) . $headerRow2, $process_per_cat['name']);

            if($category['name'] == 'PROCESS / SYSTEM SKILLS'){
                $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($start). $headerRow2 .':'.Coordinate::stringFromColumnIndex($end).$headerRow2, [ $header2ColorFill[$header2ColorFillIndex] ]);
            }

            //FILL PRODUCT LINES (sub column of process stations)
            foreach ($process_per_cat['product_lines'] as $productLine) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . $headerRow3, $productLine);

                if($category['name'] == 'PROCESS / SYSTEM SKILLS'){
                    $this->applyStyles($sheet, Coordinate::stringFromColumnIndex($currentColumn) .$headerRow3, [ $header3ColorFill[$header3ColorFillIndex], 'fontSmall' ]);
                }
                
                $currentColumn++;
            }

            $header2ColorFillIndex++;
            $header3ColorFillIndex++;
        }

        return $currentColumn;
    }

    private function generateHeader($sheet,$titleRow1,$titleRow2,$titleRow3,$totalRow1,$totalRow2,$totalRow3,$totalRow4,$legendRow1,$legendRow2,$legendRow3,$legendRow4,$legendRow5,$processStationDetails,$sectionGroup,$lastEmployeeRow){
        /*
        |--------------------------------------------------------------------------
        | DYNAMIC HEADER
        |--------------------------------------------------------------------------
        */

        $processSystemSkills = collect($this->processStationDetails)
            ->firstWhere('name', 'PROCESS / SYSTEM SKILLS');

        $machineOperationSkills = collect($this->processStationDetails)
            ->firstWhere('name', 'MACHINE OPERATION SKILLS');

        $qcCoreTools = collect($this->processStationDetails)
            ->firstWhere('name', 'QC & CORE TOOLS');

        $currentColumn = 8; // H

        if ($processSystemSkills) {
            $currentColumn = $this->generateSkillCategory(
                $sheet,
                $currentColumn,
                $processSystemSkills,
                $lastEmployeeRow
            );
        }

        if ($machineOperationSkills) {
            $currentColumn = $this->generateSkillCategory(
                $sheet,
                $currentColumn,
                $machineOperationSkills,
                $lastEmployeeRow
            );
        }

        if ($qcCoreTools) {
            $currentColumn = $this->generateSkillCategory(
                $sheet,
                $currentColumn,
                $qcCoreTools,
                $lastEmployeeRow
            );
        }

        $currentColumn += 1; // blank separator

        $currentColumn = $this->generateSkillLegend($sheet, $currentColumn, $lastEmployeeRow);

        $currentColumn += 1; // blank separator

        $currentColumn = $this->generateOtherProcessSkills($sheet, $currentColumn, $sectionGroup, $lastEmployeeRow);

        $currentColumn += 1; // blank separator

        $currentColumn = $this->generateOtherSkillLegend($sheet, $currentColumn, $lastEmployeeRow);

        /*
        |--------------------------------------------------------------------------
        | Report Title
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A1:AG1');
        $sheet->setCellValue('A1', 'TS-F1 QC INSPECTORS SKILL CHART');

        $sheet->mergeCells('A2:AG2');
        $sheet->setCellValue('A2', 'Updated as of January 2026');

        /*
        |--------------------------------------------------------------------------
        | Header Row 1
        |--------------------------------------------------------------------------
        */

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

        $sheet->mergeCells('G3:G5');
        $sheet->setCellValue('G3', 'Present Allocation');

        /*
        |--------------------------------------------------------------------------
        | Header Row 3
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue('E5', 'Years');
        $sheet->setCellValue('F5', 'Months');
        
        /*
        |--------------------------------------------------------------------------
        | SUMMARY Row 4 TO 7
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("E{$totalRow1}:F{$totalRow4}");
        $sheet->setCellValue("E{$totalRow1}", "Total number of certified QC Inspectors per skill (in terms of skill legend)");

        $sheet->setCellValue("G{$totalRow1}", "1");
        $sheet->setCellValue("G{$totalRow2}", "2");
        $sheet->setCellValue("G{$totalRow3}", "3");
        $sheet->setCellValue("G{$totalRow4}", "4");
        
        /*
        |--------------------------------------------------------------------------
        | LEGEND Row 1 TO 5
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue("E{$legendRow1}", "LEGEND");
        
        $sheet->setCellValue("E{$legendRow2}", "1");
        $sheet->setCellValue("E{$legendRow3}", "2");
        $sheet->setCellValue("E{$legendRow4}", "3");
        $sheet->setCellValue("E{$legendRow5}", "4");

        $sheet->mergeCells("F{$legendRow2}:I{$legendRow2}");
        $sheet->setCellValue("F{$legendRow2}", "Awareness and understanding only");

        $sheet->mergeCells("F{$legendRow3}:I{$legendRow3}");
        $sheet->setCellValue("F{$legendRow3}", "Can do with assistance");

        $sheet->mergeCells("F{$legendRow4}:I{$legendRow4}");
        $sheet->setCellValue("F{$legendRow4}", "Skilled, no supervision required, can lead and review work of others");

        $sheet->mergeCells("F{$legendRow5}:I{$legendRow5}");
        $sheet->setCellValue("F{$legendRow5}", "Expert  / Can perform without supervision");
    }

    private function applyAllStyle($sheet,$titleRow1,$titleRow2,$titleRow3,$totalRow1,$totalRow2,$totalRow3,$totalRow4,$legendRow1,$legendRow2,$legendRow3,$legendRow4,$legendRow5,$lastEmployeeRow){
        //Headers
        $this->applyStyles($sheet, 'A1:A2', [ 'fontSmallBold', ]);
        $this->applyStyles($sheet, 'A3:CC3', [ 'fontSmallBold', 'alignCenter', ]); //Extended Column until CC 
        $this->applyStyles($sheet, 'A3:G5', [ 'fontSmallBold','alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, 'H3:CC4', [ 'fontSmallBold','alignCenter', ]); //Extended Column until CC 
        $this->applyStyles($sheet, 'H5:CC5', [ 'fontSmall','alignCenter', ]); //Extended Column until CC 
        // $this->applyStyles($sheet, 'H3:Z3', [ 'yellowFill', ]);
        // $this->applyStyles($sheet, 'AB3:AE4', [ 'yellowFill', ]);
        // $this->applyStyles($sheet, 'AG3:AO3', [ 'yellowFill', ]);
        // $this->applyStyles($sheet, 'AQ3:AT4', [ 'yellowFill', ]);
        // $this->applyStyles($sheet, 'H4:I4', [ 'grayFill', ]);
        // $this->applyStyles($sheet, 'H5:I5', [ 'lightGrayFill', 'fontSmall' ]);
        // $this->applyStyles($sheet, 'J4:L4', [ 'orangeFill', ]);
        // $this->applyStyles($sheet, 'J5:L5', [ 'lightOrangeFill', 'fontSmall' ]);
        // $this->applyStyles($sheet, 'M4:O4', [ 'greenFill', ]);
        // $this->applyStyles($sheet, 'M5:O5', [ 'lightGreenFill', 'fontSmall']);
        // $this->applyStyles($sheet, 'P4:Q5', [ 'blueFill', ]);
        // $this->applyStyles($sheet, 'AB5:AE5', [ 'lightYellowFill', ]);
        // $this->applyStyles($sheet, 'AQ5:AT5', [ 'lightYellowFill', ]);
        // $this->applyStyles($sheet, 'R5:U5', [ 'fontSmall']);
        // $this->applyStyles($sheet, 'AG5:AO5', [ 'fontSmall']);
        // $this->applyStyles($sheet, "A6:Z{$lastEmployeeRow}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);
        // $this->applyStyles($sheet, "AB6:AE{$lastEmployeeRow}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);
        // $this->applyStyles($sheet, "AG6:AO{$lastEmployeeRow}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);
        // $this->applyStyles($sheet, "AQ6:AT{$lastEmployeeRow}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);

        //Summary
        $this->applyStyles($sheet, "E{$totalRow1}:F{$totalRow4}", [ 'yellowFill', 'fontSmallBold', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "G{$totalRow1}:G{$totalRow4}", [ 'yellowFill', 'fontSmall', 'alignCenter', 'borderThin' ]);

        // $this->applyStyles($sheet, "H{$titleRow1}:Z{$titleRow1}", [ 'yellowFill', 'fontSmallBold', 'alignCenter', 'borderThin' ]);
        // $this->applyStyles($sheet, "H{$titleRow2}:Z{$titleRow2}", [ 'fontSmallBold', 'alignCenter', 'borderThin' ]);
        // $this->applyStyles($sheet, "H{$titleRow2}:I{$titleRow2}", [ 'grayFill']);
        // $this->applyStyles($sheet, "J{$titleRow2}:L{$titleRow2}", [ 'orangeFill']);
        // $this->applyStyles($sheet, "M{$titleRow2}:O{$titleRow2}", [ 'greenFill']);
        // $this->applyStyles($sheet, "P{$titleRow2}:Q{$titleRow3}", [ 'blueFill', 'borderThin']);
        // $this->applyStyles($sheet, "H{$titleRow3}:I{$titleRow3}", [ 'lightGrayFill', 'fontSmall', 'alignCenter', 'borderThin' ]);
        // $this->applyStyles($sheet, "J{$titleRow3}:L{$titleRow3}", [ 'lightOrangeFill', 'fontSmall', 'alignCenter', 'borderThin' ]);
        // $this->applyStyles($sheet, "M{$titleRow3}:O{$titleRow3}", [ 'lightGreenFill', 'fontSmall', 'alignCenter', 'borderThin' ]);
        // $this->applyStyles($sheet, "R{$titleRow3}:U{$titleRow3}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);
        // $this->applyStyles($sheet, "V{$titleRow2}:Z{$totalRow3}", [ 'borderThin' ]);
        // $this->applyStyles($sheet, "H{$totalRow1}:Z{$totalRow4}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);

        //Legend
        $this->applyStyles($sheet, "E{$legendRow1}", ['fontSmallBold']);
        $this->applyStyles($sheet, "E{$legendRow2}:E{$legendRow5}", [ 'yellowFill', 'fontSmallBold', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "F{$legendRow2}:I{$legendRow5}", [ 'fontSmall', 'borderThin' ]);
    }

    private function setRowHeights($sheet,$titleRow1,$titleRow2,$titleRow3){
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(20);
        $sheet->getRowDimension(5)->setRowHeight(25);

        $sheet->getRowDimension($titleRow1)->setRowHeight(20);
        $sheet->getRowDimension($titleRow2)->setRowHeight(20);
        $sheet->getRowDimension($titleRow3)->setRowHeight(25);
    }

    private function setColumnWidths($sheet){
        $widths = [
            'A' => 6,
            'B' => 14,
            'C' => 28,
            'D' => 14,
            'E' => 10,
            'F' => 10,
            'G' => 18,

            'H' => 12,
            'I' => 12,
            'J' => 12,
            'K' => 12,
            'L' => 12,
            'M' => 12,
            'N' => 15,
            'O' => 12,
            'P' => 12,
            'Q' => 12,

            'R' => 12,
            'S' => 12,
            'T' => 12,
            'U' => 15,
            'V' => 12,

            'W' => 12,
            'X' => 12,
            'Y' => 12,
            'Z' => 12,

            'AB' => 7,
            'AC' => 7,
            'AD' => 7,
            'AE' => 7,

            'AG' => 7,
            'AH' => 7,
            'AI' => 7,
            'AJ' => 7,
            'AK' => 7,
            'AL' => 7,
            'AM' => 7,
            'AN' => 7,
            'AO' => 7,

            'AQ' => 9,
            'AR' => 9,
            'AS' => 9,
            'AT' => 9,
        ];

        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    public function registerEvents(): array{
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $processStationDetails = $this->processStationDetails;
                $sectionGroup = $this->sectionGroup;

                $sheet = $event->sheet->getDelegate();

                $sheet->freezePane('A6');
                            
                $sheet->getPageSetup()
                    ->setOrientation(
                        \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
                    );

                $sheet->getPageSetup()
                    ->setPaperSize(
                        \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3
                    );

                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                
                $employees = [
                    [
                        'emp_no' => '100001',
                        'name' => 'Juan Dela Cruz',
                    ],
                    [
                        'emp_no' => '100002',
                        'name' => 'Maria Santos',
                    ],
                    [
                        'emp_no' => '100003',
                        'name' => 'Pedro Reyes',
                    ],
                    [
                        'emp_no' => '100004',
                        'name' => 'Ana Cruz',
                    ],
                    [
                        'emp_no' => '100005',
                        'name' => 'Mark Lopez',
                    ],
                    [
                        'emp_no' => '100006',
                        'name' => 'Rose Garcia',
                    ],
                    [
                        'emp_no' => '100001',
                        'name' => 'Juan Dela Cruz',
                    ],
                    [
                        'emp_no' => '100002',
                        'name' => 'Maria Santos',
                    ],
                    [
                        'emp_no' => '100003',
                        'name' => 'Pedro Reyes',
                    ],
                    [
                        'emp_no' => '100004',
                        'name' => 'Ana Cruz',
                    ],
                    [
                        'emp_no' => '100005',
                        'name' => 'Mark Lopez',
                    ],
                    [
                        'emp_no' => '100006',
                        'name' => 'Rose Garcia',
                    ],
                ];

                $row = 6; //Default row to start filling employee data
                $no = 1;  //Default number to start filling employee data

                foreach ($employees as $employee) {
                    $count = 1;

                    $sheet->setCellValue("A{$row}", $no++);
                    $sheet->setCellValue("B{$row}", $employee['emp_no']);
                    $sheet->setCellValue("C{$row}", $employee['name']);

                    $row++;
                }
 
                $lastEmployeeRow = $row - 1; // Row of the last employee data row

                $processSystemSkills = collect($this->processStationDetails)
                    ->firstWhere('name', 'PROCESS / SYSTEM SKILLS');

                $machineOperationSkills = collect($this->processStationDetails)
                    ->firstWhere('name', 'MACHINE OPERATION SKILLS');

                $qcCoreTools = collect($this->processStationDetails)
                    ->firstWhere('name', 'QC & CORE TOOLS');

                $currentColumn = 8; // H

                if ($processSystemSkills) {
                    $currentColumn = $this->generateSkillCategorySummary(
                        $sheet,
                        $currentColumn,
                        $lastEmployeeRow,
                        $processSystemSkills
                    );
                }

                if ($machineOperationSkills) {
                    $currentColumn = $this->generateSkillCategorySummary(
                        $sheet,
                        $currentColumn,
                        $lastEmployeeRow,
                        $machineOperationSkills
                    );
                }

                if ($qcCoreTools) {
                    $currentColumn = $this->generateSkillCategorySummary(
                        $sheet,
                        $currentColumn,
                        $lastEmployeeRow,
                        $qcCoreTools
                    );
                }

                $blankRow = $row; // Row after the last employee data row

                $titleRow1 = $blankRow + 1; // Row for the summary title row 1
                $titleRow2 = $blankRow + 2; // Row for the summary title row 2
                $titleRow3 = $blankRow + 3; // Row for the summary title row 3

                $totalRow1 = $titleRow3 + 1; // Row for the total row after the summary title row
                $totalRow2 = $titleRow3 + 2; // Row for the total row after the summary title row
                $totalRow3 = $titleRow3 + 3; // Row for the total row after the summary title row
                $totalRow4 = $titleRow3 + 4; // Row for the total row after the summary title row

                $legendRow1 = $totalRow4 + 3; // Row for the total row after the summary title row
                $legendRow2 = $totalRow4 + 4; // Row for the total row after the summary title row
                $legendRow3 = $totalRow4 + 5; // Row for the total row after the summary title row
                $legendRow4 = $totalRow4 + 6; // Row for the total row after the summary title row
                $legendRow5 = $totalRow4 + 7; // Row for the total row after the summary title row

                $totalCertifiedQcSkillColumns = [
                    'H','I','J','K','L',
                    'M','N','O','P','Q',
                    'R','S','T','U','V',
                    'W','X','Y','Z'
                ];

                foreach ($totalCertifiedQcSkillColumns as $column) {
                    $sheet->setCellValue("{$column}{$totalRow1}", "=COUNTIF({$column}6:{$column}{$lastEmployeeRow},\"1\")");
                    $sheet->setCellValue("{$column}{$totalRow2}", "=COUNTIF({$column}6:{$column}{$lastEmployeeRow},\"2\")");
                    $sheet->setCellValue("{$column}{$totalRow3}", "=COUNTIF({$column}6:{$column}{$lastEmployeeRow},\"3\")");
                    $sheet->setCellValue("{$column}{$totalRow4}", "=COUNTIF({$column}6:{$column}{$lastEmployeeRow},\"4\")");
                }

                $this->generateHeader($sheet,$titleRow1,$titleRow2,$titleRow3,$totalRow1,$totalRow2,$totalRow3,$totalRow4,$legendRow1,$legendRow2,$legendRow3,$legendRow4,$legendRow5,$processStationDetails,$sectionGroup,$lastEmployeeRow);
                $this->applyAllStyle($sheet,$titleRow1,$titleRow2,$titleRow3,$totalRow1,$totalRow2,$totalRow3,$totalRow4,$legendRow1,$legendRow2,$legendRow3,$legendRow4,$legendRow5,$lastEmployeeRow);
                $this->setColumnWidths($sheet);
                $this->setRowHeights($sheet,$titleRow1,$titleRow2,$titleRow3); 
            }
        ];
    }

}