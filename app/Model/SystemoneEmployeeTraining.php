<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemoneEmployeeTraining extends Model
{
    // use HasFactory;
    protected $table = 'vw_training';
    protected $connection = 'mysql_systemone';
}
