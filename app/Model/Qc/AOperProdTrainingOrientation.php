<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AOperProdTrainingOrientation extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_slips_id',
        'traning_items',
        'defect_escalation',
        'production_abnormality',
        'engg_tq_orientation_docs',
        'orientation_docs',
    ];
}
