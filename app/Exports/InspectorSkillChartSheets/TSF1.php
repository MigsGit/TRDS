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

class TSF1 implements FromCollection, WithTitle, WithEvents, WithCustomStartCell
{
    private $styles = [];

    public function __construct(){
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
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        return collect();
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

    private function generateHeader($sheet,$titleRow1,$titleRow2,$titleRow3,$totalRow1,$totalRow2,$totalRow3,$totalRow4,$legendRow1,$legendRow2,$legendRow3,$legendRow4,$legendRow5){
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

        $sheet->mergeCells('H3:Q3');
        $sheet->setCellValue('H3', 'PROCESS / SYSTEM SKILLS');

        $sheet->mergeCells('R3:V3');
        $sheet->setCellValue('R3', 'MACHINE OPERATION SKILLS');

        $sheet->mergeCells('W3:Z3');
        $sheet->setCellValue('W3', 'QC & CORE TOOLS');

        $sheet->mergeCells('AB3:AE4');
        $sheet->setCellValue('AB3', 'Total number of skills of QC Inspectors (in terms of skill legend)');

        $sheet->mergeCells('AG3:AO3');
        $sheet->setCellValue('AG3', 'OTHER PROCESS / SYSTEM SKILLS');

        $sheet->mergeCells('AQ3:AT4');
        $sheet->setCellValue('AQ3', 'Total number of skills of QC Inspectors on other process (in terms of skill legend) ');

        /*
        |--------------------------------------------------------------------------
        | Header Row 2
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('H4:I4');
        $sheet->setCellValue('H4', 'IQC');

        $sheet->mergeCells('J4:L4');
        $sheet->setCellValue('J4', 'IPQC');

        $sheet->mergeCells('M4:O4');
        $sheet->setCellValue('M4', 'OQC');

        $sheet->mergeCells('P4:P5');
        $sheet->setCellValue('P4', 'QS');

        $sheet->mergeCells('Q4:Q5');
        $sheet->setCellValue('Q4', 'TU');

        $sheet->mergeCells('R4:S4');
        $sheet->setCellValue('R4', 'Burn-in Socket');

        $sheet->mergeCells('T4:U4');
        $sheet->setCellValue('T4', 'Test Socket');

        $sheet->mergeCells('V4:V5');
        $sheet->setCellValue('V4', 'NEXIV');

        $sheet->mergeCells('W4:W5');
        $sheet->setCellValue('W4', 'Basic QC Tools');

        $sheet->mergeCells('X4:X5');
        $sheet->setCellValue('X4', 'SPC');

        $sheet->mergeCells('Y4:Y5');
        $sheet->setCellValue('Y4', 'MSA');

        $sheet->mergeCells('Z4:Z5');
        $sheet->setCellValue('Z4', 'FMEA');

        $sheet->mergeCells('AG4:AI4');
        $sheet->setCellValue('AG4', 'IQC');

        $sheet->mergeCells('AJ4:AL4');
        $sheet->setCellValue('AJ4', 'IPQC');

        $sheet->mergeCells('AM4:AO4');
        $sheet->setCellValue('AM4', 'OQC');

        /*
        |--------------------------------------------------------------------------
        | Header Row 3
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue('E5', 'Years');
        $sheet->setCellValue('F5', 'Months');

        $sheet->setCellValue('H5', 'Appearance');
        $sheet->setCellValue('I5', 'Dimension');

        $sheet->setCellValue('J5', 'BGA-FP');
        $sheet->setCellValue('K5', 'BGA-LGA');
        $sheet->setCellValue('L5', 'QFP-TSOP');

        $sheet->setCellValue('M5', 'Appearance');
        $sheet->setCellValue('N5', 'Dimension (COC)');
        $sheet->setCellValue('O5', 'Packing');

        $sheet->setCellValue('R5', 'Holding Force');
        $sheet->setCellValue('S5', 'Contact Force');

        $sheet->setCellValue('T5', 'Contact Force');
        $sheet->setCellValue('U5', 'Actuation Force');

        $sheet->setCellValue('AB5', '1');
        $sheet->setCellValue('AC5', '2');
        $sheet->setCellValue('AD5', '3');
        $sheet->setCellValue('AE5', '4');

        $sheet->setCellValue('AG5', 'CN');
        $sheet->setCellValue('AH5', 'PPD');
        $sheet->setCellValue('AI5', 'YF');

        $sheet->setCellValue('AJ5', 'CN');
        $sheet->setCellValue('AK5', 'PPD');
        $sheet->setCellValue('AL5', 'YF');

        $sheet->setCellValue('AM5', 'CN');
        $sheet->setCellValue('AN5', 'PPD');
        $sheet->setCellValue('AO5', 'YF');

        $sheet->setCellValue('AQ5', '1');
        $sheet->setCellValue('AR5', '2');
        $sheet->setCellValue('AS5', '3');
        $sheet->setCellValue('AT5', '4');

        /*
        |--------------------------------------------------------------------------
        | SUMMARY Row 1
        |--------------------------------------------------------------------------
        */
            
        $sheet->mergeCells("H{$titleRow1}:Q{$titleRow1}");
        $sheet->setCellValue("H{$titleRow1}", "PROCESS / SYSTEM SKILLS");
        
        $sheet->mergeCells("R{$titleRow1}:V{$titleRow1}");
        $sheet->setCellValue("R{$titleRow1}", "MACHINE OPERATION SKILLS");

        $sheet->mergeCells("W{$titleRow1}:Z{$titleRow1}");
        $sheet->setCellValue("W{$titleRow1}", "QC & CORE TOOLS");

        /*
        |--------------------------------------------------------------------------
        | SUMMARY Row 2
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("H{$titleRow2}:I{$titleRow2}");
        $sheet->setCellValue("H{$titleRow2}", "IQC");

        $sheet->mergeCells("J{$titleRow2}:L{$titleRow2}");
        $sheet->setCellValue("J{$titleRow2}", "IPQC");

        $sheet->mergeCells("M{$titleRow2}:O{$titleRow2}");
        $sheet->setCellValue("M{$titleRow2}", "OQC");
        
        $sheet->mergeCells("P{$titleRow2}:P{$titleRow3}");
        $sheet->setCellValue("P{$titleRow2}", "QS");

        $sheet->mergeCells("Q{$titleRow2}:Q{$titleRow3}");
        $sheet->setCellValue("Q{$titleRow2}", "TU");

        $sheet->mergeCells("R{$titleRow2}:S{$titleRow2}");
        $sheet->setCellValue("R{$titleRow2}", "Burn-in Socket");

        $sheet->mergeCells("T{$titleRow2}:U{$titleRow2}");
        $sheet->setCellValue("T{$titleRow2}", "Test Socket");

        $sheet->mergeCells("V{$titleRow2}:V{$titleRow3}");
        $sheet->setCellValue("V{$titleRow2}", "NEXIV");
        
        $sheet->mergeCells("W{$titleRow2}:W{$titleRow3}");
        $sheet->setCellValue("W{$titleRow2}", "Basic QC Tools");

        $sheet->mergeCells("X{$titleRow2}:X{$titleRow3}");
        $sheet->setCellValue("X{$titleRow2}", "SPC");

        $sheet->mergeCells("Y{$titleRow2}:Y{$titleRow3}");
        $sheet->setCellValue("Y{$titleRow2}", "MSA");

        $sheet->mergeCells("Z{$titleRow2}:Z{$titleRow3}");
        $sheet->setCellValue("Z{$titleRow2}", "FMEA");

        /*
        |--------------------------------------------------------------------------
        | SUMMARY Row 3
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue("H{$titleRow3}", 'Appearance');
        $sheet->setCellValue("I{$titleRow3}", 'Dimension');
        $sheet->setCellValue("J{$titleRow3}", 'BGA-FP');
        $sheet->setCellValue("K{$titleRow3}", 'BGA-LGA');
        $sheet->setCellValue("L{$titleRow3}", 'QFP-TSOP');
        $sheet->setCellValue("M{$titleRow3}", 'Appearance');
        $sheet->setCellValue("N{$titleRow3}", 'Dimension (COC)');
        $sheet->setCellValue("O{$titleRow3}", 'Packing');
        $sheet->setCellValue("R{$titleRow3}", 'Holding Force');
        $sheet->setCellValue("S{$titleRow3}", 'Contact Force');
        $sheet->setCellValue("T{$titleRow3}", 'Contact Force');
        $sheet->setCellValue("U{$titleRow3}", 'Actuation Force');

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
        $this->applyStyles($sheet, 'A3:AO2', [ 'fontSmallBold', 'alignCenter', ]);
        $this->applyStyles($sheet, 'A3:AT5', [ 'fontSmallBold','alignCenter', ]);
        $this->applyStyles($sheet, 'A3:Z5', [ 'borderThin', ]);
        $this->applyStyles($sheet, 'AB3:AE5', [ 'borderThin',]);
        $this->applyStyles($sheet, 'AG3:AO5', [ 'borderThin', ]);
        $this->applyStyles($sheet, 'AQ3:AT5', [ 'borderThin', ]);
        $this->applyStyles($sheet, 'H3:Z3', [ 'yellowFill', ]);
        $this->applyStyles($sheet, 'AB3:AE4', [ 'yellowFill', ]);
        $this->applyStyles($sheet, 'AG3:AO3', [ 'yellowFill', ]);
        $this->applyStyles($sheet, 'AQ3:AT4', [ 'yellowFill', ]);
        $this->applyStyles($sheet, 'H4:I4', [ 'grayFill', ]);
        $this->applyStyles($sheet, 'H5:I5', [ 'lightGrayFill', 'fontSmall' ]);
        $this->applyStyles($sheet, 'J4:L4', [ 'orangeFill', ]);
        $this->applyStyles($sheet, 'J5:L5', [ 'lightOrangeFill', 'fontSmall' ]);
        $this->applyStyles($sheet, 'M4:O4', [ 'greenFill', ]);
        $this->applyStyles($sheet, 'M5:O5', [ 'lightGreenFill', 'fontSmall']);
        $this->applyStyles($sheet, 'P4:Q5', [ 'blueFill', ]);
        $this->applyStyles($sheet, 'AB5:AE5', [ 'lightYellowFill', ]);
        $this->applyStyles($sheet, 'AQ5:AT5', [ 'lightYellowFill', ]);
        $this->applyStyles($sheet, 'R5:U5', [ 'fontSmall']);
        $this->applyStyles($sheet, 'AG5:AO5', [ 'fontSmall']);
        $this->applyStyles($sheet, "A6:Z{$lastEmployeeRow}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "AB6:AE{$lastEmployeeRow}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "AG6:AO{$lastEmployeeRow}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "AQ6:AT{$lastEmployeeRow}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);

        //Summary
        $this->applyStyles($sheet, "H{$titleRow1}:Z{$titleRow1}", [ 'yellowFill', 'fontSmallBold', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "H{$titleRow2}:Z{$titleRow2}", [ 'fontSmallBold', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "H{$titleRow2}:I{$titleRow2}", [ 'grayFill']);
        $this->applyStyles($sheet, "J{$titleRow2}:L{$titleRow2}", [ 'orangeFill']);
        $this->applyStyles($sheet, "M{$titleRow2}:O{$titleRow2}", [ 'greenFill']);
        $this->applyStyles($sheet, "P{$titleRow2}:Q{$titleRow3}", [ 'blueFill', 'borderThin']);
        $this->applyStyles($sheet, "H{$titleRow3}:I{$titleRow3}", [ 'lightGrayFill', 'fontSmall', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "J{$titleRow3}:L{$titleRow3}", [ 'lightOrangeFill', 'fontSmall', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "M{$titleRow3}:O{$titleRow3}", [ 'lightGreenFill', 'fontSmall', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "R{$titleRow3}:U{$titleRow3}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "V{$titleRow2}:Z{$totalRow3}", [ 'borderThin' ]);
        $this->applyStyles($sheet, "E{$totalRow1}:F{$totalRow4}", [ 'yellowFill', 'fontSmallBold', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "G{$totalRow1}:G{$totalRow4}", [ 'yellowFill', 'fontSmall', 'alignCenter', 'borderThin' ]);
        $this->applyStyles($sheet, "H{$totalRow1}:Z{$totalRow4}", [ 'fontSmall', 'alignCenter', 'borderThin' ]);

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
                ];

                $row = 6; //Default row to start filling employee data
                $no = 1;  //Default number to start filling employee data
                
                foreach ($employees as $employee) {
                    $totalSkillsQcColumns = [
                        'AB','AC','AD','AE',
                        'AQ','AR','AS','AT'
                    ];

                    $count = 1;

                    $sheet->setCellValue("A{$row}", $no++);
                    $sheet->setCellValue("B{$row}", $employee['emp_no']);
                    $sheet->setCellValue("C{$row}", $employee['name']);

                    foreach ($totalSkillsQcColumns as $column) {
                        $sheet->setCellValue("{$column}{$row}", "=COUNTIF(H{$row}:Z{$row},\"{$count}\")");

                        $count++;

                        if ($count > 4) {
                            $count = 1;
                        }
                    }

                    $row++;
                }
 
                $lastEmployeeRow = $row - 1; // Row of the last employee data row
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

                $this->generateHeader($sheet,$titleRow1,$titleRow2,$titleRow3,$totalRow1,$totalRow2,$totalRow3,$totalRow4,$legendRow1,$legendRow2,$legendRow3,$legendRow4,$legendRow5);
                $this->applyAllStyle($sheet,$titleRow1,$titleRow2,$titleRow3,$totalRow1,$totalRow2,$totalRow3,$totalRow4,$legendRow1,$legendRow2,$legendRow3,$legendRow4,$legendRow5,$lastEmployeeRow);
                $this->setColumnWidths($sheet);
                $this->setRowHeights($sheet,$titleRow1,$titleRow2,$titleRow3); 
            }
        ];
    }

}