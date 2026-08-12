<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOpEnggSectionTrainingOrientation extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_slips_id',
        'traning_items',
        'engg_orientation_docs',
        'obs_first_result_es_oper',
        'first_sample_es_oper',
        'first_ok_es_oper',
        'first_ng_es_oper',
        'obs_second_result_es_oper',
        'second_sample_es_oper',
        'second_ok_es_oper',
        'second_ng_es_oper',
    ];
}
