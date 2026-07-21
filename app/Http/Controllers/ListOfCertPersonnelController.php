<?php

namespace App\Http\Controllers;

use App\Model\QcSlip;
use Illuminate\Http\Request;

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
        // return $request->all();
        $qcslips = QcSlip::with([
            'product_line_details',
            'op_approvers'
        ])
        ->whereNull('deleted_at')
        ->where('status', 'OK')
        ->where('section_category', $request->section)
        ->where('product_line', $request->product_line)
        ->get();

        return response()->json(['qcslips' => $qcslips]);
    }
}
