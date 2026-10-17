<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ASepTrainingOrientation extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_slips_id',
        'sep_training_orientation',
    ];
}
