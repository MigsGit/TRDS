<?php
namespace App\Exports;

use App\Exports\CertifiedPersonnelCategorySheetExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CertifiedPersonnelExport implements WithMultipleSheets
{
    protected $groupedQcSlips;

    public function __construct($groupedQcSlips)
    {
        $this->groupedQcSlips = $groupedQcSlips;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Predefined sheet names as shown in your UI screenshot
        $categories = ['OPERATOR', 'INSPECTOR', 'OPTECH', 'TECHNICIAN', 'WAREHOUSE', 'PACKING', 'SUPERVISOR', 'ENGINEERING', 'PPC'];

        foreach ($categories as $category) {
            // Match category case-insensitively
            $slips = $this->groupedQcSlips->first(function ($val, $key) use ($category) {
                return strtoupper($key) === $category;
            }) ?? collect();

            $sheets[] = new CertifiedPersonnelCategorySheetExport($category, $slips);
        }

        return $sheets;
    }
}