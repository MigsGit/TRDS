<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EQcValidationProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_slips_id',
        'engg_application_vpes_oper',
        'engg_vpes_oper',
        'vpqcs_oper',
        'application_vpqcs_oper',
    ];
}
