<?php

namespace App\Model\Qc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ATechEngTrainingQualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'es_tech_training_orientation',
        'es_tech_training_orientation_14',
        'es_tech_training_orientation_15',
        'es_tech_training_orientation_16',
    ];
}
