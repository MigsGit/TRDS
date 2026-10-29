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

        $section = $qcslips->pluck('section_category')->filter()->unique()->values()->toArray();
        $series = $qcslips->pluck('series_name')->filter()->unique()->values()->toArray();
        $product_line = $qcslips->pluck('product_line_details')->filter()->unique()->values()->toArray();
        $position = $qcslips->pluck('position_category')->filter()->unique()->values()->toArray();


        return response()->json(['section' => $section, 'series' => $series, 'product_line' => $product_line, 'qcslips' => $qcslips, 'position' => $position]);
    }

    // public function exportListCertPersonnel(Request $request){
    //     // ==========================================
    //     // 1. QUERY QC SLIPS WITH RELATIONS
    //     // ==========================================
    //     $exploded_series = explode(',', $request->series);
    //     $personel = QcSlip::with([
    //             // 'product_line_details',
    //             'op_approvers',
    //             'qc_slip_employees',
    //             'qc_slip_employees.system_one_subcon_emp_info',
    //             'qc_slip_employees.system_one_hris_emp_info',
    //             'qc_slip_employees.get_station_to',
    //             'qc_reason_certification'
    //         ])
    //         ->whereNull('deleted_at')
    //         ->where('status', 'OK')
    //         ->where('section_category', $request->section)
    //         ->where('product_line', 'like' , '%' . $request->product_line . '%')
    //         // ->where('series_name','like' , '%' . $request->series . '%')
    //         ->whereIn('series_name', $exploded_series)
    //         // ->where('position_category', $request->position)
    //         ->get()
    //         ->groupBy('position_category');

    //     // ==========================================
    //     // 2. REASON OF CERTIFICATION BATCH QUERY
    //     // ==========================================
    //     // Step 2A: Parse reason IDs into arrays
    //     $personel->transform(function ($group) {
    //         return $group->map(function ($slip) {
    //             $rawReasons = optional($slip->qc_reason_certification)->reason_of_certification;
                
    //             $slip->reason_of_certification_ids = $rawReasons 
    //                 ? array_map('trim', explode('|', $rawReasons)) 
    //                 : [];

    //             return $slip;
    //         });
    //     });


    //     // Step 2B: Extract unique Reason IDs & fetch batch details
    //     $allReasonIds = $personel->flatten(1)
    //         ->pluck('reason_of_certification_ids')
    //         ->flatten()
    //         ->unique()
    //         ->filter()
    //         ->values();

    //     $reasonDetails = DropdownMasterDetail::whereIn('id', $allReasonIds)->get();

    //     // Step 2C: Attach reason details back to slips
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

    //     // ==========================================
    //     // 3. APPROVER EMPLOYEES BATCH QUERY
    //     // ==========================================
    //     // Collect all approver IDs across all slips/groups without N+1 queries
    //     $allApproverEmpNos = $personel->flatten(1)
    //     ->flatMap(function ($slip) {
    //         return collect($slip->op_approvers)->flatMap(function ($app) {
    //             $rawEmpNos = [
    //                 $app->first_approver ?? null,
    //                 $app->first_approver_2 ?? null,
    //                 $app->second_approver ?? null,
    //                 $app->second_approver_2 ?? null,
    //             ];

    //             // Split any strings containing '|' into individual employee numbers
    //             return collect($rawEmpNos)
    //                 ->filter()
    //                 ->flatMap(function ($val) {
    //                     return array_map('trim', explode('|', $val));
    //                 });
    //         });
    //     })
    //     ->filter()
    //     ->unique()
    //     ->values();


    //     // Execute 1 single query using SystemOneHrisSubcon and 'EmpNo' column
    //     $employeeInfoMap = SystemOneHrisSubcon::whereIn('EmpNo', $allApproverEmpNos)
    //         ->get()
    //         ->keyBy('EmpNo');
    //     // return response()->json(['personel' => $personel, 'allApproverEmpNos' => $employeeInfoMap]);


    //     // ==========================================
    //     // 4. CLOSURE FOR FORMATTING APPROVERS
    //     // ==========================================
    //     $formatApprover = function ($app) use ($employeeInfoMap) {
    //         if (!$app) return ['name' => '', 'date' => ''];

    //         // Check if second approver exists and is not empty
    //         $hasSecond = !empty($app->second_approver) || !empty($app->second_approver_2);

    //         if ($hasSecond) {
    //             $rawEmpNumbers = array_filter([$app->second_approver ?? null, $app->second_approver_2 ?? null]);
    //             $rawDate       = $app->second_date ?? $app->second_approver_ddate ?? null;
    //             $rawRemarks    = $app->second_status ?? null;
    //         } else {
    //             $rawEmpNumbers = array_filter([$app->first_approver ?? null, $app->first_approver_2 ?? null]);
    //             $rawDate       = $app->first_date ?? $app->first_approver_ddate ?? null;
    //             $rawRemarks    = $app->first_status ?? null;

    //         }

    //         // Explode piped strings into an array of clean employee numbers
    //         $empNumbers = collect($rawEmpNumbers)
    //             ->flatMap(function ($val) {
    //                 return array_map('trim', explode('|', $val));
    //             })
    //             ->filter()
    //             ->values();

    //         $dateStr = !empty($rawDate) ? Carbon::parse($rawDate)->format('F d, Y') : '';

    //         // Map each individual employee number to their model and extract full name
    //         $namesString = $empNumbers
    //             ->map(function ($empNo) use ($employeeInfoMap) {
    //                 $employee = $employeeInfoMap->get($empNo);
    //                 return $employee ? ($employee->empname ?? $employee->EmpNo) : null;
    //             })
    //             ->filter()
    //             ->implode(' | ');

            

    //         return [
    //             'name' => $namesString,
    //             'date' => $dateStr,
    //             'remarks' => $rawRemarks ?? '',
    //         ];
    //     };

    //     $roleStatusMapping = [
    //         'Operator'  => ['APRODTO', 'BENGGTQ', 'CQCC'],
    //         'Inspector' => ['ALQCTQ'],
    //     ];

    //     foreach ($roleStatusMapping as $role => $statuses) {
    //         if (!$personel->has($role)) {
    //             continue;
    //         }

    //         foreach ($personel[$role] as $slip) {
    //             // Single-pass indexing: group approvers by approval_status O(N) instead of multiple O(N) searches
    //             $approversByStatus = [];
    //             foreach ($slip->op_approvers ?? [] as $approver) {
    //                 $status = is_object($approver) ? $approver->approval_status : ($approver['approval_status'] ?? null);
    //                 if ($status) {
    //                     $approversByStatus[$status] = $approver;
    //                 }
    //             }

    //             // Safely format and attach to matching approvers
    //             foreach ($statuses as $status) {
    //                 if (isset($approversByStatus[$status])) {
    //                     $approver = $approversByStatus[$status];
    //                     $formatted = $formatApprover($approver);

    //                     if (is_object($approver)) {
    //                         $approver->formatted = $formatted;
    //                     } else {
    //                         $approver['formatted'] = $formatted;
    //                     }
    //                 }
    //             }
    //         }
    //     }


    //     $prod_line = DropdownMasterDetail::where('id', $request->product_line)->first();
    //     $product_line = $prod_line->dropdown_masters_details ?? '';
    //     $filename = "Certified_Personnel_List_{$request->position}_{$request->section}_{$product_line}_{$request->series}.xlsx";
    //     // return response()->json(['personel_toexport' => $personel]);
    //     if($personel->isEmpty()){
    //         return view('errors.404', ['message' => 'No data found for the selected criteria. Please adjust your filters and try again.']);
    //     }
    //     return Excel::download(new CertifiedPersonnelExport($personel, $request->position, $product_line), $filename);
    // }

    public function exportListCertPersonnel(Request $request){
        $exploded_series = explode(',', $request->series);
    
        $positionsWithTraining = ['PartsPrep', 'MachineOperator', 'VisualOperator'];
        $categories = ['Inspector', 'Technician', 'Supervisor', 'MH', 'PartsPrep', 'MachineOperator', 'VisualOperator'];
        $otherCategories = array_diff($categories, $positionsWithTraining);

        // 1. Common relations that should ALWAYS be loaded for every position
        $commonRelations = [
            'qc_slip_employees',
            'qc_slip_employees.system_one_subcon_emp_info',
            'qc_slip_employees.system_one_hris_emp_info',
            'qc_slip_employees.get_station_to',
            'qc_reason_certification',
        ];

        // 2. Query 1: Operators (Loads training details)
        $operatorSlips = QcSlip::with(array_merge($commonRelations, ['c_lqc_training_item_results_details']))
            ->whereNull('deleted_at')
            ->where('status', 'OK')
            ->where('section_category', $request->section)
            ->where('product_line', 'like', '%' . $request->product_line . '%')
            ->whereIn('position_category', $positionsWithTraining)
            // ->whereIn('series_name', $exploded_series)
            ->get();

        // 3. Query 2: Other Categories (Loads approvers)
        $otherSlips = QcSlip::with(array_merge($commonRelations, ['op_approvers']))
            ->whereNull('deleted_at')
            ->where('status', 'OK')
            ->where('section_category', $request->section)
            ->where('product_line', 'like', '%' . $request->product_line . '%')
            ->whereIn('position_category', $otherCategories)
            // ->whereIn('series_name', $exploded_series)
            ->get();

        // 4. Combine, group dynamically under 'Operator', and convert to base collection to prevent getKey errors
        $personel = $operatorSlips->concat($otherSlips)->groupBy(function ($item) use ($positionsWithTraining) {
            if (in_array($item->position_category, $positionsWithTraining)) {
                return 'Operator';
            }
            return $item->position_category;
        })->toBase();

        // ==========================================
        // 5. REASON OF CERTIFICATION BATCH QUERY
        // ==========================================
        // Step 5A: Parse reason IDs into arrays
        $personel->transform(function ($group) {
            return $group->map(function ($slip) {
                $rawReasons = optional($slip->qc_reason_certification)->reason_of_certification;
                
                $slip->reason_of_certification_ids = $rawReasons 
                    ? array_map('trim', explode('|', $rawReasons)) 
                    : [];

                return $slip;
            });
        });

        // Step 5B: Extract unique Reason IDs & fetch batch details
        $allReasonIds = $personel->flatten(1)
            ->pluck('reason_of_certification_ids')
            ->flatten()
            ->unique()
            ->filter()
            ->values();

        $reasonDetails = DropdownMasterDetail::whereIn('id', $allReasonIds)->get();

        // Step 5C: Attach reason details back to slips
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
        // 6. PROCESS OPERATOR GROUP (Training Items Filter)
        // ==========================================
        if ($personel->has('Operator')) {
            $personel['Operator']->transform(function ($slip) {
                if ($slip->relationLoaded('c_lqc_training_item_results_details')) {
                    $filteredDetails = $slip->c_lqc_training_item_results_details
                        ->groupBy('position')
                        ->map(function ($items) {
                            return $items->sort(function ($a, $b) {
                                if ($a->day_number !== $b->day_number) {
                                    return $a->day_number <=> $b->day_number;
                                }
                                $dateA = $a->validation_date . ' ' . ($a->validation_time ?? '00:00:00');
                                $dateB = $b->validation_date . ' ' . ($b->validation_time ?? '00:00:00');
                                return strcmp($dateA, $dateB);
                            })->last();
                        })
                        ->values();

                    $slip->setRelation('c_lqc_training_item_results_details', $filteredDetails);
                }

                return $slip;
            });
        }

        // ==========================================
        // 7. PROCESS OTHER CATEGORIES (Approver Employees Batch Query)
        // ==========================================
        $nonOperatorGroups = $personel->except('Operator');

        if ($nonOperatorGroups->isNotEmpty()) {
            $allApproverEmpNos = $nonOperatorGroups->flatten(1)
                ->flatMap(function ($slip) {
                    return collect($slip->op_approvers)->flatMap(function ($app) {
                        $rawEmpNos = [
                            $app->first_approver ?? null,
                            $app->first_approver_2 ?? null,
                            $app->second_approver ?? null,
                            $app->second_approver_2 ?? null,
                        ];

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

            $employeeInfoMap = SystemOneHrisSubcon::whereIn('EmpNo', $allApproverEmpNos)
                ->get()
                ->keyBy('EmpNo');

            $formatApprover = function ($app) use ($employeeInfoMap) {
                if (!$app) return ['name' => '', 'date' => ''];

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

                $empNumbers = collect($rawEmpNumbers)
                    ->flatMap(function ($val) {
                        return array_map('trim', explode('|', $val));
                    })
                    ->filter()
                    ->values();

                $dateStr = !empty($rawDate) ? Carbon::parse($rawDate)->format('F d, Y') : '';

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

            $roleStatusMapping = [
                'Inspector' => ['ALQCTQ'],
                // Add other category status mappings here if needed
            ];

            foreach ($roleStatusMapping as $role => $statuses) {
                if (!$personel->has($role)) {
                    continue;
                }

                foreach ($personel[$role] as $slip) {
                    $approversByStatus = [];
                    foreach ($slip->op_approvers ?? [] as $approver) {
                        $status = is_object($approver) ? $approver->approval_status : ($approver['approval_status'] ?? null);
                        if ($status) {
                            $approversByStatus[$status] = $approver;
                        }
                    }

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
        }
        


        $prod_line = DropdownMasterDetail::where('id', $request->product_line)->first();
        $product_line = $prod_line->dropdown_masters_details ?? '';
        $filename = "Certified_Personnel_List_{$request->section}_{$product_line}({$request->series}).xlsx";
        // return response()->json(['personel_toexport' => $personel]);
        if($personel->isEmpty()){
            return view('errors.404', ['message' => 'No data found for the selected criteria. Please adjust your filters and try again.']);
        }
        return Excel::download(new CertifiedPersonnelExport($personel, $product_line, $positionsWithTraining), $filename);
    }
}
