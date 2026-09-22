<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AMhTrainingOrientation extends Model
{
    use HasFactory;
    protected $fillable = [
        'qc_slips_id',
        'mh_training_orientation',
    ];

}
