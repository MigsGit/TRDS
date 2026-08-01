<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BLqcCertification extends Model
{
    protected $fillable = [ 
        'qc_slips_id',
        'result_input1_inspector', 'text_result_input1_inspector',
        'hands_on_inspector',
        'hands_on_ins_3',
    ];
}
