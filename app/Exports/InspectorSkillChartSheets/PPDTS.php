<?php

namespace App\Exports\InspectorSkillChartSheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class PPDTS implements FromCollection, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect();
    }

    public function title(): string
    {
        return 'PPD-TS';
    }
}
