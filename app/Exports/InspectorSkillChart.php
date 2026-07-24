<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\InspectorSkillChartSheets\TSF1;
use App\Exports\InspectorSkillChartSheets\TSF3;
use App\Exports\InspectorSkillChartSheets\CN;
use App\Exports\InspectorSkillChartSheets\CNF3;
use App\Exports\InspectorSkillChartSheets\PPDCN;
use App\Exports\InspectorSkillChartSheets\PPDTS;
use App\Exports\InspectorSkillChartSheets\PPDF3;
use App\Exports\InspectorSkillChartSheets\YF;

class InspectorSkillChart implements WithMultipleSheets
{
    // protected $reportId;
    protected $selectedSheets;

    public function __construct(array $selectedSheets)
    {
        // $this->reportId = $reportId;
        $this->selectedSheets = $selectedSheets;
    }

    public function sheets(): array
    {
        $availableSheets = [
            'TSF1' => new TSF1(),
            'TSF3' => new TSF3(),
            'CN' => new CN(),
            'CNF3' => new CNF3(),
            'PPDCN' => new PPDCN(),
            'PPDTS' => new PPDTS(),
            'PPDF3' => new PPDF3(),
            'YF' => new YF(),
        ];

        $sheets = [];

        foreach ($this->selectedSheets as $sheet) {
            if (isset($availableSheets[$sheet])) {
                $sheets[] = $availableSheets[$sheet];
            }
        }

        return $sheets;
    }

}
