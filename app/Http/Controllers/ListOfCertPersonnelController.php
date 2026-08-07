<?php

namespace App\Http\Controllers;

use App\Exports\CertifiedPersonnelExport;
use App\Model\DropdownMasterDetail;
use App\Model\Qc\QcReasonCertification;
use App\Model\QcSlip;
use App\Model\SystemOneHrisSubcon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ListOfCertPersonnelController extends Controller
{
    public function getDropdownSelectCertPersonnel(Request $request){
        $qcslips = QcSlip::with([
            'product_line_details'
        ])
        ->whereNull('deleted_at')->get();

        $section = $qcslips->pluck('section_category')->unique()->values()->toArray();
        $series = $qcslips->pluck('series_name')->unique()->values()->toArray();

        $product_line = $qcslips->pluck('product_line_details')->unique()->values()->toArray();


        return response()->json(['section' => $section, 'series' => $series, 'product_line' => $product_line, 'qcslips' => $qcslips]);
    }

    public function exportListCertPersonnel(Request $request){
    //     $personel = QcSlip::with([
    //         'product_line_details',
    //         'op_approvers',
    //         'qc_slip_employees',
    //         'qc_slip_employees.system_one_subcon_emp_info',
    //         'qc_slip_employees.system_one_hris_emp_info',
    //         // 'qc_slip_employees.system_one_hris_subcon',
    //         'qc_slip_employees.get_station_to',
    //         'qc_reason_certification'
    //     ])
    //     ->whereNull('deleted_at')
    //     ->where('status', 'OK')
    //     ->where('section_category', $request->section)
    //     ->where('product_line', $request->product_line)
    //     ->get()
    //     ->groupBy('position_category');

    //     // Getting the reason details for each slip in a single query to avoid N+1 problem
    //     // Step 1: Add reason IDs as an array to each slip object in the group
    //     $personel->transform(function ($group) {
    //         return $group->map(function ($slip) {
    //             $rawReasons = optional($slip->qc_reason_certification)->reason_of_certification;
                
    //             // Converts "211 | 212 | 213" to ["211", "212", "213"]
    //             $slip->reason_of_certification_ids = $rawReasons 
    //                 ? array_map('trim', explode('|', $rawReasons)) 
    //                 : [];

    //             return $slip;
    //         });
    //     });

    //     // Step 2: Extract ALL unique IDs across all groups to run ONE efficient batch query
    //     $allReasonIds = $personel->flatten(1)
    //         ->pluck('reason_of_certification_ids')
    //         ->flatten()
    //         ->unique()
    //         ->filter()
    //         ->values();

    //     // Execute your 2nd query (Replace `ReasonDetail` with your actual Model name)
    //     $reasonDetails = DropdownMasterDetail::whereIn('id', $allReasonIds)->get();
    //    // Step 3: Attach the queried details directly inside each slip object
    //     $personel->transform(function ($group) use ($reasonDetails) {
    //         return $group->map(function ($slip) use ($reasonDetails) {
    //             $slip->reason_details = collect($slip->reason_of_certification_ids)
    //                 ->map(function ($id) use ($reasonDetails) {
    //                     return $reasonDetails->firstWhere('id', $id);
    //                 })
    //                 ->filter()
    //                 ->values();

    //             return $slip;
    //         });
    //     });

        // ==========================================
        // 1. QUERY QC SLIPS WITH RELATIONS
        // ==========================================
        $personel = QcSlip::with([
                'product_line_details',
                'op_approvers',
                'qc_slip_employees',
                'qc_slip_employees.system_one_subcon_emp_info',
                'qc_slip_employees.system_one_hris_emp_info',
                'qc_slip_employees.get_station_to',
                'qc_reason_certification'
            ])
            ->whereNull('deleted_at')
            ->where('status', 'OK')
            ->where('section_category', $request->section)
            ->where('product_line', $request->product_line)
            ->get()
            ->groupBy('position_category');

        // ==========================================
        // 2. REASON OF CERTIFICATION BATCH QUERY
        // ==========================================
        // Step 2A: Parse reason IDs into arrays
        $personel->transform(function ($group) {
            return $group->map(function ($slip) {
                $rawReasons = optional($slip->qc_reason_certification)->reason_of_certification;
                
                $slip->reason_of_certification_ids = $rawReasons 
                    ? array_map('trim', explode('|', $rawReasons)) 
                    : [];

                return $slip;
            });
        });

        // Step 2B: Extract unique Reason IDs & fetch batch details
        $allReasonIds = $personel->flatten(1)
            ->pluck('reason_of_certification_ids')
            ->flatten()
            ->unique()
            ->filter()
            ->values();

        $reasonDetails = DropdownMasterDetail::whereIn('id', $allReasonIds)->get();

        // Step 2C: Attach reason details back to slips
        $personel->transform(function ($group) use ($reasonDetails) {
            return $group->map(function ($slip) use ($reasonDetails) {
                $slip->reason_details = collect($slip->reason_of_certification_ids)
                    ->map(function ($id) use ($reasonDetails) {
                        return $reasonDetails->firstWhere('id', $id);
                    })
                    ->filter()
                    ->values();

                return $slip;
            });
        });

        // ==========================================
        // 3. APPROVER EMPLOYEES BATCH QUERY
        // ==========================================
        // Collect all approver IDs across all slips/groups without N+1 queries
        $allApproverEmpNos = $personel->flatten(1)
        ->flatMap(function ($slip) {
            return collect($slip->op_approvers)->flatMap(function ($app) {
                $rawEmpNos = [
                    $app->first_approver ?? null,
                    $app->first_approver_2 ?? null,
                    $app->second_approver ?? null,
                    $app->second_approver_2 ?? null,
                ];

                // Split any strings containing '|' into individual employee numbers
                return collect($rawEmpNos)
                    ->filter()
                    ->flatMap(function ($val) {
                        return array_map('trim', explode('|', $val));
                    });
            });
        })
        ->filter()
        ->unique()
        ->values();


        // Execute 1 single query using SystemOneHrisSubcon and 'EmpNo' column
        $employeeInfoMap = SystemOneHrisSubcon::whereIn('EmpNo', $allApproverEmpNos)
            ->get()
            ->keyBy('EmpNo');
        // return response()->json(['personel' => $personel, 'allApproverEmpNos' => $employeeInfoMap]);


        // ==========================================
        // 4. CLOSURE FOR FORMATTING APPROVERS
        // ==========================================
        $formatApprover = function ($app) use ($employeeInfoMap) {
            if (!$app) return ['name' => '', 'date' => ''];

            // Check if second approver exists and is not empty
            $hasSecond = !empty($app->second_approver) || !empty($app->second_approver_2);

            if ($hasSecond) {
                $rawEmpNumbers = array_filter([$app->second_approver ?? null, $app->second_approver_2 ?? null]);
                $rawDate       = $app->second_date ?? $app->second_approver_ddate ?? null;
                $rawRemarks    = $app->second_status ?? null;
            } else {
                $rawEmpNumbers = array_filter([$app->first_approver ?? null, $app->first_approver_2 ?? null]);
                $rawDate       = $app->first_date ?? $app->first_approver_ddate ?? null;
                $rawRemarks    = $app->first_status ?? null;

            }

            // Explode piped strings into an array of clean employee numbers
            $empNumbers = collect($rawEmpNumbers)
                ->flatMap(function ($val) {
                    return array_map('trim', explode('|', $val));
                })
                ->filter()
                ->values();

            $dateStr = !empty($rawDate) ? Carbon::parse($rawDate)->format('F d, Y') : '';

            // Map each individual employee number to their model and extract full name
            $namesString = $empNumbers
                ->map(function ($empNo) use ($employeeInfoMap) {
                    $employee = $employeeInfoMap->get($empNo);
                    return $employee ? ($employee->empname ?? $employee->EmpNo) : null;
                })
                ->filter()
                ->implode(' | ');

            

            return [
                'name' => $namesString,
                'date' => $dateStr,
                'remarks' => $rawRemarks ?? '',
            ];
        };

        // ==========================================
        // 5. LOOP AND MAP FINAL OUTPUT
        // ==========================================
        // return response()->json(['personel' => $personel]);
        // foreach ($personel as $positionCategory => $slipsGroup) {
        //     if ($personel->has('Operator')) {
        //         foreach ($personel['Operator'] as $slip) {
        //             $productLine = $slip->product_line_details->dropdown_masters_details ?? '';
        //             // Map Approvers
        //             $approvers = collect($slip->op_approvers);
        //             $prodApp = $approvers->firstWhere('approval_status', 'APRODTO');
        //             $engApp  = $approvers->firstWhere('approval_status', 'BENGGTQ');
        //             $qcApp   = $approvers->firstWhere('approval_status', 'CQCC');


        //             // Formatted approvers with SystemOneHrisSubcon models
        //             $prod = $formatApprover($prodApp);
        //             $eng  = $formatApprover($engApp);
        //             $qc   = $formatApprover($qcApp);

        //             $prodApp->formatted = $prod;
        //             $engApp->formatted  = $eng;
        //             $qcApp->formatted   = $qc;

        //             // $prod['name'], $eng['name'], and $qc['name'] now hold an array 
        //             // of SystemOneHrisSubcon models corresponding to the selected approvers.
        //         }
        //     }
        //     if ($personel->has('Inspector')) {
        //         foreach ($personel['Inspector'] as $slip) {
        //             $productLine = $slip->product_line_details->dropdown_masters_details ?? '';

        //             // Map Approvers
        //             $approvers = collect($slip->op_approvers);

        //             // $prodApp = $approvers->firstWhere('approval_status', 'APRODTO');
        //             // $engApp  = $approvers->firstWhere('approval_status', 'BENGGTQ');
        //             $qcApp   = $approvers->firstWhere('approval_status', 'ALQCTQ');


        //             // Formatted approvers with SystemOneHrisSubcon models
        //             // $prod = $formatApprover($prodApp);
        //             // $eng  = $formatApprover($engApp);
        //             $qc   = $formatApprover($qcApp);

        //             // $prodApp->formatted = $prod;
        //             // $engApp->formatted  = $eng;
        //             $qcApp->formatted   = $qc;

        //             // $prod['name'], $eng['name'], and $qc['name'] now hold an array 
        //             // of SystemOneHrisSubcon models corresponding to the selected approvers.
        //         }
        //     }
        // }

        $roleStatusMapping = [
            'Operator'  => ['APRODTO', 'BENGGTQ', 'CQCC'],
            'Inspector' => ['ALQCTQ'],
        ];

        foreach ($roleStatusMapping as $role => $statuses) {
            if (!$personel->has($role)) {
                continue;
            }

            foreach ($personel[$role] as $slip) {
                // Single-pass indexing: group approvers by approval_status O(N) instead of multiple O(N) searches
                $approversByStatus = [];
                foreach ($slip->op_approvers ?? [] as $approver) {
                    $status = is_object($approver) ? $approver->approval_status : ($approver['approval_status'] ?? null);
                    if ($status) {
                        $approversByStatus[$status] = $approver;
                    }
                }

                // Safely format and attach to matching approvers
                foreach ($statuses as $status) {
                    if (isset($approversByStatus[$status])) {
                        $approver = $approversByStatus[$status];
                        $formatted = $formatApprover($approver);

                        if (is_object($approver)) {
                            $approver->formatted = $formatted;
                        } else {
                            $approver['formatted'] = $formatted;
                        }
                    }
                }
            }
        }

        $prod_line = DropdownMasterDetail::where('id', $request->product_line)->first();
        $product_line = $prod_line->dropdown_masters_details ?? '';
        $filename = "Certified_Personnel_List_{$request->section}_{$product_line}.xlsx";
        // return response()->json(['personel_toexport' => $personel]);
        return Excel::download(new CertifiedPersonnelExport($personel), $filename);
    }
}
