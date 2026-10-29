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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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

    private function setupSheet(Worksheet $sheet, int $maxRow, int $maxColumnIndex, int $leftBlockEndColumnIndex){
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
        | COLUMN WIDTHS (DYNAMIC)
        |
        | A:D = fixed label/value columns
        | E..leftBlockEndColumnIndex = station grid (grows past L for 5+ stations)
        | right block = competencies/target rating (first 4) + reference/ratings (last 4)
        |--------------------------------------------------------------------------
        */

        for ($col = 1; $col <= 4; $col++) {
            $sheet->getColumnDimension($this->columnLetter($col))->setWidth(5.7);
        }

        for ($col = 5; $col <= $leftBlockEndColumnIndex; $col++) {
            $sheet->getColumnDimension($this->columnLetter($col))->setWidth(4.5);
        }

        $rightStartColumnIndex = $leftBlockEndColumnIndex + 1;

        for ($col = $rightStartColumnIndex; $col <= $rightStartColumnIndex + 3; $col++) {
            $sheet->getColumnDimension($this->columnLetter($col))->setWidth(6.7);
        }

        for ($col = $rightStartColumnIndex + 4; $col <= $maxColumnIndex; $col++) {
            $sheet->getColumnDimension($this->columnLetter($col))->setWidth(7);
        }

        /*
        |--------------------------------------------------------------------------
        | ROW HEIGHTS
        |--------------------------------------------------------------------------
        */

        $rowHeightLimit = max(30, $maxRow);

        for ($row = 1; $row <= $rowHeightLimit; $row++) {
            $sheet
            ->getRowDimension($row)
            ->setRowHeight(15);
        }

        //SET CUSTOM ROW HEIGHT
        $sheet->getRowDimension(12)->setRowHeight(32);

        $sheet->getRowDimension(15)->setRowHeight(30);

        $fullRange = 'A1:' . $this->columnLetter($maxColumnIndex) . $maxRow;

        /*
        |--------------------------------------------------------------------------
        | DEFAULT FONT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle($fullRange)
            ->getFont()
            ->setName('Arial')
            ->setSize(7);

        /*
        |--------------------------------------------------------------------------
        | CENTER ALL CELLS VERTICALLY
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle($fullRange)
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function columnLetter(int $index): string{
        return Coordinate::stringFromColumnIndex($index);
    }

    /**
     * Determines where the right-side block must start so it never overlaps
     * the station grid once stations push past column L.
     */
    private function calculateColumnMetrics($employeeSkillCardData): array{
        $stationCount = count($employeeSkillCardData[0]['target_ratings'] ?? []);

        $stationStartColumnIndex = Coordinate::columnIndexFromString('E');
        $stationEndColumnIndex = $stationCount > 0
            ? $stationStartColumnIndex + ($stationCount * 2) - 1
            : $stationStartColumnIndex - 1;

        // Left block always reserves at least through column L, widening further only if stations need more room
        $leftBlockEndColumnIndex = max(Coordinate::columnIndexFromString('L'), $stationEndColumnIndex);

        return [
            'leftBlockEndColumnIndex' => $leftBlockEndColumnIndex,
            'rightStartColumnIndex' => $leftBlockEndColumnIndex + 1,
        ];
    }

    /**
     * Predicts the right side's natural bottom row (Approval's closing row) purely from
     * data counts, mirroring the fixed-size math in createRatingsSection/createPerformanceLevel/
     * createApprovalSection, so the left side can stretch to match it before it is even built.
     */
    private function calculateRightSideNaturalEndRow($employeeSkillCardData): int{
        $competencyCount = count($employeeSkillCardData[0]['target_ratings'] ?? []);

        $ratingsSectionEndRow = 4 + 8;
        $performanceLevelFloorRow = $ratingsSectionEndRow + 1 + 3;

        $functionalCompetenciesNaturalEndRow = 6 + ($competencyCount * 2);
        $functionalCompetenciesEndRow = max($functionalCompetenciesNaturalEndRow, $performanceLevelFloorRow);

        $approvalStartRow = $functionalCompetenciesEndRow + 1;

        return $approvalStartRow + 4;
    }

    private function createSkillCard(Worksheet $sheet, $employeeSkillCardData){
        $columnMetrics = $this->calculateColumnMetrics($employeeSkillCardData);
        $leftBlockEndColumnIndex = $columnMetrics['leftBlockEndColumnIndex'];
        $rightStartColumnIndex = $columnMetrics['rightStartColumnIndex'];

        $rightSideNaturalEndRow = $this->calculateRightSideNaturalEndRow($employeeSkillCardData);

        /*
        |--------------------------------------------------------------------------
        | LEFT SIDE
        |--------------------------------------------------------------------------
        */

        $this->createLeftHeader($sheet, $leftBlockEndColumnIndex);

        $this->createEmployeeSection($sheet, $employeeSkillCardData, $leftBlockEndColumnIndex);

        $leftMaxRow = $this->createProductLineSection($sheet, $employeeSkillCardData, $rightSideNaturalEndRow);

        $this->createStationSection($sheet, $employeeSkillCardData, $leftBlockEndColumnIndex);


        /*
        |--------------------------------------------------------------------------
        | RIGHT SIDE
        |--------------------------------------------------------------------------
        */

        $this->createRightHeader($sheet, $rightStartColumnIndex);

        $ratingsSectionEndRow = $this->createRatingsSection($sheet, $employeeSkillCardData, $rightStartColumnIndex);

        // Performance Level is fixed-size, so its start/end rows are already known before Functional Competencies renders
        $performanceLevelStartRow = $ratingsSectionEndRow + 1;
        $performanceLevelFloorRow = $performanceLevelStartRow + 3;

        $functionalCompetenciesEndRow = $this->createFunctionalCompetencies(
            $sheet,
            $employeeSkillCardData,
            $rightStartColumnIndex,
            $performanceLevelFloorRow
        );

        $performanceLevelEndRow = $this->createPerformanceLevel($sheet, $rightStartColumnIndex, $performanceLevelStartRow, $functionalCompetenciesEndRow);

        // Approval spans the full right-block width, so it must clear whichever track ends lower
        $approvalStartRow = max($functionalCompetenciesEndRow, $performanceLevelEndRow) + 1;
        $naturalApprovalEndRow = $approvalStartRow + 4;

        // The taller side (left or right) sets the card's final bottom edge; Approval stretches to meet it
        $maxRow = max($leftMaxRow, $naturalApprovalEndRow);

        $approvalEndRow = $this->createApprovalSection($sheet, $rightStartColumnIndex, $approvalStartRow, $maxRow);

        $maxColumnIndex = $rightStartColumnIndex + 7;

        $this->setupSheet($sheet, $maxRow, $maxColumnIndex, $leftBlockEndColumnIndex);

        $this->applyBorders($sheet, $maxRow, $maxColumnIndex);
    }

    private function createLeftHeader(Worksheet $sheet, int $leftBlockEndColumnIndex){
        $leftEndColumn = $this->columnLetter($leftBlockEndColumnIndex);

        /*
        |--------------------------------------------------------------------------
        | COMPANY NAME
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("A1:{$leftEndColumn}2");

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

        $sheet->mergeCells("A3:{$leftEndColumn}3");

        $sheet->setCellValue(
            'A3',
            'PERSONNEL SKILL CARD'
        );

        $sheet->getStyle("A3:{$leftEndColumn}3")
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

        $sheet->mergeCells("A4:{$leftEndColumn}4");

        $sheet->setCellValue(
            'A4',
            'SECTION'
        );

        $sheet->getStyle("A4:{$leftEndColumn}4")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A4:{$leftEndColumn}4")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("A4:{$leftEndColumn}4")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');
    }

    private function createEmployeeSection(Worksheet $sheet, $employeeSkillCardData, int $leftBlockEndColumnIndex){
        $leftEndColumn = $this->columnLetter($leftBlockEndColumnIndex);

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
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setWrapText(true);
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
        | PHOTO (stretches rightward to stay aligned with the expanded station grid)
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("I5:{$leftEndColumn}9");

        $sheet->setCellValue(
            'I5',
            'PHOTO'
        );

        $sheet->getStyle("I5:{$leftEndColumn}9")
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

        $sheet->mergeCells("I10:{$leftEndColumn}10");

        $sheet->setCellValue(
            'I10',
            'POINTS'
        );
        $sheet->getStyle("I10:{$leftEndColumn}10")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function createProductLineSection(Worksheet $sheet, $employeeSkillCardData, int $minimumEndRow = 0): int{
        $prodAreastartRow = 11;
        $prodAreaendRow = 12;

        /*
        |--------------------------------------------------------------------------
        | PRODUCT AREA
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("A{$prodAreastartRow}:D{$prodAreaendRow}");

        $sheet->setCellValue("A{$prodAreastartRow}", $employeeSkillCardData[0]['production_area'][0]['production_area_name'] ?? 'NOT FOUND'); //Sample Data only

        $sheet->getStyle("A{$prodAreastartRow}:D{$prodAreaendRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle("A{$prodAreastartRow}:D{$prodAreaendRow}")
            ->getFont()
            ->setSize(10)
            ->setBold(true);

        // Shared anchor so Certification Details (A:B) and Product Lines (C:D) stay aligned as parallel tracks
        $leftDynamicStartRow = $prodAreaendRow + 1;

        /*
        |--------------------------------------------------------------------------
        | CERTIFICATION DETAILS
        |--------------------------------------------------------------------------
        */

        $startRow2 = $leftDynamicStartRow;
        $startRow4 = $leftDynamicStartRow + 2;
        $startRow5 = $leftDynamicStartRow + 3;
        $startRow6 = $leftDynamicStartRow + 4;
        $startRow7 = $leftDynamicStartRow + 5;
        $startRow8 = $leftDynamicStartRow + 6;
        $startRow9 = $leftDynamicStartRow + 7;
        $startRow10 = $leftDynamicStartRow + 8;

        $productLines = $employeeSkillCardData[0]['target_rating_product_lines'] ?? [];
        $productLineCount = count($productLines);
        $productLinesEndRow = $productLineCount > 0
            ? $leftDynamicStartRow + ($productLineCount * 2) - 1
            : $leftDynamicStartRow - 1;

        // The taller of the two parallel tracks (or the right side's predicted height) sets the card's left-side bottom edge; Certification Details stretches to meet it
        $leftMaxRow = max($startRow10, $productLinesEndRow, $minimumEndRow);

        $sheet->mergeCells("A{$startRow2}:B{$startRow4}");

        $this->addImage(
            $sheet,
            public_path('images/skill_card/certification_image.png'),
            "A{$startRow2}",
            71,
            69,
        );

        $sheet->getStyle("A{$startRow2}:B{$leftMaxRow}")
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

        // Stretches down to $leftMaxRow (instead of stopping at $startRow10) to fill any gap left by a shorter Product Lines list
        $sheet->mergeCells("A{$startRow10}:B{$leftMaxRow}");
        $sheet->setCellValue("A{$startRow10}", $employeeSkillCardData[0]['validity_period'] ?? 'NOT FOUND');

        /*
        |--------------------------------------------------------------------------
        | PRODUCT LINES (DYNAMIC - 2 rows per item, columns C:D)
        |--------------------------------------------------------------------------
        */

        $currentRow = $leftDynamicStartRow;
        $stations = $employeeSkillCardData[0]['target_ratings'] ?? [];
        $actualRatingsLookup = $this->buildActualRatingsLookup($employeeSkillCardData);

        foreach ($productLines as $index => $productLine) {
            $isLast = $index === $productLineCount - 1;
            $naturalEndRow = $currentRow + 1;
            $isStretched = $isLast && $leftMaxRow > $naturalEndRow;

            $topRow = $currentRow;
            $bottomRow = $currentRow + 1;
            $endRow = $isStretched ? $leftMaxRow : $naturalEndRow;

            $productLineName = $productLine['target_product_line_name'] ?? 'NOT FOUND';

            $range = "C{$topRow}:D{$endRow}";

            $sheet->mergeCells($range);

            $sheet->setCellValue(
                "C{$topRow}",
                $productLineName
            );

            $sheet->getStyle($range)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);

            // Cross-section matrix under each station: row 1 stays as 2 unmerged quadrant cells (Q1/Q2);
            // rows 2+ merge vertically per column (Q3/Q4) only when this item is stretched across extra rows
            $stationColumnIndex = Coordinate::columnIndexFromString('E');

            foreach ($stations as $station) {
                $col1 = $this->columnLetter($stationColumnIndex);
                $col2 = $this->columnLetter($stationColumnIndex + 1);

                if ($isStretched) {
                    $sheet->mergeCells("{$col1}{$bottomRow}:{$col1}{$endRow}");
                    $sheet->mergeCells("{$col2}{$bottomRow}:{$col2}{$endRow}");
                }

                $competencyName = $station['target_rating_name'] ?? null;
                $actualRating = $actualRatingsLookup[$productLineName][$competencyName] ?? null;
                $rating = $this->resolveRatingLevel($actualRating);

                $this->applyQuadrantRating($sheet, $col1, $col2, $topRow, $bottomRow, $rating);

                $stationColumnIndex += 2;
            }

            $currentRow = $endRow + 1;
        }

        return $leftMaxRow;
    }

    /**
     * Indexes actual_ratings by [product_line_name][functional_competency_name] => actual_rating
     * for O(1) lookups while rendering the quadrant matrix grid.
     */
    private function buildActualRatingsLookup($employeeSkillCardData): array{
        $lookup = [];

        foreach ($employeeSkillCardData[0]['actual_ratings'] ?? [] as $actualRating) {
            $productLineName = $actualRating['product_line_name'] ?? null;
            $competencyName = $actualRating['functional_competency_name'] ?? null;

            if ($productLineName === null || $competencyName === null) {
                continue;
            }

            $lookup[$productLineName][$competencyName] = $actualRating['actual_rating'] ?? null;
        }

        return $lookup;
    }

    /**
     * Maps a raw actual_rating value to a 1-4 quadrant level using grading thresholds.
     * Adjust the ranges below if the grading criteria changes.
     */
    private function resolveRatingLevel($actualRating): int{
        if ($actualRating === null || $actualRating === '') {
            return 0;
        }

        $value = (float) $actualRating;

        switch (true) {
            case $value > 36:
                return 4;
            case $value >= 25:
                return 3;
            case $value >= 13:
                return 2;
            case $value >= 1:
                return 1;
            default:
                return 0;
        }
    }

    /**
     * Fills the 4 quadrant cells of a station cross-section box based on a 1-4 rating level.
     * Q1=top-left, Q2=top-right, Q3=bottom-left, Q4=bottom-right.
     */
    private function applyQuadrantRating(Worksheet $sheet, string $col1, string $col2, int $topRow, int $bottomRow, int $rating): void{
        $quadrants = [
            'q1' => "{$col1}{$topRow}",
            'q2' => "{$col2}{$topRow}",
            'q3' => "{$col1}{$bottomRow}",
            'q4' => "{$col2}{$bottomRow}",
        ];

        switch ($rating) {
            case 1:
                $filledQuadrants = ['q1'];
                break;
            case 2:
                $filledQuadrants = ['q1', 'q2'];
                break;
            case 3:
                $filledQuadrants = ['q1', 'q2', 'q4'];
                break;
            case 4:
                $filledQuadrants = ['q1', 'q2', 'q3', 'q4'];
                break;
            default:
                $filledQuadrants = [];
        }

        foreach ($filledQuadrants as $quadrant) {
            $sheet->getStyle($quadrants[$quadrant])
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('00FFFF');
        }
    }

    private function createStationSection(Worksheet $sheet, $employeeSkillCardData, int $leftBlockEndColumnIndex){
        $stationStartRow = 11;
        $stationStartRow1 = $stationStartRow + 1;
        $leftEndColumn = $this->columnLetter($leftBlockEndColumnIndex);

        // Station Assigned title
        $sheet->mergeCells("E{$stationStartRow}:{$leftEndColumn}{$stationStartRow}");
        $sheet->setCellValue("E{$stationStartRow}", 'STATION ASSIGNED');

        $sheet->getStyle("E{$stationStartRow}:{$leftEndColumn}{$stationStartRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

        // Station columns (DYNAMIC - 2 columns per item); counts above 4 push the right-side block further right
        $stations = $employeeSkillCardData[0]['target_ratings'] ?? [];
        $currentColumnIndex = Coordinate::columnIndexFromString('E');

        foreach ($stations as $station) {
            $startColumn = $this->columnLetter($currentColumnIndex);
            $endColumn = $this->columnLetter($currentColumnIndex + 1);
            $range = "{$startColumn}{$stationStartRow1}:{$endColumn}{$stationStartRow1}";

            $sheet->mergeCells($range);

            $sheet->setCellValue(
                "{$startColumn}{$stationStartRow1}",
                $station['target_rating_name'] ?? 'NOT FOUND'
            );

            $sheet->getStyle($range)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

            $currentColumnIndex += 2;
        }
    }

    private function createRightHeader(Worksheet $sheet, int $rightStartColumnIndex){
        $colFirst = $this->columnLetter($rightStartColumnIndex);
        $colFourth = $this->columnLetter($rightStartColumnIndex + 3);
        $colFifth = $this->columnLetter($rightStartColumnIndex + 4);
        $colLast = $this->columnLetter($rightStartColumnIndex + 7);

        /*
        |--------------------------------------------------------------------------
        | PRICON
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("{$colFirst}1:{$colFourth}3");

        $this->addImage(
            $sheet,
            public_path('images/skill_card/skill_card_pricon_logo_short.jpg'),
            "{$colFirst}1",
            220,
            42,
        );

        /*
        |--------------------------------------------------------------------------
        | COMPETENCY SKILLS ASSESSMENT
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("{$colFifth}1:{$colLast}3");

        $sheet->setCellValue(
            "{$colFifth}1",
            "COMPETENCY SKILLS\nASSESSMENT"
        );

        $sheet->getStyle("{$colFifth}1:{$colLast}3")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
    }

    private function createRatingsSection(Worksheet $sheet, $employeeSkillCardData, int $rightStartColumnIndex, int $startRow = 4): int{
        $colQ = $this->columnLetter($rightStartColumnIndex + 4);
        $colR = $this->columnLetter($rightStartColumnIndex + 5);
        $colS = $this->columnLetter($rightStartColumnIndex + 6);
        $colT = $this->columnLetter($rightStartColumnIndex + 7);

        $referenceEndRow = $startRow + 1;
        $titleRow = $startRow + 2;
        $numbersRow = $startRow + 3;
        $imagesStartRow = $startRow + 4;
        $imagesEndRow = $startRow + 5;
        $descStartRow = $startRow + 6;
        $descEndRow = $startRow + 8;

        /*  
        | REFERENCE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("{$colQ}{$startRow}:{$colT}{$referenceEndRow}");

        $sheet->setCellValue(
            "{$colQ}{$startRow}",
            'REFERENCE'
        );

        $sheet->getStyle("{$colQ}{$startRow}:{$colT}{$referenceEndRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        /*
        |--------------------------------------------------------------------------
        | RATINGS TITLE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells("{$colQ}{$titleRow}:{$colT}{$titleRow}");

        $sheet->setCellValue(
            "{$colQ}{$titleRow}",
            'RATINGS'
        );

        $sheet->getStyle("{$colQ}{$titleRow}:{$colT}{$titleRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("{$colQ}{$titleRow}:{$colT}{$titleRow}")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("{$colQ}{$titleRow}:{$colT}{$titleRow}")
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
            $colQ => '1',
            $colR => '2',
            $colS => '3',
            $colT => '4',
        ];

        foreach ($ratings as $column => $rating) {

            $cell = "{$column}{$numbersRow}";

            $sheet->setCellValue(
                $cell,
                $rating
            );

            $sheet->getStyle($cell)
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
            $colQ => 'actual_1',
            $colR => 'actual_2',
            $colS => 'actual_3',
            $colT => 'actual_4',
        ];

        foreach ($rating_values as $column => $rating_v) {

            $range = "{$column}{$imagesStartRow}:{$column}{$imagesEndRow}";

            $sheet->mergeCells($range);

            $this->addImage(
                $sheet,
                public_path('images/skill_card/skill_card_'.$rating_v.'.png'),
                "{$column}{$imagesStartRow}", //cell
                340, //width
                30, //height
                12
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4 RATINGS VALUES DESCRIPTION
        |--------------------------------------------------------------------------
        */

        $rating_values_description = [
            $colQ => $employeeSkillCardData[0]['target_rating_description'][0]['target_rating_description'] ?? '',
            $colR => $employeeSkillCardData[0]['target_rating_description'][1]['target_rating_description'] ?? '',
            $colS => $employeeSkillCardData[0]['target_rating_description'][2]['target_rating_description'] ?? '',
            $colT => $employeeSkillCardData[0]['target_rating_description'][3]['target_rating_description'] ?? '',
        ];

        foreach ($rating_values_description as $column => $rating_description) {

            $range = "{$column}{$descStartRow}:{$column}{$descEndRow}";

            $sheet->mergeCells($range);

            $sheet->setCellValue(
                "{$column}{$descStartRow}",
                $rating_description
            );

            $sheet->getStyle($range)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
        }

        return $descEndRow;
    }

    private function createFunctionalCompetencies(Worksheet $sheet, $employeeSkillCardData, int $rightStartColumnIndex, int $minimumEndRow, int $headerStartRow = 4): int{
        $colM = $this->columnLetter($rightStartColumnIndex);
        $colO = $this->columnLetter($rightStartColumnIndex + 2);
        $colP = $this->columnLetter($rightStartColumnIndex + 3);

        /*
        |--------------------------------------------------------------------------
        | FUNCTIONAL COMPETENCIES
        |--------------------------------------------------------------------------
        */

        $headerEndRow = $headerStartRow + 2;

        $sheet->mergeCells("{$colM}{$headerStartRow}:{$colO}{$headerEndRow}");

        $sheet->setCellValue(
            "{$colM}{$headerStartRow}",
            'FUNCTIONAL COMPETENCIES'
        );

        $sheet->getStyle("{$colM}{$headerStartRow}:{$colO}{$headerEndRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle("{$colM}{$headerStartRow}:{$colO}{$headerEndRow}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');

        $sheet->mergeCells("{$colP}{$headerStartRow}:{$colP}{$headerEndRow}");

        $sheet->setCellValue(
            "{$colP}{$headerStartRow}",
            'TARGET RATING'
        );

        $sheet->getStyle("{$colP}{$headerStartRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle("{$colP}{$headerStartRow}:{$colP}{$headerEndRow}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');

        /*
        |--------------------------------------------------------------------------
        | DYNAMIC ROWS - 2 rows per competency; the last one stretches down to
        | $minimumEndRow so there is no gap before the Approval / Performance Level section
        |--------------------------------------------------------------------------
        */

        $competencies = $employeeSkillCardData[0]['target_ratings'] ?? [];
        $totalCompetencies = count($competencies);
        $currentRow = $headerEndRow + 1;
        $endRow = $currentRow - 1;

        foreach ($competencies as $index => $competency) {
            $naturalEndRow = $currentRow + 1;
            $isLast = $index === $totalCompetencies - 1;
            $endRow = ($isLast && $naturalEndRow < $minimumEndRow) ? $minimumEndRow : $naturalEndRow;

            $sheet->mergeCells("{$colM}{$currentRow}:{$colO}{$endRow}");
            $sheet->setCellValue("{$colM}{$currentRow}", $competency['target_rating_name'] ?? 'NOT FOUND');

            $sheet->mergeCells("{$colP}{$currentRow}:{$colP}{$endRow}");
            $sheet->setCellValue("{$colP}{$currentRow}", $competency['target_rating_value'] ?? 'NOT FOUND');

            $sheet->getStyle("{$colM}{$currentRow}:{$colP}{$endRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

            $currentRow = $endRow + 1;
        }

        return max($endRow, $minimumEndRow);
    }
    
    private function createPerformanceLevel(Worksheet $sheet, int $rightStartColumnIndex, int $startRow, int $minimumEndRow): int{
        $colQ = $this->columnLetter($rightStartColumnIndex + 4);
        $colR = $this->columnLetter($rightStartColumnIndex + 5);
        $colS = $this->columnLetter($rightStartColumnIndex + 6);
        $colT = $this->columnLetter($rightStartColumnIndex + 7);

        /*
        |--------------------------------------------------------------------------
        | PERFORMANCE LEVEL (OVER ALL)
        |--------------------------------------------------------------------------
        */

        $labelsRow = $startRow + 1;
        $imagesRow = $startRow + 2;
        $rangesStartRow = $startRow + 3;
        // Stretches down to $minimumEndRow so Performance Level stays aligned with however tall Functional Competencies grows
        $rangesEndRow = max($rangesStartRow, $minimumEndRow);

        $sheet->mergeCells("{$colQ}{$startRow}:{$colT}{$startRow}");

        $sheet->setCellValue(
            "{$colQ}{$startRow}",
            'PERFORMANCE LEVEL (OVER ALL)'
        );

        $sheet->getStyle("{$colQ}{$startRow}:{$colT}{$startRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("{$colQ}{$startRow}:{$colT}{$startRow}")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("{$colQ}{$startRow}:{$colT}{$startRow}")
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
            "{$colQ}{$labelsRow}" => 'Level 1',
            "{$colR}{$labelsRow}" => 'Level 2',
            "{$colS}{$labelsRow}" => 'Level 3',
            "{$colT}{$labelsRow}" => 'Level 4',
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
            "{$colQ}{$imagesRow}" => 'level_1',
            "{$colR}{$imagesRow}" => 'level_2',
            "{$colS}{$imagesRow}" => 'level_3',
            "{$colT}{$imagesRow}" => 'level_4',
        ];

        foreach ($rating_overall_images as $overall_image => $rating_overall) {

            $sheet->mergeCells($overall_image);

            $startCell = explode(':', $overall_image)[0];

            $this->addImage(
                $sheet,
                public_path('images/skill_card/skill_card_'.$rating_overall.'_old.jpg'),
                $startCell, //cell
                340, //width
                30, //height
                10
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4 RATINGS OVERALL RANGE
        |--------------------------------------------------------------------------
        */
        $employeeSkillCardData = $this->employeeSkillCardData;
        $total_rating_count = 0;
        for ($i = 0; $i < count($employeeSkillCardData[0]['target_ratings']); $i++) {
            $total_rating_count += $employeeSkillCardData[0]['target_ratings'][$i]['target_rating_value'];
        }
        $total_rating_count = $total_rating_count * count($employeeSkillCardData[0]['target_rating_product_lines']);
        $total_rating_count_per_range = $total_rating_count / 4; // e.g. 20

        $columns = [$colQ, $colR, $colS, $colT];
        $rating_overall_ranges = [];

        foreach ($columns as $index => $col) {
            $start = ($index * $total_rating_count_per_range) + 1;
            $end   = ($index + 1) * $total_rating_count_per_range;

            $rating_overall_ranges[$col] = $start . ' - ' . $end;
        }

        foreach ($rating_overall_ranges as $column => $label) {
            $range = "{$column}{$rangesStartRow}:{$column}{$rangesEndRow}";

            $sheet->mergeCells($range);

            $sheet->setCellValue(
                "{$column}{$rangesStartRow}",
                $label
            );

            $sheet->getStyle($range)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        return $rangesEndRow;
    }

    private function createApprovalSection(Worksheet $sheet, int $rightStartColumnIndex, int $startRow, int $maxRow): int{
        $colFirst = $this->columnLetter($rightStartColumnIndex);
        $colLast = $this->columnLetter($rightStartColumnIndex + 7);

        $imageStartRow = $startRow + 1;
        // Stretches down to $maxRow to absorb any trailing gap instead of leaving blank rows above the outer border
        $imageEndRow = max($imageStartRow + 3, $maxRow);

        $sheet->mergeCells("{$colFirst}{$startRow}:{$colLast}{$startRow}");

        $sheet->setCellValue(
            "{$colFirst}{$startRow}",
            'APPROVED BY:'
        );

        $sheet->getStyle("{$colFirst}{$startRow}:{$colLast}{$startRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("{$colFirst}{$startRow}:{$colLast}{$startRow}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('A8F3C6');

        $sheet->mergeCells("{$colFirst}{$imageStartRow}:{$colLast}{$imageEndRow}");

        $this->addImage(
            $sheet,
            public_path('images/skill_card/skill_card_approvers.jpg'),
            "{$colFirst}{$imageStartRow}",
            319,
            54,
            15,
            8
        );

        return $imageEndRow;
    }

    private function applyBorders(Worksheet $sheet, int $maxRow, int $maxColumnIndex){
        $range = 'A1:' . $this->columnLetter($maxColumnIndex) . $maxRow;

        $sheet->getStyle($range)
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
                $this->createSkillCard($sheet, $employeeSkillCardData);
            }
        ];
    }
}

?>