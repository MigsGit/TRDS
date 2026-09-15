<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Contracts\View\View;



class SkillMapExportPerProdLine implements FromView, WithStyles, WithDrawings
{
    protected $productLines;
    protected $employees;

    public function __construct($productLines, $employees)
    {
        $this->productLines = $productLines;
        $this->employees = $employees;
    }

    public function view(): View
    {
        return view('exports.skill_map_per_prod_line', [
            'productLines' => $this->productLines,
            'employees'    => $this->employees,
        ]);
    }

    public function drawings()
{
    $drawings = [];

    /*
    |--------------------------------------------------------------------------
    | Employee Skill Images
    |--------------------------------------------------------------------------
    */

    $firstProductLineColumn = 5; // E

    foreach ($this->employees as $index => $employee) {

        // Employee data starts at row 2
        $row = $index + 2;

        foreach ($this->productLines as $productLineIndex => $productLine) {

            $columnNumber = $firstProductLineColumn + $productLineIndex;

            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                $columnNumber
            );

            // Get skill level for this product line
           $level = $employee['productLines'][$productLineIndex] ?? 0;

            if ($level < 0) {
                continue;
            }

            $image = public_path("images/level{$level}.png");

            if (!file_exists($image)) {
                continue;
            }

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

            $drawing->setName(
                "Level {$level} - {$productLine}"
            );

            $drawing->setDescription(
                "Skill Level {$level} - {$productLine}"
            );

            $drawing->setPath($image);

            $drawing->setHeight(35);

            $drawing->setCoordinates($column . $row);

            // Adjust depending on your column width
            $drawing->setOffsetX(35);
            $drawing->setOffsetY(3);

            $drawings[] = $drawing;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Legend Images
    |--------------------------------------------------------------------------
    |
    | Legend layout:
    |
    | A = Level 1 image
    | B = Level 1 text
    | C = Level 2 image
    | D = Level 2 text
    | E = Level 3 image
    | F = Level 3 text
    | G = Level 4 image
    | H = Level 4 text
    |
    */

    $legendRow = count($this->employees) + 3;

    $legendColumns = [
        1 => 'A',
        2 => 'C',
        3 => 'E',
        4 => 'G',
    ];

    foreach ($legendColumns as $level => $column) {

        $image = public_path("images/level{$level}.png");

        if (!file_exists($image)) {
            continue;
        }

        $drawing = new Drawing();

        $drawing->setName(
            "Legend Level {$level}"
        );

        $drawing->setDescription(
            "Legend Level {$level}"
        );

        $drawing->setPath($image);

        $drawing->setHeight(30);

        $drawing->setCoordinates(
            $column . $legendRow
        );

        // Position image inside legend cell
        $drawing->setOffsetX(35);
        $drawing->setOffsetY(15);

        $drawings[] = $drawing;
    }


    return $drawings;
}

    public function styles(Worksheet $sheet)
    {
        /*
        |--------------------------------------------------------------------------
        | Dynamic Columns
        |--------------------------------------------------------------------------
        |
        | A = No.
        | B = Employee Name
        | C = Emp. No.
        | D = Date Hired
        | E onwards = Product Lines
        |
        */

        $firstProductLineColumn = 5; // E

        $productLineCount = count($this->productLines);

        $lastColumnNumber = $firstProductLineColumn + $productLineCount - 1;

        // Convert column number to Excel letter
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
            $lastColumnNumber
        );

        /*
        |--------------------------------------------------------------------------
        | Column Widths
        |--------------------------------------------------------------------------
        */

        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(15);

        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        // Product line columns
        for ($column = $firstProductLineColumn; $column <= $lastColumnNumber; $column++) {

            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                $column
            );

            $sheet->getColumnDimension($columnLetter)->setWidth(20);
        }

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000',
                    ],
                ],
            ],
        ]);

        // Header height
        $sheet->getRowDimension(1)->setRowHeight(30);

        /*
        |--------------------------------------------------------------------------
        | Employee Rows
        |--------------------------------------------------------------------------
        */

        $employeeStartRow = 2;
        $employeeEndRow = count($this->employees) + 1;

        if ($employeeEndRow >= $employeeStartRow) {

            // Center product line columns
            $sheet->getStyle(
                "E{$employeeStartRow}:{$lastColumn}{$employeeEndRow}"
            )->getAlignment()->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

            $sheet->getStyle(
                "A{$employeeStartRow}:{$lastColumn}{$employeeEndRow}"
            )->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => [
                            'rgb' => '000000',
                        ],
                    ],
                ],
            ]);

            // Employee row height
            for ($row = $employeeStartRow; $row <= $employeeEndRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(30);
            }

            /*
            |--------------------------------------------------------------------------
            | Center specific columns
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "A{$employeeStartRow}:A{$employeeEndRow}"
            )->getAlignment()->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

            $sheet->getStyle(
                "C{$employeeStartRow}:D{$employeeEndRow}"
            )->getAlignment()->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

            // Center product line columns
            $sheet->getStyle(
                "E{$employeeStartRow}:{$lastColumn}{$employeeEndRow}"
            )->getAlignment()->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );
        }


    /*
    |--------------------------------------------------------------------------
    | Legend
    |--------------------------------------------------------------------------
    */

    $legendTitleRow = count($this->employees) + 3;
    $legendRow = count($this->employees) + 3;

    $sheet->getRowDimension($legendTitleRow)->setRowHeight(25);
    $sheet->getRowDimension($legendRow)->setRowHeight(50);

            /*
    |--------------------------------------------------------------------------
    | Border
    |--------------------------------------------------------------------------
    */

    $lastRow = $legendRow - 2;
    $rowRow = $legendRow;

    $sheet->getStyle("A{$rowRow}:H{$rowRow}")->applyFromArray([
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
            'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
            'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);

        /*
        |--------------------------------------------------------------------------
        | Freeze Header
        |--------------------------------------------------------------------------
        */

        $sheet->freezePane('A2');

        /*
        |--------------------------------------------------------------------------
        | Page Setup
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()->setOrientation(
            \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
        );

        $sheet->getPageSetup()->setPaperSize(
            \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
        );

        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        $sheet->getPageMargins()->setTop(0.3);
        $sheet->getPageMargins()->setBottom(0.3);
        $sheet->getPageMargins()->setLeft(0.3);
        $sheet->getPageMargins()->setRight(0.3);

        return [];
    }
}
