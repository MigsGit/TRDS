<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcReasonCertification extends Model
{
    protected $fillable = [
        'qc_slips_id',
        'reason_of_certification',
        'transfer_flexibility',
        'others',
    ];
}
