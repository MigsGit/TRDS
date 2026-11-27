<?php

namespace App\Http\Controllers;

use App\Exports\InspectorCertificationMatrixExport;
use App\Model\DropdownMasterDetail;
use App\Model\QcSlip;
use Illuminate\Http\Request;
use Excel;

class InspCertMatrixController extends Controller
{
    public function exportInspectorCertMatrix(Request $request){

        // $exploded_product_line = explode(',', $request->product_line);
        // $exploded_product_line = array_map('trim', (array) $exploded_product_line);

        // $personel = QcSlip::with([
        //     'op_approvers',
        //     'qc_slip_employees' => function ($query) {
        //         $query->whereNull('deleted_at'); 
        //     },
        //     'qc_slip_employees.system_one_subcon_emp_info',
        //     'qc_slip_employees.system_one_hris_emp_info',
        //     'qc_slip_employees.get_station_to',
        //     'qc_reason_certification'
        // ])
        // ->whereNull('deleted_at')
        // ->where('status', 'OK')
        // ->where('section_category', $request->section)
        // ->where(function ($query) use ($exploded_product_line) {
        //     foreach ($exploded_product_line as $productLine) {
        //         $query->orWhere('product_line', 'LIKE', '%' . trim($productLine) . '%');
        //         // $query->orWhere('production_area', 'LIKE', '%' . trim($productArea) . '%');
        //     }
        // })
        // ->where('position_category', 'Inspector')
        // ->get()
        // ->map(function ($slip) use ($exploded_product_line) {
        //     // 1. Transform qc_slip_employees to single Object
        //     $slip->setRelation('qc_slip_employees', $slip->qc_slip_employees->first());

        //     // 2. Transform raw reasons pipe-separated string
        //     $rawReasons = optional($slip->qc_reason_certification)->reason_of_certification;
        //     $slip->reason_of_certification_ids = $rawReasons 
        //         ? array_map('trim', explode('|', $rawReasons)) 
        //         : [];
            
        //     // 3. Handle Product Line IDs
        //     $rawProductLineIds = $slip->product_line 
        //         ? array_map('trim', explode('|', $slip->product_line)) 
        //         : [];

        //     // FILTER: Keep ONLY the IDs that were selected in the request ($exploded_product_line)
        //     $filteredProductAreaIds = array_intersect($rawProductLineIds, $exploded_product_line);

        //     $slip->product_line_details = !empty($filteredProductAreaIds)
        //         ? DropdownMasterDetail::whereIn('id', $filteredProductAreaIds)->get()
        //         : collect();

        //     return $slip;
        // })
        // ->groupBy(function ($slip) {
        //     return optional($slip->qc_slip_employees)->employee_no ?? 'Unassigned';
        // })
        // // ------------------------------------------------------------------------
        // // SORT EMPLOYEES (OUTER ROWS) FROM OLDEST TO NEWEST BASED ON APPROVAL DATE
        // // ------------------------------------------------------------------------
        // ->sortBy(function ($employeeSlips) {
        //     // Find the earliest approval date among all slips of this employee
        //     $earliestTimestamp = $employeeSlips->map(function ($slip) {
        //         $opApprover = collect($slip->op_approvers)->firstWhere('approval_status', 'BLQCTC')
        //                 ?? collect($slip->op_approvers)->first();

        //         $dateStr = null;
        //         if ($opApprover) {
        //             $dateStr = !empty($opApprover['second_date']) 
        //                 ? $opApprover['second_date'] 
        //                 : ($opApprover['first_date'] ?? null);
        //         }

        //         if (empty($dateStr)) {
        //             $dateStr = $slip->appproval_at ?? '9999-12-31';
        //         }

        //         return \Carbon\Carbon::parse($dateStr)->timestamp;
        //     })->min(); // Get earliest date for this employee

        //     return $earliestTimestamp;
        // });
        
        // $prod_line = DropdownMasterDetail::whereIn('id', $exploded_product_line)->get();
        // $product_line = $prod_line->pluck('dropdown_masters_details')->flatten()->toArray();
        // $filename_prod_line = implode('_', $product_line);
        // // return gettype($product_line);

        // if($personel->isEmpty()){
        //     return view('errors.404', ['message' => 'No data found for the selected criteria. Please adjust your filters and try again.']);
        // }

        // return response()->json([
        //     'personel' => $personel,
        //     'prod_line' => $prod_line
        // ]);

        // $filename = "Inspector_Certification_Matrix_{$request->section}_{$filename_prod_line}.xlsx";
        // return Excel::download(new InspectorCertificationMatrixExport($personel, $prod_line, $request->section), $filename);

        $exploded_product_line = explode(',', $request->product_line);
        $exploded_product_line = array_map('trim', (array) $exploded_product_line);

        $personel = QcSlip::with([
            'op_approvers',
            'qc_slip_employees' => function ($query) {
                $query->whereNull('deleted_at'); 
            },
            'qc_slip_employees.system_one_subcon_emp_info',
            'qc_slip_employees.system_one_hris_emp_info',
            'qc_slip_employees.get_station_to',
            'qc_reason_certification'
        ])
        ->whereNull('deleted_at')
        ->where('status', 'OK')
        ->where('section_category', $request->section) // Keep original performance filter
        ->where(function ($query) use ($exploded_product_line) {
            foreach ($exploded_product_line as $productLine) {
                $query->orWhere('product_line', 'LIKE', '%' . trim($productLine) . '%');
            }
        })
        ->where('position_category', 'Inspector')
        ->get()
        ->map(function ($slip) use ($exploded_product_line) {
            // 1. Transform qc_slip_employees to single Object
            $slip->setRelation('qc_slip_employees', $slip->qc_slip_employees->first());

            // 2. Transform raw reasons pipe-separated string
            $rawReasons = optional($slip->qc_reason_certification)->reason_of_certification;
            $slip->reason_of_certification_ids = $rawReasons 
                ? array_map('trim', explode('|', $rawReasons)) 
                : [];
            
            // 3. Handle Product Line IDs
            $rawProductLineIds = $slip->product_line 
                ? array_map('trim', explode('|', $slip->product_line)) 
                : [];

            // FILTER: Keep ONLY the IDs that were selected in the request ($exploded_product_line)
            $filteredProductAreaIds = array_intersect($rawProductLineIds, $exploded_product_line);

            $slip->product_line_details = !empty($filteredProductAreaIds)
                ? DropdownMasterDetail::whereIn('id', $filteredProductAreaIds)->get()
                : collect();

            return $slip;
        })
        ->groupBy(function ($slip) {
            return optional($slip->qc_slip_employees)->employee_no ?? 'Unassigned';
        })
        // ------------------------------------------------------------------------
        // EXCLUDE EMPLOYEE IF THEIR LATEST GLOBAL RECORD IS IN A DIFFERENT SECTION
        // ------------------------------------------------------------------------
        ->filter(function ($employeeSlips, $employeeNo) use ($request) {
            if ($employeeNo === 'Unassigned') {
                return false;
            }

            // Helper to get timestamp of any slip
            $getSlipTimestamp = function ($slip) {
                $opApprover = collect($slip->op_approvers)->firstWhere('approval_status', 'BLQCTC')
                    ?? collect($slip->op_approvers)->first();

                $dateStr = null;
                if ($opApprover) {
                    $dateStr = !empty($opApprover['second_date']) 
                        ? $opApprover['second_date'] 
                        : ($opApprover['first_date'] ?? null);
                }

                if (empty($dateStr)) {
                    $dateStr = $slip->appproval_at ?? '1970-01-01';
                }

                return \Carbon\Carbon::parse($dateStr)->timestamp;
            };

            // Find the latest slip currently loaded for this employee
            $latestLoadedSlip = $employeeSlips->sortByDesc($getSlipTimestamp)->first();
            $latestLoadedTimestamp = $latestLoadedSlip ? $getSlipTimestamp($latestLoadedSlip) : 0;

            // Check if this employee has ANY slip in the database under a *different* section category with a *newer* date
            $hasNewerInOtherSection = QcSlip::whereNull('deleted_at')
                ->where('status', 'OK')
                ->where('section_category', '!=', $request->section)
                ->whereHas('qc_slip_employees', function ($q) use ($employeeNo) {
                    $q->where('employee_no', $employeeNo)->whereNull('deleted_at');
                })
                ->get()
                ->contains(function ($otherSlip) use ($getSlipTimestamp, $latestLoadedTimestamp) {
                    return $getSlipTimestamp($otherSlip) > $latestLoadedTimestamp;
                });

            // If they have a newer record in another section category, exclude them
            if ($hasNewerInOtherSection) {
                return false;
            }

            return true;
        })
        // ------------------------------------------------------------------------
        // SORT EMPLOYEES (OUTER ROWS) FROM OLDEST TO NEWEST BASED ON APPROVAL DATE
        // ------------------------------------------------------------------------
        ->sortBy(function ($employeeSlips) {
            $getSlipTimestamp = function ($slip) {
                $opApprover = collect($slip->op_approvers)->firstWhere('approval_status', 'BLQCTC')
                    ?? collect($slip->op_approvers)->first();

                $dateStr = null;
                if ($opApprover) {
                    $dateStr = !empty($opApprover['second_date']) 
                        ? $opApprover['second_date'] 
                        : ($opApprover['first_date'] ?? null);
                }

                if (empty($dateStr)) {
                    $dateStr = $slip->appproval_at ?? '9999-12-31';
                }

                return \Carbon\Carbon::parse($dateStr)->timestamp;
            };

            return $employeeSlips->map($getSlipTimestamp)->min();
        });
        
        $prod_line = DropdownMasterDetail::whereIn('id', $exploded_product_line)->get();
        $product_line = $prod_line->pluck('dropdown_masters_details')->flatten()->toArray();
        $filename_prod_line = implode('_', $product_line);

        if($personel->isEmpty()){
            return view('errors.404', ['message' => 'No data found for the selected criteria. Please adjust your filters and try again.']);
        }

        // return response()->json([
        //     'personel' => $personel,
        //     'prod_line' => $prod_line
        // ]);
        $clean_prod_line = preg_replace('/[^\w\-]/', '_', $filename_prod_line);
        $filename = "Inspector_Certification_Matrix_{$request->section}_{$clean_prod_line}.xlsx";
        return Excel::download(new InspectorCertificationMatrixExport($personel, $prod_line, $request->section), $filename);
    }
}
