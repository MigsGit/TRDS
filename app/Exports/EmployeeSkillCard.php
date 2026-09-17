<?php

namespace App\Exports;

use App\Exports\EmployeeSkillCardSheets\SkillCard;

// use App\Model\InspectorSkillChart\InspectorSkillChartSetting;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EmployeeSkillCard implements WithMultipleSheets
{
    protected $productLines;

    public function __construct($productLines = null)
    {
        $this->productLines = $productLines;
    }

    public function sheets(): array
    {
        $sheets = [];

        // foreach ($this->productLines as $productLine){

            $sheets[] = new SkillCard(
                // $productLine
            );

        // }

        // Summary will be added later
        // $sheets[] = new SummarySkillCardSheet(...);

        return $sheets;
    }
}
