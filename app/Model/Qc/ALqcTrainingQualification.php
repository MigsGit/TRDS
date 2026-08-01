<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ALqcTrainingQualification extends Model
{
    use HasFactory;

    protected $fillable = [
        "qc_slips_id",
        'training_orientation_inspector',
        'training_orientation_ins_4',
        'training_orientation_ins_13',
        'training_orientation_ins_21',
        'training_orientation_ins_54',
    ];
}
