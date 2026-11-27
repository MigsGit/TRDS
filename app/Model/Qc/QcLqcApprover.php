<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcLqcApprover extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_slips_id',
        'approval_status',
        'alert_prod_sec',
        'alert_prod_cc_sec',
    ];
}
