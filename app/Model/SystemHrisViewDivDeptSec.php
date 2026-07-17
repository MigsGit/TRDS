<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemHrisViewDivDeptSec extends Model
{
     protected $table = 'vw_DivDeptSec';
    protected $connection = 'mysql_systemone';
}
