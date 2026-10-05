<?php

namespace App\Exports\EmployeeSkillCardSheets;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SkillCard implements WithEvents, WithTitle
{
    protected $productLine;

    public function __construct($productLine = null)
    {
        $this->productLine = $productLine;
    }

    public function title(): string
    {
        return 'Skill Card';
        // return substr(
        //     $this->productLine->name,
        //     0,
        //     31
        // );
    }

    private function setupSheet(Worksheet $sheet)
    {
        /*
        |--------------------------------------------------------------------------
        | GRIDLINES
        |--------------------------------------------------------------------------
        */

        $sheet->setShowGridlines(false);


        /*
        |--------------------------------------------------------------------------
        | PAGE SETUP
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(1);


        /*
        |--------------------------------------------------------------------------
        | MARGINS
        |--------------------------------------------------------------------------
        */

        $sheet->getPageMargins()
            ->setTop(0)
            ->setBottom(0)
            ->setLeft(0)
            ->setRight(0)
            ->setHeader(0)
            ->setFooter(0);


        /*
        |--------------------------------------------------------------------------
        | LEFT SIDE A:H
        |--------------------------------------------------------------------------
        */

        $widths = [
            'A' => 8,
            'B' => 8,
            'C' => 8,
            'D' => 8,
            'E' => 8,
            'F' => 8,
            'G' => 8,
            'H' => 8,

            /*
            |--------------------------------------------------------------------------
            | RIGHT SIDE I:P
            |--------------------------------------------------------------------------
            */

            'I' => 8,
            'J' => 8,
            'K' => 8,
            'L' => 8,
            'M' => 8,
            'N' => 8,
            'O' => 8,
            'P' => 8,
        ];

        foreach ($widths as $column => $width) {

            $sheet
                ->getColumnDimension($column)
                ->setWidth($width);
        }

        /*
        |--------------------------------------------------------------------------
        | COLUMN WIDTHS
        |
        | A:H = LEFT SIDE
        | I:P = RIGHT SIDE
        |--------------------------------------------------------------------------
        */

        // foreach (range('A', 'P') as $column) {

        //     $sheet
        //         ->getColumnDimension($column)
        //         ->setWidth(4.5);

        // }


        /*
        |--------------------------------------------------------------------------
        | ROW HEIGHTS
        |--------------------------------------------------------------------------
        */

        for ($row = 1; $row <= 30; $row++) {

            $sheet
                ->getRowDimension($row)
                ->setRowHeight(15);

        }


        /*
        |--------------------------------------------------------------------------
        | DEFAULT FONT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('A1:P30')
            ->getFont()
            ->setName('Arial')
            ->setSize(7);


        /*
        |--------------------------------------------------------------------------
        | CENTER ALL CELLS VERTICALLY
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('A1:P30')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function createSkillCard(Worksheet $sheet)
    {
        /*
        |--------------------------------------------------------------------------
        | LEFT SIDE
        |--------------------------------------------------------------------------
        */

        $this->createLeftHeader($sheet);

        $this->createEmployeeSection($sheet);

        $this->createStationSection($sheet);


        /*
        |--------------------------------------------------------------------------
        | RIGHT SIDE
        |--------------------------------------------------------------------------
        */

        $this->createRightHeader($sheet);

        $this->createRatingsSection($sheet);

        $this->createFunctionalCompetencies($sheet);

        $this->createPerformanceLevel($sheet);

        $this->createApprovalSection($sheet);

        $this->applyBorders($sheet);
    }

    private function createLeftHeader(Worksheet $sheet)
    {
        /*
        |--------------------------------------------------------------------------
        | COMPANY NAME
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A1:H2');

        $sheet->setCellValue(
            'A1',
            'PRICON MICROELECTRONICS, INC.'
        );

        $sheet->getStyle('A1:H2')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A1:H2')
            ->getFont()
            ->setBold(true)
            ->setSize(14);


        /*
        |--------------------------------------------------------------------------
        | PERSONNEL SKILL CARD
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A3:H3');

        $sheet->setCellValue(
            'A3',
            'PERSONNEL SKILL CARD'
        );

        $sheet->getStyle('A3:H3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A3:H3')
            ->getFont()
            ->setBold(true);


        /*
        |--------------------------------------------------------------------------
        | SECTION / TEST SOLUTION
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A4:H4');

        $sheet->setCellValue(
            'A4',
            'TEST SOLUTION'
        );

        $sheet->getStyle('A4:H4')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A4:H4')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A4:H4')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');
    }

    private function createEmployeeSection(Worksheet $sheet)
    {
        $rows = [

            5 => 'Name',
            6 => 'Position',
            7 => 'Date Hired',
            8 => 'Section',
            9 => 'Date Certified',
            10 => 'Validity Period',

        ];

        foreach ($rows as $row => $label) {

            $sheet->mergeCells("A{$row}:B{$row}");

            $sheet->mergeCells("C{$row}:E{$row}");

            $sheet->setCellValue(
                "A{$row}",
                $label
            );

            $sheet->getStyle("A{$row}:E{$row}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle("A{$row}:B{$row}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }


        /*
        |--------------------------------------------------------------------------
        | LEVEL
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('F5:G10');

        $sheet->setCellValue(
            'F5',
            "LEVEL\n"
        );

        $sheet->getStyle('F5:G10')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);


        /*
        |--------------------------------------------------------------------------
        | PHOTO
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('H5:H10');

        $sheet->setCellValue(
            'H5',
            'PHOTO'
        );

        $sheet->getStyle('H5:H10')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);


        /*
        |--------------------------------------------------------------------------
        | TOTAL RATING
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A11:E11');

        $sheet->mergeCells('F11:H11');

        $sheet->setCellValue(
            'F11',
            'TOTAL RATINGS:'
        );

        $sheet->getStyle('F11:H11')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    // private function createStationSection(Worksheet $sheet){
    //     /*
    //     |--------------------------------------------------------------------------
    //     | TITLE
    //     |--------------------------------------------------------------------------
    //     */

    //     $sheet->mergeCells('A12:H12');

    //     $sheet->setCellValue(
    //         'A12',
    //         'STATION ASSIGNED'
    //     );

    //     $sheet->getStyle('A12:H12')
    //         ->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->getStyle('A12:H12')
    //         ->getFont()
    //         ->setBold(true);


    //     /*
    //     |--------------------------------------------------------------------------
    //     | STATION HEADER
    //     |--------------------------------------------------------------------------
    //     */

    //     $sheet->mergeCells('A13:B14');

    //     $sheet->mergeCells('C13:D14');


    //     $sheet->setCellValue(
    //         'A13',
    //         'CERTIFICATION'
    //     );


    //     $sheet->setCellValue(
    //         'C13',
    //         'PRODUCT LINE'
    //     );


    //     $sheet->setCellValue(
    //         'E13',
    //         "ASSEMBLY\nPROCESS"
    //     );


    //     $sheet->setCellValue(
    //         'F13',
    //         "VISUAL\nINSPECTION"
    //     );


    //     $sheet->setCellValue(
    //         'G13',
    //         "PARTS\nPREP."
    //     );


    //     $sheet->setCellValue(
    //         'H13',
    //         "MACHINE\nOPERATION"
    //     );


    //     /*
    //     |--------------------------------------------------------------------------
    //     | STYLE HEADER
    //     |--------------------------------------------------------------------------
    //     */

    //     $sheet->getStyle('A13:H14')
    //         ->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER)
    //         ->setWrapText(true);

    //     $sheet->getStyle('A13:H14')
    //         ->getFont()
    //         ->setBold(true);
    // }

    private function createStationSection(Worksheet $sheet){
        $headerRow = 13;
        $stationHeaderRow = 14;
        $dataStartRow = 15;

        // Product line
        $sheet->mergeCells("A{$headerRow}:D{$dataStartRow}");
        $sheet->setCellValue("A{$headerRow}", $this->productLine->name ?? '');

        // Station Assigned title
        $sheet->mergeCells("E{$headerRow}:L{$headerRow}");
        $sheet->setCellValue("E{$headerRow}", 'STATION ASSIGNED');

        // Station columns
        $stations = [
            'E' => 'ASSEMBLY PROCESS',
            'G' => 'VISUAL INSPECTION',
            'I' => 'PARTS PREP.',
            'K' => 'MACHINE OPERATION',
        ];

        foreach ($stations as $column => $name) {

            $endColumn = chr(ord($column) + 1);

            $sheet->mergeCells(
                "{$column}{$stationHeaderRow}:{$endColumn}{$stationHeaderRow}"
            );

            $sheet->setCellValue(
                "{$column}{$stationHeaderRow}",
                $name
            );
        }

        // Example:
        // E:F = Assembly
        // G:H = Visual Inspection
        // I:J = Parts Prep
        // K:L = Machine Operation

        // Dynamic station/process rows go here...
    }

    private function createRightHeader(Worksheet $sheet)
    {
        /*
        |--------------------------------------------------------------------------
        | PRICON
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('I1:L3');

        $sheet->setCellValue(
            'I1',
            'PRICON MICROELECTRONICS, INC.'
        );

        $sheet->getStyle('I1:L3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('I1:L3')
            ->getFont()
            ->setBold(true);


        /*
        |--------------------------------------------------------------------------
        | COMPETENCY SKILLS ASSESSMENT
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('M1:P3');

        $sheet->setCellValue(
            'M1',
            "COMPETENCY SKILLS\nASSESSMENT"
        );

        $sheet->getStyle('M1:P3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);


        /*
        |--------------------------------------------------------------------------
        | REFERENCE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('I4:P5');

        $sheet->setCellValue(
            'I4',
            'REFERENCE'
        );

        $sheet->getStyle('I4:P5')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function createRatingsSection(Worksheet $sheet)
    {
        /*
        |--------------------------------------------------------------------------
        | RATINGS TITLE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('I6:P6');

        $sheet->setCellValue(
            'I6',
            'RATINGS'
        );

        $sheet->getStyle('I6:P6')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('I6:P6')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('I6:P6')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');


        /*
        |--------------------------------------------------------------------------
        | 4 RATINGS
        |--------------------------------------------------------------------------
        */

        $ratings = [

            'I7:J7' => '1',
            'K7:L7' => '2',
            'M7:N7' => '3',
            'O7:P7' => '4',

        ];

        foreach ($ratings as $range => $rating) {

            $sheet->mergeCells($range);

            $startCell = explode(':', $range)[0];

            $sheet->setCellValue(
                $startCell,
                $rating
            );

            $sheet->getStyle($range)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
    }

    private function createFunctionalCompetencies(Worksheet $sheet)
    {
        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('I10:N11');

        $sheet->setCellValue(
            'I10',
            "FUNCTIONAL\nCOMPETENCIES"
        );

        $sheet->mergeCells('O10:P11');

        $sheet->setCellValue(
            'O10',
            "TARGET\nRATING"
        );

        $sheet->getStyle('I10:P11')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle('I10:P11')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');

        /*
        |--------------------------------------------------------------------------
        | Dynamic rows will start here
        |--------------------------------------------------------------------------
        */

        // $functionalStartRow = 12;
    }
    
    private function createPerformanceLevel(Worksheet $sheet)
    {
        $row = 20;

        $sheet->mergeCells("I{$row}:P{$row}");

        $sheet->setCellValue(
            "I{$row}",
            'PERFORMANCE LEVEL (OVER ALL)'
        );

        $sheet->getStyle("I{$row}:P{$row}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("I{$row}:P{$row}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');
    }

    private function createApprovalSection(Worksheet $sheet)
    {
        $row = 25;

        $sheet->mergeCells("I{$row}:P{$row}");

        $sheet->setCellValue(
            "I{$row}",
            'APPROVED BY:'
        );

        $sheet->getStyle("I{$row}:P{$row}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("I{$row}:P{$row}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');
    }

    private function applyBorders(Worksheet $sheet)
    {
        $sheet->getStyle('A1:P30')
            ->applyFromArray([

                'borders' => [

                    'allBorders' => [

                        'borderStyle' => Border::BORDER_THIN,

                        'color' => [
                            'rgb' => '000000',
                        ],

                    ],

                ],

            ]);
    }

    private function addCompanyLogo(
        Worksheet $sheet,
        $coordinate
    ) {
        $logo = new Drawing();

        $logo->setName('PRICON Logo');

        $logo->setDescription('PRICON Logo');

        $logo->setPath(
            public_path('images/pricon-logo.png')
        );

        $logo->setCoordinates($coordinate);

        $logo->setHeight(35);

        $logo->setOffsetX(5);

        $logo->setOffsetY(2);

        $logo->setWorksheet($sheet);
    }

    private function addEmployeePhoto(
        Worksheet $sheet,
        $photoPath,
        $coordinate
    ) {
        if (!$photoPath || !file_exists($photoPath)) {
            return;
        }

        $photo = new Drawing();

        $photo->setName('Employee Photo');

        $photo->setDescription('Employee Photo');

        $photo->setPath($photoPath);

        $photo->setCoordinates($coordinate);

        $photo->setHeight(75);

        $photo->setOffsetX(5);

        $photo->setOffsetY(5);

        $photo->setWorksheet($sheet);
    }

    private function addImage(
        Worksheet $sheet,
        string $path,
        string $coordinate,
        int $width = null,
        int $height = null,
        int $offsetX = 0,
        int $offsetY = 0
    ) {
        if (!file_exists($path)) {
            return;
        }

        $drawing = new Drawing();

        $drawing->setPath($path);

        $drawing->setCoordinates($coordinate);

        $drawing->setOffsetX($offsetX);

        $drawing->setOffsetY($offsetY);

        /*
        |--------------------------------------------------------------------------
        | Important:
        | Use either width OR height if you want to preserve ratio.
        |--------------------------------------------------------------------------
        */

        if ($width !== null) {
            $drawing->setWidth($width);
        }

        if ($height !== null) {
            $drawing->setHeight($height);
        }

        $drawing->setWorksheet($sheet);
    }

    private function addLevelImage(
        Worksheet $sheet,
        $level
    ) {
        $path = public_path(
            "images/skill_card/skill_card_level_{$level}.jpg"
        );

        $this->addImage(
            $sheet,
            $path,
            'F5',
            70
        );
    }


    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $this->setupSheet($sheet);

                $this->createSkillCard($sheet);
                // $this->addCompanyLogo($sheet, 'A1');
                // $this->addCompanyLogo($sheet, 'I1');

                $this->addImage(
                    $sheet,
                    public_path('images/skill_card/skill_card_pricon_logo_wide.jpg'),
                    'A1',
                    80
                );

                $this->addImage(
                    $sheet,
                    public_path('images/skill_card/skill_card_pricon_logo_short.jpg'),
                    'I1',
                    80
                );

                $this->addImage(
                    $sheet,
                    public_path('images/skill_card/skill_card_emp_photo.jpg'),
                    'H5',
                    null,
                    80
                );

                // $this->addImage(
                //     $sheet,
                //     public_path('images/skill_card/skill_card_approvers.jpg'),
                //     'I25',
                //     null,
                //     70
                // );

                $this->addImage(
                    $sheet,
                    public_path('images/skill_card/skill_card_approvers.jpg'),
                    'I26',
                    70,
                    70,
                    15,
                    8
                );

                $this->addLevelImage(
                    $sheet,
                    1
                );

                $this->addLevelImage(
                    $sheet,
                    2
                );

                $this->addLevelImage(
                    $sheet,
                    3
                );

                $this->addLevelImage(
                    $sheet,
                    4
                );


            }

        ];
    }
}

?>