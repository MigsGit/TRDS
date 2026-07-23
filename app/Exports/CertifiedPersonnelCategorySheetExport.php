<?php
namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CertifiedPersonnelCategorySheetExport implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected $category;
    protected $qcSlips;

    public function __construct(string $category, $qcSlips)
    {
        $this->category = $category;
        $this->qcSlips = $qcSlips;
    }

    public function title(): string
    {
        return $this->category;
    }

    public function view(): View
    {
        $rows = [];

        foreach ($this->qcSlips as $slip) {
            $productLine = $slip->product_line_details->dropdown_masters_details ?? '';

            // Map Approvers
            $approvers = collect($slip->op_approvers);

            $prodApp = $approvers->firstWhere('approval_status', 'APRODTO');
            $engApp  = $approvers->firstWhere('approval_status', 'BENGGTQ');
            $qcApp   = $approvers->firstWhere('approval_status', 'CQCC');

            $formatApprover = function ($app) {
                if (!$app) return ['name' => '', 'date' => ''];

                // Check if second approver exists and is not empty
                // $hasSecond = !empty($app->second_approver) || !empty($app->second_approver_2);

                // if ($hasSecond) {
                //     $names = array_filter([$app->second_approver ?? null, $app->second_approver_2 ?? null]);
                //     $rawDate = $app->second_date ?? $app->second_approver_ddate ?? null;
                // } else {
                //     $names = array_filter([$app->first_approver ?? null, $app->first_approver_2 ?? null]);
                //     $rawDate = $app->first_date ?? $app->first_approver_ddate ?? null;
                // }

                // $dateStr = !empty($rawDate) ? Carbon::parse($rawDate)->format('F d, Y') : '';

                $explodedNames = explode(' | ', $app->formatted['name'] ?? '');
                // dd($explodedNames);
                // $names = $app->formatted['name'] ?? '';
                $dateStr = $app->formatted['date'] ?? '';
                $rawRemarks = $app->formatted['remarks'] ?? '';

                return [
                    'name' => implode("\n", $explodedNames),
                    'date' => $dateStr,
                    'remarks' => $rawRemarks
                ];
            };


            $prod = $formatApprover($prodApp);
            $eng  = $formatApprover($engApp);
            $qc   = $formatApprover($qcApp);

            foreach ($slip->qc_slip_employees as $emp) {
                    // dd('reason_details', $slip->reason_details);
                $rows[] = [
                    'emp_no'       => $emp->employee_no,
                    'emp_name'     => $emp->employee_info->EmpName ?? '',
                    'product_line' => $productLine,
                    'station'      => $emp->get_station_to->dropdown_masters_details ?? '',
                    // 'category'     => 'CERTIFICATION',
                    'category' => Str::contains(strtolower(json_encode($slip->reason_details ?? [])), 're-certification') 
                    ? 'RE-CERTIFICATION' 
                    : 'CERTIFICATION',
                    'date_hired'   => !empty($emp->employee_info->DateHired) ? Carbon::parse($emp->employee_info->DateHired)->format('F d, Y') : '',
                    'prod_name'    => $prod['name'],
                    'prod_date'    => $prod['date'],
                    'eng_name'     => $eng['name'],
                    'eng_date'     => $eng['date'],
                    'qc_name'      => $qc['name'],
                    'qc_date'      => $qc['date'],
                    'remarks'      => 'PASSED',
                ];
            }
        }
        return view('exports.certified_personnel', [
            'rows'        => $rows,
            'updated_as'  => Carbon::now()->format('M-y'),
            'revision_no' => '1',
            'frequency'   => 'Monthly'
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Show grid lines explicitly
        $sheet->setShowGridLines(true);

        return [
            // Title formatting
            'A2' => ['font' => ['bold' => true, 'size' => 14]],
            'A4' => ['font' => ['italic' => true, 'size' => 10]],
            'C4' => ['font' => ['bold' => true, 'underline' => true]],
        ];
    }
}