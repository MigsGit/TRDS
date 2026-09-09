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
            ->where('section_category', $request->section)
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
                $filteredProductLineIds = array_intersect($rawProductLineIds, $exploded_product_line);

                $slip->product_line_details = !empty($filteredProductLineIds)
                    ? DropdownMasterDetail::whereIn('id', $filteredProductLineIds)->get()
                    : collect();

                return $slip;
            })
            ->groupBy(function ($slip) {
                return optional($slip->qc_slip_employees)->employee_no ?? 'Unassigned';
            });
        
        $prod_line = DropdownMasterDetail::whereIn('id', $exploded_product_line)->get();
        $product_line = $prod_line->pluck('dropdown_masters_details')->flatten()->toArray();
        $filename_prod_line = implode('_', $product_line);
        // return gettype($product_line);

        if($personel->isEmpty()){
            return view('errors.404', ['message' => 'No data found for the selected criteria. Please adjust your filters and try again.']);
        }

        // return response()->json([
        //     'personel' => $personel,
        //     'prod_line' => $prod_line
        // ]);

        $filename = "Inspector_Certification_Matrix_{$request->section}_{$filename_prod_line}.xlsx";
        return Excel::download(new InspectorCertificationMatrixExport($personel, $prod_line, $request->section), $filename);
    }
}
