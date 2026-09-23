<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

class SkillMapExport implements FromView, WithDrawings, WithStyles
{
    protected $productLine;
    protected $employees;
    protected $productStation;
    protected $position;

    public function __construct($productLine, $employees, $productStation, $position)
    {
        $this->productLine = $productLine;
        $this->employees = $employees;
        $this->productStation = $productStation;
        $this->position = $position;
    }

    public function view(): View
    {
        // dd($this->position);
        if($this->position == 'INSPECTOR') {
            // Custom logic for Inspector
            // dd($this->employees);
            return view('exports.skill_map_inspector', [
                'productLine'    => $this->productLine,
                'employees'      => $this->employees,
                'productStation' => $this->productStation,
                'position'       => $this->position,
            ]);
        }else if($this->position == 'OPERATOR') {
            return view('exports.skill_map_operator', [
                'productLine'    => $this->productLine,
                'employees'      => $this->employees,
                'productStation' => $this->productStation,
                'position'       => $this->position,
            ]);
        }

    }


    public function drawings()
    {
        $drawings = [];

        /*
        |--------------------------------------------------------------------------
        | Employee Skill Images
        |--------------------------------------------------------------------------
        */

        if ($this->position === 'INSPECTOR') {

            $stationColumns = [
                1 => 'E',
                2 => 'F',
                3 => 'G',
                4 => 'H',
                5 => 'I',
                6 => 'J',
            ];

        } else {

            $stationColumns = [
                1 => 'E',
                2 => 'F',
                3 => 'G',
                4 => 'H',
            ];
        }

        // dd($this->employees[0]['stations']);

        foreach ($this->employees as $index => $employee) {
            // dd($employee);
            // Employee starts at row 4
            $row = $index + 4;

            foreach ($employee['stations'] as $station => $level) {

                // dd($station)
                if ($level < 0) {
                    continue;
                }

                if (!isset($stationColumns[$station])) {
                    continue;
                }

                $image = public_path("images/level{$level}.png");

                if (!file_exists($image)) {
                    continue;
                }

                $drawing = new Drawing();

                $drawing->setName("Level {$level}");
                $drawing->setDescription("Skill Level {$level}");
                $drawing->setPath($image);

                $drawing->setHeight(35);

                $drawing->setCoordinates(
                    $stationColumns[$station] . $row
                );

                // Center image inside cell
                $drawing->setOffsetX(35);
                $drawing->setOffsetY(10);

                $drawings[] = $drawing;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Legend Images
        |--------------------------------------------------------------------------
        */

        $legendRow  = count($this->employees) + 5;

        if ($this->position === 'INSPECTOR') {
            $legendColumns = [
                1 => 'A',
                2 => 'C',
                3 => 'F',
                4 => 'I',
            ];
        } else if ($this->position === 'OPERATOR') {
            $legendColumns = [
                1 => 'A',
                2 => 'C',
                3 => 'E',
                4 => 'G',
            ];
        }


        foreach ($legendColumns as $level => $column) {

            $image = public_path("images/level{$level}.png");

            if (!file_exists($image)) {
                continue;
            }

            $drawing = new Drawing();

            $drawing->setName("Legend Level {$level}");
            $drawing->setDescription("Legend Level {$level}");
            $drawing->setPath($image);

            $drawing->setHeight(35);

            $drawing->setCoordinates($column . $legendRow);

            // Center image vertically/horizontally in its cell
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
        | Column Widths
        |--------------------------------------------------------------------------
        */

        if ($this->position === 'INSPECTOR') {
            $lastColumn = 'J';
        } else {
            $lastColumn = 'H';
        }

        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(15);

        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(18);
        $sheet->getColumnDimension('J')->setWidth(18);

        /*
        |--------------------------------------------------------------------------
        | Employee Rows
        |--------------------------------------------------------------------------
        */

        $employeeStartRow = 4;
        $employeeEndRow = $employeeStartRow + count($this->employees) - 1;

        for ($row = $employeeStartRow; $row <= $employeeEndRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(45);
        }

    /*
    |--------------------------------------------------------------------------
    | Center NO. column
    |--------------------------------------------------------------------------
    */

    $sheet->getStyle("A4:{$lastColumn}{$employeeEndRow}")->getAlignment()
    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    /*
    |--------------------------------------------------------------------------
    | Legend
    |--------------------------------------------------------------------------
    */

    $legendTitleRow = count($this->employees) + 5;
    $legendRow = count($this->employees) + 5;

    $sheet->getRowDimension($legendTitleRow)->setRowHeight(25);
    $sheet->getRowDimension($legendRow)->setRowHeight(50);

            /*
    |--------------------------------------------------------------------------
    | Border
    |--------------------------------------------------------------------------
    */

    $lastRow = $legendRow - 2;
    $rowRow = $legendRow;

    $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => [
                    'rgb' => '000000',
                ],
            ],
        ],
    ]);

    $sheet->getStyle("A{$rowRow}:{$lastColumn}{$rowRow}")->applyFromArray([
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
        | General Alignment
        |--------------------------------------------------------------------------
        */

        return [

            // Title
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 18,
                ],
            ],

            // Main header
            2 => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
                // 'fill' => [
                //     'fillType' => 'solid',
                //     'startColor' => [
                //         'rgb' => 'D9D9D9',
                //     ],
                // ],
            ],

            // Skill header
            3 => [
                'font' => [
                    'bold' => true,
                    'size' => 9,
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
            ],

            // Legend title
            $legendTitleRow => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ],

            // Legend
            $legendRow => [
                'alignment' => [
                    'horizontal' => 'left',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
                'font' => [
                    'size' => 9,
                ],
            ],
        ];
    }
}
