<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FQcValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_slips_id',
        'refdocno_input_qcvvo_oper',
        'refdocno_input_qcvvo_oper_2',
    ];
}
