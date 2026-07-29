<?php

namespace App\Exports;

use App\Model\InspectorSkillChart\InspectorSkillChartSetting;
// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\InspectorSkillChartSheets\TSF1;
use App\Exports\InspectorSkillChartSheets\TSF3;
use App\Exports\InspectorSkillChartSheets\CN;
use App\Exports\InspectorSkillChartSheets\CNF3;
use App\Exports\InspectorSkillChartSheets\PPDCN;
use App\Exports\InspectorSkillChartSheets\PPDTS;
use App\Exports\InspectorSkillChartSheets\PPDF3;
use App\Exports\InspectorSkillChartSheets\YF;

class InspectorSkillChart implements WithMultipleSheets{
    protected $selectedSheets;
    protected $processStationDetails;

    public function __construct(array $selectedSheets)
    {
        $this->selectedSheets = $selectedSheets;
        $this->processStationDetails = InspectorSkillChartSetting::where('status', 0)->orderBy('process_order')->get();
    }

    public function sheets(): array{

        $availableSheets = [
            'TS-F1' => TSF1::class,
            'TS-F3' => TSF3::class,
            'CN'    => CN::class,
            'CN-F3' => CNF3::class,
            'PPD-CN'=> PPDCN::class,
            'PPD-TS'=> PPDTS::class,
            'PPD-F3'=> PPDF3::class,
            'YF'    => YF::class,
        ];

        $sheets = [];

        foreach ($this->selectedSheets as $sheet) {
            switch ($sheet) {
                case 'TS-F1':
                case 'TS-F3':
                    $group = 'TS';
                    break;

                case 'CN':
                case 'CN-F3':
                    $group = 'CN';
                    break;

                case 'PPD-CN':
                case 'PPD-TS':
                case 'PPD-F3':
                    $group = 'PPD';
                    break;

                case 'YF':
                    $group = 'YF';
                    break;

                default:
                    $group = null;
            }

            if (!isset($availableSheets[$sheet])) {
                continue;
            }

            $categories = $this->processStationDetails
            ->where('section', $sheet)
            ->groupBy('skill_category')
            ->map(function ($processes, $category){
                return [
                    'name' => $category,
                    'processes' => $processes->map(function ($process) {
                        return [
                            'name' => $process->process_station,
                            'order' => $process->process_order,
                            'product_lines' => strtoupper($process->product_line) == 'N/A' ? [] : array_map('trim', explode(',', $process->product_line)),
                        ];
                    })->values()->toArray(),
                ];
            })
            ->values();   

            $class = $availableSheets[$sheet];
            $sheets[] = new $class($categories, $group);
        }

        return $sheets;
    }
}
