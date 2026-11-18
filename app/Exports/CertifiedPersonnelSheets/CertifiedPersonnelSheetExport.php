<?php
namespace App\Exports\CertifiedPersonnelSheets;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CertifiedPersonnelSheetExport implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected $category;
    protected $qcSlips;
    protected $product_line;
    protected $positions_with_training;

    public function __construct(string $category, $qcSlips, $product_line, $positions_with_training)
    {
        $this->category = $category;
        $this->qcSlips = $qcSlips;
        $this->product_line = $product_line;
        $this->positions_with_training = $positions_with_training;
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

            $prodApp = null;
            $engApp  = null;
            $qcApp   = null;
            // Map Approvers
            // if (in_array($this->category, $this->positions_with_training)) {
            if ($this->category == 'Operator') {
                // Perform any specific logic for positions with training
                $approvers = collect($slip->c_lqc_training_item_results_details);

                
                $prodApp = $approvers->first(function ($item) {
                    return stripos($item->position, 'prod') !== false;
                });

                $engApp  = $approvers->first(function ($item) {
                    return stripos($item->position, 'eng') !== false;
                });

                $qcApp   = $approvers->first(function ($item) {
                    return stripos($item->position, 'qc') !== false;
                });

                // 2. Extract Name and Validation Date safely
                $prod = [
                    'name'           => $prodApp ? $prodApp->trainer_name : null,
                    'date'=> $prodApp ? $prodApp->validation_date : null,
                ];

                $eng = [
                    'name'           => $engApp ? $engApp->trainer_name : null,
                    'date'=> $engApp ? $engApp->validation_date : null,
                ];

                $qc = [
                    'name'           => $qcApp ? $qcApp->trainer_name : null,
                    'date'=> $qcApp ? $qcApp->validation_date : null,
                ];

            }
            else{
                $approvers = collect($slip->op_approvers);
                switch ($this->category) {
                    case 'Inspector':
                        $qcApp = $approvers->firstWhere('approval_status', 'ALQCTQ');
                        break;
                    case 'OPTECH':
                        // $prodApp = $approvers->firstWhere('approval_status', 'APRODTO');
                        // $engApp  = $approvers->firstWhere('approval_status', 'BENGGTQ');
                        // $qcApp   = $approvers->firstWhere('approval_status', 'CQCC');
                        break;
                    case 'TECHNICIAN':
                        $prodApp = $approvers->firstWhere('approval_status', 'APRODTO');
                        $engApp  = $approvers->firstWhere('approval_status', 'BENGGTQ');
                        $qcApp   = $approvers->firstWhere('approval_status', 'CQCC');
                        break;
                    default:
                        // Default to OPERATOR approvers if category doesn't match
                        // $prodApp = $approvers->firstWhere('approval_status', 'APRODTO');
                        // $engApp  = $approvers->firstWhere('approval_status', 'BENGGTQ');
                        // $qcApp   = $approvers->firstWhere('approval_status', 'CQCC');
                }

                $formatApprover = function ($app) {
                    if (!$app) return ['name' => '', 'date' => ''];

                    $explodedNames = explode(' | ', utf8_decode($app->formatted['name']) ?? '');
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
            }

            

            foreach ($slip->qc_slip_employees as $emp) {
                    // dd('reason_details', $slip->reason_details);
                $rows[] = [
                    'emp_no'       => $emp->employee_no,
                    'emp_name'     => $emp->employee_info->EmpName ?? '',
                    // 'product_line' => $productLine,
                    'product_line' => $this->product_line,
                    'station'      => $emp->get_station_to->dropdown_masters_details ?? '',
                    // 'category'     => 'CERTIFICATION',
                    'category' => Str::contains(strtolower(json_encode($slip->reason_details ?? [])), 're-certification') 
                    ? 'RE-CERTIFICATION' 
                    : 'CERTIFICATION',
                    'date_hired'   => !empty($emp->employee_info->DateHired) ? Carbon::parse($emp->employee_info->DateHired)->format('F d, Y') : '',
                    'prod_name'    => !empty($prod['name']) ? $prod['name'] : 'N/A',
                    'prod_date'    => !empty($prod['date']) ? $prod['date'] : 'N/A',
                    'eng_name'     => !empty($eng['name']) ? $eng['name'] : 'N/A',
                    'eng_date'     => !empty($eng['date']) ? $eng['date'] : 'N/A',
                    'qc_name'      => !empty($qc['name']) ? $qc['name'] : 'N/A',
                    'qc_date'      => !empty($qc['date']) ? $qc['date'] : 'N/A',
                    'remarks'      => 'PASSED',
                ];
            }
        }

        usort($rows, function ($a, $b) {
            // Helper function to extract valid Carbon date based on priority
            $getDate = function ($row) {
                if (!empty($row['qc_date']) && $row['qc_date'] !== 'N/A') {
                    return Carbon::parse($row['qc_date']);
                }
                if (!empty($row['eng_date']) && $row['eng_date'] !== 'N/A') {
                    return Carbon::parse($row['eng_date']);
                }
                if (!empty($row['prod_date']) && $row['prod_date'] !== 'N/A') {
                    return Carbon::parse($row['prod_date']);
                }
                return null; // Fallback if all dates are N/A
            };

            $dateA = $getDate($a);
            $dateB = $getDate($b);

            // Handle cases where all dates are N/A (push them to the bottom)
            if ($dateA === null && $dateB === null) return 0;
            if ($dateA === null) return 1;
            if ($dateB === null) return -1;

            // Sort ascending (Oldest date on top)
            return $dateA->timestamp <=> $dateB->timestamp;
        });
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