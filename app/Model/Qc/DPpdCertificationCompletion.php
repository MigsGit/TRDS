<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DPpdCertificationCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_slips_id',
        'lot_1st_sample_peqcs_oper',
        '1st_injected_ng_peqcs_oper',
        '1st_detected_ng_peqcs_oper',
        '2nd_sample_peqcs_oper',
        '2nd_injected_ng_peqcs_oper',
        '2nd_detected_ng_peqcs_oper',
    ];
}
