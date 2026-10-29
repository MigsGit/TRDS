<?php
namespace App\Exports;

use App\Exports\CertifiedPersonnelSheets\CertifiedPersonnelSheetExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CertifiedPersonnelExport implements WithMultipleSheets
{
    protected $groupedQcSlips;
    // protected $position;
    protected $product_line;
    protected $positions_with_training;

    public function __construct($groupedQcSlips, $product_line, $positions_with_training)
    {
        $this->groupedQcSlips = $groupedQcSlips;
        // $this->position = $position;
        $this->product_line = $product_line;
        $this->positions_with_training = $positions_with_training;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Predefined sheet names as shown in your UI screenshot
        // $categories = ['OPERATOR', 'INSPECTOR', 'OPTECH', 'TECHNICIAN', 'WAREHOUSE', 'PACKING', 'SUPERVISOR', 'ENGINEERING', 'PPC'];
        $categories = ['Operator','Inspector', 'Technician', 'Supervisor', 'MH'];


        foreach ($categories as $category) {
            // Match category case-insensitively
            $slips = $this->groupedQcSlips->first(function ($val, $key) use ($category) {
                return strtoupper($key) === strtoupper($category);
            }) ?? collect();

            // $sheets[] = new CertifiedPersonnelSheetExport($this->position, $this->groupedQcSlips[$this->position], $this->product_line, $this->positions_with_training);
            $sheets[] = new CertifiedPersonnelSheetExport($category, $slips, $this->product_line, $this->positions_with_training);
        }

        return $sheets;


        // $sheets = [];

        // // Predefined sheet names as shown in your UI screenshot
        // $categories = ['OPERATOR', 'INSPECTOR', 'OPTECH', 'TECHNICIAN', 'WAREHOUSE', 'PACKING', 'SUPERVISOR', 'ENGINEERING', 'PPC'];

        // // foreach ($categories as $category) {
        // //     // Match category case-insensitively
        // //     $slips = $this->groupedQcSlips->first(function ($val, $key) use ($category) {
        // //         return strtoupper($key) === $category;
        // //     }) ?? collect();

        //     $sheets[] = new CertifiedPersonnelSheetExport($this->position, $this->groupedQcSlips[$this->position], $this->product_line, $this->positions_with_training);
        // // }

        // return $sheets;
    }
}