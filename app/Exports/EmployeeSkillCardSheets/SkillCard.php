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
    protected $employeeSkillCardData;

    public function __construct($employeeSkillCardData = null){
        $this->employeeSkillCardData = $employeeSkillCardData;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        // dd($this->employeeSkillCardData);
        return $this->employeeSkillCardData;
    }

    public function title(): string{
        return 'Skill Card';
    }

    private function setupSheet(Worksheet $sheet){
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
        | LEFT SIDE A:L
        |--------------------------------------------------------------------------
        */

        $widths = [
            'A' => 5.7,
            'B' => 5.7,
            'C' => 5.7,
            'D' => 5.7,

            'E' => 4.5,
            'F' => 4.5,
            'G' => 4.5,
            'H' => 4.5,
            'I' => 4.5,
            'J' => 4.5,
            'K' => 4.5,
            'L' => 4.5,

            /*
            |--------------------------------------------------------------------------
            | RIGHT SIDE M:T
            |--------------------------------------------------------------------------
            */

            'M' => 6.7,
            'N' => 6.7,
            'O' => 6.7,
            'P' => 6.7,

            'Q' => 7,
            'R' => 7,
            'S' => 7,
            'T' => 7,
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
        | A:L = LEFT SIDE
        | M:T = RIGHT SIDE
        |--------------------------------------------------------------------------
        */

        // foreach (range('A', 'T') as $column) {
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

        //SET CUSTOM ROW HEIGHT
        $sheet->getRowDimension(12)->setRowHeight(32);

        $sheet->getRowDimension(15)->setRowHeight(30);

        /*
        |--------------------------------------------------------------------------
        | DEFAULT FONT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('A1:T21')
            ->getFont()
            ->setName('Arial')
            ->setSize(7);

        /*
        |--------------------------------------------------------------------------
        | CENTER ALL CELLS VERTICALLY
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('A1:T21')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function createSkillCard(Worksheet $sheet, $employeeSkillCardData){
        /*
        |--------------------------------------------------------------------------
        | LEFT SIDE
        |--------------------------------------------------------------------------
        */

        $this->createLeftHeader($sheet);

        $this->createEmployeeSection($sheet, $employeeSkillCardData);

        $this->createProductLineSection($sheet, $employeeSkillCardData);

        $this->createStationSection($sheet, $employeeSkillCardData);


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

    private function createLeftHeader(Worksheet $sheet){
        /*
        |--------------------------------------------------------------------------
        | COMPANY NAME
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A1:L2');

        $this->addImage(
            $sheet,
            public_path('images/skill_card/skill_card_pricon_logo_wide.jpg'),
            'A1', //cell
            340, //width
            30 //height
        );

        /*
        |--------------------------------------------------------------------------
        | PERSONNEL SKILL CARD
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A3:L3');

        $sheet->setCellValue(
            'A3',
            'PERSONNEL SKILL CARD'
        );

        $sheet->getStyle('A3:L3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A3:H3')
            ->getFont()
            ->setBold(true);

        /*
        |--------------------------------------------------------------------------
        | SECTION
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A4:L4');

        $sheet->setCellValue(
            'A4',
            'SECTION'
        );

        $sheet->getStyle('A4:L4')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A4:L4')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A4:L4')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');
    }

    private function createEmployeeSection(Worksheet $sheet, $employeeSkillCardData){
        $rows = [
            5 => 'Name',
            6 => 'Position',
            7 => 'Date Hired',
            8 => 'Section',
            9 => 'Date Certified',
            10 => 'Validity Period',
        ];

        foreach ($rows as $row => $label) {
            $sheet->mergeCells("A{$row}:B{$row}"); //Merge label cells
            $sheet->mergeCells("C{$row}:D{$row}"); //Merge value cells

            $sheet->setCellValue(
                "A{$row}",
                $label
            );

            $sheet->getStyle("A{$row}:D{$row}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle("A{$row}:B{$row}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Fill in employee skill card data
            $sheet->setCellValue("C5", $employeeSkillCardData[0]['employee_name'] ?? '');
            $sheet->setCellValue("C6", $employeeSkillCardData[0]['position'] ?? '');
            $sheet->setCellValue("C7", $employeeSkillCardData[0]['date_hired'] ?? '');
            $sheet->setCellValue("C8", $employeeSkillCardData[0]['section'] ?? '');
            $sheet->setCellValue("C9", $employeeSkillCardData[0]['date_of_transfer'] ?? '');
            $sheet->setCellValue("C10", $employeeSkillCardData[0]['validity_period'] ?? '');

            $sheet->getStyle("C5:C10")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        /*
        |--------------------------------------------------------------------------
        | LEVEL
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('E5:H9');

        // $sheet->setCellValue(
        //     'E5',
        //     "LEVEL\n"
        // );

        $overall_rating = [
            'level1' => 'level_1',
            'level2' => 'level_2',
            'level3' => 'level_3',
            'level4' => 'level_4',
        ];

        $this->addImage(
                $sheet,
                public_path('images/skill_card/skill_card_overall_'.$overall_rating['level1'].'.jpg'),
                'E5', //cell
                100, //width
                95, //height
                12
            );

        // $sheet->getStyle('E5:H9')
        //     ->getAlignment()
        //     ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        //     ->setVertical(Alignment::VERTICAL_CENTER)
        //     ->setWrapText(true);

        /*
        |--------------------------------------------------------------------------
        | PHOTO
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('I5:L9');

        $sheet->setCellValue(
            'I5',
            'PHOTO'
        );

        $sheet->getStyle('I5:L9')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        /*
        |--------------------------------------------------------------------------
        | TOTAL RATING
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('E10:H10');

        $sheet->setCellValue(
            'E10',
            'TOTAL RATINGS:'
        );

        $sheet->getStyle('E10:H10')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('I10:L10');

        $sheet->setCellValue(
            'I10',
            'POINTS'
        );
        $sheet->getStyle('I10:L10')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function createProductLineSection(Worksheet $sheet, $employeeSkillCardData){
        $prodAreastartRow = 11;
        $prodAreaendRow = 12;

        //Static Positon 
        $startRow2 = $prodAreastartRow + 2;
        $startRow3 = $prodAreastartRow + 3;
        $startRow4 = $prodAreastartRow + 4;
        $startRow5 = $prodAreastartRow + 5;
        $startRow6 = $prodAreastartRow + 6;
        $startRow7 = $prodAreastartRow + 7;
        $startRow8 = $prodAreastartRow + 8;
        $startRow9 = $prodAreastartRow + 9;
        $startRow10 = $prodAreastartRow + 10;
        $startRow11 = $prodAreastartRow + 11;

        /*
        |--------------------------------------------------------------------------
        | PRODUCT AREA
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("A{$prodAreastartRow}:D{$prodAreaendRow}");

        $sheet->setCellValue("A{$prodAreastartRow}", $employeeSkillCardData[0]['product_line'][0]['product_line_name'] ?? 'NOT FOUND'); //Sample Data only

        $sheet->getStyle("A{$prodAreastartRow}:D{$prodAreaendRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A{$prodAreastartRow}:D{$prodAreaendRow}")
            ->getFont()
            ->setSize(10)
            ->setBold(true);

        /*
        |--------------------------------------------------------------------------
        | CERTIFICATION DETAILS
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("A{$startRow2}:B{$startRow4}");

        $this->addImage(
            $sheet,
            public_path('images/skill_card/certification_image.png'),
            'A13',
            71,
            69,
        );

        $sheet->getStyle("A{$startRow2}:B{$startRow10}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->mergeCells("A{$startRow5}:B{$startRow6}");
        $sheet->setCellValue("A{$startRow5}", "Date\nCertified");

        $sheet->mergeCells("A{$startRow7}:B{$startRow7}");
        $sheet->setCellValue("A{$startRow7}", $employeeSkillCardData[0]['date_of_transfer'] ?? 'NOT FOUND');

        $sheet->mergeCells("A{$startRow8}:B{$startRow9}");
        $sheet->setCellValue("A{$startRow8}", "Valid\nUntil");

        $sheet->mergeCells("A{$startRow10}:B{$startRow10}");
        $sheet->setCellValue("A{$startRow10}", $employeeSkillCardData[0]['validity_period'] ?? 'NOT FOUND');

        /*
        |--------------------------------------------------------------------------
        | PRODUCT LINES
        |--------------------------------------------------------------------------
        */
        // $employeeSkillCardData[0]['validity_period']


        $prodLineRow = $prodAreastartRow + 1;
        $prodLineRow1 = $prodLineRow + 1;
        $prodLineRow2 = $prodLineRow + 2;
        $prodLineRow3 = $prodLineRow + 3;
        $prodLineRow4 = $prodLineRow + 4;
        $prodLineRow5 = $prodLineRow + 5;
        $prodLineRow6 = $prodLineRow + 6;
        $prodLineRow7 = $prodLineRow + 7;
        $prodLineRow8 = $prodLineRow + 8;
        $prodLineRow9 = $prodLineRow + 9;

        $product_lines = [
            "C{$prodLineRow1}:D{$prodLineRow2}" => 'PROD LINE 1',
            "C{$prodLineRow3}:D{$prodLineRow4}" => 'PROD LINE 2',
            "C{$prodLineRow5}:D{$prodLineRow6}" => 'PROD LINE 3',
            "C{$prodLineRow7}:D{$prodLineRow9}" => 'PROD LINE 4',
        ];

        foreach ($product_lines as $product_line => $prod_line) {

            $sheet->mergeCells($product_line);

            $startCell = explode(':', $product_line)[0];

            $sheet->setCellValue(
                $startCell,
                $prod_line
            );

            $sheet->getStyle($product_line)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
    }

    private function createStationSection(Worksheet $sheet){
        $stationStartRow = 11;
        $stationStartRow1 = $stationStartRow + 1;

        // Station Assigned title
        $sheet->mergeCells("E{$stationStartRow}:L{$stationStartRow}");
        $sheet->setCellValue("E{$stationStartRow}", 'STATION ASSIGNED');

        $sheet->getStyle("E{$stationStartRow}:L{$stationStartRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

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
                "{$column}{$stationStartRow1}:{$endColumn}{$stationStartRow1}"
            );

            $sheet->setCellValue(
                "{$column}{$stationStartRow1}",
                $name
            );
            
            $sheet->getStyle("{$column}{$stationStartRow1}:{$endColumn}{$stationStartRow1}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
        }

        // Example:
        // E:F = Assembly
        // G:H = Visual Inspection
        // I:J = Parts Prep
        // K:L = Machine Operation

        // Dynamic station/process rows go here...
    }

    private function createRightHeader(Worksheet $sheet){
        /*
        |--------------------------------------------------------------------------
        | PRICON
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('M1:P3');

        $this->addImage(
            $sheet,
            public_path('images/skill_card/skill_card_pricon_logo_short.jpg'),
            'M1',
            220,
            42,
        );

        /*
        |--------------------------------------------------------------------------
        | COMPETENCY SKILLS ASSESSMENT
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('Q1:T3');

        $sheet->setCellValue(
            'Q1',
            "COMPETENCY SKILLS\nASSESSMENT"
        );

        $sheet->getStyle('Q1:T3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
    }

    private function createRatingsSection(Worksheet $sheet){
        /*  
        | REFERENCE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('Q4:T5');

        $sheet->setCellValue(
            'Q4',
            'REFERENCE'
        );

        $sheet->getStyle('Q4:T5')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        /*
        |--------------------------------------------------------------------------
        | RATINGS TITLE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('Q6:T6');

        $sheet->setCellValue(
            'Q6',
            'RATINGS'
        );

        $sheet->getStyle('Q6:T6')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('Q6:T6')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('Q6:T6')
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
            'Q7' => '1',
            'R7' => '2',
            'S7' => '3',
            'T7' => '4',
        ];

        foreach ($ratings as $range => $rating) {

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

        /*
        |--------------------------------------------------------------------------
        | 4 RATINGS VALUES IMAGE
        |--------------------------------------------------------------------------
        */

        $rating_values = [
            'Q8:Q9' => 'actual_1',
            'R8:R9' => 'actual_2',
            'S8:S9' => 'actual_3',
            'T8:T9' => 'actual_4',
        ];

        foreach ($rating_values as $range_value => $rating_v) {

            $sheet->mergeCells($range_value);

            $startCell = explode(':', $range_value)[0];

            $this->addImage(
                $sheet,
                public_path('images/skill_card/skill_card_'.$rating_v.'.png'),
                $startCell, //cell
                340, //width
                30, //height
                12
            );

            // $sheet->setCellValue(
            //     $startCell,
            //     $rating_v
            // );

            // $sheet->getStyle($range_value)
            //     ->getAlignment()
            //     ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            //     ->setVertical(Alignment::VERTICAL_CENTER);
        }

        /*
        |--------------------------------------------------------------------------
        | 4 RATINGS VALUES DESCRIPTION
        |--------------------------------------------------------------------------
        */

        $rating_values_description = [
            'Q10:Q12' => 'Certified (at least 1 series/product)',
            'R10:R12' => 'Certified (at least 2-3 series/products)',
            'S10:S12' => 'Certified (at least 4-6 series/products)',
            'T10:T12' => 'Certified (more than 6 series/products)',
        ];

        foreach ($rating_values_description as $range_value_desk => $rating_description) {

            $sheet->mergeCells($range_value_desk);

            $startCell = explode(':', $range_value_desk)[0];

            $sheet->setCellValue(
                $startCell,
                $rating_description
            );

            $sheet->getStyle($range_value_desk)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
        }
    }

    private function createFunctionalCompetencies(Worksheet $sheet){
        /*
        |--------------------------------------------------------------------------
        | FUNCTIONAL COMPETENCIES
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('M4:O6');

        $sheet->setCellValue(
            'M4',
            'FUNCTIONAL COMPETENCIES'
        );

        $sheet->getStyle('M4:O6')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle('M4:O6')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');

        $sheet->mergeCells('P4:P6');

        $sheet->setCellValue(
            'P4',
            'TARGET RATING'
        );

        $sheet->getStyle('P4')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle("P4:P6")
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
    
    private function createPerformanceLevel(Worksheet $sheet){

        /*
        |--------------------------------------------------------------------------
        | PERFORMANCE LEVEL (OVER ALL)
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('Q13:T13');

        $sheet->setCellValue(
            'Q13',
            'PERFORMANCE LEVEL (OVER ALL)'
        );

        $sheet->getStyle('Q13:T13')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('Q13:T13')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('Q13:T13')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');

        /*
        |--------------------------------------------------------------------------
        | 4 RATINGS OVERALL LABELS
        |--------------------------------------------------------------------------
        */

        $rating_overall_labels = [
            'Q14' => 'Level 1',
            'R14' => 'Level 2',
            'S14' => 'Level 3',
            'T14' => 'Level 4',
        ];

        foreach ($rating_overall_labels as $overall_label => $rating_overall) {
            $sheet->mergeCells($overall_label);
            $startCell = explode(':', $overall_label)[0];
            $sheet->setCellValue(
                $startCell,
                $rating_overall
            );

            $sheet->getStyle($overall_label)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        /*
        |--------------------------------------------------------------------------
        | 4 RATINGS OVERALL IMAGES
        |--------------------------------------------------------------------------
        */

        $rating_overall_images = [
            'Q15' => 'level_1',
            'R15' => 'level_2',
            'S15' => 'level_3',
            'T15' => 'level_4',
        ];

        foreach ($rating_overall_images as $overall_image => $rating_overall) {

            $sheet->mergeCells($overall_image);

            $startCell = explode(':', $overall_image)[0];

            // $sheet->setCellValue(
            //     $startCell,
            //     $rating_overall
            // );

            $this->addImage(
                $sheet,
                public_path('images/skill_card/skill_card_'.$rating_overall.'_old.jpg'),
                $startCell, //cell
                340, //width
                30, //height
                10
            );

            // $sheet->getStyle($overall_image)
            //     ->getAlignment()
            //     ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            //     ->setVertical(Alignment::VERTICAL_CENTER);
        }

        /*
        |--------------------------------------------------------------------------
        | 4 RATINGS OVERALL RANGE
        |--------------------------------------------------------------------------
        */

        $rating_overall_ranges = [
            'Q16' => 'Range1',
            'R16' => 'Range2',
            'S16' => 'Range3',
            'T16' => 'Range4',
        ];

        foreach ($rating_overall_ranges as $overall_range => $rating_overall_range) {

            // $sheet->mergeCells($overall_image);

            $startCell = explode(':', $overall_range)[0];

            $sheet->setCellValue(
                $startCell,
                $rating_overall_range
            );

            $sheet->getStyle($overall_range)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
    }

    private function createApprovalSection(Worksheet $sheet){
        $row = 17;
        $startRow = $row;

        $sheet->mergeCells("M{$startRow}:T{$startRow}");

        $sheet->setCellValue(
            "M{$startRow}",
            'APPROVED BY:'
        );

        $sheet->getStyle("M{$startRow}:T{$startRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("M{$startRow}:T{$startRow}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');
        
        $row++;
        $endRow = $row + 3;

        $sheet->mergeCells("M{$row}:T{$endRow}");

        $this->addImage(
            $sheet,
            public_path('images/skill_card/skill_card_approvers.jpg'),
            "M{$row}",
            319,
            54,
            15,
            8
        );
    }

    private function applyBorders(Worksheet $sheet){
        $sheet->getStyle('A1:T21')
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

    private function addImage(
        Worksheet $sheet,
        string $path,
        string $coordinate,
        int $width = null,
        int $height = null,
        int $offsetX = 5,
        int $offsetY = 5
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

                $employeeSkillCardData = $this->employeeSkillCardData;

                $sheet = $event->sheet->getDelegate();

                $this->setupSheet($sheet);

                $this->createSkillCard($sheet, $employeeSkillCardData);

                // $this->addImage(
                //     $sheet,
                //     public_path('images/skill_card/skill_card_emp_photo.jpg'),
                //     'H5',
                //     null,
                //     80
                // );

                // $this->addImage(
                //     $sheet,
                //     public_path('images/skill_card/skill_card_approvers.jpg'),
                //     'I26',
                //     70,
                //     70,
                //     15,
                //     8
                // );

                // $this->addLevelImage(
                //     $sheet,
                //     1
                // );

                // $this->addLevelImage(
                //     $sheet,
                //     2
                // );

                // $this->addLevelImage(
                //     $sheet,
                //     3
                // );

                // $this->addLevelImage(
                //     $sheet,
                //     4
                // );

            }

        ];
    }
}

?>