<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemOneHrisTraining extends Model
{
    protected $table = 'tbl_Training';
    protected $connection = 'mysql_systemone';
}
